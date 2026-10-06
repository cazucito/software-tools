<?php
if (!defined('ST_APP')) {
    exit;
}
/**
 * Modalidad «Línea» (B, por defecto): una línea se dibuja de 1991 a hoy;
 * cada herramienta es una estación. $featured: Turbo C (estación destacada).
 * $current: herramientas «siguen en uso» (curaduría conocida).
 */
$featYears = (int) $featured['year'] . ' — ' . ($featured['used_until'] ? (int) $featured['used_until'] : 'hoy');
?>
<section id="hero">
  <div class="eyebrow">SOFTWARE-TOOLS · ARCHIVO PERSONAL</div>
  <h1>Una línea de <em><span data-num="spann"></span> años</em>.</h1>
  <p class="sub"><span data-num="count"></span> herramientas, de <span data-num="minYear"></span> a hoy.
  Cada una, una estación en el camino.</p>
  <div class="cue"><span class="thread"></span>DESLIZA</div>
</section>

<section id="camino">
  <div class="camino-head">EL CAMINO · <span data-num="minYear"></span> → HOY</div>
  <!-- la línea (svg) y las estaciones se construyen con datos reales -->
</section>

<section id="estacion">
  <article class="editorial">
    <div class="ed-kicker rev">ESTACIÓN <?= e($featured['year']) ?> · FOTOGRAMA DEL CAMINO</div>
    <h2 class="ed-title rev"><?= e($featured['name']) ?></h2>
    <blockquote class="ed-quote rev">«<?= e($featured['context']) ?>»</blockquote>
    <div class="ed-meta rev">
      <span><?= e($featYears) ?></span><span><?= e($featured['category']) ?></span>
      <span class="chipline"><?= e(implode(' · ', $featured['tags'])) ?></span>
    </div>
    <a class="ed-link rev" href="<?= e(st_url('tools/' . $featured['slug'], ['modo' => 'linea'])) ?>">Ver la ficha completa →</a>
  </article>
</section>

<section id="hoy">
  <div class="hoy-kicker rev">LA LÍNEA LLEGA A HOY</div>
  <h2 class="rev">Y el camino continúa.</h2>
  <div class="constel">
    <?php foreach ($current as $c): ?>
    <a class="node rev" href="<?= e(st_url('tools/' . $c['slug'], ['modo' => 'linea'])) ?>">
      <span class="dot"></span><span class="n-name"><?= e($c['name']) ?></span><span class="n-year"><?= e($c['year']) ?></span>
    </a>
    <?php endforeach; ?>
  </div>
  <p class="hoy-line rev">Estas <?= e(count($current)) ?> siguen en uso. La línea no se detiene.</p>
  <div class="rev"><a class="cta" href="<?= e(st_url('tools', ['modo' => 'linea'])) ?>">Recorrer la línea completa</a></div>
</section>
