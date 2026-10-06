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
    'views_dir'    => __DIR__ . '/views',
    'base_path'    => null, // null = autodetectar desde SCRIPT_NAME

    // Modalidades (ADR-006)
    'modes'        => ['cinta', 'linea', 'maquina'],
    'mode_labels'  => ['cinta' => 'Cinta', 'linea' => 'Línea', 'maquina' => 'Máquina'],
    'default_mode' => 'linea',

    // URLs: 'query' => index.php?p=tools/eudora | 'pretty' => /tools/eudora (requiere rewrite)
    'url_style'    => 'query',

    // Prototipo: sin indexar todavía (se retira al ser la v3 oficial)
    'noindex'      => true,

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
