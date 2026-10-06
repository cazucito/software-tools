# STATE — fase actual

**Fase 3 completada (2026-10-05).** Siguiente: **Fase 4 — prototipo local navegable** (Docker php:8.1 + SQLite): home-timeline con modalidades, ficha, búsqueda FTS.

## Entregado

- ✔️ Fase 0 — hosting verificado (PHP 8.1.34, SQLite 3.53.4 + FTS5, escritura ✓; límites: 2 MB subida / 30 s / 512 MB).
- ✔️ Fase 1 — maquetas de las 3 modalidades + aprobación del dueño (todas, seleccionables; default línea).
- ✔️ Fase 2 — SPEC v3 + ADRs 005–008; specs v2 retiradas; README/AGENTS/CLAUDE actualizados.
- ✔️ Fase 3 — importador + verificador (**PARIDAD OK 48/48**, FTS ✓); `app/data/catalog.sqlite` generada; esqueleto kofro + `.agents/`.
- ✔️ Extra: fichas 28→48 (huérfanas registradas), 60 recíprocos (0 asimetrías), investigación de descargas (48 herramientas).

## Pendiente (por fase)

- **Fase 4:** prototipo local (timeline 3 modos, ficha, búsqueda) → revisión del dueño.
- **Fase 5:** app completa (comentarios, admin, descargas con clave, assets, SEO).
- **Fase 6:** inglés (revisión del dueño).
- **Fase 7:** deploy a `software-tools.pcabrera.com` + retiro de GitHub Pages. **Fase 8:** tag `v3.0.0`.

## Pendientes del dueño

- Revisar los **años inferidos** de las 20 fichas nuevas (lista en el reporte de sesión).
- Compartir la **carpeta de software** para el inventario de descargas (tamaños/hashes/clasificación).
- Íconos/imágenes: extracción de sus copias + capturas (fase de assets).
