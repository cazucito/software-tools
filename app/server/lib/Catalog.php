<?php
declare(strict_types=1);
namespace St;

if (!defined('ST_APP')) {
    exit;
}

/**
 * Consultas de lectura del catálogo: herramientas, etiquetas, relaciones,
 * estadísticas del archivo y búsqueda de texto completo (FTS5).
 */
final class Catalog
{
    /**
     * Todas las herramientas publicadas, en orden cronológico, con sus etiquetas.
     *
     * @return list<array<string,mixed>>
     */
    public static function tools(): array
    {
        $rows = Db::pdo()->query(
            'SELECT id, slug, name, year, used_until, category, context, body,
                    image, successor, successor_slug, next_version, published
             FROM tools WHERE published = 1
             ORDER BY year ASC, name ASC, slug ASC'
        )->fetchAll();

        $tags = self::tagsByTool();
        foreach ($rows as &$row) {
            $row['tags'] = $tags[(int) $row['id']] ?? [];
        }
        return $rows;
    }

    /**
     * Una herramienta por slug (con etiquetas y relaciones), o null.
     *
     * @return array<string,mixed>|null
     */
    public static function toolBySlug(string $slug): ?array
    {
        $stmt = Db::pdo()->prepare(
            'SELECT id, slug, name, year, used_until, category, context, body,
                    image, successor, successor_slug, next_version, published
             FROM tools WHERE published = 1 AND slug = ?'
        );
        $stmt->execute([$slug]);
        $tool = $stmt->fetch();
        if (!$tool) {
            return null;
        }
        $tool['tags'] = self::tagsByTool()[(int) $tool['id']] ?? [];
        $tool['related'] = self::relatedSlugs((int) $tool['id']);
        return $tool;
    }

    /**
     * Nombres+años de las herramientas relacionadas (para chips en la ficha).
     *
     * @param list<string> $slugs
     * @return list<array{slug:string,name:string,year:int}>
     */
    public static function toolsBySlugs(array $slugs): array
    {
        if (!$slugs) {
            return [];
        }
        $placeholders = implode(',', array_fill(0, count($slugs), '?'));
        $stmt = Db::pdo()->prepare(
            "SELECT slug, name, year FROM tools
             WHERE published = 1 AND slug IN ($placeholders)
             ORDER BY year ASC, name ASC"
        );
        $stmt->execute(array_values($slugs));
        return $stmt->fetchAll();
    }

    /**
     * Herramientas «en uso hoy» (sin fecha de fin), las más recientes primero.
     *
     * @param int $limit
     * @return list<array{slug:string,name:string,year:int}>
     */
    public static function currentTools(int $limit = 5): array
    {
        $stmt = Db::pdo()->prepare(
            'SELECT slug, name, year FROM tools
             WHERE published = 1 AND used_until IS NULL
             ORDER BY year DESC, name ASC
             LIMIT ?'
        );
        $stmt->bindValue(1, $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Vecinos cronológicos (anterior y siguiente) de una herramienta.
     *
     * @return array{prev:?array<string,mixed>,next:?array<string,mixed>}
     */
    public static function neighbours(string $slug): array
    {
        $rows = Db::pdo()->query(
            'SELECT slug, name, year FROM tools WHERE published = 1
             ORDER BY year ASC, name ASC, slug ASC'
        )->fetchAll();
        $prev = null;
        $next = null;
        foreach ($rows as $i => $row) {
            if ($row['slug'] === $slug) {
                $prev = $i > 0 ? $rows[$i - 1] : null;
                $next = $i < count($rows) - 1 ? $rows[$i + 1] : null;
                break;
            }
        }
        return ['prev' => $prev, 'next' => $next];
    }

    /**
     * Datos para la home-timeline: stats + herramientas (shape que consumen
     * los módulos JS de las tres modalidades).
     *
     * @return array{stats:array<string,mixed>,tools:list<array<string,mixed>>}
     */
    public static function timelineData(): array
    {
        $stats = self::stats();
        $tools = [];
        foreach (self::tools() as $row) {
            $tools[] = [
                'name'       => $row['name'],
                'slug'       => $row['slug'],
                'year'       => (int) $row['year'],
                'usedUntil'  => $row['used_until'] !== null ? (int) $row['used_until'] : null,
                'category'   => $row['category'],
                'tags'       => $row['tags'],
                'context'    => $row['context'],
                'successor'  => $row['successor'],
            ];
        }
        return ['stats' => $stats, 'tools' => $tools];
    }

    /**
     * Estadísticas del archivo para los números del copy.
     *
     * @return array<string,mixed>
     */
    public static function stats(): array
    {
        $row = Db::pdo()->query(
            'SELECT COUNT(*) AS c, MIN(year) AS mn, MAX(year) AS mx,
                    MAX(used_until) AS um
             FROM tools WHERE published = 1'
        )->fetch();
        $categories = Db::pdo()->query(
            'SELECT DISTINCT category FROM tools WHERE published = 1 ORDER BY category'
        )->fetchAll(\PDO::FETCH_COLUMN);
        $today = (int) date('Y');
        return [
            'count'        => (int) $row['c'],
            'minYear'      => (int) $row['mn'],
            'maxYear'      => (int) $row['mx'],
            'span'         => (int) $row['mx'] - (int) $row['mn'],
            'today'        => $today,
            'yearsLife'    => $today - (int) $row['mn'],
            'usedUntilMax' => (int) $row['um'],
            'categories'   => $categories,
            'tagsCount'    => (int) Db::pdo()->query('SELECT COUNT(*) FROM tags')->fetchColumn(),
        ];
    }

    /**
     * Búsqueda de texto completo (FTS5) con resaltado. Si la consulta FTS
     * fallara (sintaxis rara), cae a un LIKE simple.
     *
     * @return array{q:string,rows:list<array<string,mixed>>,total:int}
     */
    public static function search(string $q, int $limit = 50): array
    {
        $q = trim($q);
        $tokens = preg_split('/\s+/u', $q, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        if (!$tokens) {
            return ['q' => $q, 'rows' => [], 'total' => 0];
        }

        // Cada token entre comillas (escapa comillas internas) + prefijo.
        $match = implode(' ', array_map(
            static fn (string $t): string => '"' . str_replace('"', '""', $t) . '"*',
            $tokens
        ));

        $sql = "SELECT t.slug, t.name, t.year, t.category, t.context,
                       snippet(tools_fts, 1, '<mark>', '</mark>', ' … ', 16) AS snip,
                       highlight(tools_fts, 0, '<mark>', '</mark>') AS name_hl,
                       bm25(tools_fts, 10.0, 4.0, 1.0, 5.0) AS score
                FROM tools_fts
                JOIN tools t ON t.id = tools_fts.rowid
                WHERE tools_fts MATCH ?
                ORDER BY score
                LIMIT " . max(1, min(200, $limit));

        try {
            $stmt = Db::pdo()->prepare($sql);
            $stmt->execute([$match]);
            $rows = $stmt->fetchAll();
        } catch (\PDOException $e) {
            // Fallback: LIKE simple sobre nombre/contexto.
            $like = '%' . $q . '%';
            $stmt = Db::pdo()->prepare(
                'SELECT slug, name, year, category, context,
                        NULL AS snip, name AS name_hl, 0 AS score
                 FROM tools WHERE published = 1
                   AND (name LIKE ? OR context LIKE ? OR body LIKE ?)
                 ORDER BY year ASC LIMIT ' . max(1, min(200, $limit))
            );
            $stmt->execute([$like, $like, $like]);
            $rows = $stmt->fetchAll();
        }

        return ['q' => $q, 'rows' => $rows, 'total' => count($rows)];
    }

    /**
     * Ids de herramientas relacionadas (slugs) de una herramienta.
     *
     * @return list<string>
     */
    private static function relatedSlugs(int $toolId): array
    {
        // rowid = orden de inserción (fiel al orden del Markdown original)
        $stmt = Db::pdo()->prepare('SELECT related_slug FROM tool_relations WHERE tool_id = ? ORDER BY rowid');
        $stmt->execute([$toolId]);
        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }

    /**
     * Mapa tool_id => [etiquetas] en una sola consulta.
     *
     * @return array<int,list<string>>
     */
    private static function tagsByTool(): array
    {
        $map = [];
        $rows = Db::pdo()->query(
            'SELECT tt.tool_id, g.name FROM tool_tags tt
             JOIN tags g ON g.id = tt.tag_id
             ORDER BY tt.rowid'
        )->fetchAll();
        foreach ($rows as $row) {
            $map[(int) $row['tool_id']][] = (string) $row['name'];
        }
        return $map;
    }
}
