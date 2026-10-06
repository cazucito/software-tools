<?php
if (!defined('ST_APP')) {
    exit;
}
/**
 * Home: buscador protagonista + timeline de la modalidad activa.
 * Variables: $mode, $stats, $tools, $spotlight, $featured, $current, $icons.
 */
?>
<section class="st-hero-search">
  <form class="st-search" action="<?= e(st_url('search')) ?>" autocomplete="off" role="search">
    <input class="st-search__input" type="search" name="q"
           placeholder="<?= e(st_t('search.placeholder')) ?>"
           aria-label="<?= e(st_t('search.placeholder')) ?>">
    <button class="st-search__btn" type="submit"><?= e(st_t('search.submit')) ?></button>
  </form>
  <div class="st-suggest" role="listbox" hidden></div>
</section>

<?php
require st_config('views_dir') . '/modes/' . $mode . '.php';

if (!empty($tools)): ?>
<noscript>
  <section class="st-noscript">
    <h2>Archivo completo (<?= e(count($tools)) ?> herramientas)</h2>
    <ol>
      <?php foreach ($tools as $t): ?>
      <li><a href="<?= e(st_url('tools/' . $t['slug'], ['modo' => $mode])) ?>"><?= e($t['name']) ?></a> — <?= e($t['year']) ?></li>
      <?php endforeach; ?>
    </ol>
  </section>
</noscript>
<?php endif;
