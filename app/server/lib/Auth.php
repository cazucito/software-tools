<?php
declare(strict_types=1);
namespace St;

if (!defined('ST_APP')) {
    exit;
}

/**
 * Sesión y autenticación del panel admin (una sola cuenta — ADR-008).
 * Sesión nativa de PHP con cookie endurecida; CSRF con token en sesión;
 * rate-limit de intentos de login respaldado por ops.sqlite.
 */
final class Auth
{
    private static bool $started = false;

    /** Inicia la sesión una sola vez, con cookie endurecida. */
    public static function start(): void
    {
        if (self::$started || session_status() === PHP_SESSION_ACTIVE) {
            self::$started = true;
            return;
        }
        $secure = (($_SERVER['HTTPS'] ?? '') === 'on') || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
        session_name('st_admin');
        session_set_cookie_params([
            'lifetime' => 0,
            'path'     => ST_BASE . '/',
            'httponly' => true,
            'samesite' => 'Lax',
            'secure'   => $secure,
        ]);
        session_start();
        self::$started = true;
    }

    /** ¿El panel está configurado y hay sesión válida? */
    public static function isLoggedIn(): bool
    {
        self::start();
        if (empty($_SESSION['st_admin'])) {
            return false;
        }
        $ttl = (int) (st_config('admin')['session_ttl'] ?? 7200);
        if ((int) ($_SESSION['st_admin_exp'] ?? 0) < time()) {
            self::logout();
            return false;
        }
        return true;
    }

    /** ¿Está configurada la contraseña del admin? */
    public static function isEnabled(): bool
    {
        $admin = st_config('admin');
        return !empty($admin['pass_hash']);
    }

    /**
     * Intenta iniciar sesión. Devuelve null si OK o un mensaje de error.
     */
    public static function login(string $pass): ?string
    {
        $admin = st_config('admin');
        $ipHash = self::ipHash();
        $max = (int) ($admin['login_max'] ?? 5);
        $window = (int) ($admin['login_window'] ?? 900);
        if (Ops::tooMany('login', $ipHash, $max, $window)) {
            return 'Demasiados intentos. Espera unos minutos.';
        }
        if (!self::isEnabled() || !password_verify($pass, (string) $admin['pass_hash'])) {
            return 'Contraseña incorrecta.';
        }
        self::start();
        session_regenerate_id(true);
        $_SESSION['st_admin'] = true;
        $_SESSION['st_admin_exp'] = time() + (int) ($admin['session_ttl'] ?? 7200);
        $_SESSION['st_csrf'] = bin2hex(random_bytes(16));
        return null;
    }

    public static function logout(): void
    {
        self::start();
        $_SESSION = [];
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
        self::$started = false;
    }

    /** Token CSRF de la sesión (lo crea si falta). */
    public static function csrf(): string
    {
        self::start();
        if (empty($_SESSION['st_csrf'])) {
            $_SESSION['st_csrf'] = bin2hex(random_bytes(16));
        }
        return (string) $_SESSION['st_csrf'];
    }

    /** Verifica un token CSRF recibido. */
    public static function checkCsrf(?string $token): bool
    {
        self::start();
        return is_string($token) && $token !== '' && hash_equals((string) ($_SESSION['st_csrf'] ?? ''), $token);
    }

    /** Hash de IP con el secreto de la app (nunca se guarda la IP en claro). */
    public static function ipHash(): string
    {
        $ip = (string) ($_SERVER['REMOTE_ADDR'] ?? '');
        return substr(hash_hmac('sha256', $ip, (string) st_config('app_secret')), 0, 32);
    }

    // ------------------------------------------------------------------
    // Mensajes flash (entre redirects del admin)
    // ------------------------------------------------------------------

    /** @param 'ok'|'error' $type */
    public static function flash(string $type, string $message): void
    {
        self::start();
        $_SESSION['st_flash'] = ['type' => $type, 'message' => $message];
    }

    /** @return array{type:string,message:string}|null */
    public static function takeFlash(): ?array
    {
        self::start();
        $flash = $_SESSION['st_flash'] ?? null;
        unset($_SESSION['st_flash']);
        return is_array($flash) ? $flash : null;
    }
}
