# CLAUDE.md — contexto para Claude

> Las reglas operativas completas están en [AGENTS.md](AGENTS.md). Este archivo resume lo específico del proyecto. SPEC vigente: [docs/SPEC.md](docs/SPEC.md).

## Visión del proyecto

software-tools es mi archivo personal de herramientas utilizadas en más de veinte años de docencia e ingeniería. No es un catálogo genérico: es una **línea de tiempo evolutiva** con historia personal — 48 fichas, de 1991 a hoy.

## Voz y decisiones de contenido

Cada herramienta incluye **contexto personal** — es el valor diferencial; sin él, es solo otro listado de software.

**Incluir:** herramientas usadas de verdad (no solo probadas), con contexto de uso memorable.
**No incluir:** listados exhaustivos, herramientas sin historia personal, contenido genérico de Wikipedia.
**Tono:** personal pero profesional; nostálgico cuando corresponde; crítico constructivo cuando algo falló; celebratorio cuando cambió el workflow.

Al agregar una herramienta: slug único kebab-case; año preciso si se conoce; 3–7 tags; contexto de mínimo 2–3 oraciones; conectar con `related`/`successor` (reciprocidad). Si no hay contexto suficiente, **preguntar en lugar de inventar**.

## Estado actual

- **v2 en vivo** (Astro + GitHub Pages): `https://cazucito.github.io/software-tools`
- **v3 en construcción** (PHP + SQLite + 3 modalidades + descargas + es/en): `https://software-tools.pcabrera.com` (placeholder). Especificación: [docs/SPEC.md](docs/SPEC.md) · ADRs 005–008 · [plan v3](docs/plans/2026-10-05-v3-propuesta-arquitectura.md).
- Flujo de contenido y comandos: [AGENTS.md](AGENTS.md) · Estado/handoff para agentes: [.agents/](.agents/).

## Historia

- **2005–2018**: sitio Moodle con enlaces de descarga para cursos Oracle.
- **2018–2025**: GitHub Pages (Jekyll, en desuso).
- **2025**: rebirth como archivo histórico personal (v2.0.0).
- **2026**: v3 — aplicación PHP + SQLite con timeline y modalidades seleccionables.
