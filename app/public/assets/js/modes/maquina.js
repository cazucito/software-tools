/* ==========================================================================
   Modalidad «Máquina» (C) — CRT, BIOS, log y directorio Norton Commander.
   Portada de la maqueta aprobada; alimentada por datos reales.
   Ajustes tras revisión visual: el log aclara los hitos mostrados, el cursor
   queda al final de la línea de salida.
   ========================================================================== */
(function () {
  'use strict';
  window.__st = { errors: [], notes: [] };
  window.addEventListener('error', function (e) { window.__st.errors.push(String(e.message || e)); });

  var D = window.ST_DATA, S = D.stats, TOOLS = D.tools;
  function el(id) { return document.getElementById(id); }
  var BASE = document.body.getAttribute('data-base') || '';
  function toolHref(slug) {
    var mode = document.body.getAttribute('data-mode') || 'maquina';
    return BASE + '/index.php?p=tools/' + encodeURIComponent(slug) + '&modo=' + encodeURIComponent(mode);
  }
  function span(t) { return t.usedUntil ? (t.year + '\u2013' + t.usedUntil) : ('desde ' + t.year); }

  document.querySelectorAll('[data-num]').forEach(function (n) {
    var k = n.getAttribute('data-num');
    n.textContent = k === 'count' ? S.count : k === 'minYear' ? S.minYear : k === 'maxYear' ? S.maxYear :
                    k === 'yearsLife' ? (S.today - S.minYear) : '';
  });

  /* ---------- log de arranque (12 fitos reales + cierre) ---------- */
  var LOG_SLUGS = ['ms-dos-5-1', 'qbasic', 'turbo-c', 'norton-commander', 'eudora', 'windows-95',
                   'visual-basic-6', 'vmware-workstation', 'eclipse', 'intellij-idea', 'docker', 'visual-studio-code'];
  var byslug = {}; TOOLS.forEach(function (t) { byslug[t.slug] = t; });
  var logTools = LOG_SLUGS.map(function (s) { return byslug[s]; }).filter(Boolean);
  var logLines = el('logLines');
  function trail(name, w) { var dots = '.'; while ((name.length + dots.length) < w) dots += '.'; return name + ' ' + dots; }
  logTools.forEach(function (t, i) {
    var L = document.createElement('div');
    L.className = 'log-line';
    L.innerHTML = '<span class="ly">' + t.year + '&gt;</span> ' + trail(t.name, 30) +
                  ' <span class="lok">OK' + ((i === 0 && window.innerWidth > 860) ? '  (1er arranque)' : '') + '</span>';
    logLines.appendChild(L);
  });
  var fin = document.createElement('div');
  fin.className = 'log-line log-final'; fin.id = 'logFinal';
  fin.textContent = '> LOG: ' + logTools.length + ' HITOS CLAVE \u00b7 ARCHIVO: ' + S.count + ' PROGRAMAS \u2014 ' + (S.today - S.minYear) + ' A\u00d1OS';
  logLines.appendChild(fin);
  window.__st.logCount = logTools.length + 1;

  /* ---------- directorio (Norton Commander) ---------- */
  var dirList = el('dirList'), dirDetail = el('dirDetail');
  var rows = [];
  TOOLS.forEach(function (t, i) {
    var b = document.createElement('button');
    b.className = 'dir-row'; b.type = 'button';
    b.innerHTML = '<span class="r-name">' + t.name + '</span><span class="r-meta">' + span(t) + '</span>';
    b.addEventListener('click', function () { select(i); });
    dirList.appendChild(b); rows.push(b);
  });
  function select(i) {
    rows.forEach(function (r, k) { r.classList.toggle('sel', k === i); });
    if (rows[i]) { rows[i].scrollIntoView({ block: 'nearest' }); }
    var t = TOOLS[i];
    dirDetail.innerHTML =
      '<div class="d-name">' + t.name + '</div>' +
      '<div class="d-meta">' + span(t) + ' · ' + t.category + '</div>' +
      '<div class="d-tags">' + t.tags.map(function (tg) { return '<span>' + tg + '</span>'; }).join('') + '</div>' +
      '<div class="d-ctx">' + t.context + '</div>' +
      '<a class="d-link" href="' + toolHref(t.slug) + '">ABRIR FICHA &gt;</a>';
  }
  select(0);

  /* ---------- exit ---------- */
  var exitLine = el('exitLine');
  exitLine.innerHTML = 'C:\\&gt; EXIT<span class="cursor" style="width:.5em;height:.9em;margin-left:.35em"></span>';

  /* ================= MOTION ================= */
  var reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  if (reduced) { document.documentElement.classList.add('reduced'); window.__st.notes.push('reduced-motion'); return; }

  gsap.registerPlugin(ScrollTrigger);
  var lenis = new Lenis({ autoRaf: false });
  lenis.on('scroll', ScrollTrigger.update);
  gsap.ticker.add(function (time) { lenis.raf(time * 1000); });
  gsap.ticker.lagSmoothing(0);
  window.__st.notes.push('lenis ok');

  /* power-on */
  gsap.fromTo('#power', { opacity: .9 }, { opacity: 0, duration: .8, ease: 'power2.out' });

  /* boot: líneas apareciendo + cursor */
  gsap.from('#boot .bl, #boot .bl-title', { opacity: 0, x: -14, duration: .35, stagger: .28, delay: .3, ease: 'none',
    onComplete: function () { gsap.fromTo('#boot .cue', { opacity: 0 }, { opacity: 1, duration: .6 }); } });
  gsap.fromTo('#boot .cue', { opacity: 0 }, { opacity: 0, duration: .01 });

  /* log: pin + scrub */
  var logYear = el('logYear'), logBar = el('logBar'), logFiles = el('logFiles');
  var BAR_N = 14;
  gsap.timeline({
    scrollTrigger: {
      trigger: '#log', start: 'top top', end: '+=320%', pin: true, scrub: .5,
      onUpdate: function (self) {
        var p = self.progress;
        var li = Math.min(logTools.length - 1, Math.floor(p * (logTools.length + 1)));
        logYear.textContent = (p >= 0.985) ? 'HOY' : String(logTools[li].year);
        var k = Math.round(p * BAR_N);
        logBar.textContent = '[' + '\u2588'.repeat(k) + '\u2591'.repeat(BAR_N - k) + ']';
        var f = Math.max(1, Math.round(p * S.count));
        logFiles.textContent = (f < 10 ? '0' : '') + f;
      }
    }
  })
  .fromTo('.log-line', { opacity: 0 }, { opacity: 1, duration: .4, stagger: .55, ease: 'none' }, 0)
  .to({}, { duration: 2 }, 0); /* cola para que el final respire */
  window.__st.notes.push('log pinned');

  /* directorio + salida */
  gsap.from('#directorio .dir-frame', { opacity: 0, y: 26, duration: .8, ease: 'power2.out',
    scrollTrigger: { trigger: '#directorio', start: 'top 75%' } });
  gsap.from('#salida .exit-line, #salida .exit-msg, #salida .exit-cta', { opacity: 0, y: 18, duration: .6, stagger: .25, ease: 'none',
    scrollTrigger: { trigger: '#salida', start: 'top 75%' } });

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
