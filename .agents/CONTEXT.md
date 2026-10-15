# CONTEXT — software-tools

**Qué es:** archivo personal del software usado por cazucito. **58 fichas** (1991 → hoy), cada una con historia de uso. El Markdown es la fuente de autoría.

**Dónde vive:**
- Repo: `cazucito/software-tools` (público) · local: `/mnt/thoth/projects/software-tools`.
- Producción: `https://software-tools.pcabrera.com` (PHP + SQLite; home = mapa dendrita, UI única).
- Generaciones anteriores del sitio: tag `archive/pre-mapa-2026-10-15`.

**Arquitectura v3 (decidida):** PHP 8.1 + SQLite (FTS5 ✓) en hosting LiteSpeed; estructura kofro (`app/ ops/ docs/ .agents/`); `content/` (fichas + taxonomía) → importador → `catalog.sqlite`; front server-rendered + home como **mapa mental dendrita** (JS vanilla, UI única — ADR-012; sin modalidades); i18n es/en; comentarios propios; **descargas con clave individual por software** (personal-first, sin subidas); assets íconos/logos/covers; **panel admin** (catálogo+descargas+comentarios, con export a Markdown).

**Documentos rectores:** [`docs/SPEC.md`](../docs/SPEC.md) · ADRs [005](../docs/adr/005-arquitectura-v3.md)-[012](../docs/adr/012-ui-unica-mapa.md) · [plan v3](../docs/plans/2026-10-05-v3-propuesta-arquitectura.md).
