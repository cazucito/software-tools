<?php
declare(strict_types=1);
namespace St;

if (!defined('ST_APP')) {
    exit;
}

/**
 * Edición del catálogo desde el panel admin (ADR-008): escribe en
 * catalog.sqlite (por eso la app ve los cambios al instante), mantiene
 * tags/relaciones/FTS consistentes y marca el slug en catalog_dirty.
 * La reconciliación con el repo se hace por export a Markdown.
 */
final class CatalogAdmin
{
    /** Campos editables de una ficha. */
    public const EDITABLE = [
        'name', 'year', 'used_until', 'category', 'context', 'body',
        'image', 'successor', 'successor_slug', 'published',
    ];

    /**
     * Guarda los campos de una herramienta. Devuelve error o null.
     *
     * @param array<string,mixed> $fields
     */
    public static function updateTool(string $slug, array $fields): ?string
    {
        $stmt = Db::catalogW()->prepare('SELECT id FROM tools WHERE slug = ?');
        $stmt->execute([$slug]);
        $id = $stmt->fetchColumn();
        if ($id === false) {
            return 'La herramienta no existe.';
        }
        $id = (int) $id;
        $pdo = Db::catalogW();

        $pdo->prepare(
            'UPDATE tools SET name = ?, year = ?, used_until = ?, category = ?, context = ?,
                              body = ?, image = ?, successor = ?, successor_slug = ?, published = ?,
                              updated_at = ?
             WHERE slug = ?'
        )->execute([
            (string) $fields['name'],
            (int) $fields['year'],
            $fields['used_until'] !== null && $fields['used_until'] !== '' ? (int) $fields['used_until'] : null,
            (string) $fields['category'],
            (string) $fields['context'],
            (string) $fields['body'],
            $fields['image'] !== '' ? (string) $fields['image'] : null,
            $fields['successor'] !== '' ? (string) $fields['successor'] : null,
            $fields['successor_slug'] !== '' ? (string) $fields['successor_slug'] : null,
            !empty($fields['published']) ? 1 : 0,
            gmdate('c'),
            $slug,
        ]);

        self::setTags($id, $fields['tags'] ?? []);
        self::setRelations($id, $fields['related'] ?? []);
        self::refreshFts($id, $slug);
        Ops::markDirty($slug);
        return null;
    }

    /** Reemplaza las etiquetas de una herramienta (preserva el orden dado). @param list<string> $tags */
    public static function setTags(int $toolId, array $tags): void
    {
        $pdo = Db::catalogW();
        $tags = array_values(array_unique(array_filter(array_map(
            static fn ($t): string => trim((string) $t),
            $tags
        ), static fn (string $t): bool => $t !== '')));
        $pdo->prepare('DELETE FROM tool_tags WHERE tool_id = ?')->execute([$toolId]);
        foreach ($tags as $tag) {
            $pdo->prepare('INSERT OR IGNORE INTO tags(name) VALUES (?)')->execute([$tag]);
            $tagId = (int) $pdo->query('SELECT id FROM tags WHERE name = ' . $pdo->quote($tag))->fetchColumn();
            $pdo->prepare('INSERT OR IGNORE INTO tool_tags(tool_id, tag_id) VALUES (?,?)')->execute([$toolId, $tagId]);
        }
    }

    /** Reemplaza las relaciones de una herramienta. @param list<string> $slugs */
    public static function setRelations(int $toolId, array $slugs): void
    {
        $pdo = Db::catalogW();
        $slugs = array_values(array_unique(array_filter(array_map(
            static fn ($s): string => trim((string) $s),
            $slugs
        ), static fn (string $s): bool => $s !== '')));
        sort($slugs);
        $pdo->prepare('DELETE FROM tool_relations WHERE tool_id = ?')->execute([$toolId]);
        foreach ($slugs as $rel) {
            $pdo->prepare('INSERT OR IGNORE INTO tool_relations(tool_id, related_slug) VALUES (?,?)')->execute([$toolId, $rel]);
        }
    }

    /** Público: alterna published (1/0). */
    public static function togglePublished(string $slug): void
    {
        $pdo = Db::catalogW();
        $pdo->prepare('UPDATE tools SET published = CASE published WHEN 1 THEN 0 ELSE 1 END, updated_at = ? WHERE slug = ?')
            ->execute([gmdate('c'), $slug]);
        Ops::markDirty($slug);
    }

    /** Reconstruye la fila FTS de una herramienta (nombre, contexto, cuerpo, tags). */
    private static function refreshFts(int $toolId, string $slug): void
    {
        $pdo = Db::catalogW();
        $stmt = $pdo->prepare(
            'SELECT t.name, t.context, t.body,
                    (SELECT GROUP_CONCAT(g.name, \' \') FROM tool_tags tt JOIN tags g ON g.id = tt.tag_id WHERE tt.tool_id = t.id) AS tags
             FROM tools t WHERE t.id = ?'
        );
        $stmt->execute([$toolId]);
        $row = $stmt->fetch();
        if (!$row) {
            return;
        }
        $pdo->prepare('DELETE FROM tools_fts WHERE rowid = ?')->execute([$toolId]);
        $pdo->prepare('INSERT INTO tools_fts(rowid, name, context, body, tags) VALUES (?,?,?,?,?)')
            ->execute([$toolId, $row['name'], $row['context'], $row['body'], (string) ($row['tags'] ?? '')]);
    }
}
