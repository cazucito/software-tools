<?php
declare(strict_types=1);

/**
 * Front controller de software-tools v3.
 *
 * Enrutado por query-string (index.php?p=ruta) con soporte de PATH_INFO.
 * Estilo "pretty" (/tools/eudora) se habilita con rewrite del servidor +
 * config url_style=pretty (Fase 7).
 *
 * Localiza app/server/ en cualquiera de los dos despliegues:
 *   - desarrollo:  app/public/index.php  → ../server (hermano)
 *   - hosting:     proto/index.php       → ./server  (mismo nivel)
 */

use St\Catalog;
use St\View;

$serverDir = is_dir(__DIR__ . '/server') ? __DIR__ . '/server' : dirname(__DIR__) . '/server';
require $serverDir . '/bootstrap.php';

try {
    // ----- Ruta solicitada -------------------------------------------------
    $routePath = trim((string) ($_GET['p'] ?? ''), '/');
    if ($routePath === '') {
        $routePath = trim((string) ($_SERVER['PATH_INFO'] ?? ''), '/');
    }
    $segments = $routePath === ''
        ? []
        : array_values(array_filter(explode('/', $routePath), static fn (string $s): bool => $s !== ''));

    // ----- Modalidad activa (ADR-006) --------------------------------------
    $modes = st_config('modes');
    $mode = (string) ($_GET['modo'] ?? '');
    if (!in_array($mode, $modes, true)) {
        $mode = (string) st_config('default_mode');
    }

    // Parámetros que viajan entre páginas (sin p/modo/y).
    $params = [];
    foreach ($_GET as $key => $value) {
        if (!in_array($key, ['p', 'modo', 'y'], true) && is_string($value)) {
            $params[$key] = $value;
        }
    }

    $common = ['mode' => $mode, 'modes' => $modes, 'params' => $params];

    $notFound = static function () use ($common): void {
        http_response_code(404);
        View::page('404', $common + [
            'route' => '',
            'title' => 'No encontrado — software-tools',
        ]);
    };

    // ----- Home: timeline con modalidades ----------------------------------
    if ($segments === []) {
        $timeline = Catalog::timelineData();
        View::page('home', $common + [
            'route'       => '',
            'title'       => 'software-tools — ' . $timeline['stats']['count'] . ' herramientas, una historia (' . $timeline['stats']['minYear'] . ' → hoy)',
            'description' => 'Archivo personal de software: ' . $timeline['stats']['count'] . ' herramientas de '
                . $timeline['stats']['minYear'] . ' a hoy, cada una con su historia de uso.',
            'stDataJson'  => json_encode(
                $timeline,
                JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
            ),
            'modeScript'  => $mode,
            'tools'       => $timeline['tools'],
            'stats'       => $timeline['stats'],
            'spotlight'   => Catalog::toolBySlug('eudora'),   // fotograma destacado de «Cinta»
            'featured'    => Catalog::toolBySlug('turbo-c'),  // estación destacada de «Línea»
            'current'     => Catalog::toolsBySlugs(['docker', 'intellij-idea', 'kubernetes', 'visual-studio-code']),
        ]);
        exit;
    }

    // ----- Catálogo completo -----------------------------------------------
    if ($segments === ['tools']) {
        $all = Catalog::tools();
        View::page('tools-index', $common + [
            'route' => 'tools',
            'title' => 'Catálogo — software-tools',
            'description' => 'Las ' . count($all) . ' herramientas del archivo, por década.',
            'tools' => $all,
            'stats' => Catalog::stats(),
        ]);
        exit;
    }

    // ----- Ficha de herramienta ---------------------------------------------
    if (count($segments) === 2 && $segments[0] === 'tools') {
        $slug = $segments[1];
        if (!preg_match('/^[a-z0-9-]+$/', $slug)) {
            $notFound();
            exit;
        }
        $tool = Catalog::toolBySlug($slug);
        if (!$tool) {
            $notFound();
            exit;
        }
        $succTool = null;
        if (!empty($tool['successor_slug'])) {
            $succTool = Catalog::toolBySlug((string) $tool['successor_slug']);
        }
        $bodyHtml = (new Parsedown())->setSafeMode(true)->text((string) $tool['body']);
        View::page('tool', $common + [
            'route'      => 'tools/' . $slug,
            'title'      => $tool['name'] . ' (' . $tool['year'] . ') — software-tools',
            'description' => mb_substr((string) $tool['context'], 0, 160),
            'tool'       => $tool,
            'related'    => Catalog::toolsBySlugs($tool['related']),
            'neighbours' => Catalog::neighbours($slug),
            'succTool'   => $succTool,
            'bodyHtml'   => $bodyHtml,
        ]);
        exit;
    }

    // ----- Búsqueda ---------------------------------------------------------
    if ($segments === ['search']) {
        $q = trim((string) ($_GET['q'] ?? ''));
        $result = $q !== '' ? Catalog::search($q) : ['q' => '', 'rows' => [], 'total' => 0];
        View::page('search', $common + [
            'route' => 'search',
            'title' => $q !== '' ? ('Buscar: ' . $q . ' — software-tools') : 'Buscar — software-tools',
            'q'      => $q,
            'result' => $result,
        ]);
        exit;
    }

    // ----- API: búsqueda instantánea ----------------------------------------
    if ($segments === ['api', 'search']) {
        header('Content-Type: application/json; charset=utf-8');
        $q = trim((string) ($_GET['q'] ?? ''));
        $result = $q !== '' ? Catalog::search($q, 30) : ['q' => '', 'rows' => [], 'total' => 0];
        $rows = array_map(static function (array $r): array {
            return [
                'slug'     => $r['slug'],
                'name'     => $r['name'],
                'year'     => (int) $r['year'],
                'category' => $r['category'],
                'snippet'  => $r['snip'] !== null ? strip_tags((string) $r['snip']) : '',
            ];
        }, $result['rows']);
        echo json_encode(
            ['q' => $result['q'], 'total' => $result['total'], 'results' => $rows],
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );
        exit;
    }

    $notFound();
} catch (Throwable $e) {
    http_response_code(500);
    error_log('[software-tools] ' . $e->getMessage());
    if (st_config('debug')) {
        header('Content-Type: text/plain; charset=utf-8');
        echo $e->getMessage() . "\n\n" . $e->getTraceAsString();
    } else {
        header('Content-Type: text/plain; charset=utf-8');
        echo 'Error interno. Inténtalo de nuevo.';
    }
}
