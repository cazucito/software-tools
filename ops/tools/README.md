# ops/tools/ — importación y verificación

- **`import_catalog.py`** — importador Markdown → SQLite.
  `python3 ops/tools/import_catalog.py [--source src/content/tools] [--db app/data/catalog.sqlite]`
  Idempotente: recrea el contenido de la BD en cada corrida.
- **`verify_parity.py`** — verificación de que nada se perdió entre la fuente (Markdown) y el espejo (SQLite): campos por ficha, checksums, referencias, simetría de `related`, FTS.
  `python3 ops/tools/verify_parity.py` → reporte a `ops/reports/`.

Reglas: la BD **nunca** se edita a mano (se regenera); los 4 slugs de «versión siguiente» (`maple-6`, `mathematica-3`, `matlab-r14`, `quickbasic-4-5`) se modelan como `next_version` de su ficha base.
