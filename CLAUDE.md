# CLAUDE.md — contexto para Claude

> Las reglas operativas completas están en [AGENTS.md](AGENTS.md). Este archivo resume lo específico del proyecto. SPEC vigente: [docs/SPEC.md](docs/SPEC.md).

## Visión del proyecto

software-tools es mi archivo personal de herramientas utilizadas en más de veinte años de docencia e ingeniería. No es un catálogo genérico: es un **mapa evolutivo** (categorías → familias → herramientas, 1991 → hoy) con historia personal — 57 fichas.

## Voz y decisiones de contenido

Cada herramienta incluye **contexto personal** — es el valor diferencial; sin él, es solo otro listado de software.

**Incluir:** herramientas usadas de verdad (no solo probadas), con contexto de uso memorable.
**No incluir:** listados exhaustivos, herramientas sin historia personal, contenido genérico de Wikipedia.
**Tono:** personal pero profesional; nostálgico cuando corresponde; crítico constructivo cuando algo falló; celebratorio cuando cambió el workflow.

Al agregar una herramienta: slug único kebab-case; año preciso si se conoce; 3–7 tags; contexto de mínimo 2–3 oraciones; conectar con `related`/`successor` (reciprocidad). Si no hay contexto suficiente, **preguntar en lugar de inventar**.

## Estado actual

- **v3 en producción** (PHP + SQLite; home = mapa dendrita, UI única — [ADR-012](docs/adr/012-ui-unica-mapa.md)): `https://software-tools.pcabrera.com`. Especificación: [docs/SPEC.md](docs/SPEC.md) · ADRs 005–012 · [plan v3](docs/plans/2026-10-05-v3-propuesta-arquitectura.md).
- v2 (Astro + GitHub Pages) **retirada**; vive en el tag `archive/pre-mapa-2026-10-15`.
- Flujo de contenido y comandos: [AGENTS.md](AGENTS.md) · Estado/handoff para agentes: [.agents/](.agents/).

## Historia

- **2005–2018**: sitio Moodle con enlaces de descarga para cursos Oracle.
- **2018–2025**: GitHub Pages (Jekyll, en desuso).
- **2025**: rebirth como archivo histórico personal (v2.0.0).
- **2026**: v3 — aplicación PHP + SQLite; home como mapa mental dendrita (UI única; las tres modalidades iniciales se retiraron).
