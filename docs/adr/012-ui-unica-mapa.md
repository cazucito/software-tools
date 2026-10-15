# ADR-012 — UI única: el mapa dendrita es la home

**Estado:** aceptada (2026-10-15) · **Reemplaza a:** [ADR-006](006-modalidades.md) · **Relacionada:** [ADR-010](010-escala-y-assets.md), maqueta `sketches/002-mapa-mental/`

## Contexto

El ADR-006 conservó las tres direcciones visuales de la Fase 1 (cinta, línea, máquina) como modalidades seleccionables. Con 57 fichas el recorrido cronológico deja de escalar, y la maqueta 002 exploró una alternativa: un **mapa mental** donde el primer anillo son las categorías, el segundo las familias y el tercero las herramientas, con búsqueda y un panel de detalle sin salir de la página. El dueño eligió la física «dendrita» (2026-10-15) y pidió dejarla como UI principal, en la home.

## Decisión

1. **Una sola UI.** La home (`/`) es el mapa dendrita (`app/server/views/mapa.php` + `assets/js/mapa.js` + `assets/css/mapa.css`, vanilla, sin dependencias). El panel enlaza a la ficha real (`/tools/<slug>`); la búsqueda del mapa es *la* búsqueda de la home (la cabecera no lleva formulario de sitio; `/search` sigue existiendo y enlazado).
2. **Se retiran las tres modalidades**: vistas, CSS y JS por modo, selector, preferencia en `localStorage`, GSAP/Lenis y las claves `modes/mode_labels/default_mode`. `?modo=…` se **ignora en silencio** (los enlaces viejos siguen respondiendo 200).
3. **Las páginas de contenido** (fichas, catálogo, búsqueda, admin) conservan su aspecto: el antiguo `mode-linea.css` pasó a `content.css` (solo tokens). Estilos: `base.css` + `content.css` + `scale.css` en contenido; `base.css` + `mapa.css` en la home. Body: `mode-mapa` / `mode-content`.
4. **Las familias entran al pipeline de contenido.** La taxonomía autorada vive en `content/taxonomy.json` (umbral, `category_labels`, `category_colors`, `families`). `ops/tools/import_catalog.py` la valida (slug desconocido, duplicado o categoría equivocada ⇒ aborta sin escribir; no listadas ⇒ familia `sin-clasificar` con aviso; categorías con ≤ umbral ⇒ planas) y la vuelca en la BD: `tools.family`, tabla `families` y `categories.label_es/color`. PHP **nunca lee el JSON**: `Catalog::mapData()` arma el payload desde SQLite, con la misma forma que consume el motor.
5. La home incrusta `ST_DATA = Catalog::mapData()`; el `<noscript>` lista las 57 herramientas (a11y + SEO), y se mantienen canonical, título, descripción y JSON-LD.

## Consecuencias

- **Todo lo derivado se regenera** con `python3 ops/tools/import_catalog.py` (añade columnas si la BD ya existía). El catálogo en vivo necesita ese import (o `deploy.py --catalog`) **antes o junto con** el código nuevo: `mapData()` lee `families` y `tools.family`.
- **e2e actualizado** (`ops/tests/e2e.sh`): el enlace legacy `?modo=cinta` debe dar 200 y renderizar el mapa; el marcador del hero se reemplaza por el contenedor `#st-mapa`, y `ST_DATA` debe traer 57 herramientas y 14 familias.
- **`content/` es el hogar de lo autorado** (hoy `taxonomy.json`; las fichas se mudan ahí en un commit posterior). La maqueta 002 sigue funcionando y lee el mismo `content/taxonomy.json`.
- Recategorizar o agregar una categoría/familia = editar el JSON (+ la ficha) y reimportar; no hay código que tocar.
- Se pierde el recorrido cronológico como presentación principal; el catálogo (`/tools`) conserva el filtro por década.
- El historial de las tres modalidades queda en el tag `archive/pre-mapa-2026-10-15`.
