# software-tools

Archivo personal del software: **58 herramientas, 1991 → hoy** — cada una con su historia de uso. La home es un **mapa mental** navegable (categorías → familias → herramientas) con búsqueda; cada ficha puede traer descargas personales.

## Estado

- **EN PRODUCCIÓN:** `software-tools.pcabrera.com` — aplicación **PHP 8.1 + SQLite**: mapa mental dendrita como home ([ADR-012](docs/adr/012-ui-unica-mapa.md)), búsqueda FTS5, catálogo, fichas, descargas tras contraseña general y panel admin.
- Generaciones anteriores (sitios estáticos anteriores, las tres modalidades) viven solo en la historia de git y en el tag `archive/pre-mapa-2026-10-15`.

## Estructura

| Ruta | Qué es |
|---|---|
| `content/` | **Lo autorado**: `tools/*.md` (las fichas, fuente de autoría de todo) y `taxonomy.json` (familias, etiquetas y colores de categoría del mapa) |
| `app/` | La aplicación: `public/` (docroot), `server/` (PHP), `data/` (`catalog.sqlite` generado) |
| `ops/` | Pipeline: importador, verificación de paridad, deploy, tests, reportes |
| `docs/` | [SPEC](docs/SPEC.md), [ADRs](docs/adr/), planes, notas |
| `.agents/` | Continuidad para agentes de IA |
| `sketches/` | Maqueta de concepto 002 (mapa mental), origen de la UI actual |

## Trabajar aquí

```bash
# Contenido (fichas + taxonomía) → base de datos: el ÚNICO paso de build (sin npm)
python3 ops/tools/import_catalog.py      # regenera app/data/catalog.sqlite
python3 ops/tools/verify_parity.py       # verifica que nada se perdió (PARIDAD OK)

# Local (PHP 8.1 + SQLite)
docker run --rm -d --name st-proto -p 127.0.0.1:8091:8091 -v "$PWD":/srv -w /srv php:8.1-cli php -S 0.0.0.0:8091 -t app/public
rm -f app/data/ops.sqlite && bash ops/tests/e2e.sh   # e2e (borra el ops.sqlite local: rate-limit de login)

# Deploy a producción (catálogo con el patrón .uploading + RENAME — ver ops/README.md)
python3 ops/deploy/deploy.py --root
```

Guía completa para agentes: [AGENTS.md](AGENTS.md) · Índice de documentación: [docs/README.md](docs/README.md).

*Convenciones: commits en inglés (Conventional Commits) · documentación en español · slugs estables · nada se pierde.*
