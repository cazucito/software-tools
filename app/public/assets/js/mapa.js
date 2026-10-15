/* ==========================================================================
   software-tools v3 — mapa.js (home, ADR-012).
   Motor «dendrita»: árbol orgánico categorías → familias → herramientas.
   Port de sketches/002-mapa-mental/b-dendrita, sin dependencias. Lee
   window.ST_DATA = Catalog::mapData() (embebido por el servidor) y nunca
   abandona la página: la ficha se abre en un panel con enlace a la real.
   Parámetros de depuración: ?static desactiva las animaciones (capturas/QA);
   ?all arranca en el modo «expandir todo» (equivale a pulsar #expand).
   window.__mapa = {state, D, render} es un gancho de QA (no forma parte de la API).
   ========================================================================== */
(() => {
'use strict';
if (new URLSearchParams(location.search).has('static')) document.documentElement.classList.add('noanim');

const BASE = document.body.getAttribute('data-base') || '';
const PRETTY = document.body.getAttribute('data-url-style') === 'pretty';
// ficha real de una herramienta (equivalente a st_url('tools/<slug>') del servidor)
const toolUrl = slug => PRETTY
  ? `${BASE}/tools/${encodeURIComponent(slug)}`
  : `${BASE}/index.php?p=tools/${encodeURIComponent(slug)}`;

function fatal(msg) {
  const d = document.createElement('div');
  d.className = 'hint'; d.style.cssText = 'top:4rem;bottom:auto;color:#f87171';
  d.id = 'data-error'; d.textContent = msg;
  document.body.appendChild(d);
}
const validData = d => !!d && Array.isArray(d.categories) && Array.isArray(d.tools)
  && d.categories.every(c => c && typeof c.slug === 'string' && typeof c.count === 'number'
    && Array.isArray(c.subs) && c.subs.every(s => s && typeof s.slug === 'string')
    && (c.color == null || typeof c.color === 'string'))
  && d.tools.every(t => t && typeof t.slug === 'string' && typeof t.name === 'string'
    && (t.tags == null || (Array.isArray(t.tags) && t.tags.every(g => typeof g === 'string')))
    && (t.rel == null || (Array.isArray(t.rel) && t.rel.every(r => typeof r === 'string'))));

if (validData(window.ST_DATA)) init(window.ST_DATA);
else fatal('Datos inválidos: falta window.ST_DATA.');

function init(raw) {
// el resto de campos es opcional: se normalizan con valores por defecto
const D = {
  categories: raw.categories.map(c => ({...c, label: c.label ?? c.slug,
    subs: c.subs.map(s => ({...s, label: s.label ?? s.slug, count: s.count ?? 0}))})),
  tools: raw.tools.map(t => ({...t, year: t.year ?? '—', ctx: t.ctx ?? '',
    tags: Array.isArray(t.tags) ? t.tags : [], rel: Array.isArray(t.rel) ? t.rel : []})),
};
const CAT_COLOR = {editor:'#7dd3fc',lenguaje:'#a78bfa',database:'#34d399',infra:'#fbbf24',
                   os:'#f472b6',tool:'#94a3b8',other:'#64748b'};
const PALETTE = ['#7dd3fc','#a78bfa','#34d399','#fbbf24','#f472b6','#fb923c','#2dd4bf','#a3e635'];
const catBySlug  = Object.fromEntries(D.categories.map(c => [c.slug, c]));
const toolBySlug = Object.fromEntries(D.tools.map(t => [t.slug, t]));
// color de categoría: dato → mapa local → hash determinista del slug
const slugHash = s => { let h = 0; for (const ch of s) h = (h * 31 + ch.charCodeAt(0)) >>> 0; return h; };
const HEX = /^#[0-9a-f]{6}$/i;
const colorOf = c => (typeof c.color === 'string' && HEX.test(c.color) && c.color) || CAT_COLOR[c.slug] || PALETTE[slugHash(c.slug) % PALETTE.length];
const LABEL_MIN = 30;      // separación mínima (px) para etiquetas no activas (familias y hojas)
const LABEL_CAT_MIN = 34;  // ídem para categorías
const MIN_SP = 24;         // paso vertical mínimo (px) del abanico antes de recortar con chip de desborde
const clamp = (v, lo, hi) => Math.max(lo, Math.min(hi, v));
const panelIsOpen = () => document.getElementById('panel').classList.contains('is-open');
const isMobile = () => window.innerWidth <= 760;

// columnas del árbol; en móvil estrecho se comprimen para que la columna de hojas quede dentro del viewport
// límite derecho del abanico: en móvil el panel es una hoja inferior, no cubre columnas
const xMaxOf = (W, compact, open) => W - (open && !compact ? 380 : 24);
const xCols = (compact, W, open) => {
  const xCat = compact ? 170 : 268, xSub = xCat + (compact ? 150 : 205);
  const xLeaf = xSub + (compact ? 165 : 228);
  const c = (compact && xLeaf > W - 32)
    ? {xRoot: 44, xCat: 112, xSub: 212, xLeaf: 317, tight: true}
    : {xRoot: compact ? 64 : 104, xCat, xSub, xLeaf, tight: false};
  // invariante: xLeaf <= xMax. Desplazamiento uniforme a la izquierda; si el tronco
  // saldría de xRoot >= 34 (círculo completo visible), se comprime el resto de columnas.
  const xMax = xMaxOf(W, compact, open), shift = c.xLeaf - xMax;
  if (shift <= 0) return c;
  if (c.xRoot - shift >= 34) {
    return {xRoot: c.xRoot - shift, xCat: c.xCat - shift, xSub: c.xSub - shift, xLeaf: xMax, tight: c.tight};
  }
  const k = Math.max(xMax - 34, 0) / (c.xLeaf - c.xRoot), f = x => 34 + (x - c.xRoot) * k;
  return {xRoot: 34, xCat: f(c.xCat), xSub: f(c.xSub), xLeaf: Math.max(xMax, 34), tight: c.tight};
};

// abanico de hojas: 1 columna hasta FAN_SINGLE; más → multicolumna con tope de alto y de ancho.
// Presupuesto horizontal: la última columna debe quedar a la izquierda de xMax (el panel es un overlay).
const FAN_SINGLE = 14, SP = 40, MAX_COLS = 4;
const fanPlan = (n, availH, xCol, W, compact, open) => {
  const colW = compact ? 150 : 200;
  const xMax = xMaxOf(W, compact, open);
  const maxCols = Math.max(1, Math.floor((xMax - 8 - xCol) / colW) + 1);
  const colsCap = Math.min(MAX_COLS, maxCols);
  const rowsMax = Math.max(4, Math.floor(availH / SP) + 1);
  const cap = Math.min(620, availH);
  let cols = Math.min(colsCap, n > FAN_SINGLE ? Math.max(2, Math.ceil(n / rowsMax)) : 1);
  let rpc = Math.ceil(n / cols);
  // primero se abren columnas (hasta colsCap); comprimir el paso es el último recurso
  while ((rpc - 1) * SP > cap && cols < colsCap) { cols++; rpc = Math.ceil(n / cols); }
  let shown = n, overflow = 0;
  const rows = Math.floor(cap / MIN_SP) + 1;
  if (rpc > rows) {
    // no cabe ni comprimido a MIN_SP: se muestran slots-1 hojas + 1 chip de desborde
    shown = cols * rows - 1; overflow = n - shown; rpc = rows;
  }
  const sp = rpc > 1 ? Math.min(SP, cap / (rpc - 1)) : SP;
  return {cols, rpc, sp, shown, overflow, xMax, colW};
};

const svg  = document.getElementById('map');
const panel= document.getElementById('panel');
const pinner=document.getElementById('pinner');
const qEl  = document.getElementById('q');
const ddEl = document.getElementById('dd');

// mode 'normal': a lo sumo una categoría y una familia abiertas (conjuntos de 0–1 elementos);
// mode 'all': vista completa; los clics solo podan/reabren ramas (toggles locales).
const state = { mode: 'normal', openCats: new Set(), openSubs: new Set(), selected: null };
const first = set => set.values().next().value ?? null;
const hintEl = document.getElementById('hint');
const HINTS = {
  normal: hintEl ? hintEl.textContent : '',
  all: 'vista completa · clic en categoría o familia para plegar · clic en herramienta → ficha · hilos = relacionadas',
};
const LAST_POS = new Map();
let W, H;

const NS = 'http://www.w3.org/2000/svg';
function mk(tag, attrs, parent) {
  const el = document.createElementNS(NS, tag);
  for (const k in attrs) el.setAttribute(k, attrs[k]);
  (parent || svg).appendChild(el);
  return el;
}
function tip(g, text) { mk('title', {}, g).textContent = text; }
function short(n, w=20) { return n.length > w ? n.slice(0, w-1) + '…' : n; }
const esc = s => String(s).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
function hex2rgb(h){const v=parseInt(h.slice(1),16);return `${(v>>16)&255},${(v>>8)&255},${v&255}`;}

function layout() {
  W = window.innerWidth; H = window.innerHeight;
  svg.setAttribute('viewBox', `0 0 ${W} ${H}`);
}

function render() {
  if (hintEl) hintEl.textContent = HINTS[state.mode];
  if (state.mode === 'all') { renderAll(); return; }
  const openCat = first(state.openCats), openSub = first(state.openSubs);
  LAST_POS.clear();
  layout();
  svg.innerHTML = '';
  const LABELS = [];
  const compact = isMobile();
  const {xRoot, xCat, xSub, xLeaf, tight} = xCols(compact, W, panelIsOpen());
  const cy = H/2 + 6;

  const cats = D.categories;
  const n = cats.length;
  const spread = Math.min(compact ? 84 : 112, (H - 190) / Math.max(n - 1, 1));
  const yOf = i => cy + (i - (n-1)/2) * spread;

  // nervio con curvatura (dobla hacia el lado del destino)
  const nerve = (x1, y1, x2, y2, stroke, w, dy0=2) => {
    const bow = (y2 >= y1 ? 1 : -1) * 22;
    mk('path', {d: `M ${x1} ${y1+dy0} C ${x1+56} ${y1+bow}, ${x2-40} ${y2}, ${x2-8} ${y2}`,
      class:'branch draw', stroke, 'stroke-width': w});
  };

  // tronco
  const tg = mk('g', {});
  mk('circle', {cx:xRoot, cy, r:34, fill:'#121a29', stroke:'#7dd3fc', 'stroke-width':2.2}, tg);
  mk('text', {x:xRoot, y:cy+5, fill:'#7dd3fc', 'font-size':14, 'font-weight':700, 'text-anchor':'middle'}, tg)
    .textContent = 'st';
  mk('text', {x:xRoot, y:cy+56, fill:'#eef2f7', 'font-size':11.5, 'text-anchor':'middle',
    style:'paint-order:stroke;stroke:#0b0f17;stroke-width:3px'}, tg)
    .textContent = 'software-tools';

  const colW = compact ? 150 : 200;
  const fanLayout = (n, availH, xCol) => fanPlan(n, availH, xCol, W, compact, panelIsOpen());

  // hojas de una lista: primero TODOS los nervios, luego nodos+etiquetas
  const drawLeaves = (tl, srcX, srcY, xCol, color) => {
    if (!tl.length) return;
    const availTop = 84, availBot = H - 48;
    const {cols, rpc, sp, shown, overflow, xMax} = fanLayout(tl.length, availBot - availTop, xCol);
    const total = tl.length, tl0 = tl;
    tl = tl.slice(0, shown);
    const fanH = (rpc - 1) * sp;
    const cyF = Math.max(availTop + fanH/2, Math.min(availBot - fanH/2, srcY));
    const showAll = sp >= LABEL_MIN;
    for (const [k, t] of tl.entries()) {
      const col = Math.floor(k / rpc), row = k % rpc;
      const x = xCol + col * colW, ty = cyF - fanH/2 + row * sp;
      LAST_POS.set(t.slug, [x, ty]);
      const sel = state.selected === t.slug;
      nerve(srcX, srcY, x - 8, ty, sel ? '#eef2f7' : `rgba(${hex2rgb(color)},0.55)`, sel ? 1.8 : 1.3, 16);
    }
    for (const [k, t] of tl.entries()) {
      const [lx, ty] = LAST_POS.get(t.slug);
      const sel = state.selected === t.slug;
      const lg = mk('g', {class:'node leaf-pop', style:`animation-delay:${Math.min(k*16,420)}ms`});
      tip(lg, t.name + ' · ' + t.year);
      mk('circle', {class:'kk', cx:lx, cy:ty, r: sel ? 7.5 : (showAll ? 6 : 4),
        fill: sel ? '#eef2f7' : color, stroke:'#0b0f17', 'stroke-width':1}, lg);
      if (showAll || sel) {
        const lbl = mk('text', {x:lx+12, y:ty+3.5, class:'leafLbl' + (sel ? ' sel' : '')}, lg);
        lbl.textContent = `${short(t.name, cols > 1 ? (compact ? 14 : 20) : (compact ? 16 : 24))} · ${t.year}`;
      }
      lg.addEventListener('click', () => selectTool(t.slug));
    }
    if (overflow > 0) {
      const col = Math.floor(shown / rpc), row = shown % rpc;
      const x = xCol + col * colW, ty = cyF - fanH/2 + row * sp;
      const og = mk('g', {class:'node leaf-pop overflow', 'data-overflow': overflow});
      tip(og, `${overflow} de ${total} herramientas sin mostrar`);
      const cw = compact ? 150 : 172, cx0 = Math.min(x - 8, xMax - cw);  // el chip nunca pasa de xMax
      mk('rect', {x:cx0, y:ty-11, width:cw, height:22, rx:11,
        fill:'#101826', stroke:color, 'stroke-width':1.2, 'stroke-dasharray':'4 3'}, og);
      mk('text', {x:cx0+12, y:ty+3.5, class:'leafLbl'}, og).textContent =
        `+${overflow} más · ver lista`;
      const hidden = tl0.slice(shown);
      og.addEventListener('click', e => {
        e.stopPropagation();  // el listener global de documento cerraría el desplegable
        qEl.value = ''; showToolList(hidden); qEl.focus();
      });
      nerve(srcX, srcY, x - 8, ty, `rgba(${hex2rgb(color)},0.35)`, 1.1, 16);
    }
  };

  cats.forEach((cat, i) => {
    const y = yOf(i);
    const color = colorOf(cat);
    const open = openCat === cat.slug;
    const rC = clamp(spread * 0.30, 10, 24);  // el nodo se achica si hay muchas categorías

    // rama principal (curva orgánica), arranca en el borde del tronco
    const dxT = xCat - xRoot, dyT = y - cy, lenT = Math.hypot(dxT, dyT) || 1;
    const sx0 = xRoot + 34*dxT/lenT, sy0 = cy + 34*dyT/lenT;
    const c1x = xRoot + (xCat - xRoot)*0.45, c1y = y + (i%2 ? 26 : -26);
    const d = `M ${sx0} ${sy0} C ${c1x} ${cy}, ${c1x} ${c1y}, ${xCat} ${y}`;
    mk('path', {d, class:'branch' + (open ? '' : ' draw'), stroke: `rgba(${hex2rgb(color)},${open?0.9:0.5})`});
    if (open) mk('path', {d, class:'branch draw', stroke: color, 'stroke-width': 2});

    // nodo de categoría
    const g = mk('g', {class:'node'});
    tip(g, `${cat.label} · ${cat.count}`);
    mk('circle', {class:'kk', cx:xCat, cy:y, r:rC, fill:'#101826', stroke:color, 'stroke-width': open?2.4:1.6}, g);
    mk('text', {x:xCat, y:y+(rC < 16 ? 3 : 4), fill:color, 'font-size': rC < 16 ? 8 : 10.5,
      'text-anchor':'middle', 'font-weight':700}, g)
      .textContent = cat.count;
    const onCatClick = () => {
      state.openCats.clear(); state.openSubs.clear();
      if (!open) state.openCats.add(cat.slug);
      if (state.selected && toolBySlug[state.selected].cat !== first(state.openCats)) state.selected = null;
      render();
    };
    if (spread >= LABEL_CAT_MIN || open)
      LABELS.push({x: xCat+rC+6, y: rC < 16 ? y+4 : y-9, text: cat.label, strong: open,
        size: 12, dim: '#98a2b3', halo: 3, onClick: onCatClick});
    g.addEventListener('click', onCatClick);

    if (!open) return;

    const subs = cat.subs || [];
    if (subs.length) {
      // segundo anillo: familias (fan vertical propio, clampado al viewport)
      const ns = subs.length;
      const spreadS = Math.min(compact ? 44 : 58, (H - 170) / Math.max(ns - 1, 1));
      let sh = 0;
      const top0 = y - (ns-1)/2 * spreadS, bot0 = y + (ns-1)/2 * spreadS;
      if (bot0 > H - 40) sh -= bot0 - (H - 40);
      if (top0 + sh < 86) sh += 86 - (top0 + sh);
      const yS = k => y + (k - (ns-1)/2) * spreadS + sh;
      const rS = clamp(spreadS * 0.33, 7, 14);

      // nervios (salida radial del hub) primero; etiquetas al final (z-order global)
      subs.forEach((s, k) => {
        const yy = yS(k);
        const ax = xSub-xCat, ay = yy-y, al = Math.hypot(ax, ay) || 1;
        nerve(xCat+(rC+2)*ax/al, y+(rC+2)*ay/al, xSub-rS-2, yy, `rgba(${hex2rgb(color)},0.5)`, 1.3, 16);
      });
      subs.forEach((s, k) => {
        const yy = yS(k);
        const openS = openSub === s.slug;
        const sg = mk('g', {class:'node'});
        tip(sg, `${s.label} · ${s.count}`);
        mk('circle', {class:'kk', cx:xSub, cy:yy, r:rS, fill:'#101826', stroke:color, 'stroke-width': openS?2:1.4}, sg);
        if (rS >= 10) mk('text', {x:xSub, y:yy+3.5, fill:color, 'font-size':8.5, 'text-anchor':'middle', 'font-weight':700}, sg)
          .textContent = s.count;
        const onClick = () => {
          state.openSubs.clear();
          if (!openS) state.openSubs.add(s.slug);
          if (state.selected && toolBySlug[state.selected].sub !== first(state.openSubs)) state.selected = null;
          render();
        };
        if (spreadS >= LABEL_MIN || openS)
          LABELS.push({x: xSub+rS+12, y: yy+3.5, text: short(s.label, tight ? 11 : compact ? 20 : 28),
            strong: openS, size: 10.5, dim: '#9aa7ba', halo: 3.5, onClick});
        sg.addEventListener('click', onClick);
        if (!openS) return;
        const tl = D.tools.filter(t => t.cat === cat.slug && t.sub === s.slug);
        drawLeaves(tl, xSub+rS, yy, xLeaf, color);
      });
    } else {
      // categoría plana (≤ umbral): herramientas directas
      const tl = D.tools.filter(t => t.cat === cat.slug);
      drawLeaves(tl, xCat+rC+2, y, xSub, color);
    }
  });

  if (state.selected) drawLinksFor(state.selected);

  // etiquetas al final: por encima de todas las curvas
  for (const L of LABELS) {
    const el = mk('text', {x:L.x, y:L.y, 'font-size': L.size,
      fill: L.strong ? '#eef2f7' : L.dim,
      style:`paint-order:stroke;stroke:#0b0f17;stroke-width:${L.halo}px;cursor:pointer`});
    el.textContent = L.text;
    el.addEventListener('click', L.onClick);
  }
}

/* ---------- modo «expandir todo»: árbol completo en bandas ---------- */
// Cada categoría recibe una banda vertical proporcional a sus filas (familias, o herramientas si es plana).
// Orden de capas: nervios → nodos → etiquetas. Ningún recorte silencioso: lo que no cabe va a un chip.
function renderAll() {
  LAST_POS.clear();
  layout();
  svg.innerHTML = '';
  const LABELS = [], NODES = [];
  const compact = isMobile(), open = panelIsOpen();
  const {xRoot, xCat, xSub, xLeaf, tight} = xCols(compact, W, open);
  const xMax = xMaxOf(W, compact, open);
  const cy = H/2 + 6;
  const availTop = 84, availBot = H - 48;
  const cats = D.categories;

  const nerve = (x1, y1, x2, y2, stroke, w, dy0=2) => {
    const bow = (y2 >= y1 ? 1 : -1) * 22;
    mk('path', {d: `M ${x1} ${y1+dy0} C ${x1+56} ${y1+bow}, ${x2-40} ${y2}, ${x2-8} ${y2}`,
      class:'branch draw', stroke, 'stroke-width': w});
  };
  const toolsOf = c => D.tools.filter(t => t.cat === c.slug);

  // bandas por categoría
  const units = cats.map(c => c.subs.length || toolsOf(c).length || 1);
  const totalUnits = units.reduce((a, b) => a + b, 0);
  let acc = availTop;
  const bands = units.map(u => { const h = (availBot - availTop) * u / totalUnits; const b = {top: acc, h}; acc += h; return b; });

  // tronco
  const tg = mk('g', {});
  mk('circle', {cx:xRoot, cy, r:34, fill:'#121a29', stroke:'#7dd3fc', 'stroke-width':2.2}, tg);
  mk('text', {x:xRoot, y:cy+5, fill:'#7dd3fc', 'font-size':14, 'font-weight':700, 'text-anchor':'middle'}, tg)
    .textContent = 'st';
  mk('text', {x:xRoot, y:cy+56, fill:'#eef2f7', 'font-size':11.5, 'text-anchor':'middle',
    style:'paint-order:stroke;stroke:#0b0f17;stroke-width:3px'}, tg)
    .textContent = 'software-tools';

  // columna de hojas dentro de [top, top+h]; devuelve nada. Los nodos se difieren (z-order).
  const leafColumn = (tl, x, top, h, srcX, srcY, color, labelMin) => {
    const n = tl.length; if (!n) return;
    const maxShown = Math.floor(h / 7) + 1;
    let shown = n, overflow = 0;
    if (n > maxShown) { shown = Math.max(maxShown - 1, 0); overflow = n - shown; }
    const slots = shown + (overflow ? 1 : 0);
    const sp = clamp(h / slots, 7, 26);
    const y0 = top + h/2 - (slots - 1) * sp / 2;
    for (let k = 0; k < shown; k++) {
      const t = tl[k], ty = y0 + k * sp, sel = state.selected === t.slug;
      LAST_POS.set(t.slug, [x, ty]);
      nerve(srcX, srcY, x - 8, ty, sel ? '#eef2f7' : `rgba(${hex2rgb(color)},0.32)`, sel ? 1.8 : 0.9, 16);
      NODES.push(() => {
        const lg = mk('g', {class:'node leaf-pop'});
        tip(lg, t.name + ' · ' + t.year);
        mk('circle', {class:'kk', cx:x, cy:ty, r: sel ? 7 : 3.5, fill: sel ? '#eef2f7' : color,
          stroke:'#0b0f17', 'stroke-width':1}, lg);
        lg.addEventListener('click', () => selectTool(t.slug));
      });
      if (sp >= labelMin || sel)
        LABELS.push({x: x + 12, y: ty + 3.5, cls: 'leafLbl' + (sel ? ' sel' : ''),
          text: `${short(t.name, compact ? 14 : 22)} · ${t.year}`, onClick: () => selectTool(t.slug)});
    }
    if (overflow > 0) {
      const ty = y0 + shown * sp, hidden = tl.slice(shown);
      nerve(srcX, srcY, x - 8, ty, `rgba(${hex2rgb(color)},0.35)`, 1.1, 16);
      NODES.push(() => {
        const og = mk('g', {class:'node leaf-pop overflow', 'data-overflow': overflow});
        tip(og, `${overflow} de ${n} herramientas sin mostrar`);
        const cw = compact ? 150 : 172, cx0 = Math.min(x - 8, xMax - cw);
        mk('rect', {x:cx0, y:ty-11, width:cw, height:22, rx:11,
          fill:'#101826', stroke:color, 'stroke-width':1.2, 'stroke-dasharray':'4 3'}, og);
        mk('text', {x:cx0+12, y:ty+3.5, class:'leafLbl'}, og).textContent = `+${overflow} más · ver lista`;
        og.addEventListener('click', e => {
          e.stopPropagation();  // el listener global de documento cerraría el desplegable
          qEl.value = ''; showToolList(hidden); qEl.focus();
        });
      });
    }
  };

  cats.forEach((cat, i) => {
    const {top, h: bandH} = bands[i];
    const y = top + bandH/2;
    const color = colorOf(cat);
    const isOpen = state.openCats.has(cat.slug);
    const rC = clamp(bandH * 0.30, 6, 24);

    const dxT = xCat - xRoot, dyT = y - cy, lenT = Math.hypot(dxT, dyT) || 1;
    const sx0 = xRoot + 34*dxT/lenT, sy0 = cy + 34*dyT/lenT;
    const c1x = xRoot + (xCat - xRoot)*0.45, c1y = y + (i%2 ? 26 : -26);
    const d = `M ${sx0} ${sy0} C ${c1x} ${cy}, ${c1x} ${c1y}, ${xCat} ${y}`;
    mk('path', {d, class:'branch' + (isOpen ? '' : ' draw'), stroke: `rgba(${hex2rgb(color)},${isOpen?0.9:0.5})`});
    if (isOpen) mk('path', {d, class:'branch draw', stroke: color, 'stroke-width': 2});

    const onCatClick = () => {
      if (state.openCats.has(cat.slug)) {
        state.openCats.delete(cat.slug);
        if (state.selected && toolBySlug[state.selected].cat === cat.slug) state.selected = null;
      } else state.openCats.add(cat.slug);
      render();
    };
    NODES.push(() => {
      const g = mk('g', {class:'node'});
      tip(g, `${cat.label} · ${cat.count}`);
      mk('circle', {class:'kk', cx:xCat, cy:y, r:rC, fill:'#101826', stroke:color, 'stroke-width': isOpen?2.4:1.6}, g);
      mk('text', {x:xCat, y:y+(rC < 16 ? 3 : 4), fill:color, 'font-size': rC < 16 ? 8 : 10.5,
        'text-anchor':'middle', 'font-weight':700}, g).textContent = cat.count;
      g.addEventListener('click', onCatClick);
    });
    if (bandH >= 18 || isOpen)
      LABELS.push({x: xCat+rC+6, y: rC < 16 ? y+4 : y-9, text: cat.label, strong: isOpen,
        size: 12, dim: '#98a2b3', halo: 3, onClick: onCatClick});

    if (!isOpen) return;
    const subs = cat.subs;
    if (subs.length) {
      const ns = subs.length, pitchF = bandH / ns;
      const rS = clamp(pitchF * 0.35, 7, 14);
      const total = Math.max(toolsOf(cat).length, 1);
      let subTop = top;
      subs.forEach((s, k) => {
        const yy = top + (k + 0.5) * pitchF;
        const openS = state.openSubs.has(s.slug);
        const tl = D.tools.filter(t => t.cat === cat.slug && t.sub === s.slug);
        const hF = bandH * tl.length / total;
        const ax = xSub-xCat, ay = yy-y, al = Math.hypot(ax, ay) || 1;
        nerve(xCat+(rC+2)*ax/al, y+(rC+2)*ay/al, xSub-rS-2, yy, `rgba(${hex2rgb(color)},0.5)`, 1.3, 16);
        const onClick = () => {
          if (state.openSubs.has(s.slug)) {
            state.openSubs.delete(s.slug);
            if (state.selected && toolBySlug[state.selected].sub === s.slug) state.selected = null;
          } else state.openSubs.add(s.slug);
          render();
        };
        NODES.push(() => {
          const sg = mk('g', {class:'node'});
          tip(sg, `${s.label} · ${s.count}`);
          mk('circle', {class:'kk', cx:xSub, cy:yy, r:rS, fill:'#101826', stroke:color, 'stroke-width': openS?2:1.4}, sg);
          if (rS >= 10) mk('text', {x:xSub, y:yy+3.5, fill:color, 'font-size':8.5, 'text-anchor':'middle', 'font-weight':700}, sg)
            .textContent = s.count;
          sg.addEventListener('click', onClick);
        });
        if (pitchF >= 20)
          LABELS.push({x: xSub+rS+12, y: yy+3.5, text: short(s.label, tight ? 11 : compact ? 20 : 28),
            strong: openS, size: 10.5, dim: '#9aa7ba', halo: 3.5, onClick});
        if (openS) leafColumn(tl, xLeaf, subTop, hF, xSub+rS, yy, color, 15);
        subTop += hF;
      });
    } else {
      leafColumn(toolsOf(cat), xSub, top, bandH, xCat+rC+2, y, color, 20);
    }
  });

  for (const f of NODES) f();
  if (state.selected) drawLinksFor(state.selected);
  for (const L of LABELS) {
    const el = L.cls
      ? mk('text', {x:L.x, y:L.y, class:L.cls, style:'cursor:pointer'})
      : mk('text', {x:L.x, y:L.y, 'font-size': L.size, fill: L.strong ? '#eef2f7' : L.dim,
          style:`paint-order:stroke;stroke:#0b0f17;stroke-width:${L.halo}px;cursor:pointer`});
    el.textContent = L.text;
    el.addEventListener('click', L.onClick);
  }
}

function drawLinksFor(slug) {
  const t = toolBySlug[slug]; if (!t) return;
  const pos = LAST_POS;
  if (!pos.has(slug)) return;
  const [sx, sy] = pos.get(slug);
  const targets = t.rel.concat(t.succ && toolBySlug[t.succ] ? [t.succ] : []);
  targets.forEach((r, i) => {
    if (!pos.has(r)) return; // destino no visible → el panel lo lista
    const [tx, ty] = pos.get(r);
    const mx = (sx+tx)/2, my = Math.min(sy,ty) - Math.abs(tx-sx)*0.12 - 14;
    mk('path', {d:`M ${sx} ${sy} Q ${mx} ${my} ${tx} ${ty}`, class:'link',
      stroke: r === t.succ ? '#fbbf24' : 'rgba(238,242,247,.55)', style:`animation-delay:${i*60}ms`});
  });
}

function selectTool(slug, {expand=true} = {}) {
  const t = toolBySlug[slug]; if (!t) return;
  if (expand) {
    if (state.mode === 'all') {   // vista completa: sin exclusividad, solo se garantiza la rama visible
      state.openCats.add(t.cat);
      if (t.sub) state.openSubs.add(t.sub);
    } else {
      state.openCats.clear(); state.openSubs.clear();
      state.openCats.add(t.cat);
      if (t.sub) state.openSubs.add(t.sub);
    }
  }
  state.selected = slug;
  openPanel(t);   // antes de render(): el presupuesto horizontal depende del estado del panel
  render();
}

/* ---------- panel ---------- */
function openPanel(t) {
  document.body.classList.add('panel-open');
  panel.classList.add('is-open');
  panel.setAttribute('aria-hidden','false');
  const cat = catBySlug[t.cat];
  const sub = t.sub && cat ? ((cat.subs || []).find(s => s.slug === t.sub) || null) : null;
  const years = t.until ? `${t.year} – ${t.until}` : `desde ${t.year}`;
  const path = [cat ? cat.label : t.cat, sub ? sub.label : null].filter(Boolean).join(' → ');
  pinner.innerHTML = `
    <div class="panel__meta">${esc(path)} · ${esc(years)}</div>
    <h2>${esc(t.name)}</h2>
    <p class="ctx">${esc(t.ctx)}</p>
    ${t.tags.length ? `<h3>Tags</h3><div class="chips">${t.tags.map(g => `<button data-tag="${esc(g)}">${esc(g)}</button>`).join('')}</div>` : ''}
    ${t.rel.length || t.succ ? `<h3>Relacionadas</h3><div class="rel">` +
      t.rel.map(r => toolBySlug[r] ? `<button data-go="${esc(r)}">↗ ${esc(toolBySlug[r].name)}</button>` : '').join('') +
      (t.succ && toolBySlug[t.succ] ? `<button data-go="${esc(t.succ)}">→ sucesora: ${esc(toolBySlug[t.succ].name)}</button>` : '') +
      `</div>` : ''}
    <a class="live" href="${esc(toolUrl(t.slug))}">ver ficha completa →</a>`;
  pinner.querySelectorAll('[data-go]').forEach(b => b.addEventListener('click', () => selectTool(b.dataset.go)));
  pinner.querySelectorAll('[data-tag]').forEach(b => b.addEventListener('click', () => {
    qEl.value = b.dataset.tag; runSearch(b.dataset.tag); qEl.focus();
  }));
  pinner.scrollTop = 0;
}
function closePanel() { document.body.classList.remove('panel-open'); panel.classList.remove('is-open'); panel.setAttribute('aria-hidden','true'); render(); }
document.getElementById('pclose').addEventListener('click', closePanel);
const expandAll = () => {
  state.mode = 'all';
  state.openCats = new Set(D.categories.map(c => c.slug));
  state.openSubs = new Set(D.categories.flatMap(c => c.subs.map(s => s.slug)));
  render();
};
document.getElementById('collapse').addEventListener('click', () => {
  state.mode = 'normal'; state.openCats.clear(); state.openSubs.clear(); state.selected = null; render();
});
const expandBtn = document.getElementById('expand');
if (expandBtn) expandBtn.addEventListener('click', expandAll);

/* ---------- búsqueda ---------- */
const INDEX = D.tools.map(t => ({t, hay:(t.name+' '+t.slug+' '+t.tags.join(' ')+' '+t.ctx).toLowerCase()}));
function searchTools(q) {
  q = q.trim().toLowerCase(); if (!q) return [];
  const out = [];
  for (const {t, hay} of INDEX) {
    const name = t.name.toLowerCase(); let s = -1;
    if (name.startsWith(q)) s = 0; else if (name.includes(q)) s = 1;
    else if (t.tags.some(g => g.toLowerCase().includes(q))) s = 2;
    else if (hay.includes(q)) s = 3;
    if (s >= 0) out.push({t, s});
  }
  return out.sort((a,b)=>a.s-b.s||a.t.name.localeCompare(b.t.name)).slice(0,8).map(x=>x.t);
}
function runSearch(q) {
  const res = searchTools(q);
  ddEl.innerHTML='';
  if (!q.trim()) { ddEl.classList.remove('is-open'); return; }
  if (!res.length) { ddEl.innerHTML = `<div class="empty">sin resultados para «${esc(q.trim())}»</div>`; ddEl.classList.add('is-open'); return; }
  showToolList(res);
}
// desplegable de herramientas (resultados de búsqueda o hojas ocultas de un abanico)
function showToolList(list) {
  ddEl.innerHTML = '';
  list.forEach(t => {
    const b = document.createElement('button');
    const cat = catBySlug[t.cat];
    const sub = t.sub && cat ? ((cat.subs || []).find(s => s.slug === t.sub) || null) : null;
    b.innerHTML = `${esc(t.name)}<span>${esc([cat ? cat.label : t.cat, sub ? sub.label : null].filter(Boolean).join(' → '))} · ${esc(t.year)}</span>`;
    b.addEventListener('click', () => { ddEl.classList.remove('is-open'); qEl.value=''; selectTool(t.slug); });
    ddEl.appendChild(b);
  });
  ddEl.classList.add('is-open');
}
qEl.addEventListener('input', () => runSearch(qEl.value));
qEl.addEventListener('keydown', e => {
  if (e.key === 'Escape') { ddEl.classList.remove('is-open'); qEl.blur(); }
  if (e.key === 'Enter') { if (!ddEl.classList.contains('is-open')) return; const f = ddEl.querySelector('button'); if (f) f.click(); }
});
document.addEventListener('click', e => { if (!e.target.closest('.hd__search')) ddEl.classList.remove('is-open'); });
document.addEventListener('keydown', e => {
  if (e.key === 'Escape' && panel.classList.contains('is-open')) closePanel();
  if (e.key === '/' && document.activeElement !== qEl) { e.preventDefault(); qEl.focus(); }
});

/* ---------- arranque ---------- */
window.__mapa = { state, D, render };   // gancho de QA (consola/CDP)
if (new URLSearchParams(location.search).has('all')) expandAll(); else render();
}
})();
