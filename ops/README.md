# ops/ — operación

```
ops/
├── deploy/    → deploy.py (Fase 7; ftplib + BWS; SOLO complementa, nunca borra)
├── tests/     → smoke/e2e (lint PHP, curl, paridad)
├── tools/     → importador Markdown→SQLite + verificador de paridad  ← YA FUNCIONANDO
└── reports/   → reportes generados (paridad, QA)
```

## Recetas actuales

```bash
# Regenerar la base de datos desde las fichas
python3 ops/tools/import_catalog.py

# Verificar paridad fuente ↔ BD (exit 0 = OK; reporte en ops/reports/)
python3 ops/tools/verify_parity.py
```

Ver también [AGENTS.md](../AGENTS.md) (flujo de contenido completo) y [SPEC v3 §8](../docs/SPEC.md) (operación).
