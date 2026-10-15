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
- ✔️ **Fase 8 — descargas: colección privada + sitio oficial** (ADR-011, 2026-10-07): taxonomía obra/versión/variante/era+año; **contraseña general** (`settings.downloads_pass_hash`, cookie `st_dl_all` 2 h, rate-limit 5→10 min) reemplaza `download_keys`; visibilidad binaria `clave`/`enlace`; sección **Sitio oficial** pública (referencias verificadas, versión↓→año↓→nombre↑) y **Colección privada oculta** hasta desbloquear (solo chips + tooltip contextual año·era·nº·variantes); orden global versión↓→año↓→variante↑→nombre↑; año de grupo = min(años); streamer 403 para todo lo alojado sin cookie; registro manual `no_file` + FTP (límite 2 MB); `ALLOWED_EXT` +`sh`,`nbm`; NetBeans piloto: 44 archivos/6.8 GB en vivo, 26 grupos + Sin versión; inventario `ops/tools/downloads_inventory.py` → `ops/reports/downloads-inventory.json`; fix 500 del resumen admin (`keyHash` eliminada → `dlHasPass`); fix `locales_dir` en hosting (traducciones renderizaban claves); **E2E 61/61**.
- ✔️ **Lote `_diagram` (2026-10-15)** — herramientas de pensamiento visual y diagramación: 7 fichas nuevas (Axon Idea Processor, Balsamiq Mockups, MindManager, PersonalBrain, TheBrain 8, UMLet, System Mechanic) → catálogo **57**; 7 binarios (371 MB) subidos a `downloads/` (7/7 verificados) + 27 filas del lote (7 alojadas + 20 referencias «Sitio oficial»: sitio/primera/última versión; total **91**); read-back admin + gate 403 + sweep desbloqueado OK; **E2E 66/66**; años de primer uso alineados a la ventana declarada 1996–2004 (estimados, commit `aa730ed`). Rotación de la pass admin de producción (nueva en BWS `H_SOFTWARE_TOOLS_ADMIN_PASS`; `app_secret` en `H_SOFTWARE_TOOLS_APP_SECRET`); config vivo respaldado como `server/config.local.php.bak-2026-10-15`.
- ✔️ **Maqueta 002 — mapa mental (2026-10-15)**: tres físicas navegables (plex radial / dendrita / constelación) con los requisitos del dueño (primer anillo = categorías, todo en una página, búsqueda que navega, clic en nodo/resultado → panel de ficha). Datos reales del catálogo (6 categorías, 57 fichas) vía `tools/prep_data.py` → `data.js`. Código en `sketches/002-mapa-mental/` (commit `dcc26c6`); desplegada para revisión en `/proto/sketches/002-mapa-mental/`. Verificación: DOM checks en navegador (panel/búsqueda/navegación), capturas headless y rondas de revisión visual (halo en labels, anti-solape, `?static=1`).

## Pendiente

- **UI única (mapa mental)**: el dueño elige la física; al elegir, producción = un modo mapa sobre los datos existentes (las páginas `/tools/<slug>` se conservan; las 3 modalidades actuales quedan congeladas). 
- Nuevo alert Dependabot **#65 `sprintf-js` (medium, CVE-2026-97058)** — triage pendiente (dependencia de tooling de build).
- ✔️ **GitHub Pages retirado** por el dueño (2026-10-15): `cazucito.github.io/software-tools` responde 404 — verificado. (La API de Pages aún lista el recurso; presumiblemente caché.)
- **Revisión del dueño**: años de primer uso (borradores) de las 7 fichas `_diagram`; confirmar si System Mechanic (utilidad de sistema, no diagramación) se queda en la colección.
- **Comentarios deshabilitados** en el sitio (2026-10-09, `comments.enabled=false` por pedido del dueño: spam/seguridad) hasta definir políticas y mecanismos; el e2e los prueba con override de desarrollo en config.local.php.
- **Fase 6 (inglés completo)** de ficha/sistema; base i18n lista.
- **Assets reales** (ADR-010, nivel 1): ~20–30 íconos icónicos cuando comparta su carpeta; los monogramas por década ya son la identidad del resto.
- Descargas: inventario de su carpeta (tiers publico/clave/enlace).