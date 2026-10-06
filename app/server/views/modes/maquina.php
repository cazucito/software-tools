<?php
if (!defined('ST_APP')) {
    exit;
}
/**
 * Modalidad «Máquina» (C): CRT fosforescente, arranque BIOS, log y
 * directorio estilo Norton Commander. Todo se alimenta de datos reales.
 */
?>
<div class="power" id="power"></div>
<div class="crt"></div><div class="crt-vignette"></div><div class="flicker"></div>

<section id="boot">
  <div class="bl-title">SOFTWARE-TOOLS <span class="dim">BIOS v3.0</span></div>
  <div class="bl">ARCHIVO PERSONAL DE SOFTWARE — <span data-num="minYear"></span> → HOY</div>
  <div class="bl">CPU: MEMORIA HUMANA ..... 640K OK</div>
  <div class="bl">UNIDAD C: ............... <b><span data-num="count"></span> PROGRAMAS</b></div>
  <div class="bl">ÚLTIMA INSTALACIÓN: ...... <span data-num="maxYear"></span></div>
  <div class="bl">SESIÓN: .................. <span data-num="yearsLife"></span> AÑOS DE HISTORIAL</div>
  <div class="bl">TECLADO ......... OK &nbsp; RATÓN ......... OK &nbsp; MÓDEM ......... OK</div>
  <div class="bl" style="margin-top:1.6rem">&gt; ARRANCANDO <span class="cursor"></span></div>
  <div class="cue">▼ PRESIONA SCROLL PARA CONTINUAR</div>
</section>

<section id="log">
  <div class="log-wrap">
    <div class="log-lines" id="logLines"></div>
    <div class="log-side">
      <div class="side-label">AÑO EN PANTALLA</div>
      <div class="side-year" id="logYear">1991</div>
      <div class="side-label">PROGRESO</div>
      <div class="side-bar" id="logBar">[░░░░░░░░░░░░░░]</div>
      <div class="side-label">AVANCE DEL ARCHIVO</div>
      <div class="side-files"><span id="logFiles">01</span> / <span data-num="count"></span></div>
    </div>
  </div>
</section>

<section id="directorio">
  <div class="dir-title">C:\TOOLS — DIRECTORIO COMPLETO · <span data-num="count"></span> ENTRADAS · CLIC PARA INSPECCIONAR</div>
  <div class="dir-frame">
    <div class="dir-pane left">
      <div class="dir-head"><span>NOMBRE</span><span>AÑOS</span></div>
      <div id="dirList"></div>
    </div>
    <div class="dir-detail" id="dirDetail"></div>
  </div>
  <div class="dir-hint">HOVER = RESALTAR · CLIC = SELECCIONAR · LA LISTA SE DESPLAZA</div>
</section>

<section id="salida">
  <div class="exit-line" id="exitLine">C:\&gt; EXIT</div>
  <div class="exit-msg">CIERRE DE SESIÓN. <span data-num="count"></span> PROGRAMAS REGISTRADOS · <span data-num="minYear"></span> — HOY.</div>
  <div><a class="exit-cta" href="<?= e(st_url('tools', ['modo' => 'maquina'])) ?>">&gt; ABRIR CATÁLOGO COMPLETO <span class="cursor" style="width:.5em;height:.9em"></span></a></div>
</section>
