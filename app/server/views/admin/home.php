<?php
if (!defined('ST_APP')) {
    exit;
}
/** Dashboard. Variables: $counts, $downloads, $dirty, $totalTools. */
$filesTotal = 0;
foreach ($downloads as $d) {
    $filesTotal += (int) $d['files'];
}
$pending = $counts['pending'] ?? 0;
?>
<h1 class="ad-title">Panel</h1>
<div class="ad-cards">
  <section class="ad-card">
    <h2>Comentarios</h2>
    <p class="ad-card__big"><?= e($counts['approved'] + $pending + $counts['spam']) ?></p>
    <p class="ad-card__meta">
      <?= e($counts['approved']) ?> aprobados ·
      <a href="<?= e(st_url('admin/comments', ['estado' => 'pending'])) ?>"><?= e($pending) ?> pendientes</a> ·
      <a href="<?= e(st_url('admin/comments', ['estado' => 'spam'])) ?>"><?= e($counts['spam']) ?> spam</a>
    </p>
    <a class="ad-btn" href="<?= e(st_url('admin/comments')) ?>">Moderar</a>
  </section>

  <section class="ad-card">
    <h2>Descargas</h2>
    <p class="ad-card__big"><?= e($filesTotal) ?></p>
    <p class="ad-card__meta"><?= e(count($downloads)) ?> herramientas con archivos registrados</p>
    <a class="ad-btn" href="<?= e(st_url('admin/downloads')) ?>">Gestionar</a>
  </section>

  <section class="ad-card">
    <h2>Catálogo</h2>
    <p class="ad-card__big"><?= e($totalTools) ?></p>
    <p class="ad-card__meta">herramientas · <a href="<?= e(st_url('admin/export')) ?>"><?= e(count($dirty)) ?> pendientes de export</a></p>
    <a class="ad-btn" href="<?= e(st_url('admin/catalog')) ?>">Editar</a>
  </section>
</div>

<p class="ad-hint">
  Los cambios del catálogo se ven al instante en el sitio y quedan marcados como
  <strong>pendientes de export</strong>: descarga el Markdown desde <em>Export</em> y pásalo al repo para dejar el archivo versionado en sincronía.
</p>
