<?php
if (!defined('ST_APP')) {
    exit;
}
/**
 * Gestión de descargas de una herramienta. Variables: $tool, $files,
 * $unregistered, $hasKey, $maxUpload, $csrf.
 */
$slug = (string) $tool['slug'];
?>
<h1 class="ad-title">Descargas · <?= e($tool['name']) ?> <span class="ad-badge ad-badge--muted"><?= e($slug) ?></span></h1>
<p class="ad-note"><a href="<?= e(st_url('tools/' . $slug)) ?>" target="_blank" rel="noopener">Ver la ficha ↗</a></p>

<!-- Clave del software -->
<fieldset class="ad-fieldset">
  <legend>🔑 Clave de esta herramienta</legend>
  <p class="ad-note">Los archivos con visibilidad «clave» solo se descargan tras escribir esta contraseña (sesión corta por herramienta). Los archivos «públicos» no la necesitan.</p>
  <?php if ($hasKey): ?>
  <p><span class="ad-badge ad-badge--ok">clave definida</span> （hash argon2/bcrypt almacenado, nunca en claro）</p>
  <?php endif; ?>
  <form class="ad-inline-form" method="post" action="<?= e(st_url('admin/downloads/' . $slug)) ?>">
    <input type="hidden" name="csrf" value="<?= e($csrf) ?>">
    <input type="hidden" name="action" value="set_key">
    <div class="ad-field">
      <label for="key">Nueva clave (mín. 4 caracteres)</label>
      <input id="key" type="password" name="key" minlength="4" required autocomplete="new-password">
    </div>
    <button class="ad-btn ad-btn--primary" type="submit"><?= $hasKey ? 'Cambiar clave' : 'Definir clave' ?></button>
  </form>
  <?php if ($hasKey): ?>
  <form method="post" action="<?= e(st_url('admin/downloads/' . $slug)) ?>" onsubmit="return confirm('¿Eliminar la clave? Los archivos «clave» quedarán bloqueados.');">
    <input type="hidden" name="csrf" value="<?= e($csrf) ?>">
    <input type="hidden" name="action" value="del_key">
    <button class="ad-btn ad-btn--danger ad-btn--sm" type="submit">Eliminar clave</button>
  </form>
  <?php endif; ?>
</fieldset>

<!-- Archivos registrados -->
<fieldset class="ad-fieldset">
  <legend>📦 Archivos registrados</legend>
  <?php if (!$files): ?>
  <p class="ad-note">Todavía no hay archivos registrados para <?= e($tool['name']) ?>.</p>
  <?php else: ?>
  <div class="ad-table-wrap">
  <table class="ad-table">
    <thead><tr><th>Archivo</th><th>Tamaño</th><th>sha256</th><th></th></tr></thead>
    <tbody>
      <?php foreach ($files as $f): ?>
      <tr>
        <td colspan="4">
          <form class="ad-inline-form" method="post" action="<?= e(st_url('admin/downloads/' . $slug)) ?>">
            <input type="hidden" name="csrf" value="<?= e($csrf) ?>">
            <input type="hidden" name="action" value="update_file">
            <input type="hidden" name="id" value="<?= e((int) $f['id']) ?>">
            <strong class="ad-file"><?= e($f['filename']) ?></strong>
            <span class="ad-note"><?= e(St\Downloads::humanSize((int) $f['bytes'])) ?></span>
            <span class="ad-note" title="<?= e((string) $f['sha256']) ?>"><?= e(substr((string) $f['sha256'], 0, 12)) ?>…</span>
            <select name="visibility">
              <?php foreach (['publico' => 'Público', 'clave' => 'Con clave', 'enlace' => 'Solo enlace'] as $v => $label): ?>
              <option value="<?= e($v) ?>"<?= $f['visibility'] === $v ? ' selected' : '' ?>><?= e($label) ?></option>
              <?php endforeach; ?>
            </select>
            <input type="text" name="license" placeholder="Licencia / nota" value="<?= e((string) ($f['license_note'] ?? '')) ?>" style="min-width:180px">
            <input type="url" name="source_url" placeholder="URL oficial (si «solo enlace»)" value="<?= e((string) ($f['source_url'] ?? '')) ?>" style="min-width:220px">
            <button class="ad-btn ad-btn--sm" type="submit">Guardar</button>
          </form>
        </td>
      </tr>
      <tr>
        <td colspan="4">
          <form method="post" action="<?= e(st_url('admin/downloads/' . $slug)) ?>" onsubmit="return confirm('¿Borrar el registro<?= $f['visibility'] !== 'enlace' ? ' y el archivo de disco' : '' ?>?');">
            <input type="hidden" name="csrf" value="<?= e($csrf) ?>">
            <input type="hidden" name="action" value="delete_file">
            <input type="hidden" name="id" value="<?= e((int) $f['id']) ?>">
            <input type="hidden" name="with_file" value="1">
            <button class="ad-btn ad-btn--danger ad-btn--sm" type="submit">Borrar registro y archivo</button>
          </form>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  </div>
  <?php endif; ?>
</fieldset>

<!-- Subida directa -->
<fieldset class="ad-fieldset">
  <legend>⬆️ Subir archivo (≤ <?= e($maxUpload) ?>)</legend>
  <form class="ad-inline-form" method="post" enctype="multipart/form-data"
        action="<?= e(st_url('admin/downloads/' . $slug)) ?>">
    <input type="hidden" name="csrf" value="<?= e($csrf) ?>">
    <input type="hidden" name="action" value="upload">
    <div class="ad-field">
      <label for="file">Archivo</label>
      <input id="file" type="file" name="file" required>
    </div>
    <select name="visibility">
      <option value="clave" selected>Con clave (copia personal)</option>
      <option value="publico">Público (redistribuible)</option>
      <option value="enlace">Solo enlace (no se sube)</option>
    </select>
    <input type="text" name="license" placeholder="Licencia / nota">
    <input type="url" name="source_url" placeholder="URL oficial">
    <button class="ad-btn ad-btn--primary" type="submit">Subir y registrar</button>
  </form>
  <p class="ad-note">Archivos mayores: súbelos por FTP a <code>downloads/<?= e($slug) ?>/</code> y regístralos en el siguiente bloque.</p>
</fieldset>

<!-- Subidos por FTP, aún sin registrar -->
<?php if ($unregistered): ?>
<fieldset class="ad-fieldset">
  <legend>📂 En disco sin registrar (subidos por FTP)</legend>
  <p class="ad-note">Un clic los registra con sha256 automático.</p>
  <?php foreach ($unregistered as $uf): ?>
  <form class="ad-inline-form" method="post" action="<?= e(st_url('admin/downloads/' . $slug)) ?>">
    <input type="hidden" name="csrf" value="<?= e($csrf) ?>">
    <input type="hidden" name="action" value="register">
    <input type="hidden" name="filename" value="<?= e($uf['filename']) ?>">
    <strong class="ad-file"><?= e($uf['filename']) ?></strong>
    <span class="ad-note"><?= e($uf['human_size']) ?></span>
    <select name="visibility">
      <option value="clave" selected>Con clave (copia personal)</option>
      <option value="publico">Público (redistribuible)</option>
      <option value="enlace">Solo enlace</option>
    </select>
    <input type="text" name="license" placeholder="Licencia / nota">
    <input type="url" name="source_url" placeholder="URL oficial (si «solo enlace»)">
    <button class="ad-btn ad-btn--primary ad-btn--sm" type="submit">Registrar</button>
  </form>
  <?php endforeach; ?>
</fieldset>
<?php else: ?>
<p class="ad-hint">No hay archivos sin registrar en <code>downloads/<?= e($slug) ?>/</code>.</p>
<?php endif; ?>