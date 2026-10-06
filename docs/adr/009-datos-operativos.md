# ADR-009 — Datos operativos en `ops.sqlite` (separado del catálogo generado)

**Estado:** aceptada (2026-10-05) · **Refina:** [ADR-005](005-arquitectura-v3.md), [ADR-008](008-admin.md) · **Detalle:** [SPEC v3 §4](../SPEC.md)

## Contexto

[ADR-005](005-arquitectura-v3.md) decidió que `catalog.sqlite` es un **artefacto generado** (Markdown → importador) y que la app escribe «solo tablas operativas». Al llegar la Fase 5 (comentarios, descargas, claves, assets, ediciones del admin) surgió el conflicto: un reimport o un redeploy del catálogo **no debe tocar —ni borrar—** los datos operativos, y las referencias por `tool_id` se rompen si el importador reasigna ids.

## Decisión

1. **Dos archivos SQLite** en `app/data/`:
   - `catalog.sqlite` — generado por el importador; **reemplazable**; la app solo lo lee.
   - `ops.sqlite` — **operativo**; lo escribe la app; el importador y el deploy **nunca lo tocan**.
2. Las tablas operativas (`comments`, `downloads`, `download_keys`, `tool_assets`, `rate_events`, `catalog_dirty`, `meta`) viven en `ops.sqlite` y referencian **`tool_slug`** (estable), no ids.
3. `ops.sqlite` **se auto-inicializa**: la app crea su esquema (`CREATE TABLE IF NOT EXISTS`) en la primera escritura. En el hosting vive en `proto/data/` (protegido con deny). `WAL` + `busy_timeout`.
4. Las ediciones del admin al catálogo escriben en `catalog.sqlite` (para verse al instante) y marcan su slug en `catalog_dirty` (ops): el **export a Markdown** es la vía de reconciliación ([ADR-008](008-admin.md)).
5. El despliegue **nunca sube `ops.sqlite`**; el catálogo solo se sube con bandera explícita (para no pisar ediciones vivas sin exportarlas antes).

## Alternativas consideradas

- **Todo en un archivo con tablas preservadas en el reimport**: frágil — cualquier error de merge pierde comentarios; y los ids se reasignan al cambiar el contenido.
- **MySQL**: descartado (ADR-005); SQLite sobra para esta escala.
- **Sin capa operativa** (comentarios en un JSON): sin consultas, sin moderación cómoda; descartado.

## Consecuencias

- ✔️ Reimport y redeploy **seguros**: recomponer el catálogo no puede dañar datos operativos.
- ✔️ Backup claro: `catalog.sqlite` es reconstruible desde git; `ops.sqlite` es pequeño y preciado (entra al respaldo).
- ⚠️ Los JOIN entre catálogo y operaciones se hacen en PHP (por slug) — aceptable a esta escala.
- ⚠️ Antes de actualizar el catálogo en producción habrá que **exportar** las ediciones vivas (documentado en HANDOFF).
