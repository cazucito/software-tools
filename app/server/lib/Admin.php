<?php
declare(strict_types=1);
namespace St;

if (!defined('ST_APP')) {
    exit;
}

/**
 * Panel de administración (ADR-008). Punto de entrada: /admin[/...].
 * Una sola cuenta; todas las acciones POST verifican CSRF; los cambios de
 * catálogo escriben en catalog.sqlite y quedan marcados para export (ADR-009).
 */
final class Admin
{
    /**
     * Enruta las peticiones del panel. Termina la respuesta.
     *
     * @param list<string>        $segments
     * @param array<string,mixed> $common
     */
    public static function handle(array $segments, array $common): void
    {
        $section = $segments[1] ?? 'home';

        if ($section === 'login') {
            self::login($common);
            return;
        }
        if ($section === 'logout') {
            self::logout();
            return;
        }
        if (!Auth::isLoggedIn()) {
            st_redirect(st_url('admin/login'));
        }

        switch ($section) {
            case 'home':
                self::home($common);
                return;
            case 'comments':
                self::comments($common);
                return;
            case 'downloads':
                self::downloads($segments, $common);
                return;
            case 'catalog':
                self::catalog($segments, $common);
                return;
            case 'export':
                self::export($segments, $common);
                return;
            default:
                http_response_code(404);
                self::render('home', $common + ['title' => 'No encontrado']);
        }
    }

    // ------------------------------------------------------------------
    // Sesión
    // ------------------------------------------------------------------

    private static function login(array $common): void
    {
        if (Auth::isLoggedIn()) {
            st_redirect(st_url('admin/home'));
        }
        $error = null;
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
            if (!Auth::checkCsrf($_POST['csrf'] ?? null)) {
                $error = 'Sesión de formulario inválida; intenta de nuevo.';
            } else {
                $error = Auth::login((string) ($_POST['password'] ?? ''));
                if ($error === null) {
                    Auth::flash('ok', 'Sesión iniciada.');
                    st_redirect(st_url('admin/home'));
                }
            }
        }
        self::render('login', $common + [
            'title'   => 'Acceso — admin',
            'error'   => $error,
            'enabled' => Auth::isEnabled(),
        ]);
    }

    private static function logout(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST' && Auth::checkCsrf($_POST['csrf'] ?? null)) {
            Auth::logout();
            st_redirect(st_url('admin/login'));
        }
        st_redirect(st_url('admin/home'));
    }

    // ------------------------------------------------------------------
    // Dashboard
    // ------------------------------------------------------------------

    private static function home(array $common): void
    {
        self::render('home', $common + [
            'title'     => 'Panel — software-tools',
            'counts'    => Ops::commentCounts(),
            'downloads' => Ops::downloadsAll(),
            'dirty'     => Ops::dirtyList(),
            'totalTools' => count(Catalog::tools()),
        ]);
    }

    // ------------------------------------------------------------------
    // Comentarios
    // ------------------------------------------------------------------

    private static function comments(array $common): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
            self::requireCsrf();
            $action = (string) ($_POST['action'] ?? '');
            $id = (int) ($_POST['id'] ?? 0);
            if ($action === 'status' && $id > 0) {
                $value = (string) ($_POST['value'] ?? 'approved');
                if (in_array($value, ['approved', 'pending', 'spam'], true)) {
                    Ops::commentSetStatus($id, $value);
                    Auth::flash('ok', 'Comentario actualizado.');
                }
            } elseif ($action === 'delete' && $id > 0) {
                Ops::commentDelete($id);
                Auth::flash('ok', 'Comentario borrado.');
            }
            st_redirect(st_url('admin/comments', array_filter(['estado' => $_POST['estado'] ?? null])));
        }

        $filter = (string) ($_GET['estado'] ?? '');
        $status = in_array($filter, ['approved', 'pending', 'spam'], true) ? $filter : null;
        self::render('comments', $common + [
            'title'   => 'Comentarios — admin',
            'filter'  => $filter,
            'rows'    => Ops::commentsAll($status),
            'counts'  => Ops::commentCounts(),
            'toolNames' => self::toolNames(),
        ]);
    }

    // ------------------------------------------------------------------
    // Descargas
    // ------------------------------------------------------------------

    private static function downloads(array $segments, array $common): void
    {
        $slug = $segments[2] ?? '';

        if ($slug === '') {
            $names = self::toolNames();
            $rows = Ops::downloadsAll();
            self::render('downloads', $common + [
                'title'     => 'Descargas — admin',
                'rows'      => $rows,
                'toolNames' => $names,
                'dlHasPass' => Downloads::hasGlobalPass(),
            ]);
            return;
        }

        $tool = Catalog::toolBySlug($slug);
        if ($tool === null) {
            http_response_code(404);
            self::render('admin/home', $common + ['title' => 'No encontrado']);
            return;
        }

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
            self::requireCsrf();
            $action = (string) ($_POST['action'] ?? '');
            $back = st_url('admin/downloads/' . $slug);

            if ($action === 'set_dl_pass') {
                $pass = (string) ($_POST['dl_pass'] ?? '');
                if (mb_strlen($pass) < 8) {
                    Auth::flash('error', 'La contraseña debe tener al menos 8 caracteres.');
                } else {
                    $algo = defined('PASSWORD_ARGON2ID') ? PASSWORD_ARGON2ID : PASSWORD_BCRYPT;
                    Ops::settingSet('downloads_pass_hash', password_hash($pass, $algo));
                    Auth::flash('ok', 'Contraseña general de descargas actualizada.');
                }
            } elseif ($action === 'register') {
                $version = self::nullable((string) ($_POST['version'] ?? ''));
                $variant = self::nullable((string) ($_POST['variant'] ?? ''));
                $year = self::nullable((string) ($_POST['year'] ?? ''));
                $result = !empty($_POST['no_file'])
                    ? Downloads::registerMeta(
                        $slug,
                        (string) ($_POST['filename'] ?? ''),
                        (int) ($_POST['size'] ?? 0),
                        trim((string) ($_POST['sha256'] ?? '')),
                        self::dlVis(),
                        self::nullable((string) ($_POST['license'] ?? '')),
                        self::nullable((string) ($_POST['source_url'] ?? '')),
                        $version,
                        $variant,
                        $year
                    )
                    : Downloads::register(
                        $slug,
                        (string) ($_POST['filename'] ?? ''),
                        self::dlVis(),
                        self::nullable((string) ($_POST['license'] ?? '')),
                        self::nullable((string) ($_POST['source_url'] ?? '')),
                        $version,
                        $variant,
                        $year
                    );
                Auth::flash(empty($result['error']) ? 'ok' : 'error', (string) ($result['error'] ?? 'Archivo registrado.'));
            } elseif ($action === 'update_file') {
                $nuName = self::nullable((string) ($_POST['filename'] ?? ''));
                $nuName = $nuName === null ? null : Downloads::safeRel($nuName);
                if ($_POST['filename'] !== '' && $nuName === null) {
                    Auth::flash('error', 'Nombre de archivo (ruta relativa) no permitido.');
                } else {
                    Ops::downloadUpdate(
                        (int) ($_POST['id'] ?? 0),
                        self::dlVis(),
                        self::nullable((string) ($_POST['license'] ?? '')),
                        self::nullable((string) ($_POST['source_url'] ?? '')),
                        self::nullable((string) ($_POST['version'] ?? '')),
                        self::nullable((string) ($_POST['variant'] ?? '')),
                        self::nullable((string) ($_POST['year'] ?? '')),
                        $nuName
                    );
                    Auth::flash('ok', 'Archivo actualizado.');
                }
            } elseif ($action === 'delete_file') {
                $withFile = !empty($_POST['with_file']);
                Downloads::remove((int) ($_POST['id'] ?? 0), $withFile);
                Auth::flash('ok', $withFile ? 'Registro y archivo borrados.' : 'Registro borrado (el archivo sigue en disco).');
            } elseif ($action === 'upload') {
                Auth::flash(...self::handleUpload($slug));
            }
            st_redirect($back);
        }

        $files = Ops::downloadsFor($slug);
        foreach ($files as &$file) {
            $file['human_size'] = Downloads::humanSize((int) $file['bytes']);
            $file['sha_short'] = substr((string) $file['sha256'], 0, 12);
        }
        self::render('download-manage', $common + [
            'title'        => $tool['name'] . ' — descargas',
            'tool'         => $tool,
            'files'        => $files,
            'unregistered' => Downloads::scan($slug),
            'dlHasPass'    => Downloads::hasGlobalPass(),
            'maxUpload'    => Downloads::humanSize((int) st_config('downloads')['max_upload']),
        ]);
    }

    /** Subida directa de un archivo (≤ límite). @return array{0:string,1:string} */
    private static function handleUpload(string $slug): array
    {
        if (empty($_FILES['file']) || !is_array($_FILES['file']) || ($_FILES['file']['error'] ?? 1) !== UPLOAD_ERR_OK) {
            return ['error', 'No llegó ningún archivo (o excedió el límite del hosting).'];
        }
        $size = (int) $_FILES['file']['size'];
        if ($size <= 0 || $size > (int) st_config('downloads')['max_upload']) {
            return ['error', 'El archivo excede el límite de subida directa; usa FTP.'];
        }
        $filename = Downloads::safeRel((string) $_FILES['file']['name']);
        if ($filename === null) {
            return ['error', 'Nombre de archivo no permitido.'];
        }
        if (!preg_match('/^[a-z0-9-]+$/', $slug)) {
            return ['error', 'Slug inválido.'];
        }
        $dir = rtrim((string) st_config('downloads_dir'), '/') . '/' . $slug . '/' . dirname($filename);
        if (!is_dir($dir) && !mkdir($dir, 0755, true)) {
            return ['error', 'No se pudo crear downloads/' . $slug . '/.'];
        }
        if (!move_uploaded_file((string) $_FILES['file']['tmp_name'], $dir . '/' . basename($filename))) {
            return ['error', 'No se pudo guardar el archivo.'];
        }
        $result = Downloads::register(
            $slug,
            $filename,
            self::dlVis(),
            self::nullable((string) ($_POST['license'] ?? '')),
            self::nullable((string) ($_POST['source_url'] ?? '')),
            self::nullable((string) ($_POST['version'] ?? '')),
            self::nullable((string) ($_POST['variant'] ?? ''))
        );
        return empty($result['error']) ? ['ok', 'Archivo subido y registrado.'] : ['error', (string) $result['error']];
    }

    /** Visibilidad simplificada: alojado (con contraseña general) o solo enlace. */
    private static function dlVis(): string
    {
        return (($_POST['visibility'] ?? 'clave') === 'enlace') ? 'enlace' : 'clave';
    }

    // ------------------------------------------------------------------
    // Catálogo
    // ------------------------------------------------------------------

    private static function catalog(array $segments, array $common): void
    {
        $slug = $segments[2] ?? '';
        $dirty = [];
        foreach (Ops::dirtyList() as $row) {
            $dirty[(string) $row['tool_slug']] = (string) $row['changed_at'];
        }

        if ($slug === '') {
            if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
                self::requireCsrf();
                if ((string) ($_POST['action'] ?? '') === 'toggle') {
                    CatalogAdmin::togglePublished((string) ($_POST['slug'] ?? ''));
                    Auth::flash('ok', 'Publicación actualizada.');
                }
                st_redirect(st_url('admin/catalog'));
            }
            self::render('catalog', $common + [
                'title'  => 'Catálogo — admin',
                'tools'  => Catalog::tools(),
                'dirty'  => $dirty,
            ]);
            return;
        }

        $tool = Catalog::toolBySlug($slug);
        if ($tool === null) {
            http_response_code(404);
            self::render('admin/home', $common + ['title' => 'No encontrado']);
            return;
        }

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
            self::requireCsrf();
            $action = (string) ($_POST['action'] ?? 'save');
            $back = st_url('admin/catalog/' . $slug);

            if ($action === 'save') {
                $fields = [
                    'name'          => trim((string) ($_POST['name'] ?? '')),
                    'year'          => (int) ($_POST['year'] ?? 0),
                    'used_until'    => trim((string) ($_POST['used_until'] ?? '')),
                    'category'      => trim((string) ($_POST['category'] ?? 'tool')),
                    'context'       => trim((string) ($_POST['context'] ?? '')),
                    'body'          => rtrim((string) ($_POST['body'] ?? '')),
                    'image'         => trim((string) ($_POST['image'] ?? '')),
                    'successor'     => trim((string) ($_POST['successor'] ?? '')),
                    'successor_slug' => trim((string) ($_POST['successor_slug'] ?? '')),
                    'published'     => !empty($_POST['published']),
                    'tags'          => self::csv((string) ($_POST['tags'] ?? '')),
                    'related'       => self::csv((string) ($_POST['related'] ?? '')),
                ];
                $error = CatalogAdmin::updateTool($slug, $fields);
                Auth::flash($error === null ? 'ok' : 'error', $error ?? 'Ficha guardada (pendiente de export al repo).');
            } elseif ($action === 'asset_upload') {
                Auth::flash(...self::handleAssetUpload($slug));
            }
            st_redirect($back);
        }

        $tool['related_csv'] = implode(', ', $tool['related']);
        $tool['tags_csv'] = implode(', ', $tool['tags']);
        self::render('catalog-edit', $common + [
            'title'  => $tool['name'] . ' — editar',
            'tool'   => $tool,
            'dirty'  => isset($dirty[$slug]),
            'assets' => Ops::assetsFor($slug),
        ]);
    }

    /** Subida/actualización de un asset (icon | logo | cover). @return array{0:string,1:string} */
    private static function handleAssetUpload(string $slug): array
    {
        $kind = (string) ($_POST['kind'] ?? '');
        if (!in_array($kind, ['icon', 'logo', 'cover'], true)) {
            return ['error', 'Tipo de asset inválido.'];
        }
        if (empty($_FILES['asset']['tmp_name']) || ($_FILES['asset']['error'] ?? 1) !== UPLOAD_ERR_OK) {
            return ['error', 'No llegó ningún archivo.'];
        }
        $size = (int) $_FILES['asset']['size'];
        if ($size <= 0 || $size > (int) st_config('downloads')['max_upload']) {
            return ['error', 'La imagen excede el límite (1.5 MB).'];
        }
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = (string) $finfo->file((string) $_FILES['asset']['tmp_name']);
        $ext = ['image/png' => 'png', 'image/jpeg' => 'jpg', 'image/webp' => 'webp', 'image/svg+xml' => 'svg'][$mime] ?? null;
        if ($ext === null) {
            return ['error', 'Formato de imagen no permitido (png, jpg, webp, svg).'];
        }
        $dir = rtrim((string) st_config('assets_tools_dir'), '/') . '/' . $slug;
        if (!is_dir($dir) && !mkdir($dir, 0755, true)) {
            return ['error', 'No se pudo crear assets/tools/' . $slug . '/.'];
        }
        $file = $kind . '.' . $ext;
        // Limpia versiones previas de este tipo con otras extensiones.
        foreach (glob($dir . '/' . $kind . '.*') ?: [] as $old) {
            if (basename($old) !== $file) {
                unlink($old);
            }
        }
        if (!move_uploaded_file((string) $_FILES['asset']['tmp_name'], $dir . '/' . $file)) {
            return ['error', 'No se pudo guardar la imagen.'];
        }
        Ops::assetSet(
            $slug,
            $kind,
            'assets/tools/' . $slug . '/' . $file,
            self::nullable((string) ($_POST['source'] ?? '')),
            self::nullable((string) ($_POST['license'] ?? ''))
        );
        return ['ok', 'Imagen actualizada.'];
    }

    // ------------------------------------------------------------------
    // Export a Markdown
    // ------------------------------------------------------------------

    private static function export(array $segments, array $common): void
    {
        $slug = $segments[2] ?? '';

        if ($slug === '') {
            if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
                self::requireCsrf();
                if ((string) ($_POST['action'] ?? '') === 'mark_exported') {
                    $slugs = array_map(static fn (array $r): string => (string) $r['tool_slug'], Ops::dirtyList());
                    Ops::dirtyClear($slugs);
                    Auth::flash('ok', 'Pendientes marcados como exportados (' . count($slugs) . ').');
                }
                st_redirect(st_url('admin/export'));
            }
            if (!empty($_GET['bundle'])) {
                Export::bundle();
            }
            self::render('export', $common + [
                'title' => 'Export — admin',
                'dirty' => Ops::dirtyList(),
                'tools' => Catalog::tools(),
            ]);
            return;
        }

        Export::download($slug); // termina la respuesta
    }

    // ------------------------------------------------------------------
    // Utilidades
    // ------------------------------------------------------------------

    /** @return array<string,string> slug => nombre */
    private static function toolNames(): array
    {
        $names = [];
        foreach (Catalog::tools() as $tool) {
            $names[(string) $tool['slug']] = (string) $tool['name'];
        }
        return $names;
    }

    private static function requireCsrf(): void
    {
        if (!Auth::checkCsrf($_POST['csrf'] ?? null)) {
            http_response_code(400);
            exit('CSRF');
        }
    }

    private static function nullable(string $value): ?string
    {
        $value = trim($value);
        return $value === '' ? null : $value;
    }

    /** @return list<string> */
    private static function csv(string $value): array
    {
        $parts = array_map('trim', explode(',', $value));
        return array_values(array_filter($parts, static fn (string $p): bool => $p !== ''));
    }

    private static function render(string $template, array $vars): void
    {
        $vars['flash'] = Auth::takeFlash();
        $vars['csrf'] = Auth::csrf();
        View::admin($template, $vars);
    }
}
