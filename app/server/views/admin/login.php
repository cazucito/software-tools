<?php
if (!defined('ST_APP')) {
    exit;
}
/** Acceso al panel. Variables: $error, $enabled, $csrf. */
?>
<div class="ad-login">
  <h1 class="ad-login__title">software-tools <span>admin</span></h1>
  <?php if (!$enabled): ?>
  <p class="ad-flash ad-flash--error">El panel no está configurado todavía (falta <code>admin.pass_hash</code> en <code>config.local.php</code>).</p>
  <?php else: ?>
    <?php if ($error): ?><p class="ad-flash ad-flash--error"><?= e($error) ?></p><?php endif; ?>
    <form class="ad-login__form" method="post" action="<?= e(st_url('admin/login')) ?>">
      <input type="hidden" name="csrf" value="<?= e($csrf) ?>">
      <label for="password">Contraseña</label>
      <input id="password" type="password" name="password" required autofocus autocomplete="current-password">
      <button class="ad-btn ad-btn--primary" type="submit">Entrar</button>
    </form>
  <?php endif; ?>
  <p class="ad-login__back"><a href="<?= e(st_url('')) ?>">← volver al sitio</a></p>
</div>
