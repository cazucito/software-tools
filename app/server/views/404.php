<?php
if (!defined('ST_APP')) {
    exit;
}
/** Página 404. */
?>
<section class="st-404">
  <div class="st-kicker">ERROR 404</div>
  <h1>Esta estación no existe.</h1>
  <p class="st-hint">La herramienta o página que buscas no está en el archivo.
    <a href="<?= e(st_url('')) ?>">Volver al inicio →</a></p>
</section>
