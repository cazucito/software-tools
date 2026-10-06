<?php
declare(strict_types=1);
namespace St;

if (!defined('ST_APP')) {
    exit;
}

/**
 * Renderizado de plantillas PHP (app/server/views). Cada plantilla recibe
 * sus variables por extract() dentro de un ámbito aislado.
 */
final class View
{
    /**
     * Renderiza una plantilla y devuelve el HTML resultante.
     *
     * @param string              $template ruta relativa sin extensión (ej. 'tool', 'modes/linea')
     * @param array<string,mixed> $vars     variables disponibles en la plantilla
     */
    public static function render(string $template, array $vars = []): string
    {
        $file = st_config('views_dir') . '/' . $template . '.php';
        if (!is_file($file)) {
            throw new \RuntimeException('Plantilla no encontrada: ' . $template);
        }
        return (static function () use ($file, $vars): string {
            extract($vars, EXTR_SKIP);
            ob_start();
            require $file;
            return (string) ob_get_clean();
        })();
    }

    /** Renderiza la plantilla dentro del layout común y la imprime. */
    public static function page(string $template, array $vars = []): void
    {
        $vars['content'] = self::render($template, $vars);
        echo self::render('layout', $vars);
    }

    /** Renderiza una página del panel admin (layout propio, sin modalidades). */
    public static function admin(string $template, array $vars = []): void
    {
        $vars['content'] = self::render('admin/' . $template, $vars);
        echo self::render('admin/layout', $vars);
    }
}
