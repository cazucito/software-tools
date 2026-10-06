<?php
if (!defined('ST_APP')) {
    exit;
}
/** Export a Markdown. Variables: $dirty, $tools, $csrf. */
?>
<h1 class="ad-title">Export a Markdown</h1>
<p class="ad-note">
  Descarga las fichas editadas y llévalas al repo (<code>src/content/tools/</code>) — así el Markdown
  vuelve a ser la fuente y la próxima importación no perderá nada. El export reproduce el formato
  canónico (round-trip verificado contra las fuentes).
</p>

<?php if ($dirty): ?>
<div class="ad-block">
  <h2>Pendientes de export (<?= e(count($dirty)) ?>)</h2>
  <div class="ad-table-wrap">
  <table class="ad-table">
    <thead><tr><th>Slug</th><th>Cambiado</th><th>Descargar</th></tr></thead>
    <tbody>
      <?php foreach ($dirty as $row): ?>
      <tr>
        <td><code><?= e($row['tool_slug']) ?></code></td>
        <td><?= e($row['changed_at']) ?></td>
        <td><a class="ad-btn ad-btn--sm" href="<?= e(st_url('admin/export/' . $row['tool_slug'])) ?>">⬇ .md</a></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  </div>
  <form method="post" action="<?= e(st_url('admin/export')) ?>" style="margin-top:.8rem">
    <input type="hidden" name="csrf" value="<?= e($csrf) ?>">
    <input type="hidden" name="action" value="mark_exported">
    <button class="ad-btn" type="submit">Marcar todo lo pendiente como exportado</button>
  </form>
</div>
<?php else: ?>
<p class="ad-flash ad-flash--ok">No hay cambios pendientes: el catálogo está en sincronía con el repo.</p>
<?php endif; ?>

<div class="ad-block">
  <h2>Todas las fichas</h2>
  <p class="ad-note"><a class="ad-btn ad-btn--sm" href="<?= e(st_url('admin/export', ['bundle' => 1])) ?>">⬇ Export completo (bundle)</a></p>
  <div class="ad-table-wrap" style="margin-top:.8rem">
  <table class="ad-table">
    <thead><tr><th>Slug</th><th>Nombre</th><th>Descargar</th></tr></thead>
    <tbody>
      <?php foreach ($tools as $t): ?>
      <tr>
        <td><code><?= e($t['slug']) ?></code></td>
        <td><?= e($t['name']) ?></td>
        <td><a class="ad-btn ad-btn--sm" href="<?= e(st_url('admin/export/' . $t['slug'])) ?>">⬇ .md</a></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  </div>
</div>