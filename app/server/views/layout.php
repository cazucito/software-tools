<?php
if (!defined('ST_APP')) {
    exit;
}
/**
 * Layout común: head (SEO: canonical, Open Graph, hreflang, JSON-LD),
 * cabecera (marca, enlaces, búsqueda), contenido y pie.
 * Home = mapa (ADR-012): body.mode-mapa, cabecera con la búsqueda del propio
 * mapa; el resto de páginas usa body.mode-content y el formulario de búsqueda.
 * Variables esperadas: $content, $route, $title, $description, $canonical,
 * $ogType, $jsonLd, $stDataJson, $isMapa, $lang.
 */
$title       = $title ?? 'software-tools';
$description = $description ?? '';
$canonical   = $canonical ?? '';
$ogType      = $ogType ?? 'website';
$lang        = $lang ?? 'es';
$isMapa      = !empty($isMapa);
?><!doctype html>
<html lang="<?= e($lang) ?>">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($title) ?></title>
<?php if ($description !== ''): ?>
<meta name="description" content="<?= e($description) ?>">
<?php endif; ?>
<?php if ($canonical !== ''): ?>
<link rel="canonical" href="<?= e($canonical) ?>">
<link rel="alternate" hreflang="<?= e($lang) ?>" href="<?= e($canonical) ?>">
<link rel="alternate" hreflang="x-default" href="<?= e($canonical) ?>">
<meta property="og:title" content="<?= e($title) ?>">
<?php if ($description !== ''): ?>
<meta property="og:description" content="<?= e($description) ?>">
<?php endif; ?>
<meta property="og:url" content="<?= e($canonical) ?>">
<meta property="og:type" content="<?= e($ogType) ?>">
<meta property="og:site_name" content="software-tools">
<meta property="og:locale" content="es_MX">
<meta name="twitter:card" content="summary">
<?php endif; ?>
<?php if (st_config('noindex')): ?>
<meta name="robots" content="noindex">
<?php endif; ?>
<?php if (!empty($jsonLd)): ?>
<script type="application/ld+json"><?= $jsonLd ?></script>
<?php endif; ?>
<link rel="icon" href="data:,">
<?php if ($isMapa): ?>
<link rel="stylesheet" href="<?= e(st_asset('css/base.css')) ?>">
<link rel="stylesheet" href="<?= e(st_asset('css/mapa.css')) ?>">
<noscript><style>body.mode-mapa{overflow:auto}#map,.mapa-ui{display:none!important}</style></noscript>
<?php else: ?>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300..600;1,9..144,300..600&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= e(st_asset('css/base.css')) ?>">
<link rel="stylesheet" href="<?= e(st_asset('css/content.css')) ?>">
<link rel="stylesheet" href="<?= e(st_asset('css/scale.css')) ?>">
<?php endif; ?>
</head>
<body class="<?= $isMapa ? 'mode-mapa' : 'mode-content' ?>" data-base="<?= e(ST_BASE) ?>" data-url-style="<?= e(st_config('url_style')) ?>">
<a class="st-skip" href="#contenido">Saltar al contenido</a>

<header class="st-header">
  <div class="st-header__inner">
    <a class="st-header__brand" href="<?= e(st_url('')) ?>">software-tools</a>
    <div class="st-header__right">
<?php if ($isMapa): ?>
      <div class="mapa-ui">
        <button type="button" id="collapse">contraer todo</button>
        <div class="hd__search">
          <input id="q" type="search" placeholder="Buscar herramienta, tag, contexto…" autocomplete="off" aria-label="Buscar en el mapa">
          <div class="dd" id="dd" role="listbox"></div>
        </div>
      </div>
      <a class="st-header__link" href="<?= e(st_url('tools')) ?>"><?= e(st_t('header.catalog')) ?></a>
      <a class="st-header__link" href="<?= e(st_url('search')) ?>"><?= e(st_t('header.search')) ?></a>
<?php else: ?>
      <a class="st-header__link" href="<?= e(st_url('tools')) ?>"><?= e(st_t('header.catalog')) ?></a>
      <a class="st-header__link st-search-link" href="<?= e(st_url('search')) ?>"><?= e(st_t('header.search')) ?></a>
      <form class="st-search" action="<?= e(st_url('search')) ?>" method="get" role="search">
        <?php if (st_config('url_style') !== 'pretty'): ?>
        <input type="hidden" name="p" value="search">
        <?php endif; ?>
        <input class="st-search__input" type="search" name="q" placeholder="<?= e(st_t('header.search_placeholder')) ?>"
               autocapitalize="off" autocomplete="off" spellcheck="false" aria-label="<?= e(st_t('header.search')) ?>">
      </form>
<?php endif; ?>
    </div>
  </div>
</header>

<main id="contenido">
<?= $content ?>
</main>

<footer class="st-footer">
  <span><?= e(st_t('footer.prototype')) ?></span>
</footer>

<?php if (!empty($stDataJson)): ?>
<script>window.ST_DATA = <?= $stDataJson ?>;</script>
<?php endif; ?>
<?php if ($isMapa): ?>
<script src="<?= e(st_asset('js/mapa.js')) ?>"></script>
<?php else: ?>
<script src="<?= e(st_asset('js/app.js')) ?>"></script>
<?php endif; ?>
</body>
</html>
