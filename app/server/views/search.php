<?php
if (!defined('ST_APP')) {
    exit;
}
/**
 * Búsqueda (FTS5). Variables: $q (consulta), $result (rows + total), $mode.
 * Los fragmentos llegan con <mark>…</mark> insertados por SQLite FTS sobre
 * NUESTRO propio contenido: se imprimen tal cual (no hay HTML de terceros).
 */
?>
<section class="st-searchpage">
  <h1 class="st-searchpage__title">Buscar en el archivo</h1>

  <form class="st-bigsearch" action="<?= e(st_url('search')) ?>" method="get" role="search">
    <?php if (st_config('url_style') !== 'pretty'): ?>
    <input type="hidden" name="p" value="search">
    <?php endif; ?>
    <input type="hidden" name="modo" value="<?= e($mode) ?>">
    <input class="st-bigsearch__input" type="search" name="q" value="<?= e($q) ?>"
           placeholder="contenedores, tesis, oracle…" autocapitalize="off" autocomplete="off" spellcheck="false" autofocus>
    <button class="st-bigsearch__btn" type="submit">Buscar</button>
  </form>

  <?php if ($q === ''): ?>
    <p class="st-hint">Busca por nombre, contexto, cuerpo o etiquetas. Prueba «contenedores», «tesis» u «oracle».</p>
  <?php elseif ($result['total'] === 0): ?>
    <p class="st-hint">Sin resultados para «<?= e($q) ?>».
      <a href="<?= e(st_url('tools', ['modo' => $mode])) ?>">Explora el catálogo completo →</a></p>
  <?php else: ?>
    <p class="st-count"><?= e($result['total']) ?> resultado<?= $result['total'] === 1 ? '' : 's' ?> para «<?= e($q) ?>»</p>
    <ol class="st-results">
      <?php foreach ($result['rows'] as $row): ?>
      <li class="st-result">
        <a class="st-result__link" href="<?= e(st_url('tools/' . $row['slug'], ['modo' => $mode])) ?>">
          <span class="st-result__year"><?= e($row['year']) ?></span>
          <span class="st-result__name"><?= $row['name_hl'] ?></span>
          <span class="st-result__cat"><?= e($row['category']) ?></span>
        </a>
        <?php if (!empty($row['snip'])): ?>
        <p class="st-result__snip"><?= $row['snip'] ?></p>
        <?php elseif (!empty($row['context'])): ?>
        <p class="st-result__snip"><?= e($row['context']) ?></p>
        <?php endif; ?>
      </li>
      <?php endforeach; ?>
    </ol>
  <?php endif; ?>
</section>
