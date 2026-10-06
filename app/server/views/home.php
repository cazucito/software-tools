<?php
if (!defined('ST_APP')) {
    exit;
}
/**
 * Home: timeline de la modalidad activa. El parcial de modo recibe
 * todas las variables del controlador ($mode, $spotlight, $current, etc.).
 */
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
