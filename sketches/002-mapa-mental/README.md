# Maqueta 002 · Mapa mental (modo único candidato)

Tres físicas para el FUTURO modo único del sitio (reemplaza Cinta/Línea/Máquina):

- `a-plex/` — radial tipo «plex» (nodo activo al centro; linaje TheBrain/PersonalBrain).
- `b-dendrita/` — árbol orgánico que crece por ramas (linaje MindManager).
- `c-constelacion/` — clusters con cámara libre, sinapsis y estrellas.

## Requisitos cubiertos (pedido del dueño, 2026-10-15)

- Primer anillo = **categorías** (no años).
- **Todo en una misma página**: el mapa nunca se abandona; la ficha se abre en panel.
- **Búsqueda requerida**: filtra name/slug/tags/contexto y navega el mapa al resultado.
- Clic en nodo de herramienta (o resultado) → **detalle de la ficha** + enlace a la ficha real.

## Familias (segundo anillo) — decisión del dueño 2026-10-15

- Regla global y determinista: una categoría con **más de 6** herramientas se
  subdivide en familias; el resto muestra sus herramientas directas.
  Resultado: Herramientas→9, Editores→3, Lenguajes→2; Bases de datos,
  Infraestructura y Sistemas operativos quedan planas.
- Taxonomía como DATO en `content/taxonomy.json` (compartida con el pipeline de producción) (umbral, etiquetas de categoría,
  familias). Recategorizar o añadir categorías = editar ese JSON (+ el catálogo).
  `prep_data.py` aborta sin escribir ante slugs desconocidos, duplicados o una
  herramienta en la categoría equivocada; las herramientas no listadas van a la
  familia «Sin clasificar» y el script imprime sus slugs.
- Dendrita v2: clic categoría → familias · clic familia → herramientas · clic
  herramienta → ficha (breadcrumb «categoría → familia» en el panel).

## Datos

`data.js` se genera desde el catálogo (fuente única):

```bash
python3 sketches/002-mapa-mental/tools/prep_data.py   # tras import_catalog.py si cambian fichas
```

## Deep-links (para capturas/QA)

- `?cat=editor` — abre una categoría.
- `?tool=umlet` — selecciona una herramienta (abre su panel).

## Captura

Chrome headless sobre servidor local:

```bash
cd sketches/002-mapa-mental && python3 -m http.server 8097
google-chrome --headless=new --window-size=1440,900 --virtual-time-budget=5000 \
  --screenshot=out.png "http://127.0.0.1:8097/a-plex/?tool=umlet"
```

Pendiente (al aprobar una física): producción es `app/server/views/modes/mapa.php` + assets
del modo, sustituyendo el selector de modalidades; las páginas `/tools/<slug>` se conservan.
- `?data=../stress-data.js` — carga otro conjunto de datos (mismo origen). El
  fixture sintético (14 categorías, 173 herramientas, familia de 40, categoría
  plana de 25) se regenera con `python3 tools/make_stress_data.py`.
- `?static=1` — sin animaciones (capturas).

## Presupuestos de layout y límites conocidos (b-dendrita)

- Abanico de hojas: la última columna nunca pasa de `xMax = W − (panel abierto ? 380 : 24)`
  (en móvil el panel es una hoja inferior, no resta ancho). Invariante `xLeaf <= xMax`: si las
  columnas no caben, se desplazan en bloque a la izquierda (o se comprimen si el tronco saldría
  de x=24). Se abren columnas antes de comprimir el paso (`SP` 40 → mínimo `MIN_SP` 24); si ni
  así caben, se muestran `slots−1` hojas y un chip `+N más · ver lista` que abre el desplegable
  con las N ocultas y enfoca `#q`. Invariante: visibles + N == total.
- El tope depende del estado del panel en el momento del render; abrir o cerrar el panel
  re-renderiza (el estado se conserva).
- Verificación numérica: `?selftest=1` expone `window.__fanPlan` (función pura de layout).
- Compromiso aceptado: la comprobación de mismo origen de `?data=` solo valida la URL
  inicial; una redirección same-origin podría servir un script clásico de otro origen
  (sin fuga de datos; el fixture es una maqueta local).
