/* ==========================================================================
   Modalidad «Línea» (B, por defecto) — la línea se dibuja de 1991 a hoy;
   cada herramienta es una estación. Portada de la maqueta aprobada.
   Mejora del port: cada estación enlaza a su ficha al iluminarse.
   ========================================================================== */
(function () {
  'use strict';
  window.__st = { errors: [], notes: [] };
  window.addEventListener('error', function (e) { window.__st.errors.push(String(e.message || e)); });

  var D = window.ST_DATA, S = D.stats, TOOLS = D.tools;
  var byslug = {}; TOOLS.forEach(function (t) { byslug[t.slug] = t; });
  function esc(s) { return String(s).replace(/[&<>"']/g, function (c) { return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]; }); }
  function firstSentence(s) { var m = String(s).match(/^[^.!?]*[.!?]/); return m ? m[0] : s; }
  var BASE = document.body.getAttribute('data-base') || '';
  function toolHref(slug) {
    var mode = document.body.getAttribute('data-mode') || 'linea';
    return BASE + '/index.php?p=tools/' + encodeURIComponent(slug) + '&modo=' + encodeURIComponent(mode);
  }

  document.querySelectorAll('[data-num]').forEach(function (n) {
    var k = n.getAttribute('data-num');
    n.textContent = k === 'count' ? S.count : k === 'minYear' ? S.minYear : k === 'spann' ? (S.today - S.minYear) : '';
  });

  /* ---------- estaciones (herramientas reales, cronológicas) ---------- */
  var STATION_SLUGS = ['ms-dos-5-1', 'turbo-c', 'eudora', 'windows-95', 'vmware-workstation',
                       'eclipse', 'mozilla-firefox', 'intellij-idea', 'docker', 'visual-studio-code'];
  var stations = STATION_SLUGS.map(function (slug) { return byslug[slug]; }).filter(Boolean);
  window.__st.stations = stations.length;

  var NS = 'http://www.w3.org/2000/svg';
  var camino = document.getElementById('camino');
  var svg, glowPath, mainPath, dot, stDots = [];
  var L = 0, pts = [];

  function buildLine() {
    var W = camino.clientWidth, H = camino.clientHeight;
    var A = Math.min(W * 0.30, 420);            /* amplitud del meandro */
    var cx = W / 2, n = stations.length;
    var padTop = H * 0.045, padBot = H * 0.07;
    var segH = (H - padTop - padBot) / n;
    var d = '', x = cx, y0 = padTop;
    d += 'M ' + x.toFixed(1) + ' ' + y0.toFixed(1);
    for (var i = 0; i < n; i++) {
      var y1 = y0 + segH;
      var tx = (i % 2 === 0) ? cx + A : cx - A;
      var c1y = y0 + segH * 0.35, c2y = y1 - segH * 0.35;
      d += ' C ' + x.toFixed(1) + ' ' + c1y.toFixed(1) + ', ' + tx.toFixed(1) + ' ' + c2y.toFixed(1) + ', ' + tx.toFixed(1) + ' ' + y1.toFixed(1);
      x = tx; y0 = y1;
    }
    svg.setAttribute('width', W); svg.setAttribute('height', H);
    svg.setAttribute('viewBox', '0 0 ' + W + ' ' + H);
    glowPath.setAttribute('d', d); mainPath.setAttribute('d', d);
    L = mainPath.getTotalLength();
    var arr = String(L); mainPath.style.strokeDasharray = arr; glowPath.style.strokeDasharray = arr;
    pts = stations.map(function (_, i) { return mainPath.getPointAtLength(L * (0.035 + i * (0.93 / (stations.length - 1)))); });
  }

  function buildDOM() {
    svg = document.createElementNS(NS, 'svg'); svg.id = 'line-svg';
    glowPath = document.createElementNS(NS, 'path');
    glowPath.setAttribute('fill', 'none'); glowPath.setAttribute('stroke', '#38bdf8');
    glowPath.setAttribute('stroke-width', '7'); glowPath.setAttribute('stroke-opacity', '0.10');
    glowPath.setAttribute('stroke-linecap', 'round');
    mainPath = document.createElementNS(NS, 'path'); mainPath.id = 'journey';
    mainPath.setAttribute('fill', 'none'); mainPath.setAttribute('stroke', '#38bdf8');
    mainPath.setAttribute('stroke-width', '2'); mainPath.setAttribute('stroke-linecap', 'round');
    dot = document.createElementNS(NS, 'circle'); dot.id = 'journey-dot';
    dot.setAttribute('r', '4'); dot.setAttribute('fill', '#38bdf8');
    svg.appendChild(glowPath); svg.appendChild(mainPath); svg.appendChild(dot);
    camino.appendChild(svg);

    stDots = [];
    stations.forEach(function (t, i) {
      var s = document.createElement('div');
      s.className = 'station'; s.setAttribute('data-i', i);
      s.innerHTML = '<span class="st-year">' + t.year + '</span>' +
                    '<span class="st-name"><a href="' + toolHref(t.slug) + '">' + esc(t.name) + '</a></span>' +
                    '<span class="st-line">' + esc(firstSentence(t.context)) + '</span>';
      camino.appendChild(s);
      var c = document.createElementNS(NS, 'circle');
      c.setAttribute('class', 'st-dot'); c.setAttribute('r', '4');
      c.setAttribute('fill', '#020617'); c.setAttribute('stroke', '#38bdf8'); c.setAttribute('stroke-width', '1.5');
      svg.appendChild(c); stDots.push(c);
    });
  }

  function placeStations() {
    var W = camino.clientWidth;
    stations.forEach(function (_, i) {
      var s = camino.querySelector('.station[data-i="' + i + '"]');
      var p = pts[i];
      s.style.left = p.x + 'px'; s.style.top = p.y + 'px';
      var left = p.x < W / 2;
      var avail = left ? (p.x - 42) : (W - p.x - 42);
      if (avail < 140) { left = !left; avail = left ? (p.x - 42) : (W - p.x - 42); }
      s.classList.toggle('l-left', left);
      s.classList.toggle('l-right', !left);
      s.style.maxWidth = Math.max(120, Math.min(300, avail)) + 'px';
      if (stDots[i]) { stDots[i].setAttribute('cx', p.x); stDots[i].setAttribute('cy', p.y); }
    });
  }

  buildDOM();
  buildLine();
  placeStations();

  var reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  if (reduced) {
    document.documentElement.classList.add('reduced');
    mainPath.style.strokeDashoffset = 0; glowPath.style.strokeDashoffset = 0;
    var endp = mainPath.getPointAtLength(L);
    dot.setAttribute('cx', endp.x); dot.setAttribute('cy', endp.y);
    camino.querySelectorAll('.station').forEach(function (s) { s.classList.add('lit'); });
    stDots.forEach(function (c) { c.classList.add('lit'); });
    window.__st.notes.push('reduced-motion');
    return;
  }

  gsap.registerPlugin(ScrollTrigger);
  var lenis = new Lenis({ autoRaf: false });
  lenis.on('scroll', ScrollTrigger.update);
  gsap.ticker.add(function (time) { lenis.raf(time * 1000); });
  gsap.ticker.lagSmoothing(0);
  window.__st.notes.push('lenis ok');

  /* dibujo de la línea + estaciones encendidas */
  var stationEls = [], drawTl = null;
  function initDraw() {
    stationEls = Array.prototype.slice.call(camino.querySelectorAll('.station'));
    drawTl = gsap.fromTo([mainPath, glowPath], { strokeDashoffset: function (i, t) { return L; } },
      { strokeDashoffset: 0, ease: 'none',
        scrollTrigger: {
          trigger: '#camino', start: 'top 62%', end: 'bottom 75%', scrub: 0.5,
          onUpdate: function (self) {
            var p = self.progress;
            var pt = mainPath.getPointAtLength(L * p);
            dot.setAttribute('cx', pt.x); dot.setAttribute('cy', pt.y);
            var step = 0.93 / (stations.length - 1);
            stationEls.forEach(function (s, i) {
              var lit = p >= (0.035 + i * step) - 0.004;
              s.classList.toggle('lit', lit);
              if (stDots[i]) stDots[i].classList.toggle('lit', lit);
            });
          }
        }
      });
  }
  initDraw();
  window.__st.notes.push('line draw ok');

  /* re-build en resize (limpio, sin recargar) */
  var rT;
  window.addEventListener('resize', function () {
    clearTimeout(rT);
    rT = setTimeout(function () {
      if (drawTl) { if (drawTl.scrollTrigger) drawTl.scrollTrigger.kill(); drawTl.kill(); }
      svg.remove();
      camino.querySelectorAll('.station').forEach(function (s) { s.remove(); });
      buildDOM(); buildLine(); placeStations();
      initDraw();
      ScrollTrigger.refresh();
    }, 350);
  });

  /* hero + editorial + hoy */
  gsap.from('#hero h1, #hero .eyebrow, #hero .sub, #hero .cue', { opacity: 0, y: 30, duration: 1, stagger: .14, ease: 'power2.out' });
  gsap.from('#estacion .rev', { opacity: 0, y: 34, duration: .9, stagger: .14, ease: 'power2.out',
    scrollTrigger: { trigger: '#estacion', start: 'top 70%' } });
  gsap.from('#hoy .rev', { opacity: 0, y: 30, duration: .8, stagger: .12, ease: 'power2.out',
    scrollTrigger: { trigger: '#hoy', start: 'top 72%' } });

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
  window.__st.lineLength = Math.round(L);
})();
