# software-tools

Archivo personal del software: **50 herramientas, 1991 → hoy** — cada una con su historia de uso. Pronto: un timeline navegable con tres modalidades, búsqueda, comentarios y descargas personales.

## Estado

- **v2 — EN VIVO:** [cazucito.github.io/software-tools](https://cazucito.github.io/software-tools) (sitio estático; se retira al desplegar la v3)
- **v3 — EN CONSTRUCCIÓN:** `software-tools.pcabrera.com` (placeholder público) — aplicación **PHP + SQLite** con timeline (modalidades *cinta / línea / máquina*), búsqueda FTS5, comentarios, descargas con clave por software e idiomas **es/en**.

## Estructura

| Ruta | Qué es |
|---|---|
| `src/content/tools/` | **Las fichas** (Markdown — fuente de autoría de todo) |
| `app/` | v3: `public/` (docroot), `server/` (PHP), `data/` (`catalog.sqlite` generado) |
| `ops/` | Importador, verificación de paridad, deploy, tests, reportes |
| `docs/` | [SPEC](docs/SPEC.md), [ADRs](docs/adr/), planes, notas |
| `.agents/` | Continuidad para agentes de IA |
| `sketches/` | Maquetas de concepto de la Fase 1 (las 3 modalidades) |

## Trabajar aquí

```bash
# Contenido → base de datos (v3)
python3 ops/tools/import_catalog.py      # regenera app/data/catalog.sqlite
python3 ops/tools/verify_parity.py       # verifica que nada se perdió (PARIDAD OK)

# Sitio v2 (el que está en vivo)
npm ci --legacy-peer-deps && npm run dev  # local
npm run build                             # build de producción
```

Guía completa para agentes: [AGENTS.md](AGENTS.md) · Índice de documentación: [docs/README.md](docs/README.md).

*Convenciones: commits en inglés (Conventional Commits) · documentación en español · slugs estables · nada se pierde.*
