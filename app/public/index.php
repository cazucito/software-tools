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

use St\Admin;
use St\Catalog;
use St\Comments;
use St\Downloads;
use St\I18n;
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

    // ----- Idioma (base; /en/ se activa en Fase 6) -------------------------
    $lang = (string) (st_config('lang') ?: 'es');
    if (($segments[0] ?? '') === 'en' && isset((st_config('langs') ?? [])['en'])) {
        $lang = 'en';
        array_shift($segments);
    }
    I18n::setLang($lang);

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

    $common = ['mode' => $mode, 'modes' => $modes, 'params' => $params, 'lang' => $lang];

    // Mapa de íconos (1 query por página) para listas y fichas — ADR-010.
    $common['icons'] = \St\Ops::iconMap();

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
        $timeline['icons'] = \St\Ops::iconMap();
        $canonical = st_abs_url('');
        View::page('home', $common + [
            'route'       => '',
            'title'       => 'software-tools — ' . $timeline['stats']['count'] . ' herramientas, una historia (' . $timeline['stats']['minYear'] . ' → hoy)',
            'description' => 'Archivo personal de software: ' . $timeline['stats']['count'] . ' herramientas de '
                . $timeline['stats']['minYear'] . ' a hoy, cada una con su historia de uso.',
            'canonical'   => $canonical,
            'ogType'      => 'website',
            'jsonLd'      => json_encode([
                '@context'   => 'https://schema.org',
                '@type'      => 'WebSite',
                'name'       => 'software-tools',
                'url'        => $canonical,
                'description' => 'Archivo personal de software, 1991 → hoy.',
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
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

    // ----- Catálogo completo (con filtros combinables, ADR-010) -------------
    if ($segments === ['tools']) {
        $filters = [
            'd' => isset($_GET['decada']) && preg_match('/^\d{4}$/', (string) $_GET['decada'])
                ? (int) $_GET['decada'] : null,
            'c' => trim((string) ($_GET['categoria'] ?? '')),
            't' => trim((string) ($_GET['tag'] ?? '')),
        ];
        $active = array_filter($filters, static fn ($v): bool => $v !== null && $v !== '');
        $all = Catalog::toolsFiltered($active);
        $stats = Catalog::stats();
        View::page('tools-index', $common + [
            'route' => 'tools',
            'title' => 'Catálogo — software-tools',
            'description' => 'Las ' . count($all) . ' herramientas del archivo, por década.',
            'canonical' => st_abs_url('tools', $active),
            'tools' => $all,
            'stats' => $stats,
            'filters' => $filters,
            'tagOptions' => Catalog::tagsIndex(),
            'catOptions' => Catalog::categories(),
            'decades' => range((int) floor((int) $stats['minYear'] / 10) * 10, (int) floor((int) $stats['maxYear'] / 10) * 10, 10),
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

        // Avisos post-redirect (comentario / desbloqueo de descarga).
        $notice = null;
        $c = (string) ($_GET['c'] ?? '');
        if ($c !== '') {
            $map = [
                'ok'       => 'comments.sent',
                'held'     => 'comments.held',
                'generic'  => 'comments.error_generic',
                'author'   => 'comments.error_author',
                'body'     => 'comments.error_body',
                'too_fast' => 'comments.error_too_fast',
                'rate'     => 'comments.error_rate',
                'ancient'  => 'comments.error_ancient',
            ];
            if (isset($map[$c])) {
                $notice = [
                    'type' => in_array($c, ['ok', 'held'], true) ? 'ok' : 'error',
                    'text' => st_t($map[$c], ['max' => (int) st_config('comments')['max_len']]),
                ];
            }
        }
        $d = (string) ($_GET['d'] ?? '');
        if ($d === 'ok') {
            $notice = ['type' => 'ok', 'text' => st_t('downloads.unlocked')];
        } elseif ($d === 'wrong') {
            $notice = ['type' => 'error', 'text' => st_t('downloads.wrong_key')];
        } elseif ($d === 'rate') {
            $notice = ['type' => 'error', 'text' => st_t('downloads.rate')];
        }

        $canonical = st_abs_url('tools/' . $slug);
        View::page('tool', $common + [
            'route'      => 'tools/' . $slug,
            'title'      => $tool['name'] . ' (' . $tool['year'] . ') — software-tools',
            'description' => mb_substr((string) $tool['context'], 0, 160),
            'canonical'  => $canonical,
            'ogType'     => 'article',
            'jsonLd'     => json_encode([
                '@context'            => 'https://schema.org',
                '@type'               => 'SoftwareApplication',
                'name'                => $tool['name'],
                'description'         => $tool['context'],
                'url'                 => $canonical,
                'applicationCategory' => 'DeveloperApplication',
                'operatingSystem'     => 'Any',
                'isPartOf'            => ['@type' => 'WebSite', 'name' => 'software-tools', 'url' => st_abs_url('')],
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'tool'       => $tool,
            'related'    => Catalog::toolsBySlugs($tool['related']),
            'sameCat'    => Catalog::toolsByCategory((string) $tool['category'], (int) $tool['id'], 6),
            'neighbours' => Catalog::neighbours($slug),
            'succTool'   => $succTool,
            'bodyHtml'   => $bodyHtml,
            'notice'     => $notice,
            'dloads'     => Downloads::forTool($slug),
            'dlHasPass'   => Downloads::hasGlobalPass(),
            'dlUnlocked'  => Downloads::hasAccess($slug),
            'commentsEnabled' => (bool) st_config('comments')['enabled'],
            'commentsList'    => Comments::forTool($slug),
            'commentToken'    => Comments::timeToken(),
            'assets'          => \St\Ops::assetsFor($slug),
        ]);
        exit;
    }

    // ----- Comentarios: publicación -----------------------------------------
    if ($segments === ['comment'] && ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
        $slug = (string) ($_POST['slug'] ?? '');
        if (!preg_match('/^[a-z0-9-]+$/', $slug) || !Catalog::toolBySlug($slug)) {
            $notFound();
            exit;
        }
        $error = Comments::submit($slug, $_POST);
        $flag = $error ?? (!empty(st_config('comments')['direct']) ? 'ok' : 'held');
        st_redirect(st_url('tools/' . $slug, ['modo' => $mode, 'c' => $flag]) . '#comentarios');
    }

    // ----- Descargas: desbloqueo por clave ----------------------------------
    if ($segments[0] === 'download' && count($segments) === 2 && ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
        $slug = $segments[1];
        if (!preg_match('/^[a-z0-9-]+$/', $slug) || !Catalog::toolBySlug($slug)) {
            $notFound();
            exit;
        }
        $result = Downloads::unlockAll((string) ($_POST['dl_pass'] ?? ''));
        $flag = $result === null ? 'ok' : ($result === 'rate' ? 'rate' : 'wrong');
        st_redirect(st_url('tools/' . $slug, ['modo' => $mode, 'd' => $flag]) . '#descargas');
    }

    // ----- Descargas: streamer ----------------------------------------------
    if ($segments[0] === 'download' && count($segments) === 3) {
        $slug = $segments[1];
        $file = $segments[2];
        if (!preg_match('/^[a-z0-9-]+$/', $slug)) {
            $notFound();
            exit;
        }
        Downloads::stream($slug, $file); // termina la respuesta
    }

    // ----- Panel de administración ------------------------------------------
    if ($segments[0] === 'admin') {
        Admin::handle($segments, $common);
        exit;
    }

    // ----- Sitemap -----------------------------------------------------------
    if ($segments === ['sitemap.xml']) {
        header('Content-Type: application/xml; charset=utf-8');
        $urls = [st_abs_url(''), st_abs_url('tools'), st_abs_url('search')];
        foreach (Catalog::tools() as $t) {
            $urls[] = st_abs_url('tools/' . $t['slug']);
        }
        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        foreach ($urls as $u) {
            echo '  <url><loc>' . htmlspecialchars($u, ENT_XML1) . '</loc></url>' . "\n";
        }
        echo '</urlset>' . "\n";
        exit;
    }

    // ----- Búsqueda ---------------------------------------------------------
    if ($segments === ['search']) {
        $q = trim((string) ($_GET['q'] ?? ''));
        $result = $q !== '' ? Catalog::search($q) : ['q' => '', 'rows' => [], 'total' => 0];
        View::page('search', $common + [
            'route' => 'search',
            'title' => $q !== '' ? ('Buscar: ' . $q . ' — software-tools') : 'Buscar — software-tools',
            'canonical' => st_abs_url('search'),
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
