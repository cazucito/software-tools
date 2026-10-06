<?php
declare(strict_types=1);

/**
 * Arranque de la aplicación: carga la configuración, registra el autoload
 * de las clases propias (namespace St\) y expone helpers globales.
 * Debe incluirse una sola vez desde el front controller (app/public/index.php).
 */

define('ST_APP', true);
define('ST_ROOT', dirname(__DIR__)); // app/

$GLOBALS['st_config'] = require __DIR__ . '/config.php';

// ---------------------------------------------------------------------------
// Autoload de clases propias: St\Catalog -> app/server/lib/Catalog.php
// ---------------------------------------------------------------------------
spl_autoload_register(static function (string $class): void {
    if (strpos($class, 'St\\') !== 0) {
        return;
    }
    $relative = substr($class, 3);
    $file = __DIR__ . '/lib/' . str_replace('\\', '/', $relative) . '.php';
    if (is_file($file)) {
        require $file;
    }
});

// Markdown (Parsedown vendored — solo lectura de nuestros propios .md)
require_once __DIR__ . '/vendor/parsedown/Parsedown.php';

// ---------------------------------------------------------------------------
// Ruta base ('' en la raíz del dominio, '/proto' bajo subdirectorio, etc.)
// ---------------------------------------------------------------------------
$autoBase = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/index.php')), '/');
$base = st_config('base_path') ?? $autoBase;
define('ST_BASE', ($base === '.' || $base === '/') ? '' : $base);

// ---------------------------------------------------------------------------
// Errores: en producción solo log; en debug, a pantalla
// ---------------------------------------------------------------------------
if (st_config('debug')) {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    error_reporting(E_ALL);
}

// ---------------------------------------------------------------------------
// Helpers globales
// ---------------------------------------------------------------------------

/** Lee la configuración (entera o una clave). */
function st_config(?string $key = null)
{
    $config = $GLOBALS['st_config'];
    return $key === null ? $config : ($config[$key] ?? null);
}

/** Escapa texto para HTML. */
function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

/**
 * Construye una URL interna.
 * Estilo 'query':  /index.php?p=tools/eudora&modo=cinta
 * Estilo 'pretty': /tools/eudora?modo=cinta  (requiere rewrite en el server)
 */
function st_url(string $route = '', array $params = []): string
{
    if (st_config('url_style') === 'pretty') {
        $url = $route === '' ? ST_BASE . '/' : ST_BASE . '/' . ltrim($route, '/');
        if ($params) {
            $url .= '?' . http_build_query($params);
        }
        return $url;
    }

    $url = ST_BASE . '/index.php';
    $query = [];
    if ($route !== '') {
        $query[] = 'p=' . $route; // charset restringido: [a-z0-9/-]
    }
    foreach ($params as $key => $value) {
        $query[] = rawurlencode((string) $key) . '=' . rawurlencode((string) $value);
    }
    return $query ? $url . '?' . implode('&', $query) : $url;
}

/** URL de un asset estático (css/js/libs). */
function st_asset(string $path): string
{
    return ST_BASE . '/assets/' . ltrim($path, '/');
}
