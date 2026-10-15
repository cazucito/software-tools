/* ==========================================================================
   software-tools v3 — app.js (páginas de contenido; la home usa mapa.js).
   Atajo «/» para enfocar la búsqueda de la cabecera.
   ========================================================================== */
(function () {
  'use strict';

  document.addEventListener('keydown', function (ev) {
    if (ev.key !== '/' || ev.metaKey || ev.ctrlKey || ev.altKey) { return; }
    var el = document.activeElement;
    if (el && (el.tagName === 'INPUT' || el.tagName === 'TEXTAREA')) { return; }
    var target = document.querySelector('.st-search__input');
    if (target) { ev.preventDefault(); target.focus(); }
  });
})();
