/* ==========================================================================
   software-tools v3 — app.js (común a todas las modalidades).
   ========================================================================== */
(function () {
  'use strict';

  // Recordar la modalidad activa (el servidor ya la validó y la aplicó).
  try {
    var modo = document.body.getAttribute('data-mode');
    if (modo) { localStorage.setItem('st.modo', modo); }
  } catch (err) { /* navegación privada sin almacenamiento: seguir */ }

  // Atajo «/» para enfocar la búsqueda.
  document.addEventListener('keydown', function (ev) {
    if (ev.key !== '/' || ev.metaKey || ev.ctrlKey || ev.altKey) { return; }
    var el = document.activeElement;
    if (el && (el.tagName === 'INPUT' || el.tagName === 'TEXTAREA')) { return; }
    var input = document.querySelector('.st-search__input');
    if (input) { ev.preventDefault(); input.focus(); }
  });
})();
