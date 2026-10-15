<?php
if (!defined('ST_APP')) {
    exit;
}
/**
 * Home: el archivo como mapa mental (dendrita, ADR-012). El motor vive en
 * assets/js/mapa.js y lee window.ST_DATA (Catalog::mapData()). La búsqueda del
 * mapa está en la cabecera (layout). El <noscript> lista las herramientas
 * completas (a11y + SEO). Variables: $tools.
 */
?>
<div id="st-mapa" class="mapa">
  <svg id="map" role="application" aria-label="Mapa mental del archivo (dendrita: categorías → familias → herramientas)"></svg>

  <aside class="panel" id="panel" aria-hidden="true">
    <button class="panel__close" id="pclose" type="button" aria-label="Cerrar">×</button>
    <div class="panel__inner" id="pinner"></div>
  </aside>

  <div class="hint mapa-ui" id="hint">clic en categoría → familias · clic en familia → herramientas · clic en herramienta → ficha · hilos = relacionadas</div>
</div>

<?php if (!empty($tools)): ?>
<noscript>
  <section class="st-noscript">
    <h2>Archivo completo (<?= e(count($tools)) ?> herramientas)</h2>
    <ol>
      <?php foreach ($tools as $t): ?>
      <li><a href="<?= e(st_url('tools/' . $t['slug'])) ?>"><?= e($t['name']) ?></a> — <?= e($t['year']) ?></li>
      <?php endforeach; ?>
    </ol>
  </section>
</noscript>
<?php endif; ?>
