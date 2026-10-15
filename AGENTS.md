# AGENTS.md — Guía para agentes de IA

## Proyecto

**software-tools**: archivo personal del software usado por cazucito (**57 fichas, 1991 → hoy**). La **v3** (PHP + SQLite en `software-tools.pcabrera.com`) está **en producción**; su home es el **mapa mental dendrita** y es la única UI ([ADR-012](docs/adr/012-ui-unica-mapa.md)). La v2 (Astro / GitHub Pages) está retirada: vive en el tag `archive/pre-mapa-2026-10-15`.

## Fuentes de verdad

- [`docs/SPEC.md`](docs/SPEC.md) + [`docs/adr/`](docs/adr/) (005–012) — especificación vigente de la v3.
- [`docs/plans/2026-10-05-v3-propuesta-arquitectura.md`](docs/plans/2026-10-05-v3-propuesta-arquitectura.md) — fases y decisiones del dueño.
- **Contenido:** `src/content/tools/*.md` — el Markdown es la **fuente de autoría** de las fichas; `content/taxonomy.json` lo es de las familias/etiquetas/colores del mapa.

## Estructura

```
content/        → taxonomy.json (taxonomía autorada del mapa)
src/            → legado v2 (Astro) — solo `content/tools/*.md` (LAS FICHAS) sigue vivo
app/            → v3: public/ (docroot), server/ (PHP), data/ (catalog.sqlite generado)
ops/            → deploy/ · tests/ · tools/ · reports/
docs/           → SPEC, adr/, plans/, notes/
.agents/        → continuidad entre sesiones (estado, handoff)
site/           → legado (página «en construcción»)
sketches/       → maquetas de concepto (002: mapa mental, origen de la UI)
```

## Flujo de contenido (agregar o editar una herramienta)

1. Editar/crear `src/content/tools/<slug>.md` (esquema: SPEC §4; cuerpo en Markdown).
   Si cambia una categoría/familia: editar `content/taxonomy.json` (el importador valida y aborta ante slugs desconocidos/duplicados).
2. `python3 ops/tools/import_catalog.py` — regenera `app/data/catalog.sqlite` (incluye familias, etiquetas y colores del mapa).
3. `python3 ops/tools/verify_parity.py` — **debe dar PARIDAD OK** (exit 0).
4. Commit convencional (inglés) + push.

## Comandos

```bash
# v3 (PHP + SQLite)
python3 ops/tools/import_catalog.py     # fichas + taxonomía → catalog.sqlite (único paso de build)
python3 ops/tools/verify_parity.py      # paridad fuente ↔ BD (reporte en ops/reports/)

# deploy: ops/deploy/deploy.py — SOLO complementa, nunca borra
# e2e local: rm -f app/data/ops.sqlite && bash ops/tests/e2e.sh   # 0 fallos esperados
```

## Reglas de oro

1. **El contenido es el valor** — nada se pierde; la paridad es verificable (`verify_parity`).
2. **Sin subidas públicas**; las descargas son del dueño (clave individual por software).
3. **No tocar la lógica viva sin pedido explícito** (la v3 está en producción).
4. **Commits en inglés** (Conventional Commits, detallados); **docs/specs en español**.
5. Slugs kebab-case **estables**; año numérico; **contexto personal obligatorio**.
6. PHP objetivo **8.1**; límites del hosting (2 MB de subida → chunking; 30 s).
7. Ante duda, **preguntar antes de asumir**. Calidad de contenido > velocidad.
