<?php
declare(strict_types=1);
namespace St;

if (!defined('ST_APP')) {
    exit;
}

/**
 * Publicación de comentarios (SPEC 6.5): honeypot invisible + time-trap
 * firmado (HMAC) + rate-limit por IP-hash. Publicación directa con
 * moderación retroactiva (configurable a cola previa en config.php).
 */
final class Comments
{
    /** Token de tiempo firmado que viaja en el formulario (time-trap). */
    public static function timeToken(): array
    {
        $t = time();
        $sig = hash_hmac('sha256', (string) $t, (string) st_config('app_secret'));
        return ['t' => $t, 'sig' => $sig];
    }

    /**
     * Procesa un envío. Devuelve null si quedó publicado/encolado, o una
     * clave de error: generic | author | body | too_fast | rate | ancient.
     */
    public static function submit(string $slug, array $in): ?string
    {
        $cfg = st_config('comments');
        if (empty($cfg['enabled'])) {
            return 'generic';
        }

        // Honeypot: el campo «website» debe venir vacío (los bots lo rellenan).
        if (trim((string) ($in['website'] ?? '')) !== '') {
            return 'generic';
        }

        // Time-trap: firma del instante de render + ventana razonable.
        $t = (int) ($in['t'] ?? 0);
        $sig = (string) ($in['tsig'] ?? '');
        $expect = hash_hmac('sha256', (string) $t, (string) st_config('app_secret'));
        if (!hash_equals($expect, $sig)) {
            return 'generic';
        }
        $age = time() - $t;
        if ($age < (int) $cfg['min_seconds']) {
            return 'too_fast';
        }
        if ($age > (int) $cfg['max_seconds']) {
            return 'ancient';
        }

        $author = trim((string) preg_replace('/\s+/u', ' ', (string) ($in['author'] ?? '')));
        $body = (string) preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/u', '', (string) ($in['body'] ?? ''));
        $body = trim($body);
        if ($author === '' || mb_strlen($author) > 60) {
            return 'author';
        }
        $max = (int) $cfg['max_len'];
        $bodyLen = mb_strlen($body);
        if ($bodyLen < 2 || $bodyLen > $max) {
            return 'body';
        }

        $ipHash = Auth::ipHash();
        if (Ops::tooMany('comment', $ipHash, (int) $cfg['rate_max'], (int) $cfg['rate_window'])) {
            return 'rate';
        }

        $status = !empty($cfg['direct']) ? 'approved' : 'pending';
        Ops::commentInsert($slug, I18n::lang(), $author, $body, $ipHash, $status);
        return null;
    }

    /** Comentarios aprobados de una herramienta, con fecha legible. */
    public static function forTool(string $slug): array
    {
        $comments = Ops::commentsFor($slug, 'approved');
        foreach ($comments as &$comment) {
            $comment['fecha'] = self::humanDate((string) $comment['created_at']);
        }
        return $comments;
    }

    /** Fecha legible en la zona del sitio: «5 oct 2026, 19:04». */
    public static function humanDate(string $iso): string
    {
        $ts = strtotime($iso);
        if ($ts === false) {
            return '';
        }
        $months = ['ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sep', 'oct', 'nov', 'dic'];
        return date('j', $ts) . ' ' . $months[(int) date('n', $ts) - 1] . ' ' . date('Y', $ts)
            . ', ' . date('H:i', $ts);
    }
}
