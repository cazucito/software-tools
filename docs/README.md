# Documentación — software-tools

Índice de la documentación vigente. Convención: specs y ADRs en español (idioma del repo); commits y código en inglés.

## Documentos vigentes

- **[SPEC.md](SPEC.md)** — Especificación funcional y técnica de la **v3** (aplicación PHP + SQLite en `software-tools.pcabrera.com`). Documento canónico.
- **[adr/](adr/)** — Decisiones de arquitectura. Serie vigente (v3): [005](adr/005-arquitectura-v3.md) arquitectura, [006](adr/006-modalidades.md) modalidades, [007](adr/007-descargas.md) descargas, [008](adr/008-admin.md) panel admin. *(La serie 001–004 pertenece a la era v2: vive en la historia de git y en el tag `v2.0.0`.)*
- **[plans/](plans/)** — Propuestas y planes de trabajo. [Plan v3](plans/2026-10-05-v3-propuesta-arquitectura.md) (v1.3) es el documento de fases y decisiones del dueño.
- **[notes/](notes/)** — Investigaciones de apoyo: [fichas pendientes](notes/2026-10-05-fichas-pendientes.md), [descargas-estado](notes/2026-10-05-descargas-estado.md).

## Herramientas relacionadas

- Importador y verificación: `ops/tools/` (Markdown → `app/data/catalog.sqlite` + reporte de paridad en `ops/reports/`).
- Continuidad para agentes: `.agents/` y [AGENTS.md](../AGENTS.md) en la raíz.
