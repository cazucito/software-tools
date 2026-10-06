# AGENTS.md — Guía para agentes de IA

## Proyecto

**software-tools**: archivo personal del software usado por cazucito (**48 fichas, 1991 → hoy**). La **v2** (Astro, GitHub Pages) está **EN VIVO**; la **v3** (PHP + SQLite en `software-tools.pcabrera.com`) está **en construcción**. Hasta el deploy de la v3 (Fase 7), `main` sigue desplegando el sitio v2 — **no romperlo**.

## Fuentes de verdad

- [`docs/SPEC.md`](docs/SPEC.md) + [`docs/adr/005–008`](docs/adr/) — especificación vigente de la v3.
- [`docs/plans/2026-10-05-v3-propuesta-arquitectura.md`](docs/plans/2026-10-05-v3-propuesta-arquitectura.md) — fases y decisiones del dueño.
- **Contenido:** `src/content/tools/*.md` — el Markdown es la **fuente de autoría**.

## Estructura

```
src/            → v2 (Astro) — transitorio hasta Fase 7
  content/tools/*.md   ← LAS FICHAS (no mover todavía)
app/            → v3: public/ (docroot), server/ (PHP), data/ (catalog.sqlite generado)
ops/            → deploy/ · tests/ · tools/ · reports/
docs/           → SPEC, adr/, plans/, notes/
.agents/        → continuidad entre sesiones (estado, handoff)
site/           → página «en construcción» desplegada en la raíz del subdominio
sketches/       → maquetas de concepto (Fase 1; se archivan al cerrar la v3)
```

## Flujo de contenido (agregar o editar una herramienta)

1. Editar/crear `src/content/tools/<slug>.md` (esquema: SPEC §4; cuerpo en Markdown).
2. `python3 ops/tools/import_catalog.py` — regenera `app/data/catalog.sqlite`.
3. `python3 ops/tools/verify_parity.py` — **debe dar PARIDAD OK** (exit 0).
4. `npm run build` — valida que el sitio v2 siga construyendo.
5. Commit convencional (inglés) + push.

## Comandos

```bash
# v2 (Astro — el sitio en vivo)
npm run dev · npm run generate-data · npm run build · npm run preview

# v3 (PHP + SQLite — en construcción)
python3 ops/tools/import_catalog.py     # Markdown → catalog.sqlite
python3 ops/tools/verify_parity.py      # paridad fuente ↔ BD (reporte en ops/reports/)

# deploy v3 (Fase 7): ops/deploy/deploy.py — SOLO complementa, nunca borra
```

## Reglas de oro

1. **El contenido es el valor** — nada se pierde; la paridad es verificable (`verify_parity`).
2. **Sin subidas públicas**; las descargas son del dueño (clave individual por software).
3. **No tocar la lógica viva sin pedido explícito** (v2 está en producción).
4. **Commits en inglés** (Conventional Commits, detallados); **docs/specs en español**.
5. Slugs kebab-case **estables**; año numérico; **contexto personal obligatorio**.
6. PHP objetivo **8.1**; límites del hosting (2 MB de subida → chunking; 30 s).
7. Ante duda, **preguntar antes de asumir**. Calidad de contenido > velocidad.
