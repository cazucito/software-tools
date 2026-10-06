<?php
if (!defined('ST_APP')) {
    exit;
}
/**
 * Ficha de herramienta. Variables: $tool, $related (herramientas),
 * $neighbours (prev/next), $succTool (herramienta sucesora o null),
 * $bodyHtml (cuerpo Markdown ya renderizado), $mode.
 */
$used  = $tool['used_until'] !== null ? (int) $tool['used_until'] : null;
$years = $used !== null
    ? ((int) $tool['year'] . ' — ' . $used)
    : ('desde ' . (int) $tool['year']);
$decade = ((int) floor(((int) $tool['year']) / 10) * 10) . 's';
$succName = $succTool ? $succTool['name'] : $tool['successor'];
?>
<article class="st-tool">
  <nav class="st-crumb" aria-label="Migas">
    <a href="<?= e(st_url('', ['modo' => $mode])) ?>">← el archivo</a>
    <span class="st-crumb__sep">/</span>
    <a href="<?= e(st_url('tools', ['modo' => $mode])) ?>">catálogo</a>
  </nav>

  <header class="st-tool__head">
    <div class="st-kicker"><?= e(strtoupper((string) $tool['category'])) ?> · <?= e($decade) ?> · <?= e($years) ?></div>
    <h1 class="st-tool__name"><?= e($tool['name']) ?></h1>
    <?php if ($tool['tags']): ?>
    <div class="st-tags">
      <?php foreach ($tool['tags'] as $tag): ?><span class="st-tag"><?= e($tag) ?></span><?php endforeach; ?>
    </div>
    <?php endif; ?>
  </header>

  <p class="st-lead"><?= e($tool['context']) ?></p>

  <div class="st-body"><?= $bodyHtml ?></div>

  <?php if ($succName !== null && $succName !== ''): ?>
  <aside class="st-succ">
    <span class="st-succ__label">Después vino</span>
    <?php if ($succTool): ?>
    <a class="st-succ__name" href="<?= e(st_url('tools/' . $succTool['slug'], ['modo' => $mode])) ?>"><?= e($succName) ?></a>
    <?php else: ?>
    <strong class="st-succ__name"><?= e($succName) ?></strong>
    <?php endif; ?>
  </aside>
  <?php endif; ?>

  <?php if ($related): ?>
  <section class="st-related">
    <h2 class="st-section-title">Herramientas relacionadas</h2>
    <div class="st-tags">
      <?php foreach ($related as $rel): ?>
      <a class="st-tag st-tag--link" href="<?= e(st_url('tools/' . $rel['slug'], ['modo' => $mode])) ?>"><?= e($rel['name']) ?> <span class="st-tag__year"><?= e($rel['year']) ?></span></a>
      <?php endforeach; ?>
    </div>
  </section>
  <?php endif; ?>

  <nav class="st-neighbours" aria-label="Cronología">
    <?php if ($neighbours['prev']): ?>
    <a class="st-neigh st-neigh--prev" href="<?= e(st_url('tools/' . $neighbours['prev']['slug'], ['modo' => $mode])) ?>">
      <span class="st-neigh__dir">← anterior</span>
      <span class="st-neigh__name"><?= e($neighbours['prev']['name']) ?></span>
      <span class="st-neigh__year"><?= e($neighbours['prev']['year']) ?></span>
    </a>
    <?php else: ?><span></span><?php endif; ?>
    <?php if ($neighbours['next']): ?>
    <a class="st-neigh st-neigh--next" href="<?= e(st_url('tools/' . $neighbours['next']['slug'], ['modo' => $mode])) ?>">
      <span class="st-neigh__dir">siguiente →</span>
      <span class="st-neigh__name"><?= e($neighbours['next']['name']) ?></span>
      <span class="st-neigh__year"><?= e($neighbours['next']['year']) ?></span>
    </a>
    <?php endif; ?>
  </nav>
</article>
