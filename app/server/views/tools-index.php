<?php
if (!defined('ST_APP')) {
    exit;
}
/**
 * Catálogo completo agrupado por década. Variables: $tools (con tags), $mode, $stats.
 */
$groups = [];
foreach ($tools as $t) {
    $decade = (int) floor(((int) $t['year']) / 10) * 10;
    $groups[$decade][] = $t;
}
?>
<section class="st-catalog">
  <header class="st-catalog__head">
    <div class="st-kicker">EL ARCHIVO COMPLETO</div>
    <h1 class="st-catalog__title"><?= e($stats['count']) ?> herramientas · <?= e($stats['minYear']) ?> → <?= e($stats['maxYear']) ?></h1>
    <p class="st-catalog__sub">Ordenadas por década. Cada una con su historia y sus relaciones.</p>
  </header>

  <?php foreach ($groups as $decade => $rows): ?>
  <section class="st-decade">
    <h2 class="st-decade__title"><?= e($decade) ?>s</h2>
    <ol class="st-list">
      <?php foreach ($rows as $t): ?>
      <li class="st-list__item">
        <a class="st-list__link" href="<?= e(st_url('tools/' . $t['slug'], ['modo' => $mode])) ?>">
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
