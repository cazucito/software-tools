<?php
declare(strict_types=1);
namespace St;

if (!defined('ST_APP')) {
    exit;
}

/**
 * Conexiones SQLite de la aplicación (ADR-005 + ADR-009):
 *
 *   - pdo()      → catalog.sqlite en SOLO LECTURA (artefacto generado).
 *   - catalogW() → catalog.sqlite en escritura (SOLO panel admin; ediciones
 *                  de catálogo, que quedan marcadas en catalog_dirty).
 *   - ops()      → ops.sqlite lectura/escritura (datos operativos; se
 *                  auto-inicializa en la primera escritura).
 */
final class Db
{
    private static ?\PDO $pdo = null;
    private static ?\PDO $catalogW = null;
    private static ?\PDO $ops = null;

    /** Esquema de ops.sqlite (auto-inicialización). */
    private const OPS_SCHEMA = [
        'CREATE TABLE IF NOT EXISTS comments(
            id INTEGER PRIMARY KEY, tool_slug TEXT NOT NULL, lang TEXT NOT NULL DEFAULT "es",
            author TEXT NOT NULL, body TEXT NOT NULL, created_at TEXT NOT NULL,
            status TEXT NOT NULL DEFAULT "approved" CHECK(status IN ("pending","approved","spam")),
            ip_hash TEXT)',
        'CREATE INDEX IF NOT EXISTS idx_comments_tool ON comments(tool_slug, status)',
        'CREATE TABLE IF NOT EXISTS downloads(
            id INTEGER PRIMARY KEY, tool_slug TEXT NOT NULL, filename TEXT NOT NULL, relpath TEXT NOT NULL,
            bytes INTEGER, sha256 TEXT,
            visibility TEXT NOT NULL DEFAULT "clave" CHECK(visibility IN ("publico","clave","enlace")),
            license_note TEXT, source_url TEXT, created_at TEXT, UNIQUE(tool_slug, filename))',
        'CREATE TABLE IF NOT EXISTS download_keys(
            tool_slug TEXT PRIMARY KEY, pass_hash TEXT NOT NULL, updated_at TEXT)',
        'CREATE TABLE IF NOT EXISTS tool_assets(
            id INTEGER PRIMARY KEY, tool_slug TEXT NOT NULL,
            kind TEXT NOT NULL CHECK(kind IN ("icon","logo","cover")), file TEXT NOT NULL,
            source TEXT, license_note TEXT, created_at TEXT, UNIQUE(tool_slug, kind))',
        'CREATE TABLE IF NOT EXISTS rate_events(
            id INTEGER PRIMARY KEY, bucket TEXT NOT NULL, key TEXT NOT NULL, created_at INTEGER NOT NULL)',
        'CREATE INDEX IF NOT EXISTS idx_rate ON rate_events(bucket, key, created_at)',
        'CREATE TABLE IF NOT EXISTS catalog_dirty(
            tool_slug TEXT PRIMARY KEY, changed_at TEXT NOT NULL)',
        'CREATE TABLE IF NOT EXISTS meta(key TEXT PRIMARY KEY, value TEXT)',
    ];

    /** PDO de solo lectura sobre el catálogo generado. */
    public static function pdo(): \PDO
    {
        if (self::$pdo === null) {
            self::$pdo = self::open((string) st_config('data_file'), true);
        }
        return self::$pdo;
    }

    /** PDO de escritura sobre el catálogo (solo panel admin). */
    public static function catalogW(): \PDO
    {
        if (self::$catalogW === null) {
            self::$catalogW = self::open((string) st_config('data_file'), false);
        }
        return self::$catalogW;
    }

    /** PDO operativo (lectura/escritura); inicializa el esquema si hace falta. */
    public static function ops(): \PDO
    {
        if (self::$ops === null) {
            $file = (string) st_config('ops_file');
            try {
                $pdo = self::open($file, false, false); // se crea si no existe
                $pdo->exec('PRAGMA busy_timeout = 5000');
                $pdo->exec('PRAGMA journal_mode = TRUNCATE');
                foreach (self::OPS_SCHEMA as $ddl) {
                    $pdo->exec($ddl);
                }
            } catch (\PDOException $e) {
                throw new \RuntimeException(
                    'No se pudo abrir/inicializar ops.sqlite (' . $file . '): ' . $e->getMessage()
                );
            }
            self::$ops = $pdo;
        }
        return self::$ops;
    }

    /**
     * Abre un archivo SQLite con opciones comunes.
     * ops.sqlite se crea al vuelo (mustExist=false); catalog.sqlite siempre existe.
     */
    private static function open(string $file, bool $readOnly, bool $mustExist = true): \PDO
    {
        if ($mustExist && !is_file($file)) {
            throw new \RuntimeException(
                'No existe la base de datos (' . $file . '). '
                . 'Genera app/data/catalog.sqlite con: python3 ops/tools/import_catalog.py'
            );
        }
        $pdo = new \PDO('sqlite:' . $file, null, null, [
            \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
        ]);
        $pdo->exec('PRAGMA foreign_keys = ON');
        if ($readOnly) {
            $pdo->exec('PRAGMA query_only = ON');
        } else {
            $pdo->exec('PRAGMA busy_timeout = 5000');
        }
        return $pdo;
    }
}
