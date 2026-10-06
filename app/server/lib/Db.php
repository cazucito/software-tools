<?php
declare(strict_types=1);
namespace St;

if (!defined('ST_APP')) {
    exit;
}

/**
 * Conexión única a la base de datos (app/data/catalog.sqlite), en solo lectura.
 * La base es un artefacto generado por ops/tools/import_catalog.py: la
 * aplicación nunca la modifica (los datos operativos llegan en fases futuras).
 */
final class Db
{
    private static ?\PDO $pdo = null;

    /** Devuelve el PDO compartido, abriéndolo la primera vez. */
    public static function pdo(): \PDO
    {
        if (self::$pdo === null) {
            $file = st_config('data_file');
            if (!is_string($file) || !is_file($file)) {
                throw new \RuntimeException(
                    'No existe la base de datos (' . $file . '). '
                    . 'Genera app/data/catalog.sqlite con: python3 ops/tools/import_catalog.py'
                );
            }
            self::$pdo = new \PDO('sqlite:' . $file, null, null, [
                \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            ]);
            self::$pdo->exec('PRAGMA foreign_keys = ON');
            self::$pdo->exec('PRAGMA query_only = ON');
        }
        return self::$pdo;
    }
}
