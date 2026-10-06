# app/ — la aplicación v3

Estructura estilo kofro para la aplicación **PHP + SQLite** que será `software-tools.pcabrera.com` (ver [SPEC v3](../docs/SPEC.md) y [ADR-005](../docs/adr/005-arquitectura-v3.md)).

```
app/
├── public/   → lo desplegable (docroot): index.php, assets/, locales/, js/, downloads/ (protegido)
├── server/   → PHP: lib/ (módulos namespaced), controladores, config.php
└── data/     → catalog.sqlite — GENERADO por ops/tools/import_catalog.py (no se commitea)
```

**Estado (2026-10-05):** `data/` ya contiene la BD real generada y verificada (48 fichas, PARIDAD OK). `public/` y `server/` se construyen en las Fases 4–5.
