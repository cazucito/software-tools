/* ==========================================================================
   Modalidad «Cinta» (A) — la cinta de film recorre los años con el scroll.
   Portada de la maqueta aprobada; alimentada por datos reales (window.ST_DATA).
   Ajustes tras revisión visual: años con varias entradas en abanico más claro,
   Eudora al frente de su año, contador de año sincronizado con el cabezal,
   la cabeza de reproducción no cruza el texto, cola de cinta más corta.
   ========================================================================== */
(function () {
  'use strict';
  window.__st = { errors: [], notes: [] };
  window.addEventListener('error', function (e) { window.__st.errors.push(String(e.message || e)); });

  var D = window.ST_DATA, S = D.stats, TOOLS = D.tools;
  function esc(s) { return String(s).replace(/[&<>"']/g, function (c) { return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]; }); }
  function el(id) { return document.getElementById(id); }
  var BASE = document.body.getAttribute('data-base') || '';
  function toolHref(slug) {
    var mode = document.body.getAttribute('data-mode') || 'cinta';
    return BASE + '/index.php?p=tools/' + encodeURIComponent(slug) + '&modo=' + encodeURIComponent(mode);
  }

  /* números reales en el copy */
  document.querySelectorAll('[data-num]').forEach(function (n) {
    var k = n.getAttribute('data-num');
    n.textContent = k === 'count' ? S.count : k === 'minYear' ? S.minYear : k === 'yearsLife' ? (S.today - S.minYear)
      : k === 'maxYear' ? S.maxYear : k === 'span' ? S.span : '';
  });

  /* ---------- construir cinta ---------- */
  var PX = (window.innerWidth < 720 ? 190 : 240), PAD = 140;
  var tapeW = (S.maxYear - S.minYear) * PX + PAD * 2 + 160;
  var tape = el('tape');
  tape.style.width = tapeW + 'px';

  var byYear = {};
  TOOLS.forEach(function (t) { (byYear[t.year] = byYear[t.year] || []).push(t); });

  /* mismo orden cronológico, pero Eudora al final de su año (queda al frente). */
  var ordered = TOOLS.slice().sort(function (a, b) {
    return (a.year - b.year) || ((a.slug === 'eudora' ? 1 : 0) - (b.slug === 'eudora' ? 1 : 0)) || String(a.name).localeCompare(String(b.name));
  });

  ordered.forEach(function (t) {
    var k = byYear[t.year].indexOf(t);          // posición dentro del mismo año
    var dx = k * 38;                             // abanico horizontal (años repetidos)
    var dy = k === 0 ? 0 : (k % 2 ? 16 : -16);   // alternancia vertical del abanico
    var f = document.createElement('a');
    f.className = 'frame' + (t.slug === 'eudora' ? ' frame-flag' : '');
    f.setAttribute('href', toolHref(t.slug));
    f.setAttribute('title', t.name + ' — abrir ficha');
    f.style.left = (PAD + (t.year - S.minYear) * PX + dx) + 'px';
    f.style.top = 'calc(50% + ' + dy + 'px)';
    f.innerHTML = '<div class="f-year">' + t.year + '</div><div class="f-name">' + esc(t.name) + '</div>' +
                  '<div class="f-cat">' + esc(t.category) + '</div><div class="f-dot"></div>';
    tape.appendChild(f);
  });

  [1990, 2000, 2010].forEach(function (dec) {
    var m = document.createElement('div');
    m.className = 'decade';
    m.style.left = (PAD + (dec - S.minYear) * PX - PX / 2) + 'px';
    m.innerHTML = '<span>' + dec + 's</span>';
    tape.appendChild(m);
  });

  /* hero strip (marquesina) */
  var strip = el('heroStrip');
  var sample = TOOLS.filter(function (_, i) { return i % 3 === 0; });
  for (var pass = 0; pass < 2; pass++) {
    sample.forEach(function (t) {
      var f = document.createElement('div');
      f.className = 'strip-frame';
      f.innerHTML = '<div class="y">' + t.year + '</div><div class="n">' + esc(t.name) + '</div>';
      strip.appendChild(f);
    });
  }

  /* rebobina (dos filas) */
  function fillRow(id, arr) {
    var row = el(id);
    arr.forEach(function (t) {
      var f = document.createElement('div');
      f.className = 'rush-frame';
      f.innerHTML = '<div class="y">' + t.year + '</div><div class="n">' + esc(t.name) + '</div>';
      row.appendChild(f);
    });
  }
  var half = Math.ceil(TOOLS.length / 2);
  fillRow('rush1', TOOLS.slice(0, half));
  fillRow('rush2', TOOLS.slice(half));

  /* ---------- motion ---------- */
  var reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  if (reduced) { document.documentElement.classList.add('reduced'); window.__st.notes.push('reduced-motion'); return; }

  gsap.registerPlugin(ScrollTrigger);
  var lenis = new Lenis({ autoRaf: false });
  lenis.on('scroll', ScrollTrigger.update);
  gsap.ticker.add(function (time) { lenis.raf(time * 1000); });
  gsap.ticker.lagSmoothing(0);
  window.__st.notes.push('lenis ok');

  /* marquesina del hero */
  gsap.to('.strip-in', { xPercent: -50, duration: 70, repeat: -1, ease: 'none' });

  /* entrada del hero */
  gsap.from('#hero h1, #hero .eyebrow, #hero .sub', { opacity: 0, y: 26, duration: 1, stagger: .12, ease: 'power2.out' });

  /* LA CINTA — pin + scrub horizontal */
  function tapeDist() { return Math.max(0, tape.scrollWidth - window.innerWidth); }
  var yearNow = el('yearNow'), scaleFill = el('scaleFill');
  gsap.timeline({
    scrollTrigger: {
      trigger: '#tape-scene', start: 'top top', end: function () { return '+=' + (tapeDist() + window.innerHeight * 0.35); },
      pin: true, scrub: 0.6, invalidateOnRefresh: true,
      onUpdate: function (self) {
        var p = self.progress;
        /* año bajo el cabezal (centro de la pantalla), no interpolación bruta */
        var x = p * tapeDist() + window.innerWidth / 2 - PAD;
        var yv = Math.max(S.minYear, Math.min(S.maxYear, S.minYear + Math.round(x / PX)));
        yearNow.textContent = p >= 0.985 ? 'HOY' : yv;
        scaleFill.style.width = (p * 100) + '%';
      }
    }
  }).to(tape, { x: function () { return -tapeDist(); }, ease: 'none' }, 0);
  window.__st.notes.push('tape pinned');

  /* SPOTLIGHT — el fotograma crece */
  var spotTl = gsap.timeline({
    scrollTrigger: { trigger: '#spotlight', start: 'top top', end: '+=240%', pin: true, scrub: 0.6 }
  });
  spotTl.fromTo('.spot-frame', { scale: 0.5, opacity: 0.35 }, { scale: 1, opacity: 1, duration: 1.1, ease: 'power2.out' }, 0);
  spotTl.to('.spot-frame', { borderColor: '#e8b45a', boxShadow: '0 0 60px rgba(232,180,90,.35)', duration: .5 }, 1.0);
  spotTl.fromTo('.spot-card .rev', { opacity: 0, y: 26 }, { opacity: 1, y: 0, stagger: .16, duration: .5 }, 1.3);
  window.__st.notes.push('spotlight pinned');

  /* REBOBINA */
  gsap.fromTo('#rush1', { xPercent: -14 }, { xPercent: 2, ease: 'none',
    scrollTrigger: { trigger: '#rush', start: 'top bottom', end: 'bottom top', scrub: 1 } });
  gsap.fromTo('#rush2', { xPercent: -2 }, { xPercent: -16, ease: 'none',
    scrollTrigger: { trigger: '#rush', start: 'top bottom', end: 'bottom top', scrub: 1 } });
  gsap.from('.rush-line, .cta-wrap', { opacity: 0, y: 30, duration: .8, stagger: .2, ease: 'power2.out',
    scrollTrigger: { trigger: '.rush-line', start: 'top 85%' } });

  /* deep-link para capturas/depuración: ?y=0.5 salta a esa fracción del scroll */
  var PP = new URLSearchParams(location.search);
  if (PP.has('y')) {
    setTimeout(function () {
      var m = document.documentElement.scrollHeight - window.innerHeight;
      window.scrollTo(0, m * parseFloat(PP.get('y')));
      ScrollTrigger.update();
    }, 900);
  }

  window.__st.ready = true;
  window.__st.triggers = ScrollTrigger.getAll().length;
})();
