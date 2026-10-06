<?php
if (!defined('ST_APP')) {
    exit;
}
/** Catálogo: lista con edición/despublicación. Variables: $tools, $dirty, $csrf. */
?>
<h1 class="ad-title">Catálogo · <?= e(count($tools)) ?> herramientas</h1>
<p class="ad-note">Al guardar, los cambios aparecen al instante en el sitio y la ficha queda «pendiente de export» hasta que descargues su Markdown desde <em>Export</em> y lo lleves al repo.</p>

<div class="ad-table-wrap">
<table class="ad-table">
  <thead><tr><th>Año</th><th>Nombre</th><th>Slug</th><th>Categoría</th><th>Estado</th><th>Export</th><th></th></tr></thead>
  <tbody>
    <?php foreach ($tools as $t): ?>
    <tr>
      <td><?= e((int) $t['year']) ?></td>
      <td><a href="<?= e(st_url('admin/catalog/' . $t['slug'])) ?>"><?= e($t['name']) ?></a></td>
      <td><code><?= e($t['slug']) ?></code></td>
      <td><?= e($t['category']) ?></td>
      <td><?= $t['published'] ? '<span class="ad-badge ad-badge--ok">publicada</span>' : '<span class="ad-badge ad-badge--err">oculta</span>' ?></td>
      <td><?= isset($dirty[$t['slug']]) ? '<span class="ad-badge ad-badge--warn">pendiente</span>' : '<span class="ad-badge ad-badge--muted">al día</span>' ?></td>
      <td><div class="ad-actions">
        <a class="ad-btn ad-btn--sm" href="<?= e(st_url('admin/catalog/' . $t['slug'])) ?>">Editar</a>
        <form method="post" action="<?= e(st_url('admin/catalog')) ?>">
          <input type="hidden" name="csrf" value="<?= e($csrf) ?>">
          <input type="hidden" name="action" value="toggle">
          <input type="hidden" name="slug" value="<?= e($t['slug']) ?>">
          <button class="ad-btn ad-btn--sm<?= $t['published'] ? ' ad-btn--danger' : '' ?>" type="submit"><?= $t['published'] ? 'Ocultar' : 'Publicar' ?></button>
        </form>
      </div></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>
</div>