<?php
if (!defined('ST_APP')) {
    exit;
}
/**
 * Layout común: head, cabecera (marca, selector de modalidad, búsqueda),
 * contenido y pie. Variables esperadas: $content, $mode, $modes, $route,
 * $params, $title, $description, $stDataJson (opcional), $modeScript (opcional).
 */
$modes       = $modes ?? [];
$params      = $params ?? [];
$title       = $title ?? 'software-tools';
$description = $description ?? '';
?><!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($title) ?></title>
<?php if ($description !== ''): ?>
<meta name="description" content="<?= e($description) ?>">
<?php endif; ?>
<?php if (st_config('noindex')): ?>
<meta name="robots" content="noindex">
<?php endif; ?>
<link rel="icon" href="data:,">
<?php if ($mode === 'cinta'): ?>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;700&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">
<?php elseif ($mode === 'linea'): ?>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300..600;1,9..144,300..600&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<?php else: ?>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:ital,wght@0,400;0,500;0,600;1,400&display=swap" rel="stylesheet">
<?php endif; ?>
<link rel="stylesheet" href="<?= e(st_asset('libs/lenis.css')) ?>">
<link rel="stylesheet" href="<?= e(st_asset('css/base.css')) ?>">
<link rel="stylesheet" href="<?= e(st_asset('css/mode-' . $mode . '.css')) ?>">
<script>
/* Preferencia de modalidad guardada: aplicarla si la URL no la trae. */
(function () {
  try {
    var modes = <?= json_encode(array_values($modes)) ?>;
    var stored = localStorage.getItem('st.modo');
    var url = new URL(location.href);
    if (stored && modes.indexOf(stored) >= 0 && !url.searchParams.has('modo') && stored !== <?= json_encode($mode) ?>) {
      url.searchParams.set('modo', stored);
      location.replace(url.toString());
    }
  } catch (err) { /* sin localStorage: seguir con la modalidad por defecto */ }
})();
</script>
</head>
<body class="mode-<?= e($mode) ?>" data-mode="<?= e($mode) ?>" data-base="<?= e(ST_BASE) ?>" data-url-style="<?= e(st_config('url_style')) ?>">
<a class="st-skip" href="#contenido">Saltar al contenido</a>

<header class="st-header">
  <div class="st-header__inner">
    <a class="st-header__brand" href="<?= e(st_url('')) ?>">software-tools</a>
    <nav class="st-header__modes" aria-label="Modalidad de presentación">
      <?php $modeLabels = st_config('mode_labels'); ?>
      <?php foreach ($modes as $m): ?>
      <a class="st-modem<?= $m === $mode ? ' is-active' : '' ?>"
         href="<?= e(st_url($route, array_merge($params, ['modo' => $m]))) ?>"
         title="Modalidad <?= e($modeLabels[$m] ?? ucfirst($m)) ?>"><?= e($modeLabels[$m] ?? ucfirst($m)) ?></a>
      <?php endforeach; ?>
    </nav>
    <div class="st-header__right">
      <a class="st-header__link" href="<?= e(st_url('tools', ['modo' => $mode])) ?>">Catálogo</a>
      <a class="st-header__link st-search-link" href="<?= e(st_url('search', ['modo' => $mode])) ?>">Buscar</a>
      <form class="st-search" action="<?= e(st_url('search')) ?>" method="get" role="search">
        <?php if (st_config('url_style') !== 'pretty'): ?>
        <input type="hidden" name="p" value="search">
        <?php endif; ?>
        <input type="hidden" name="modo" value="<?= e($mode) ?>">
        <input class="st-search__input" type="search" name="q" placeholder="Buscar…"
               autocapitalize="off" autocomplete="off" spellcheck="false" aria-label="Buscar en el archivo">
      </form>
    </div>
  </div>
</header>

<main id="contenido">
<?= $content ?>
</main>

<footer class="st-footer">
  <span>prototipo v3 · fase 4 — modalidad «<?= e($mode) ?>» · datos reales del archivo · <?= e(st_config('site_tagline')) ?></span>
</footer>

<?php if (!empty($stDataJson)): ?>
<script>window.ST_DATA = <?= $stDataJson ?>;</script>
<?php endif; ?>
<script src="<?= e(st_asset('js/app.js')) ?>"></script>
<?php if (!empty($modeScript)): ?>
<script src="<?= e(st_asset('libs/gsap.min.js')) ?>"></script>
<script src="<?= e(st_asset('libs/ScrollTrigger.min.js')) ?>"></script>
<script src="<?= e(st_asset('libs/lenis.min.js')) ?>"></script>
<script src="<?= e(st_asset('js/modes/' . $modeScript . '.js')) ?>"></script>
<?php endif; ?>
</body>
</html>
