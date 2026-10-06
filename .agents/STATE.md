# STATE — fase actual

**Fase 7 completada (2026-10-05): v3 EN PRODUCCIÓN en la raíz** → `https://software-tools.pcabrera.com/` (URLs limpias, indexable, admin en `/admin`). El prototipo queda vivo en `/proto/` (noindex) como espacio de pruebas.

## Entregado (Fases 0–7 de la v3)

- ✔️ Fase 0 — hosting verificado (PHP 8.1.34, SQLite 3.53.4 + FTS5).
- ✔️ Fase 1 — maquetas de las 3 modalidades aprobadas (seleccionables; default línea).
- ✔️ Fase 2 — SPEC v3 + ADRs 005–010; specs v2 retiradas.
- ✔️ Fase 3 — importador + verificador (**PARIDAD 48/48**); catalog/ops sqlite (ADR-009).
- ✔️ Extra — fichas 28→48; 60 recíprocos; estudio de descargas.
- ✔️ Fase 4 — prototipo navegable (3 modalidades, ficha, FTS5, décadas).
- ✔️ Fase 5 — comentarios, panel admin, descargas con clave, i18n es/en, SEO; **E2E 58/58** + smoke en vivo.
- ✔️ **Fase 6.5/ADR-010 — escala a cientos**: barra de búsqueda hero en el home con sugerencias FTS5; filtros combinables (década+categoría+tag) en el catálogo; arte por herramienta (ícono real o monograma por década `st-era-*`); zoom por década en Cinta y Línea; `content-visibility` para rendimiento.
- ✔️ **Fase 7 — producción en la raíz**: deploy `--root` (renombra placeholder y `/conceptos/` a `_bak-*`, nunca borra), `.htaccess` de rewrite (URLs limpias), `noindex=false` + `url_style=pretty` en config viva, sitemap canonical pretty; `data/`+`downloads/` 403; admin OK. **Pendiente del dueño: unpublish de GitHub Pages** (Settings → Pages → Unpublish) — kaelaxiom no tiene permiso admin.

## Pendiente

- **GitHub Pages a retirar por el dueño** (v2 sigue visible en cazucito.github.io/software-tools hasta ese clic).
- **Fase 6 (inglés completo)** de ficha/sistema; base i18n lista.
- **Assets reales** (ADR-010, nivel 1): ~20–30 íconos icónicos cuando comparta su carpeta; los monogramas por década ya son la identidad del resto.
- Descargas: inventario de su carpeta (tiers publico/clave/enlace).