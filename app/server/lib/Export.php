<?php
declare(strict_types=1);
namespace St;

if (!defined('ST_APP')) {
    exit;
}

/**
 * Exportación a Markdown (ADR-008): reconstruye el formato canónico de las
 * fichas (src/content/tools/*.md) a partir de la base de datos.
 * Con las fuentes sin editar, el export debe reproducirlas prácticamente
 * idénticas (round-trip); verificado por ops/tools/check_export.py.
 */
final class Export
{
    /** Markdown completo de una herramienta (frontmatter + cuerpo). */
    public static function toolMarkdown(array $tool): string
    {
        $usedUntil = $tool['used_until'] !== null ? (string) (int) $tool['used_until'] : 'null';
        $successor = ($tool['successor'] ?? null) !== null && $tool['successor'] !== '' ? self::q((string) $tool['successor']) : 'null';
        $successorSlug = ($tool['successor_slug'] ?? null) !== null && $tool['successor_slug'] !== '' ? self::q((string) $tool['successor_slug']) : 'null';

        $lines = [];
        $lines[] = '---';
        $lines[] = 'name: ' . self::q((string) $tool['name']);
        $lines[] = 'slug: ' . self::q((string) $tool['slug']);
        $lines[] = 'year: ' . (int) $tool['year'];
        $lines[] = 'category: ' . self::q((string) $tool['category']);
        $lines[] = 'tags: ' . self::arr($tool['tags'] ?? []);
        if (!empty($tool['image'])) {
            $lines[] = 'image: ' . self::q((string) $tool['image']);
        }
        $lines[] = 'context: ' . self::q((string) $tool['context']);
        $lines[] = 'usedUntil: ' . $usedUntil;
        $lines[] = 'successor: ' . $successor;
        $lines[] = 'successorSlug: ' . $successorSlug;
        $lines[] = 'related: ' . self::arr($tool['related'] ?? $tool['related_slugs'] ?? []);
        $lines[] = 'published: ' . (!empty($tool['published']) ? 'true' : 'false');
        $lines[] = '---';
        $lines[] = '';
        $lines[] = rtrim((string) $tool['body']);
        $lines[] = '';
        return implode("\n", $lines);
    }

    /** Cuerpo del export: frontmatter regenerado + cuerpo (para descarga directa). */
    public static function download(string $slug): void
    {
        $tool = Catalog::toolBySlug($slug);
        if ($tool === null) {
            http_response_code(404);
            exit('404');
        }
        $md = self::toolMarkdown($tool);
        header('Content-Type: text/markdown; charset=utf-8');
        header('X-Content-Type-Options: nosniff');
        header('Content-Disposition: attachment; filename="' . $slug . '.md"');
        echo $md;
        exit;
    }

    /** Bundle de todas las herramientas exportadas, concatenadas. */
    public static function bundle(): void
    {
        $tools = Catalog::tools();
        $pieces = [];
        foreach ($tools as $tool) {
            $tool['related'] = [];
            $stmt = Db::pdo()->prepare('SELECT related_slug FROM tool_relations WHERE tool_id = ? ORDER BY related_slug');
            $stmt->execute([(int) $tool['id']]);
            $tool['related'] = $stmt->fetchAll(\PDO::FETCH_COLUMN);
            $pieces[] = self::toolMarkdown($tool);
        }
        header('Content-Type: text/markdown; charset=utf-8');
        header('X-Content-Type-Options: nosniff');
        header('Content-Disposition: attachment; filename="software-tools-export.md"');
        echo implode("\n---\n\n", $pieces);
        exit;
    }

    /** Comillas dobles estilo canónico de las fichas. */
    private static function q(string $s): string
    {
        return '"' . str_replace(['\\', '"'], ['\\\\', '\\"'], $s) . '"';
    }

    /** @param array<int,string> $items */
    private static function arr(array $items): string
    {
        return '[' . implode(', ', array_map(static fn ($s): string => self::q((string) $s), $items)) . ']';
    }
}
