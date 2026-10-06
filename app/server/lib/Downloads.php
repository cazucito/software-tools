<?php
declare(strict_types=1);
namespace St;

if (!defined('ST_APP')) {
    exit;
}

/**
 * Descargas (ADR-007): archivos servidos SOLO por PHP (nunca URL directa);
 * desbloqueo por clave individual de software con cookie firmada por
 * herramienta; registro de archivos subidos por FTP o subida directa ≤1.5 MB.
 */
final class Downloads
{
    /** Extensiones aceptadas para archivos de descarga (whitelist). */
    public const ALLOWED_EXT = [
        'zip', 'rar', '7z', 'tar', 'gz', 'tgz', 'xz', 'bz2', 'jar', 'whl',
        'exe', 'msi', 'dmg', 'iso', 'bin', 'img', 'dsk', 'cab', 'sit', 'hqx',
        'pdf', 'txt', 'md', 'nfo', 'jpg', 'jpeg', 'png', 'webp', 'mp4',
    ];

    // ------------------------------------------------------------------
    // Lado público (ficha)
    // ------------------------------------------------------------------

    /** Archivos de una herramienta con tamaño legible y estado de acceso. */
    public static function forTool(string $slug): array
    {
        $rows = Ops::downloadsFor($slug);
        $open = self::hasAccess($slug);
        foreach ($rows as &$row) {
            $row['open'] = ($row['visibility'] === 'publico') || $open;
            $row['human_size'] = self::humanSize((int) $row['bytes']);
            $row['sha_short'] = substr((string) $row['sha256'], 0, 12);
        }
        return $rows;
    }

    /** ¿Hay clave configurada para esta herramienta? */
    public static function hasKey(string $slug): bool
    {
        return Ops::keyHash($slug) !== null;
    }

    public static function humanSize(int $bytes): string
    {
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 1, '.', '') . ' MB';
        }
        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 0, '.', '') . ' KB';
        }
        return $bytes . ' B';
    }

    // ------------------------------------------------------------------
    // Sesión de descarga por herramienta (cookie firmada)
    // ------------------------------------------------------------------

    public static function cookieName(string $slug): string
    {
        return 'st_dl_' . substr(hash('sha256', $slug), 0, 10);
    }

    /** Abre la sesión de descarga para una herramienta. */
    public static function grant(string $slug): void
    {
        $ttl = (int) st_config('downloads')['key_session_ttl'];
        $exp = time() + $ttl;
        $sig = hash_hmac('sha256', $slug . '|' . $exp, (string) st_config('app_secret'));
        setcookie(self::cookieName($slug), $exp . '.' . $sig, [
            'expires'  => $exp,
            'path'     => ST_BASE . '/',
            'httponly' => true,
            'samesite' => 'Lax',
            'secure'   => (($_SERVER['HTTPS'] ?? '') === 'on'),
        ]);
    }

    /** ¿Está abierta la sesión de descarga de esta herramienta? */
    public static function hasAccess(string $slug): bool
    {
        $raw = (string) ($_COOKIE[self::cookieName($slug)] ?? '');
        if ($raw === '' || strpos($raw, '.') === false) {
            return false;
        }
        [$exp, $sig] = explode('.', $raw, 2);
        $exp = (int) $exp;
        if ($exp < time()) {
            return false;
        }
        $expect = hash_hmac('sha256', $slug . '|' . $exp, (string) st_config('app_secret'));
        return hash_equals($expect, $sig);
    }

    /**
     * Intenta desbloquear con la clave del software.
     * Devuelve null si OK, o 'wrong' | 'rate' | 'no_key'.
     */
    public static function unlock(string $slug, string $pass): ?string
    {
        $hash = Ops::keyHash($slug);
        if ($hash === null) {
            return 'no_key';
        }
        $cfg = st_config('downloads');
        $key = Auth::ipHash() . '|' . $slug;
        if (Ops::tooMany('dlkey', $key, (int) $cfg['key_rate_max'], (int) $cfg['key_rate_window'])) {
            return 'rate';
        }
        if (!password_verify($pass, $hash)) {
            return 'wrong';
        }
        self::grant($slug);
        return null;
    }

    // ------------------------------------------------------------------
    // Lado admin (registro de archivos)
    // ------------------------------------------------------------------

    /** Nombre de archivo seguro (kebab libre pero sin rutas ni extensiones raras). */
    public static function safeName(string $name): ?string
    {
        $name = trim($name);
        if (!preg_match('/^[A-Za-z0-9][A-Za-z0-9._ -]{0,120}$/', $name)) {
            return null;
        }
        $ext = strtolower((string) pathinfo($name, PATHINFO_EXTENSION));
        if ($ext === '' || !in_array($ext, self::ALLOWED_EXT, true)) {
            return null;
        }
        return $name;
    }

    /** Ruta absoluta válida dentro de downloads_dir (o null). */
    public static function pathFor(string $slug, string $filename): ?string
    {
        if (!preg_match('/^[a-z0-9-]+$/', $slug) || self::safeName($filename) === null) {
            return null;
        }
        $base = realpath((string) st_config('downloads_dir'));
        if ($base === false) {
            return null;
        }
        $path = $base . '/' . $slug . '/' . $filename;
        $real = realpath($path);
        if ($real === false || strpos($real, $base . '/') !== 0) {
            return null;
        }
        return $real;
    }

    /**
     * Registra un archivo que ya está en disco (subido por FTP) o acaba de
     * subirse. Devuelve la fila o un mensaje de error.
     */
    public static function register(string $slug, string $filename, string $visibility, ?string $license, ?string $sourceUrl): array
    {
        $filename = self::safeName($filename) ?? '';
        if ($filename === '') {
            return ['error' => 'Nombre de archivo no permitido.'];
        }
        $path = self::pathFor($slug, $filename);
        if ($path === null || !is_file($path)) {
            return ['error' => 'El archivo no existe en downloads/' . $slug . '/.'];
        }
        if (!in_array($visibility, ['publico', 'clave', 'enlace'], true)) {
            $visibility = 'clave';
        }
        $bytes = (int) filesize($path);
        $sha = hash_file('sha256', $path) ?: '';
        Ops::downloadInsert($slug, $filename, $slug . '/' . $filename, $bytes, $sha, $visibility, $license, $sourceUrl);
        return ['ok' => true, 'filename' => $filename, 'bytes' => $bytes, 'sha256' => $sha];
    }

    /** Archivos presentes en downloads/<slug>/ que aún no están registrados. */
    public static function scan(string $slug): array
    {
        if (!preg_match('/^[a-z0-9-]+$/', $slug)) {
            return [];
        }
        $dir = rtrim((string) st_config('downloads_dir'), '/') . '/' . $slug;
        if (!is_dir($dir)) {
            return [];
        }
        $known = [];
        foreach (Ops::downloadsFor($slug) as $row) {
            $known[(string) $row['filename']] = true;
        }
        $out = [];
        foreach (scandir($dir) ?: [] as $entry) {
            if ($entry === '.' || $entry === '..' || isset($known[$entry])) {
                continue;
            }
            $path = $dir . '/' . $entry;
            if (!is_file($path) || self::safeName($entry) === null) {
                continue;
            }
            $out[] = ['filename' => $entry, 'bytes' => (int) filesize($path), 'human_size' => self::humanSize((int) filesize($path))];
        }
        usort($out, static fn (array $a, array $b): int => strcmp($a['filename'], $b['filename']));
        return $out;
    }

    /** Elimina un registro; opcionalmente el archivo de disco. */
    public static function remove(int $id, bool $withFile): bool
    {
        $row = Ops::downloadDelete($id);
        if ($row === null) {
            return false;
        }
        if ($withFile) {
            $path = self::pathFor((string) $row['tool_slug'], (string) $row['filename']);
            if ($path !== null && is_file($path)) {
                unlink($path);
            }
        }
        return true;
    }

    // ------------------------------------------------------------------
    // Streamer
    // ------------------------------------------------------------------

    /** Sirve el archivo si el acceso lo permite. Termina la respuesta. */
    public static function stream(string $slug, string $filename): void
    {
        $row = Ops::downloadBy($slug, $filename);
        if ($row === null || $row['visibility'] === 'enlace') {
            http_response_code(404);
            exit('404');
        }
        if ($row['visibility'] === 'clave' && !self::hasAccess($slug)) {
            http_response_code(403);
            exit('403');
        }
        $path = self::pathFor($slug, $filename);
        if ($path === null || !is_file($path)) {
            http_response_code(404);
            exit('404');
        }
        header('Content-Type: application/octet-stream');
        header('X-Content-Type-Options: nosniff');
        header('Content-Disposition: attachment; filename="' . str_replace('"', '', $filename) . '"');
        header('Content-Length: ' . (string) filesize($path));
        header('Cache-Control: private, max-age=0, must-revalidate');
        readfile($path);
        exit;
    }
}
