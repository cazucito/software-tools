<?php
if (!defined('ST_APP')) {
    exit;
}
/**
 * Catálogo completo agrupado por década, con filtros combinables (ADR-010).
 * Variables: $tools, $stats, $mode, $icons, $filters, $tagOptions, $catOptions, $decades.
 */
$groups = [];
foreach ($tools as $t) {
    $decade = (int) floor(((int) $t['year']) / 10) * 10;
    $groups[$decade][] = $t;
}
$hasFilter = !empty($filters['d']) || !empty($filters['c']) || !empty($filters['t']);
?>
<section class="st-catalog">
  <header class="st-catalog__head">
    <div class="st-kicker">EL ARCHIVO COMPLETO</div>
    <h1 class="st-catalog__title"><?= e($stats['count']) ?> herramientas · <?= e($stats['minYear']) ?> → <?= e($stats['maxYear']) ?></h1>
    <p class="st-catalog__sub">Ordenadas por década<?= $hasFilter ? ' · filtradas' : '' ?>. Cada una con su historia y sus relaciones.</p>
  </header>

  <nav class="st-filters" aria-label="Filtros">
    <div class="st-filters__row">
      <span class="st-filters__label">Década</span>
      <?php foreach ($decades as $d): ?>
      <a class="st-chip<?= ($filters['d'] ?? null) === $d ? ' is-on' : '' ?>"
         href="<?= e(st_url('tools', array_filter(['decada' => $d, 'categoria' => $filters['c'] ?? null, 'tag' => $filters['t'] ?? null], static fn ($v): bool => $v !== null && $v !== ''))) ?>">
        <?= e($d) ?>s
      </a>
      <?php endforeach; ?>
    </div>
    <div class="st-filters__row">
      <span class="st-filters__label">Categoría</span>
      <?php foreach ($catOptions as $cat): ?>
      <a class="st-chip<?= ($filters['c'] ?? '') === $cat ? ' is-on' : '' ?>"
         href="<?= e(st_url('tools', array_filter(['decada' => $filters['d'] ?? null, 'categoria' => $cat, 'tag' => $filters['t'] ?? null], static fn ($v): bool => $v !== null && $v !== ''))) ?>">
        <?= e($cat) ?>
      </a>
      <?php endforeach; ?>
    </div>
    <div class="st-filters__row">
      <span class="st-filters__label">Tag</span>
      <?php foreach ($tagOptions as $tag): ?>
      <a class="st-chip<?= ($filters['t'] ?? '') === $tag['name'] ? ' is-on' : '' ?>"
         href="<?= e(st_url('tools', array_filter(['decada' => $filters['d'] ?? null, 'categoria' => $filters['c'] ?? null, 'tag' => $tag['name']], static fn ($v): bool => $v !== null && $v !== ''))) ?>">
        <?= e($tag['name']) ?><span class="st-chip__n"><?= e($tag['count']) ?></span>
      </a>
      <?php endforeach; ?>
    </div>
    <?php if ($hasFilter): ?>
    <a class="st-chip st-chip--off" href="<?= e(st_url('tools')) ?>">✕ limpiar filtros</a>
    <?php endif; ?>
  </nav>

  <?php if (!$tools): ?>
  <p class="st-hint">Nada por aquí con esos filtros. Prueba a limpiarlos.</p>
  <?php endif; ?>

  <?php foreach ($groups as $decade => $rows): ?>
  <section class="st-decade">
    <h2 class="st-decade__title"><?= e($decade) ?>s</h2>
    <ol class="st-list">
      <?php foreach ($rows as $t): ?>
      <li class="st-list__item">
        <a class="st-list__link" href="<?= e(st_url('tools/' . $t['slug'], ['modo' => $mode])) ?>">
          <span class="st-list__art"><?= st_tool_art((string) $t['slug'], (string) $t['name'], (int) $t['year'], $icons) ?></span>
          <span class="st-list__year"><?= e($t['year']) ?></span>
          <span class="st-list__name"><?= e($t['name']) ?></span>
          <span class="st-list__cat"><?= e($t['category']) ?></span>
        </a>
      </li>
      <?php endforeach; ?>
    </ol>
  </section>
  <?php endforeach; ?>
</section>