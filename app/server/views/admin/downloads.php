<?php
if (!defined('ST_APP')) {
    exit;
}
/** Descargas: resumen por herramienta. Variables: $rows, $toolNames, $keyed. */
$filesTotal = 0;
$bytesTotal = 0;
foreach ($rows as $d) {
    $filesTotal += (int) $d['files'];
    $bytesTotal += (int) $d['total_bytes'];
}
?>
<h1 class="ad-title">Descargas</h1>
<p class="ad-note">
  Los archivos viven en <code>downloads/&lt;slug&gt;/</code> (accesibles solo vía PHP) y se ofrecen con
  <strong>clave individual por software</strong> (ADR-007). Sube archivos grandes «a mano» a esa carpeta
  (FTP) y luego <em>regístralos</em> aquí; los pequeños se pueden subir directamente desde cada herramienta.
  Subida directa máx. <?= e(St\Downloads::humanSize((int) st_config('downloads')['max_upload'])) ?>.
</p>

<div class="ad-cards">
  <section class="ad-card">
    <h2>Archivos registrados</h2>
    <p class="ad-card__big"><?= e($filesTotal) ?></p>
    <p class="ad-card__meta">en <?= e(count($rows)) ?> herramientas · <?= e(St\Downloads::humanSize($bytesTotal)) ?></p>
  </section>
</div>

<div class="ad-block">
  <h2>Por herramienta</h2>
  <?php if (!$toolNames): ?>
  <p class="ad-hint">Sin herramientas.</p>
  <?php else: ?>
  <div class="ad-table-wrap">
  <table class="ad-table">
    <thead><tr><th>Herramienta</th><th>Archivos</th><th>Tamaño</th><th>Clave</th><th></th></tr></thead>
    <tbody>
      <?php foreach ($toolNames as $slug => $name): ?>
      <?php $row = null;
      foreach ($rows as $d) {
          if ($d['tool_slug'] === $slug) { $row = $d; break; }
      } ?>
      <tr>
        <td><a href="<?= e(st_url('tools/' . $slug)) ?>" target="_blank" rel="noopener"><?= e($name) ?></a></td>
        <td><?= $row ? e((int) $row['files']) : '<span class="ad-badge ad-badge--muted">0</span>' ?></td>
        <td><?= $row ? e(St\Downloads::humanSize((int) $row['total_bytes'])) : '—' ?></td>
        <td><?= $keyed[$slug] ? '<span class="ad-badge ad-badge--ok">definida</span>' : '<span class="ad-badge ad-badge--muted">sin clave</span>' ?></td>
        <td><a class="ad-btn ad-btn--sm" href="<?= e(st_url('admin/downloads/' . $slug)) ?>">Gestionar</a></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  </div>
  <?php endif; ?>
</div>