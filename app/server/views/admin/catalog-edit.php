<?php
if (!defined('ST_APP')) {
    exit;
}
/**
 * Edición de una ficha. Variables: $tool (con tags_csv, related_csv),
 * $dirty, $assets, $csrf.
 */
$slug = (string) $tool['slug'];
$catOptions = ['editor', 'lenguaje', 'database', 'infra', 'framework', 'tool', 'os', 'other'];
?>
<h1 class="ad-title">Editar · <?= e($tool['name']) ?> <span class="ad-badge ad-badge--muted"><?= e($slug) ?></span></h1>
<?php if ($dirty): ?>
<p class="ad-flash ad-flash--error">Tiene cambios guardados que todavía no se exportaron al repo.</p>
<?php endif; ?>
<p class="ad-note">
  <a href="<?= e(st_url('admin/export/' . $slug)) ?>">⬇ Descargar Markdown de esta ficha</a>
  · <a href="<?= e(st_url('tools/' . $slug)) ?>" target="_blank" rel="noopener">Ver en el sitio ↗</a>
</p>

<form class="ad-form" method="post" action="<?= e(st_url('admin/catalog/' . $slug)) ?>">
  <input type="hidden" name="csrf" value="<?= e($csrf) ?>">
  <input type="hidden" name="action" value="save">

  <fieldset class="ad-fieldset">
    <legend>Básicos</legend>
    <div class="ad-row">
      <div class="ad-field">
        <label for="name">Nombre</label>
        <input id="name" type="text" name="name" required value="<?= e((string) $tool['name']) ?>">
      </div>
      <div class="ad-field">
        <label for="year">Año de primer uso</label>
        <input id="year" type="number" name="year" min="1970" max="2100" required value="<?= e((int) $tool['year']) ?>">
      </div>
    </div>
    <div class="ad-row">
      <div class="ad-field">
        <label for="used_until">Usada hasta (vacío = sin fecha de fin)</label>
        <input id="used_until" type="number" name="used_until" min="1970" max="2100" placeholder="1998"
               value="<?= $tool['used_until'] !== null ? e((int) $tool['used_until']) : '' ?>">
      </div>
      <div class="ad-field">
        <label for="category">Categoría</label>
        <select id="category" name="category">
          <?php foreach ($catOptions as $cat): ?>
          <option value="<?= e($cat) ?>"<?= $tool['category'] === $cat ? ' selected' : '' ?>><?= e($cat) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>
    <div class="ad-field">
      <label for="tags">Etiquetas (separadas por coma)</label>
      <input id="tags" type="text" name="tags" value="<?= e((string) $tool['tags_csv']) ?>">
    </div>
  </fieldset>

  <fieldset class="ad-fieldset">
    <legend>Textos</legend>
    <div class="ad-field">
      <label for="context">Contexto (destacado en listados y búsquedas)</label>
      <textarea id="context" name="context" rows="3"><?= e((string) $tool['context']) ?></textarea>
    </div>
    <div class="ad-field">
      <label for="body">Cuerpo (Markdown)</label>
      <textarea id="body" name="body" rows="18"><?= e((string) $tool['body']) ?></textarea>
    </div>
  </fieldset>

  <fieldset class="ad-fieldset">
    <legend>Relaciones</legend>
    <div class="ad-row">
      <div class="ad-field">
        <label for="successor">Sucesora (texto, ej. «Microsoft Outlook»)</label>
        <input id="successor" type="text" name="successor" value="<?= e((string) ($tool['successor'] ?? '')) ?>">
      </div>
      <div class="ad-field">
        <label for="successor_slug">Slug de la sucesora</label>
        <input id="successor_slug" type="text" name="successor_slug" value="<?= e((string) ($tool['successor_slug'] ?? '')) ?>">
      </div>
    </div>
    <div class="ad-field">
      <label for="related">Relacionadas (slugs separados por coma)</label>
      <input id="related" type="text" name="related" value="<?= e((string) $tool['related_csv']) ?>">
    </div>
  </fieldset>

  <fieldset class="ad-fieldset">
    <legend>Medios</legend>
    <div class="ad-field">
      <label for="image">Ruta de imagen (heredada de v2, opcional)</label>
      <input id="image" type="text" name="image" value="<?= e((string) ($tool['image'] ?? '')) ?>">
    </div>
  </fieldset>

  <label class="ad-check">
    <input type="checkbox" name="published" value="1"<?= $tool['published'] ? ' checked' : '' ?>>
    Publicada (visible en el sitio)
  </label>

  <div><button class="ad-btn ad-btn--primary" type="submit">Guardar ficha</button></div>
</form>

<fieldset class="ad-fieldset" style="margin-top:1.6rem">
  <legend>🖼 Assets de la herramienta (ícono / logo / cover)</legend>
  <p class="ad-note">Solo imágenes reales (png, jpg, webp, svg ≤ <?= e(St\Downloads::humanSize((int) st_config('downloads')['max_upload'])) ?>). Sin imagen: la ficha usa el monograma tipográfico.</p>
  <?php foreach (['icon' => 'Ícono', 'logo' => 'Logo', 'cover' => 'Portada'] as $kind => $label): ?>
  <form class="ad-inline-form" method="post" enctype="multipart/form-data"
        action="<?= e(st_url('admin/catalog/' . $slug)) ?>">
    <input type="hidden" name="csrf" value="<?= e($csrf) ?>">
    <input type="hidden" name="action" value="asset_upload">
    <input type="hidden" name="kind" value="<?= e($kind) ?>">
    <strong class="ad-file"><?= e($label) ?></strong>
    <?php if (!empty($assets[$kind])): ?>
    <span class="ad-note">✓ <?= e($assets[$kind]['file']) ?></span>
    <?php else: ?><span class="ad-note">— sin definir —</span><?php endif; ?>
    <input type="file" name="asset" accept="image/png,image/jpeg,image/webp,image/svg+xml" required>
    <input type="text" name="source" placeholder="Origen (ej. captura propia)" style="min-width:200px">
    <input type="text" name="license" placeholder="Licencia / nota">
    <button class="ad-btn ad-btn--sm" type="submit">Subir</button>
  </form>
  <?php endforeach; ?>
</fieldset>