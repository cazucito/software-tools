<?php
if (!defined('ST_APP')) {
    exit;
}
/** Layout del panel admin. Variables: $content, $title, $flash, $csrf. */
$title = $title ?? 'admin — software-tools';
$flash = $flash ?? null;
?><!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($title) ?></title>
<meta name="robots" content="noindex">
<link rel="icon" href="data:,">
<link rel="stylesheet" href="<?= e(st_asset('css/admin.css')) ?>">
</head>
<body class="ad">
<header class="ad-header">
  <div class="ad-header__in">
    <a class="ad-brand" href="<?= e(st_url('admin/home')) ?>">software-tools <span>admin</span></a>
    <nav class="ad-nav">
      <a href="<?= e(st_url('admin/home')) ?>">Panel</a>
      <a href="<?= e(st_url('admin/comments')) ?>">Comentarios</a>
      <a href="<?= e(st_url('admin/downloads')) ?>">Descargas</a>
      <a href="<?= e(st_url('admin/catalog')) ?>">Catálogo</a>
      <a href="<?= e(st_url('admin/export')) ?>">Export</a>
    </nav>
    <div class="ad-header__right">
      <a class="ad-ext" href="<?= e(st_url('')) ?>" target="_blank" rel="noopener">Ver sitio ↗</a>
      <form method="post" action="<?= e(st_url('admin/logout')) ?>">
        <input type="hidden" name="csrf" value="<?= e($csrf) ?>">
        <button class="ad-linkbtn" type="submit">Salir</button>
      </form>
    </div>
  </div>
</header>
<main class="ad-main">
  <?php if (is_array($flash)): ?>
  <div class="ad-flash ad-flash--<?= e((string) $flash['type']) ?>"><?= e((string) $flash['message']) ?></div>
  <?php endif; ?>
  <?= $content ?>
</main>
<footer class="ad-footer"><span>software-tools · panel de administración</span></footer>
</body>
</html>
