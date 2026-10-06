# STATE — fase actual

**Fase 4 completada (2026-10-05).** Prototipo **EN VIVO** para revisión del dueño: `https://software-tools.pcabrera.com/proto/`. Siguiente: **Fase 5 — app completa** (comentarios, admin, descargas con clave, assets, SEO) tras su feedback.

## Entregado

- ✔️ Fase 0 — hosting verificado (PHP 8.1.34, SQLite 3.53.4 + FTS5, escritura ✓; límites: 2 MB subida / 30 s / 512 MB).
- ✔️ Fase 1 — maquetas de las 3 modalidades + aprobación del dueño (todas, seleccionables; default línea).
- ✔️ Fase 2 — SPEC v3 + ADRs 005–008; specs v2 retiradas; README/AGENTS/CLAUDE actualizados.
- ✔️ Fase 3 — importador + verificador (**PARIDAD OK 48/48**, FTS ✓); `app/data/catalog.sqlite` generada; esqueleto kofro + `.agents/`.
- ✔️ Extra: fichas 28→48 (huérfanas registradas), 60 recíprocos (0 asimetrías), investigación de descargas (48 herramientas).
- ✔️ **Fase 4 — prototipo navegable** (`app/`): PHP 8.1 + SQLite real; home con las **3 modalidades** (selector + `?modo=` + `localStorage`); ficha completa (Markdown→HTML, sucesora, relacionadas, vecinos); búsqueda FTS5 con resaltado + `/api/search` JSON; catálogo por décadas; deploy a `/proto/` verificado (todas las rutas 200, `data/` con deny 403, sin indexar). Revisión visual con nits aplicados (tildes, cabecera móvil, abanico de la cinta, contador de año, cursor CRT).

## Pendiente (por fase)

- **Fase 5:** app completa — comentarios (honeypot + time-trap + rate-limit), **panel admin** (ADR-008), **descargas con clave por software** (ADR-007), assets por herramienta, SEO (sitemap/hreflang/JSON-LD), i18n base.
- **Fase 6:** inglés (revisión del dueño).
- **Fase 7:** deploy a la raíz de `software-tools.pcabrera.com` + retiro de GitHub Pages. **Fase 8:** tag `v3.0.0`.

## Pendientes del dueño

- **Revisar el prototipo**: `software-tools.pcabrera.com/proto/` (probar Cinta / Línea / Máquina, una ficha, la búsqueda) y dar feedback.
- Revisar los **años inferidos** de las 20 fichas nuevas (varias sin `usedUntil` → la interfaz muestra «desde {año}»).
- Compartir la **carpeta de software** para el inventario de descargas (tamaños/hashes/clasificación).
- Íconos/imágenes: extracción de sus copias + capturas (fase de assets).
