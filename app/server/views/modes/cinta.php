<?php
if (!defined('ST_APP')) {
    exit;
}
/**
 * Modalidad «Cinta» (A): una cinta de film recorre los años con el scroll.
 * $spotlight: herramienta destacada (Eudora) con tags y sucesora.
 */
$spotYears = (int) $spotlight['year'] . ' — ' . ($spotlight['used_until'] ? (int) $spotlight['used_until'] : 'hoy');
$sentences = preg_split('/(?<=[.!?])\s+/u', trim((string) $spotlight['context'])) ?: [];
$spotLine1 = $sentences[0] ?? '';
$spotLine2 = trim(implode(' ', array_slice($sentences, 1)));
?>
<div class="grain"></div>

<section id="hero">
  <div class="eyebrow">CINTA Nº 1 · <span data-num="minYear"></span> → HOY</div>
  <h1>Una vida<br>en <em>una cinta</em>.</h1>
  <p class="sub"><span data-num="count"></span> herramientas. <span data-num="yearsLife"></span> años de oficio.
  Fotograma a fotograma: el software que me acompañó desde <span data-num="minYear"></span>.</p>
  <div class="hero-strip"><div class="strip-in" id="heroStrip"></div></div>
  <div class="scroll-cue">▼ DESLIZA PARA REPRODUCIR</div>
</section>

<section id="tape-scene">
  <div class="scene-inner">
    <div class="scene-head">REPRODUCIENDO <b>LA CINTA</b><span class="mhide"> · <span data-num="minYear"></span> → HOY</span></div>
    <div class="tape-band"><div class="tape" id="tape"></div></div>
    <div class="playhead"></div>
    <div class="readout">
      <span id="yearNow">1991</span>
      <span class="readout-label">AÑO EN PANTALLA</span>
    </div>
    <div class="scale">
      <div class="scale-track"><div class="scale-fill" id="scaleFill"></div></div>
      <div class="scale-ends"><span><span data-num="minYear"></span></span><span><span data-num="maxYear"></span>+</span></div>
    </div>
  </div>
</section>

<section id="spotlight">
  <div class="spot-wrap">
    <div class="spot-frame">
      <div class="f-year"><?= e($spotYears) ?></div>
      <div class="f-name"><?= e($spotlight['name']) ?></div>
      <div class="f-cat"><?= e($spotlight['category']) ?></div>
    </div>
    <div class="spot-card">
      <div class="spot-kicker rev">FOTOGRAMA DESTACADO</div>
      <h2 class="rev"><?= e($spotlight['name']) ?></h2>
      <div class="spot-meta rev"><?= e($spotYears) ?> · <?= e(strtoupper((string) $spotlight['category'])) ?> · <?= e(count($spotlight['tags'])) ?> ETIQUETAS</div>
      <p class="spot-line1 rev"><?= e($spotLine1) ?></p>
      <?php if ($spotLine2 !== ''): ?><p class="spot-line2 rev"><?= e($spotLine2) ?></p><?php endif; ?>
      <div class="spot-tags rev"><?php foreach ($spotlight['tags'] as $tag): ?><span><?= e($tag) ?></span><?php endforeach; ?></div>
      <?php if (!empty($spotlight['successor'])): ?>
      <div class="spot-succ rev">→ SUCESORA: <?= e($spotlight['successor']) ?></div>
      <?php endif; ?>
      <div class="rev"><a class="ed-link" href="<?= e(st_url('tools/' . $spotlight['slug'], ['modo' => 'cinta'])) ?>">Ver la ficha completa →</a></div>
    </div>
  </div>
</section>

<section id="rush">
  <div class="rush-row" id="rush1"></div>
  <div class="rush-mid">· · · REBOBINA · · ·</div>
  <div class="rush-row" id="rush2"></div>
  <p class="rush-line">…y después de <b><span data-num="count"></span> herramientas</b>, la cinta sigue rodando.</p>
  <div class="cta-wrap"><a class="cta" href="<?= e(st_url('tools', ['modo' => 'cinta'])) ?>">▶ EXPLORAR EL CATÁLOGO COMPLETO</a></div>
</section>
