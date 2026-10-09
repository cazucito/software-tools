# CONTEXT — software-tools

**Qué es:** archivo personal del software usado por cazucito. **50 fichas** (1991 → hoy), cada una con historia de uso. El Markdown es la fuente de autoría.

**Dónde vive:**
- Repo: `cazucito/software-tools` (público) · local: `/mnt/thoth/projects/software-tools`.
- v2 (en vivo): `https://cazucito.github.io/software-tools` (Astro; se retira al desplegar v3).
- v3 (en construcción): `software-tools.pcabrera.com` — placeholder público + `/conceptos/` (maquetas).

**Arquitectura v3 (decidida):** PHP 8.1 + SQLite (FTS5 ✓) en hosting LiteSpeed; estructura kofro (`app/ ops/ docs/ .agents/`); Markdown → importador → `catalog.sqlite`; front server-rendered + JS vendored (GSAP/Lenis); **3 modalidades** seleccionables (cinta/línea/máquina; default **línea**); i18n es/en; comentarios propios; **descargas con clave individual por software** (personal-first, sin subidas); assets íconos/logos/covers; **panel admin** (catálogo+descargas+comentarios, con export a Markdown).

**Documentos rectores:** [`docs/SPEC.md`](../docs/SPEC.md) · ADRs [005](../docs/adr/005-arquitectura-v3.md)-[008](../docs/adr/008-admin.md) · [plan v3](../docs/plans/2026-10-05-v3-propuesta-arquitectura.md).
