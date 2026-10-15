# ops/ — operación

```
ops/
├── deploy/    → deploy.py (ftplib + BWS; SOLO complementa, nunca borra)
├── tests/     → smoke/e2e (lint PHP, curl, paridad)
├── tools/     → importador Markdown→SQLite + verificador de paridad
└── reports/   → reportes generados (paridad, QA)
```

## Recetas actuales

```bash
# Regenerar la base de datos desde las fichas
python3 ops/tools/import_catalog.py

# Verificar paridad fuente ↔ BD (exit 0 = OK; reporte en ops/reports/)
python3 ops/tools/verify_parity.py

# e2e local (con el server de AGENTS.md arriba; borra el ops.sqlite local)
rm -f app/data/ops.sqlite && bash ops/tests/e2e.sh        # 0 fallos

# Deploy (credenciales BWS en runtime)
python3 ops/deploy/deploy.py --root                       # producción; catálogo: patrón .uploading + RENAME
```

Ver también [AGENTS.md](../AGENTS.md) (flujo de contenido completo) y [SPEC v3 §8](../docs/SPEC.md) (operación).
