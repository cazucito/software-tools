/* ==========================================================================
   software-tools v3 — app.js (común a todas las modalidades).
   ADR-010: helper de arte (ícono real / monograma por década) + buscador hero.
   ========================================================================== */
(function () {
  'use strict';

  var BASE = document.body.getAttribute('data-base') || '';

  // Recordar la modalidad activa (el servidor ya la validó y la aplicó).
  try {
    var modo = document.body.getAttribute('data-mode');
    if (modo) { localStorage.setItem('st.modo', modo); }
  } catch (err) { /* navegación privada sin almacenamiento: seguir */ }

  function esc(s) {
    return String(s).replace(/[&<>"']/g, function (c) {
      return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
    });
  }
  function stMonogram(name) {
    var words = String(name).trim().split(/\s+/), out = '';
    for (var i = 0; i < words.length && out.length < 2; i++) {
      var m = (words[i] || '').match(/[\p{L}\p{N}]/u);
      if (m) { out += m[0].toUpperCase(); }
    }
    return out || '·';
  }

  // Arte de herramienta (ADR-010): ícono real o monograma teñido por década.
  window.stArt = function (t, icons) {
    var file = icons && icons[t.slug];
    if (file) {
      var rel = String(file).replace(/^assets\//, '');
      return '<img class="st-art" src="' + esc(BASE + '/assets/' + rel) + '" alt="' + esc(t.name) + '" loading="lazy">';
    }
    var era = Math.floor(t.year / 10) * 10;
    return '<span class="st-art st-art--mono st-era-' + era + '">' + esc(stMonogram(t.name)) + '</span>';
  };

  // ---- Buscador hero (home): sugerencias instantáneas (FTS5) --------------
  var input = document.querySelector('.st-hero-search .st-search__input');
  var box = document.querySelector('.st-hero-search .st-suggest');
  if (input && box) {
    var tmr = null, items = [];
    function closeSuggest() { box.hidden = true; box.innerHTML = ''; items = []; }
    input.addEventListener('input', function () {
      clearTimeout(tmr);
      var q = input.value.trim();
      if (q.length < 2) { closeSuggest(); return; }
      tmr = setTimeout(function () {
        fetch(BASE + '/index.php?p=api/search&q=' + encodeURIComponent(q))
          .then(function (r) { return r.json(); })
          .then(function (data) {
            if (!data.results || !data.results.length) {
              box.hidden = false;
              box.innerHTML = '<div class="st-suggest__empty">Sin coincidencias para «' + esc(q) + '»</div>';
              items = [];
              return;
            }
            items = data.results.slice(0, 8);
            box.hidden = false;
            box.innerHTML = items.map(function (r) {
              return '<a class="st-suggest__row" href="' +
                esc(BASE + '/index.php?p=tools/' + encodeURIComponent(r.slug) + '&modo=' + encodeURIComponent(modo || 'linea')) + '">' +
                '<span class="st-suggest__name">' + esc(r.name) + '</span>' +
                '<span class="st-suggest__meta">' + r.year + ' · ' + esc(r.category) + '</span></a>';
            }).join('') +
              '<a class="st-suggest__all" href="' +
              esc(BASE + '/index.php?p=search&q=' + encodeURIComponent(q)) + '">' +
              'Ver los ' + data.total + ' resultados →</a>';
          })
          .catch(function () { /* sin red: el envío normal del form funciona */ });
      }, 180);
    });
    document.addEventListener('keydown', function (ev) {
      if (box.hidden) { return; }
      if (ev.key === 'Escape') { closeSuggest(); return; }
      var rows = box.querySelectorAll('.st-suggest__row');
      if (!rows.length) { return; }
      var idx = Array.prototype.indexOf.call(rows, document.activeElement);
      if (ev.key === 'ArrowDown') { ev.preventDefault(); if (rows[idx + 1]) { rows[idx + 1].focus(); } }
      else if (ev.key === 'ArrowUp') { ev.preventDefault(); if (idx > 0) { rows[idx - 1].focus(); } }
    });
    input.addEventListener('blur', function () { setTimeout(closeSuggest, 180); });
    input.addEventListener('focus', function () { if (items.length) { box.hidden = false; } });
  }

  // Atajo «/» para enfocar la búsqueda.
  document.addEventListener('keydown', function (ev) {
    if (ev.key !== '/' || ev.metaKey || ev.ctrlKey || ev.altKey) { return; }
    var el = document.activeElement;
    if (el && (el.tagName === 'INPUT' || el.tagName === 'TEXTAREA')) { return; }
    var target = input || document.querySelector('.st-search__input');
    if (target) { ev.preventDefault(); target.focus(); }
  });
})();