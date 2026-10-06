# app/data/ — datos generados

- `catalog.sqlite` — **artefacto derivado** (no se commitea; ignorado por git).
  - Lo genera: `python3 ops/tools/import_catalog.py` (fuente: `src/content/tools/*.md`).
  - Lo valida: `python3 ops/tools/verify_parity.py` → reporte en `ops/reports/`.
  - Esquema completo: [SPEC v3 §4](../../docs/SPEC.md).

La **fuente de autoría es el Markdown** ([ADR-005](../../docs/adr/005-arquitectura-v3.md)); este directorio se puede reconstruir desde cero en cualquier momento. En producción, este archivo vive en el server (más los datos operativos: comentarios, descargas, claves).
