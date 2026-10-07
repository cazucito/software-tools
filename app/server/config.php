<?php
declare(strict_types=1);

/**
 * Configuración de la aplicación software-tools v3.
 *
 * Sin secretos en este archivo: los valores sensibles (admin, FTP, claves)
 * viven en BWS y se integran en fases posteriores. Puede sobrescribirse con
 * config.local.php (no versionado) que retorne un array cuyas claves pisan
 * las de aquí — ese archivo sí puede contener valores locales.
 */

$config = [
    // Identidad
    'site_name'    => 'software-tools',
    'site_tagline' => 'Archivo personal de software · 1991 → hoy',

    // Rutas
    'data_file'    => dirname(__DIR__) . '/data/catalog.sqlite', // app/data/catalog.sqlite
    'ops_file'     => dirname(__DIR__) . '/data/ops.sqlite',     // operativo (ADR-009)
    'views_dir'    => __DIR__ . '/views',
    'locales_dir'  => is_dir(dirname(__DIR__) . '/locales')
        ? dirname(__DIR__) . '/locales'                            // hosting: locales/ hermana de server/
        : dirname(__DIR__) . '/public/locales',                    // desarrollo
    'downloads_dir' => is_dir(dirname(__DIR__) . '/downloads')
        ? dirname(__DIR__) . '/downloads'                          // hosting (raíz o /proto: downloads/ hermana de server/)
        : dirname(__DIR__) . '/public/downloads',                  // desarrollo (app/public/downloads)
    'assets_tools_dir' => is_dir(dirname(__DIR__) . '/assets/tools')
        ? dirname(__DIR__) . '/assets/tools'                       // hosting: assets/tools/ hermana de server/
        : dirname(__DIR__) . '/public/assets/tools',               // desarrollo
    'base_path'    => null, // null = autodetectar desde SCRIPT_NAME

    // Modalidades (ADR-006)
    'modes'        => ['cinta', 'linea', 'maquina'],
    'mode_labels'  => ['cinta' => 'Cinta', 'linea' => 'Línea', 'maquina' => 'Máquina'],
    'default_mode' => 'linea',

    // URLs: 'query' => index.php?p=tools/eudora | 'pretty' => /tools/eudora (requiere rewrite)
    'url_style'    => 'query',

    // Prototipo: sin indexar todavía (se retira al ser la v3 oficial)
    'noindex'      => true,

    // i18n (base; extracción completa de cadenas en Fase 6)
    'lang'         => 'es',
    'langs'        => ['es' => 'Español'],
    'timezone'     => 'America/Mexico_City',

    // Secreto de la app (HMAC de time-trap y sesiones de descarga).
    // En producción vive en config.local.php (no versionado).
    'app_secret'   => 'dev-secret-cambiar-en-produccion',

    // Panel admin (ADR-008). pass_hash = null → panel desactivado.
    'admin'        => [
        'pass_hash'    => null,
        'session_ttl'  => 7200,
        'login_max'    => 5,
        'login_window' => 900,
    ],

    // Comentarios (SPEC 6.5)
    'comments'     => [
        'enabled'     => true,
        'direct'      => true, // publicación directa + moderación retroactiva
        'min_seconds' => 3,
        'max_seconds' => 7200,
        'max_len'     => 2000,
        'rate_max'    => 3,
        'rate_window' => 600,
    ],

    // Descargas (ADR-007)
    'downloads'    => [
        'key_session_ttl' => 7200,    // duración del desbloqueo por herramienta
        'key_rate_max'    => 5,
        'key_rate_window' => 600,
        'max_upload'      => 1500000, // subida directa (el hosting corta en ~2 MB)
    ],

    // Debug: muestra errores en pantalla (solo desarrollo local)
    'debug'        => false,
];

if (is_file(__DIR__ . '/config.local.php')) {
    $local = require __DIR__ . '/config.local.php';
    if (is_array($local)) {
        $config = array_merge($config, $local);
    }
}

return $config;
