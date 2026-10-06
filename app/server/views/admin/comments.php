<?php
if (!defined('ST_APP')) {
    exit;
}
/**
 * Moderación de comentarios. Variables: $rows, $filter, $counts, $toolNames, $csrf.
 */
$tabs = [
    ''           => 'Todos',
    'approved'   => 'Aprobados',
    'pending'    => 'Pendientes',
    'spam'       => 'Spam',
];
?>
<h1 class="ad-title">Comentarios</h1>
<div class="ad-tabs ad-note">
  <?php foreach ($tabs as $value => $label): ?>
  <a class="ad-btn ad-btn--sm<?= $filter === $value ? ' ad-btn--primary' : '' ?>"
     href="<?= e(st_url('admin/comments', $value !== '' ? ['estado' => $value] : [])) ?>"><?= e($label) ?></a>
  <?php endforeach; ?>
</div>

<?php if (!$rows): ?>
<p class="ad-hint">No hay comentarios<?= $filter !== '' ? ' en esta categoría' : '' ?>.</p>
<?php else: ?>
<div class="ad-table-wrap">
<table class="ad-table">
  <thead>
    <tr><th>#</th><th>Herramienta</th><th>Autor</th><th>Comentario</th><th>Fecha</th><th>Estado</th><th>Acciones</th></tr>
  </thead>
  <tbody>
    <?php foreach ($rows as $row): ?>
    <tr>
      <td><?= e((int) $row['id']) ?></td>
      <td><a href="<?= e(st_url('tools/' . $row['tool_slug'])) ?>" target="_blank" rel="noopener"><?= e($toolNames[$row['tool_slug']] ?? $row['tool_slug']) ?> ↗</a></td>
      <td><?= e($row['author']) ?></td>
      <td title="<?= e($row['body']) ?>"><?= e(mb_substr((string) $row['body'], 0, 180)) ?><?= mb_strlen((string) $row['body']) > 180 ? ' …' : '' ?></td>
      <td><?= e(St\Comments::humanDate((string) $row['created_at'])) ?></td>
      <td><span class="ad-badge ad-badge--<?= $row['status'] === 'approved' ? 'ok' : ($row['status'] === 'spam' ? 'err' : 'warn') ?>"><?= e($row['status']) ?></span></td>
      <td><div class="ad-actions">
        <?php if ($row['status'] !== 'approved'): ?>
        <form method="post" action="<?= e(st_url('admin/comments')) ?>">
          <input type="hidden" name="csrf" value="<?= e($csrf) ?>">
          <input type="hidden" name="action" value="status">
          <input type="hidden" name="id" value="<?= e((int) $row['id']) ?>">
          <input type="hidden" name="value" value="approved">
          <input type="hidden" name="estado" value="<?= e($filter) ?>">
          <button class="ad-btn ad-btn--sm" type="submit">Aprobar</button>
        </form>
        <?php endif; ?>
        <?php if ($row['status'] !== 'spam'): ?>
        <form method="post" action="<?= e(st_url('admin/comments')) ?>">
          <input type="hidden" name="csrf" value="<?= e($csrf) ?>">
          <input type="hidden" name="action" value="status">
          <input type="hidden" name="id" value="<?= e((int) $row['id']) ?>">
          <input type="hidden" name="value" value="spam">
          <input type="hidden" name="estado" value="<?= e($filter) ?>">
          <button class="ad-btn ad-btn--sm ad-btn--danger" type="submit">Spam</button>
        </form>
        <?php endif; ?>
        <form method="post" action="<?= e(st_url('admin/comments')) ?>" onsubmit="return confirm('¿Borrar definitivamente este comentario?');">
          <input type="hidden" name="csrf" value="<?= e($csrf) ?>">
          <input type="hidden" name="action" value="delete">
          <input type="hidden" name="id" value="<?= e((int) $row['id']) ?>">
          <input type="hidden" name="estado" value="<?= e($filter) ?>">
          <button class="ad-btn ad-btn--sm ad-btn--danger" type="submit">Borrar</button>
        </form>
      </div></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>
</div>
<?php endif; ?>