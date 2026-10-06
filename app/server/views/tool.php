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
$locked = false;
foreach ($dloads as $f) {
    if ($f['visibility'] === 'clave' && !$f['open']) {
        $locked = true;
    }
}
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
      <?php if (!empty($assets['icon'])): ?>
      <img src="<?= e(st_asset((string) $assets['icon']['file'])) ?>" alt="" width="64" height="64" loading="lazy">
      <?php else: ?>
      <span class="st-monogram"><?= e(st_monogram((string) $tool['name'])) ?></span>
      <?php endif; ?>
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

  <?php if ($dloads): ?>
  <section class="st-downloads" id="descargas">
    <h2 class="st-section-title"><?= e(st_t('downloads.title')) ?></h2>

    <?php if ($locked): ?>
    <form class="st-dlkey" method="post" action="<?= e(st_url('download/' . $tool['slug'])) ?>">
      <p class="st-dlkey__note">🔒 <?= e(st_t('downloads.unlock_title')) ?></p>
      <div class="st-dlkey__row">
        <input type="password" name="key" required minlength="4" autocomplete="off"
               placeholder="<?= e(st_t('downloads.key')) ?>" aria-label="<?= e(st_t('downloads.key')) ?>">
        <button type="submit"><?= e(st_t('downloads.unlock')) ?></button>
      </div>
    </form>
    <?php elseif ($hasDlKey): ?>
    <p class="st-dlkey__note st-dlkey__note--open">🔓 <?= e(st_t('downloads.unlocked')) ?></p>
    <?php endif; ?>

    <ul class="st-dllist">
      <?php foreach ($dloads as $f): ?>
      <li class="st-dl">
        <div class="st-dl__main">
          <span class="st-dl__name"><?= e($f['filename']) ?></span>
          <span class="st-dl__meta">
            <?= e(st_t('downloads.size')) ?>: <?= e($f['human_size']) ?> ·
            <abbr title="<?= e((string) $f['sha256']) ?>"><?= e(st_t('downloads.sha')) ?>: <?= e($f['sha_short']) ?>…</abbr>
            <?php if ($f['license_note']): ?> · <?= e($f['license_note']) ?><?php endif; ?>
          </span>
        </div>
        <div class="st-dl__action">
          <?php if ($f['visibility'] === 'enlace' && $f['source_url']): ?>
          <a class="st-dl__btn" href="<?= e((string) $f['source_url']) ?>" rel="noopener noreferrer"><?= e(st_t('downloads.external_link')) ?> ↗</a>
          <?php elseif ($f['open']): ?>
          <a class="st-dl__btn" href="<?= e(st_url('download/' . $tool['slug'] . '/' . rawurlencode((string) $f['filename']))) ?>"><?= e(st_t('downloads.download')) ?> ↓</a>
          <?php else: ?>
          <span class="st-dl__lock">🔒 <?= e(st_t('downloads.private')) ?></span>
          <?php endif; ?>
        </div>
        <span class="st-dl__badge st-dl__badge--<?= e($f['visibility']) ?>"><?= e(st_t('downloads.' . ($f['visibility'] === 'publico' ? 'public' : ($f['visibility'] === 'enlace' ? 'external' : 'private')))) ?></span>
      </li>
      <?php endforeach; ?>
    </ul>
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
