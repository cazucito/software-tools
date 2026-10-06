# ADR-010 — Escala a cientos de herramientas y estrategia de assets

**Estado:** aceptado (2026-10-05). **Contexto:** el catálogo pasará de 48 a
varios cientos de herramientas. Dos retos: (1) la navegación no puede depender
del timeline completo como portada, y (2) conseguir un ícono/imagen real por
herramienta es inviable (esfuerzo, derechos, inconsistencia visual).

## Decisión: navegación con escala

- **La búsqueda es el punto de entrada principal**: barra destacada en el home
  con sugerencias instantáneas (FTS5, `/api/search`), atajo `/`; la página de
  resultados mantiene el `<mark>`.
- **Filtros combinables** en el catálogo: década + categoría + tag (URLs
  estables → SEO), render server-side.
- **Zoom por década** en Cinta y Línea: clic en un marcador de década aísla esa
  ventana; segunda pulsación restablece. `content-visibility: auto` en
  tarjetas para rendimiento con cientos de nodos (virtualización completa se
  difiere hasta que el conteo real lo exija).
- Las modalidades siguen siendo experiencias; la portada pasa a ser
  búsqueda + destacados + contadores vivos.

## Decisión: assets en tres niveles

1. **Ícono real** (kind `icon` en `tool_assets`, archivo en
   `assets/tools/<slug>/`): solo herramientas icónicas (~20–30), con fuente y
   licencia trazables.
2. **Monograma tipográfico como identidad de sistema**: letra(s) inicial(es)
   con **color por década** (`st-era-<década>`) y micro-textura de era; se
   percibe intencional, nunca como hueco.
3. **Capturas** (`kind screenshot`, solo en ficha): pocas, las que cuentan
   historia; la ficha sobria sigue siendo válida.

**Consecuencias:** el render de imagen es un helper único
(`st_tool_art(slug, name, year, icons)`): si hay ícono → `<img>`, si no →
monograma por década; un único mapa `iconMap()` por página (1 query, no N).

## Alternativas descartadas

- Ícono real para todas: inviable a escala y visualmente ruidoso.
- Píxel-art/ilustración generada: no trazable a fuentes reales (regla del
  proyecto: assets solo reales y trazables).
- Timeline como portada permanente: no escala; se degrada la experiencia.