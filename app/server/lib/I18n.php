<?php
declare(strict_types=1);
namespace St;

if (!defined('ST_APP')) {
    exit;
}

/**
 * i18n base: cadenas de UI desde app/public/locales/{lang}.json.
 * El idioma activo se decide en el controlador (ES por defecto; EN en Fase 6).
 * Uso en plantillas: <?= e(st_t('comments.submit')) ?>
 */
final class I18n
{
    private static string $lang = 'es';

    /** @var array<string,string>|null */
    private static ?array $strings = null;

    /** Fija el idioma activo y carga sus cadenas. */
    public static function setLang(string $lang): void
    {
        $langs = st_config('langs') ?? [];
        if (!isset($langs[$lang])) {
            $lang = 'es';
        }
        self::$lang = $lang;
        self::$strings = null;
    }

    public static function lang(): string
    {
        return self::$lang;
    }

    /**
     * Traduce una clave con parámetros «:nombre».
     * Si falta la clave, devuelve la propia clave (visible en QA).
     *
     * @param array<string,string|int> $params
     */
    public static function t(string $key, array $params = []): string
    {
        if (self::$strings === null) {
            $file = rtrim((string) st_config('locales_dir'), '/') . '/' . self::$lang . '.json';
            $data = is_file($file) ? json_decode((string) file_get_contents($file), true) : null;
            self::$strings = is_array($data) ? $data : [];
        }
        $text = self::$strings[$key] ?? $key;
        foreach ($params as $name => $value) {
            $text = str_replace(':' . $name, (string) $value, $text);
        }
        return $text;
    }
}
