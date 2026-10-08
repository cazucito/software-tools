<?php
if (!defined('ST_APP')) {
    exit;
}
/**
 * Ficha de herramienta. Variables: $tool, $related, $neighbours, $succTool,
 * $bodyHtml, $notice, $dloads, $hasDlKey, $commentsEnabled, $commentsList,
 * $commentToken, $assets, $mode.
 */
$used  = $tool['used_until'] !== null ? (int) $tool['used_until'] : null;
$years = $used !== null
    ? st_t('tool.range', ['from' => (int) $tool['year'], 'to' => $used])
    : st_t('tool.since', ['year' => (int) $tool['year']]);
$decade = ((int) floor(((int) $tool['year']) / 10) * 10) . 's';
$succName = $succTool ? $succTool['name'] : $tool['successor'];
$locked = $dlHasPass && !$dlUnlocked; // todo archivo alojado: contraseña general
$maxLen = (int) st_config('comments')['max_len'];
?>
<article class="st-tool">
  <nav class="st-crumb" aria-label="Migas">
    <a href="<?= e(st_url('', ['modo' => $mode])) ?>">← <?= e(st_t('common.home')) ?></a>
    <span class="st-crumb__sep">/</span>
    <a href="<?= e(st_url('tools', ['modo' => $mode])) ?>"><?= e(st_t('header.catalog')) ?></a>
  </nav>

  <?php if (!empty($notice)): ?>
  <div class="st-notice st-notice--<?= e($notice['type']) ?>"><?= e($notice['text']) ?></div>
  <?php endif; ?>

  <header class="st-tool__head">
    <div class="st-tool__icon" aria-hidden="true">
      <?= st_tool_art((string) $tool['slug'], (string) $tool['name'], (int) $tool['year'], $icons) ?>
    </div>
    <div class="st-tool__headtext">
      <div class="st-kicker"><?= e(strtoupper((string) $tool['category'])) ?> · <?= e($decade) ?> · <?= e($years) ?></div>
      <h1 class="st-tool__name"><?= e($tool['name']) ?></h1>
      <?php if ($tool['tags']): ?>
      <div class="st-tags">
        <?php foreach ($tool['tags'] as $tag): ?><span class="st-tag"><?= e($tag) ?></span><?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>
  </header>

  <p class="st-lead"><?= e($tool['context']) ?></p>

  <div class="st-body"><?= $bodyHtml ?></div>

  <?php if ($succName !== null && $succName !== ''): ?>
  <aside class="st-succ">
    <span class="st-succ__label"><?= e(st_t('tool.successor')) ?></span>
    <?php if ($succTool): ?>
    <a class="st-succ__name" href="<?= e(st_url('tools/' . $succTool['slug'], ['modo' => $mode])) ?>"><?= e($succName) ?></a>
    <?php else: ?>
    <strong class="st-succ__name"><?= e($succName) ?></strong>
    <?php endif; ?>
  </aside>
  <?php endif; ?>

  <?php if ($related): ?>
  <section class="st-related">
    <h2 class="st-section-title"><?= e(st_t('tool.related')) ?></h2>
    <div class="st-tags">
      <?php foreach ($related as $rel): ?>
      <a class="st-tag st-tag--link" href="<?= e(st_url('tools/' . $rel['slug'], ['modo' => $mode])) ?>"><?= e($rel['name']) ?> <span class="st-tag__year"><?= e($rel['year']) ?></span></a>
      <?php endforeach; ?>
    </div>
  </section>
  <?php endif; ?>

  <?php if (!empty($sameCat)): ?>
  <section class="st-related">
    <h2 class="st-section-title"><?= e(st_t('tool.same_category', ['category' => (string) $tool['category']])) ?></h2>
    <div class="st-tags">
      <?php foreach ($sameCat as $rel): ?>
      <a class="st-tag st-tag--link" href="<?= e(st_url('tools/' . $rel['slug'], ['modo' => $mode])) ?>"><?= e($rel['name']) ?> <span class="st-tag__year"><?= e($rel['year']) ?></span></a>
      <?php endforeach; ?>
    </div>
  </section>
  <?php endif; ?>

  <?php if ($dloads): ?>
  <section class="st-downloads" id="descargas">
    <h2 class="st-section-title"><?= e(st_t('downloads.title')) ?></h2>

    <?php
    // Reparto: referencias oficiales (públicas) vs archivos propios (privados).
    $refsAll = [];
    $filesAll = [];
    foreach ($dloads as $f) {
        if ($f['visibility'] === 'enlace' && !empty($f['source_url'])) {
            $refsAll[] = $f;
        } else {
            $filesAll[] = $f;
        }
    }
    // Orden de las referencias: versión descendente (sin versión al final).
    $verSort = static function (string $v): float {
        if (preg_match('/^(\d+)(?:\.(\d+))?(?:\.(\d+))?$/', $v, $m)) {
            return (float) ($m[1] . '.' . str_pad($m[2] ?? '0', 2, '0') . '.' . str_pad($m[3] ?? '0', 2, '0'));
        }
        return -1.0;
    };
    usort($refsAll, static function (array $a, array $b) use ($verSort): int {
        $va = (string) ($a['version'] ?? '');
        $vb = (string) ($b['version'] ?? '');
        if (($va === '') !== ($vb === '')) {
            return $va === '' ? 1 : -1;
        }
        $c = $verSort($vb) <=> $verSort($va);
        if ($c !== 0) {
            return $c;
        }
        $ya = (int) ($a['year'] ?? 0);
        $yb = (int) ($b['year'] ?? 0);
        if ($ya !== $yb) {
            return $yb <=> $ya;
        }
        return strtolower((string) $a['filename']) <=> strtolower((string) $b['filename']);
    });
    ?>
    <?php if ($refsAll): ?>
    <div class="st-dls-official" id="sitio-oficial">
      <span class="st-dls__sub">🌐 <?= e(st_t('downloads.official_title')) ?></span>
      <p class="st-dlref__hint"><?= e(st_t('downloads.official_note')) ?></p>
      <?php foreach ($refsAll as $f): ?>
      <div class="st-dl st-dl--ref" title="<?= e((string) $f['source_url']) ?>">
        <span class="st-dl__name"><?= e($f['version'] !== '' ? $f['version'] . ' · ' : '') ?><?= e($f['variant'] !== '' ? $f['variant'] : st_t('downloads.official')) ?></span>
        <span class="st-dl__size"></span>
        <span class="st-dl__action">
          <a class="st-dl__btn" href="<?= e((string) $f['source_url']) ?>" rel="noopener noreferrer"><?= e(st_t('downloads.external_link')) ?> ↗</a>
        </span>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php if ($filesAll): ?>
    <div class="st-dls-private" id="coleccion-privada">
    <span class="st-dls__sub">🔒 <?= e(st_t('downloads.private_title')) ?></span>

    <?php if ($locked): ?>
    <form class="st-dlkey" method="post" action="<?= e(st_url('download/' . $tool['slug'])) ?>">
      <p class="st-dlkey__note">🔒 <?= e(st_t('downloads.unlock_title')) ?></p>
      <div class="st-dlkey__row">
        <input type="password" name="dl_pass" required minlength="8" autocomplete="off"
               placeholder="<?= e(st_t('downloads.password')) ?>" aria-label="<?= e(st_t('downloads.password')) ?>">
        <button type="submit"><?= e(st_t('downloads.unlock')) ?></button>
      </div>
    </form>
    <?php elseif ($dlHasPass): ?>
    <p class="st-dlkey__note st-dlkey__note--open">🔓 <?= e(st_t('downloads.unlocked')) ?></p>
    <?php endif; ?>

    <?php
    // Agrupación por versión (ADR-011 prototipo): numérica desc, sin versión al final.
    $dlGroups = [];
    foreach ($filesAll as $f) {
        $v = (string) ($f['version'] ?? '');
        if (!isset($dlGroups[$v])) {
            $dlGroups[$v] = ['label' => $v, 'rows' => []];
        }
        $dlGroups[$v]['rows'][] = $f;
    }
    foreach ($dlGroups as &$g) {
        $g['id'] = 'v-' . ($g['label'] === '' ? 'sin' : strtolower((string) preg_replace('/[^a-z0-9.]+/i', '-', $g['label'])));
        $g['era'] = st_era((string) $tool['slug'], $g['label']);
        // Año del grupo: año mínimo de sus filas (año de lanzamiento de la versión).
        $gy = PHP_INT_MAX;
        foreach ($g['rows'] as $r) {
            $gy = min($gy, (int) ($r['year'] ?? PHP_INT_MAX));
        }
        $g['year'] = $gy === PHP_INT_MAX ? '' : (string) $gy;
    }
    unset($g);
    usort($dlGroups, static function (array $a, array $b) use ($verSort): int {
        if (($a['label'] === '') !== ($b['label'] === '')) {
            return $a['label'] === '' ? 1 : -1;
        }
        return $verSort($b['label']) <=> $verSort($a['label']);
    });
    ?>
    <?php if (count($dlGroups) > 1): ?>
    <nav class="st-dl-nav" aria-label="<?= e(st_t('downloads.versions')) ?>">
      <span class="st-dl-nav__label"><?= e(st_t('downloads.versions')) ?></span>
      <?php foreach ($dlGroups as $g): if ($g['label'] === '') continue;
          // Detalle contextual del chip (tooltip nativo): año · era · nº archivos · variantes
          $gy = (string) ($g['year'] ?? '');
          $era = (string) ($g['era'] ?? '');
          $vars = [];
          foreach ($g['rows'] as $r) {
              $v = (string) ($r['variant'] ?? '');
              if ($v !== '' && $v !== 'IDE' && !in_array($v, $vars, true)) {
                  $vars[] = $v;
              }
          }
          $n = count($g['rows']);
          $tParts = [];
          if ($gy !== '') {
              $tParts[] = st_t('downloads.year') . ' ' . $gy;
          }
          if ($era !== '') {
              $tParts[] = $era;
          }
          $tParts[] = $n . ' ' . ($n === 1 ? st_t('downloads.file') : st_t('downloads.files'));
          $detail = implode(' · ', $tParts);
          if ($vars) {
              $detail .= ' — ' . implode(', ', array_slice($vars, 0, 4)) . (count($vars) > 4 ? '…' : '');
          }
      ?>
      <?php if ($dlUnlocked): ?>
      <a class="st-dl-nav__chip" href="#<?= e($g['id']) ?>" title="<?= e($detail) ?>"><?= e($g['label']) ?><?php if ($n > 1): ?><span class="st-dl-nav__n"><?= e((string) $n) ?></span><?php endif; ?></a>
      <?php else: ?>
      <span class="st-dl-nav__chip" title="<?= e($detail) ?>"><?= e($g['label']) ?><?php if ($n > 1): ?><span class="st-dl-nav__n"><?= e((string) $n) ?></span><?php endif; ?></span>
      <?php endif; ?>
      <?php endforeach; ?>
    </nav>
    <?php endif; ?>
    <?php if ($dlUnlocked): ?>
    <ul class="st-dllist">
      <?php foreach ($dlGroups as $g): ?>
      <li class="st-dlgroup">
        <h3 class="st-dlgroup__title" id="<?= e($g['id']) ?>"><?= e($g['label'] !== '' ? $g['label'] : st_t('downloads.ungrouped')) ?>
          <?php $era = st_era((string) $tool['slug'], $g['label']); if ($era !== ''): ?>
          <span class="st-dlgroup__era"><?= e($era) ?></span>
          <?php endif; ?>
          <?php if (!empty($g['year'])): ?>
          <span class="st-dlgroup__year"><?= e((string) $g['year']) ?></span>
          <?php endif; ?>
        </h3>
                <?php
                    // Criterio global de orden dentro del grupo: año ↓ → variante ↑ (alfabético) → nombre ↑
                    $gRows = $g['rows'];
                    usort($gRows, static function (array $a, array $b): int {
                        $ya = (int) ($a['year'] ?? 0);
                        $yb = (int) ($b['year'] ?? 0);
                        if ($ya !== $yb) {
                            return $yb <=> $ya;
                        }
                        $va = strtolower((string) ($a['variant'] ?? ''));
                        $vb = strtolower((string) ($b['variant'] ?? ''));
                        if ($va !== $vb) {
                            return $va <=> $vb;
                        }
                        return strtolower((string) $a['filename']) <=> strtolower((string) $b['filename']);
                    });
                ?>
                        <ul class="st-dlgroup__files">
                        <?php foreach ($gRows as $f):
            // Nombre corto de la fila: variante (si no es IDE) · plataforma
            $b = strtolower((string) $f['filename']);
            $plat = str_contains($b, 'windows') || str_contains($b, 'win') ? 'Windows'
                : (str_contains($b, 'macosx') || str_contains($b, '.dmg') ? 'macOS'
                : (str_contains($b, 'linux') ? 'Linux'
                : (str_contains($b, 'solaris') ? 'Solaris'
                : strtoupper((string) pathinfo((string) $f['filename'], PATHINFO_EXTENSION)))));
            $label = ($f['variant'] !== '' && $f['variant'] !== 'IDE')
                ? $f['variant'] . ' · ' . $plat : $plat;
            $tip = $f['filename'] . ' — sha256: ' . $f['sha256']
                . ($f['license_note'] ? ' — ' . $f['license_note'] : '');
        ?>
        <li class="st-dl" title="<?= e($tip) ?>">
          <span class="st-dl__name"><?= e($label) ?></span>
          <?php if (!$f['present']): ?><span class="st-dl__pend">⏳</span><?php endif; ?>
          <span class="st-dl__size"><?= e($f['human_size']) ?></span>
          <span class="st-dl__action">
            <?php if ($f['open'] && $f['present']): ?>
            <a class="st-dl__btn" href="<?= e(st_url('download/' . $tool['slug'] . '/' . implode('/', array_map('rawurlencode', explode('/', (string) $f['filename']))))) ?>"><?= e(st_t('downloads.download')) ?> ↓</a>
            <?php else: ?>
            <span class="st-dl__lock">🔒</span>
            <?php endif; ?>
          </span>
        </li>
        <?php endforeach; ?>
        </ul>
      </li>
      <?php endforeach; ?>
    </ul>
    <?php endif; ?>
    </div>
    <?php endif; ?>
  </section>
  <?php endif; ?>

  <section class="st-comments" id="comentarios">
    <h2 class="st-section-title"><?= e(st_t('comments.title')) ?>
      <?php if ($commentsList): ?><span class="st-count-badge"><?= e(count($commentsList)) ?></span><?php endif; ?>
    </h2>

    <?php if ($commentsList): ?>
    <ol class="st-clist">
      <?php foreach ($commentsList as $c): ?>
      <li class="st-comment">
        <div class="st-comment__head">
          <strong><?= e($c['author']) ?></strong>
          <time class="st-comment__date"><?= e($c['fecha']) ?></time>
        </div>
        <p><?= nl2br(e($c['body'])) ?></p>
      </li>
      <?php endforeach; ?>
    </ol>
    <?php else: ?>
    <p class="st-hint"><?= e(st_t('comments.none')) ?></p>
    <?php endif; ?>

    <?php if ($commentsEnabled): ?>
    <form class="st-cform" method="post" action="<?= e(st_url('comment')) ?>">
      <div class="st-cform__hp" aria-hidden="true">
        <label>No rellenar este campo
          <input type="text" name="website" tabindex="-1" autocomplete="off">
        </label>
      </div>
      <input type="hidden" name="slug" value="<?= e((string) $tool['slug']) ?>">
      <input type="hidden" name="t" value="<?= e((string) $commentToken['t']) ?>">
      <input type="hidden" name="tsig" value="<?= e((string) $commentToken['sig']) ?>">
      <div class="st-cform__row">
        <input type="text" name="author" required maxlength="60" placeholder="<?= e(st_t('comments.name')) ?>">
      </div>
      <div class="st-cform__row">
        <textarea name="body" required maxlength="<?= e($maxLen) ?>" rows="4" placeholder="<?= e(st_t('comments.body')) ?>"></textarea>
      </div>
      <button class="st-cform__btn" type="submit"><?= e(st_t('comments.submit')) ?></button>
    </form>
    <?php else: ?>
    <p class="st-hint"><?= e(st_t('comments.disabled')) ?></p>
    <?php endif; ?>
  </section>

  <nav class="st-neighbours" aria-label="Cronología">
    <?php if ($neighbours['prev']): ?>
    <a class="st-neigh st-neigh--prev" href="<?= e(st_url('tools/' . $neighbours['prev']['slug'], ['modo' => $mode])) ?>">
      <span class="st-neigh__dir"><?= e(st_t('tool.prev')) ?></span>
      <span class="st-neigh__name"><?= e($neighbours['prev']['name']) ?></span>
      <span class="st-neigh__year"><?= e($neighbours['prev']['year']) ?></span>
    </a>
    <?php else: ?><span></span><?php endif; ?>
    <?php if ($neighbours['next']): ?>
    <a class="st-neigh st-neigh--next" href="<?= e(st_url('tools/' . $neighbours['next']['slug'], ['modo' => $mode])) ?>">
      <span class="st-neigh__dir"><?= e(st_t('tool.next')) ?></span>
      <span class="st-neigh__name"><?= e($neighbours['next']['name']) ?></span>
      <span class="st-neigh__year"><?= e($neighbours['next']['year']) ?></span>
    </a>
    <?php endif; ?>
  </nav>
</article>
