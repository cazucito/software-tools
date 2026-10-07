<?php
if (!defined('ST_APP')) {
    exit;
}
/**
 * Gestión de descargas de una herramienta. Variables: $tool, $files,
 * $unregistered, $dlHasPass, $maxUpload, $csrf.
 */
$slug = (string) $tool['slug'];
?>
<h1 class="ad-title">Descargas · <?= e($tool['name']) ?> <span class="ad-badge ad-badge--muted"><?= e($slug) ?></span></h1>
<p class="ad-note"><a href="<?= e(st_url('tools/' . $slug)) ?>" target="_blank" rel="noopener">Ver la ficha ↗</a></p>

<!-- Contraseña general de descargas (todo el sitio) -->
<fieldset class="ad-fieldset">
  <legend>🔑 Contraseña general de descargas</legend>
  <p class="ad-note">Una sola contraseña protege <strong>todas</strong> las descargas alojadas del sitio (sesión de 2 h, cookie firmada). Los archivos «enlace» son públicos y no la necesitan.</p>
  <p>
    <span class="ad-badge <?= $dlHasPass ? 'ad-badge--ok' : '' ?>"><?= $dlHasPass ? 'contraseña definida' : 'SIN definición (descargas bloqueadas para todos)' ?></span>
  </p>
  <form class="ad-inline-form" method="post" action="<?= e(st_url('admin/downloads/' . $slug)) ?>">
    <input type="hidden" name="csrf" value="<?= e($csrf) ?>">
    <input type="hidden" name="action" value="set_dl_pass">
    <div class="ad-field">
      <label for="dl_pass">Nueva contraseña (mín. 8 caracteres)</label>
      <input id="dl_pass" type="password" name="dl_pass" minlength="8" required autocomplete="new-password">
    </div>
    <button class="ad-btn ad-btn--primary" type="submit"><?= $dlHasPass ? 'Cambiar contraseña' : 'Definir contraseña' ?></button>
  </form>
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
              <?php foreach (['clave' => 'Alojado (con contraseña)', 'enlace' => 'Solo enlace oficial'] as $v => $label): ?>
              <option value="<?= e($v) ?>"<?= $f['visibility'] === $v ? ' selected' : '' ?>><?= e($label) ?></option>
              <?php endforeach; ?>
            </select>
            <input type="text" name="license" placeholder="Licencia / nota" value="<?= e((string) ($f['license_note'] ?? '')) ?>" style="min-width:180px">
            <input type="url" name="source_url" placeholder="URL oficial (si «solo enlace»)" value="<?= e((string) ($f['source_url'] ?? '')) ?>" style="min-width:220px">
            <input type="text" name="version" placeholder="Versión (8.2)" value="<?= e((string) ($f['version'] ?? '')) ?>" style="min-width:80px" title="Versión del software">
            <input type="text" name="variant" placeholder="Variante (IDE…)" value="<?= e((string) ($f['variant'] ?? '')) ?>" style="min-width:130px" title="Edición/pack (Visual Web, Profiler…)">
            <input type="text" name="year" placeholder="Año (2013)" value="<?= e((string) ($f['year'] ?? '')) ?>" style="min-width:70px" title="Año aproximado de la versión">
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

<!-- Registro manual: archivo grande (por FTP) aún no subido -->
<fieldset class="ad-fieldset">
  <legend>📝 Registro manual (archivo grande subido por FTP después)</legend>
  <p class="ad-note">Registra la metadata ahora (tamaño + sha256); la ficha lo muestra como «pendiente de subir» y queda operativo al llegar el archivo por FTP.</p>
  <form class="ad-inline-form" method="post" action="<?= e(st_url('admin/downloads/' . $slug)) ?>">
    <input type="hidden" name="csrf" value="<?= e($csrf) ?>">
    <input type="hidden" name="action" value="register">
    <input type="hidden" name="no_file" value="1">
    <input type="text" name="filename" placeholder="Nombre del archivo" required style="min-width:220px">
    <input type="number" name="size" placeholder="Tamaño (bytes)" required style="min-width:110px">
    <input type="text" name="sha256" placeholder="sha256 (64 hex)" required pattern="[a-fA-F0-9]{64}" style="min-width:280px">
    <select name="visibility">
      <option value="clave" selected>Alojado (con contraseña)</option>
      <option value="enlace">Solo enlace oficial</option>
    </select>
    <input type="text" name="license" placeholder="Licencia / nota">
    <input type="url" name="source_url" placeholder="URL oficial (si «solo enlace»)">
    <input type="text" name="version" placeholder="Versión (8.2)" style="min-width:80px">
    <input type="text" name="variant" placeholder="Variante (IDE…)" style="min-width:130px">
    <input type="text" name="year" placeholder="Año (2013)" style="min-width:70px">
    <button class="ad-btn ad-btn--primary" type="submit">Registrar metadata</button>
  </form>
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
      <option value="clave" selected>Alojado (con contraseña)</option>
      <option value="enlace">Solo enlace oficial (no se sube)</option>
    </select>
    <input type="text" name="license" placeholder="Licencia / nota">
    <input type="url" name="source_url" placeholder="URL oficial">
    <input type="text" name="version" placeholder="Versión (8.2)" style="min-width:80px">
    <input type="text" name="variant" placeholder="Variante (IDE…)" style="min-width:130px">
    <input type="text" name="year" placeholder="Año (2013)" style="min-width:70px">
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
      <option value="clave" selected>Alojado (con contraseña)</option>
      <option value="enlace">Solo enlace oficial</option>
    </select>
    <input type="text" name="license" placeholder="Licencia / nota">
    <input type="url" name="source_url" placeholder="URL oficial (si «solo enlace»)">
    <input type="text" name="version" placeholder="Versión (8.2)" style="min-width:80px">
    <input type="text" name="variant" placeholder="Variante (IDE…)" style="min-width:130px">
    <input type="text" name="year" placeholder="Año (2013)" style="min-width:70px">
    <button class="ad-btn ad-btn--primary ad-btn--sm" type="submit">Registrar</button>
  </form>
  <?php endforeach; ?>
</fieldset>
<?php else: ?>
<p class="ad-hint">No hay archivos sin registrar en <code>downloads/<?= e($slug) ?>/</code>.</p>
<?php endif; ?>