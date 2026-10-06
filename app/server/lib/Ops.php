<?php
declare(strict_types=1);
namespace St;

if (!defined('ST_APP')) {
    exit;
}

/**
 * Acceso a los datos operativos (ops.sqlite — ADR-009): comentarios,
 * descargas, claves, assets, rate-limit y marcas de export pendiente.
 * Las referencias a herramientas se hacen por slug (estable).
 */
final class Ops
{
    // ------------------------------------------------------------------
    // Comentarios
    // ------------------------------------------------------------------

    /** Inserta un comentario y devuelve su id. */
    public static function commentInsert(string $slug, string $lang, string $author, string $body, ?string $ipHash, string $status): int
    {
        $stmt = Db::ops()->prepare(
            'INSERT INTO comments(tool_slug, lang, author, body, created_at, status, ip_hash)
             VALUES (?,?,?,?,?,?,?)'
        );
        $stmt->execute([$slug, $lang, $author, $body, gmdate('c'), $status, $ipHash]);
        return (int) Db::ops()->lastInsertId();
    }

    /** Comentarios de una herramienta (por estado), los más recientes primero. */
    public static function commentsFor(string $slug, string $status = 'approved', int $limit = 100): array
    {
        $stmt = Db::ops()->prepare(
            'SELECT id, author, body, created_at, status FROM comments
             WHERE tool_slug = ? AND status = ? ORDER BY id DESC LIMIT ?'
        );
        $stmt->bindValue(1, $slug);
        $stmt->bindValue(2, $status);
        $stmt->bindValue(3, $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /** Todos los comentarios (moderación), filtrados por estado si se indica. */
    public static function commentsAll(?string $status = null, int $limit = 500): array
    {
        if ($status !== null) {
            $stmt = Db::ops()->prepare(
                'SELECT * FROM comments WHERE status = ? ORDER BY id DESC LIMIT ?'
            );
            $stmt->bindValue(1, $status);
            $stmt->bindValue(2, $limit, \PDO::PARAM_INT);
        } else {
            $stmt = Db::ops()->prepare('SELECT * FROM comments ORDER BY id DESC LIMIT ?');
            $stmt->bindValue(1, $limit, \PDO::PARAM_INT);
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /** Cambia el estado de un comentario (approved | pending | spam). */
    public static function commentSetStatus(int $id, string $status): void
    {
        $stmt = Db::ops()->prepare('UPDATE comments SET status = ? WHERE id = ?');
        $stmt->execute([$status, $id]);
    }

    /** Borra un comentario definitivamente. */
    public static function commentDelete(int $id): void
    {
        Db::ops()->prepare('DELETE FROM comments WHERE id = ?')->execute([$id]);
    }

    /** Conteo por estado: ['approved' => n, 'pending' => n, 'spam' => n]. */
    public static function commentCounts(): array
    {
        $out = ['approved' => 0, 'pending' => 0, 'spam' => 0];
        foreach (Db::ops()->query('SELECT status, COUNT(*) c FROM comments GROUP BY status') as $row) {
            $out[(string) $row['status']] = (int) $row['c'];
        }
        return $out;
    }

    // ------------------------------------------------------------------
    // Rate limit (genérico)
    // ------------------------------------------------------------------

    /**
     * ¿Se excedió el límite? Registra el intento si aún hay margen.
     * Purga los eventos viejos de esa cubeta al pasar.
     */
    public static function tooMany(string $bucket, string $key, int $max, int $windowSeconds): bool
    {
        $pdo = Db::ops();
        $pdo->prepare('DELETE FROM rate_events WHERE bucket = ? AND created_at < ?')
            ->execute([$bucket, time() - $windowSeconds * 2]);
        $stmt = $pdo->prepare(
            'SELECT COUNT(*) FROM rate_events WHERE bucket = ? AND key = ? AND created_at >= ?'
        );
        $stmt->execute([$bucket, $key, time() - $windowSeconds]);
        if ((int) $stmt->fetchColumn() >= $max) {
            return true;
        }
        $pdo->prepare('INSERT INTO rate_events(bucket, key, created_at) VALUES (?,?,?)')
            ->execute([$bucket, $key, time()]);
        return false;
    }

    // ------------------------------------------------------------------
    // Descargas (archivos)
    // ------------------------------------------------------------------

    /** Archivos de descarga de una herramienta. */
    public static function downloadsFor(string $slug): array
    {
        $stmt = Db::ops()->prepare(
            'SELECT * FROM downloads WHERE tool_slug = ? ORDER BY filename'
        );
        $stmt->execute([$slug]);
        return $stmt->fetchAll();
    }

    /** Todos los archivos (vista admin), con conteo por herramienta. */
    public static function downloadsAll(): array
    {
        return Db::ops()->query(
            'SELECT tool_slug, COUNT(*) files, SUM(bytes) total_bytes, MAX(created_at) last_at
             FROM downloads GROUP BY tool_slug ORDER BY tool_slug'
        )->fetchAll();
    }

    /** Un archivo concreto (para el streamer). */
    public static function downloadBy(string $slug, string $filename): ?array
    {
        $stmt = Db::ops()->prepare('SELECT * FROM downloads WHERE tool_slug = ? AND filename = ?');
        $stmt->execute([$slug, $filename]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /** Registra un archivo de descarga. */
    public static function downloadInsert(
        string $slug,
        string $filename,
        string $relpath,
        int $bytes,
        string $sha256,
        string $visibility,
        ?string $license,
        ?string $sourceUrl
    ): int {
        $stmt = Db::ops()->prepare(
            'INSERT INTO downloads(tool_slug, filename, relpath, bytes, sha256, visibility, license_note, source_url, created_at)
             VALUES (?,?,?,?,?,?,?,?,?)
             ON CONFLICT(tool_slug, filename) DO UPDATE SET
               relpath=excluded.relpath, bytes=excluded.bytes, sha256=excluded.sha256,
               visibility=excluded.visibility, license_note=excluded.license_note, source_url=excluded.source_url'
        );
        $stmt->execute([$slug, $filename, $relpath, $bytes, $sha256, $visibility, $license, $sourceUrl, gmdate('c')]);
        return (int) Db::ops()->lastInsertId();
    }

    /** Borra el registro de un archivo; devuelve la fila previa. */
    public static function downloadDelete(int $id): ?array
    {
        $stmt = Db::ops()->prepare('SELECT * FROM downloads WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if ($row) {
            Db::ops()->prepare('DELETE FROM downloads WHERE id = ?')->execute([$id]);
        }
        return $row ?: null;
    }

    /** Actualiza visibilidad / licencia / enlace de un archivo. */
    public static function downloadUpdate(int $id, string $visibility, ?string $license, ?string $sourceUrl): void
    {
        $stmt = Db::ops()->prepare(
            'UPDATE downloads SET visibility = ?, license_note = ?, source_url = ? WHERE id = ?'
        );
        $stmt->execute([$visibility, $license, $sourceUrl, $id]);
    }

    // ------------------------------------------------------------------
    // Claves por software (ADR-007)
    // ------------------------------------------------------------------

    public static function keyHash(string $slug): ?string
    {
        $stmt = Db::ops()->prepare('SELECT pass_hash FROM download_keys WHERE tool_slug = ?');
        $stmt->execute([$slug]);
        $hash = $stmt->fetchColumn();
        return $hash === false ? null : (string) $hash;
    }

    public static function keySet(string $slug, string $hash): void
    {
        Db::ops()->prepare(
            'INSERT INTO download_keys(tool_slug, pass_hash, updated_at) VALUES (?,?,?)
             ON CONFLICT(tool_slug) DO UPDATE SET pass_hash=excluded.pass_hash, updated_at=excluded.updated_at'
        )->execute([$slug, $hash, gmdate('c')]);
    }

    public static function keyDelete(string $slug): void
    {
        Db::ops()->prepare('DELETE FROM download_keys WHERE tool_slug = ?')->execute([$slug]);
    }

    // ------------------------------------------------------------------
    // Assets por herramienta
    // ------------------------------------------------------------------

    /** Mapa slug → archivo de ícono (kind='icon') para toda la página — ADR-010. @return array<string,string> */
    public static function iconMap(): array
    {
        try {
            $rows = Db::ops()->query("SELECT tool_slug, file FROM tool_assets WHERE kind = 'icon'")->fetchAll();
        } catch (\Throwable) {
            return [];
        }
        $map = [];
        foreach ($rows as $row) {
            $map[(string) $row['tool_slug']] = (string) $row['file'];
        }
        return $map;
    }

    /** Assets de una herramienta: kind => fila. */
    public static function assetsFor(string $slug): array
    {
        $stmt = Db::ops()->prepare('SELECT * FROM tool_assets WHERE tool_slug = ?');
        $stmt->execute([$slug]);
        $out = [];
        foreach ($stmt->fetchAll() as $row) {
            $out[(string) $row['kind']] = $row;
        }
        return $out;
    }

    public static function assetSet(string $slug, string $kind, string $file, ?string $source, ?string $license): void
    {
        Db::ops()->prepare(
            'INSERT INTO tool_assets(tool_slug, kind, file, source, license_note, created_at) VALUES (?,?,?,?,?,?)
             ON CONFLICT(tool_slug, kind) DO UPDATE SET
               file=excluded.file, source=excluded.source, license_note=excluded.license_note'
        )->execute([$slug, $kind, $file, $source, $license, gmdate('c')]);
    }

    // ------------------------------------------------------------------
    // Export pendiente / metadatos
    // ------------------------------------------------------------------

    public static function markDirty(string $slug): void
    {
        Db::ops()->prepare(
            'INSERT INTO catalog_dirty(tool_slug, changed_at) VALUES (?,?)
             ON CONFLICT(tool_slug) DO UPDATE SET changed_at=excluded.changed_at'
        )->execute([$slug, gmdate('c')]);
    }

    /** @return list<array{tool_slug:string,changed_at:string}> */
    public static function dirtyList(): array
    {
        return Db::ops()->query('SELECT tool_slug, changed_at FROM catalog_dirty ORDER BY changed_at')->fetchAll();
    }

    /** @param list<string> $slugs */
    public static function dirtyClear(array $slugs): void
    {
        if (!$slugs) {
            return;
        }
        $in = implode(',', array_fill(0, count($slugs), '?'));
        Db::ops()->prepare("DELETE FROM catalog_dirty WHERE tool_slug IN ($in)")->execute(array_values($slugs));
    }

    public static function metaSet(string $key, string $value): void
    {
        Db::ops()->prepare(
            'INSERT INTO meta(key, value) VALUES (?,?)
             ON CONFLICT(key) DO UPDATE SET value=excluded.value'
        )->execute([$key, $value]);
    }

    public static function metaGet(string $key): ?string
    {
        $stmt = Db::ops()->prepare('SELECT value FROM meta WHERE key = ?');
        $stmt->execute([$key]);
        $value = $stmt->fetchColumn();
        return $value === false ? null : (string) $value;
    }
}
