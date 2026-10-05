# Plan de acción — software-tools v3 (propuesta)

**Estado:** Propuesta · borrador para revisión — 2026-10-05 · *pendiente de tu aprobación antes de escribir SPEC v3 y código*
**Cambio de arquitectura:** salir de GitHub Pages → aplicación con base de datos en `software-tools.pcabrera.com`, con el **timeline** como eje del producto.

---

## 1. Objetivo

1. Migrar el catálogo a una arquitectura con **base de datos (SQLite)** servida dinámicamente desde hosting compartido.
2. Reorientar la experiencia completa alrededor de un **timeline**: la línea de 20+ años de software usado como navegación principal y metáfora del sitio.
3. Mantener paridad de contenido (28 fichas) y ganar capacidades: búsqueda real (SQLite FTS5), consultas por épocas, relaciones entre herramientas, futura edición.

## 2. Estado de partida (ya verificado)

- **v2.0.0 congelada** como punto de restauración (tag en el repo; sitio actual en https://cazucito.github.io/software-tools/).
- **Subdominio `software-tools.pcabrera.com`** ya resuelve al hosting compartido (nginx; 403 mientras el docroot esté vacío — comportamiento esperado).
- **Credenciales FTP listas en BWS**: `software-tools_FTP_SERVER / PORT / USERNAME / PASSWORD`.
- Fuente de contenido actual: Markdown + frontmatter en `src/content/tools/` → `tools.json` ([ADR-003](../adr/003-modelo-de-datos.md)).
- Documentos vigentes: [SPEC v2](../SPEC.md) · [ADR-002](../adr/002-stack-tecnologico.md) · [ADR-003](../adr/003-modelo-de-datos.md) · [ADR-004](../adr/004-búsqueda-y-comentarios.md).

## 3. Arquitectura propuesta (a aprobar)

**Stack:** PHP 8 + SQLite (mismo patrón probado del ecosistema: kofro, glunoto). Sin frameworks: render server-side + JavaScript progresivo mínimo.

- **Datos — evolución de ADR-003 (dual-source → triple):** el Markdown sigue siendo la **fuente de verdad por autoría** (versionado en git, editable por agentes); un **importador** (`ops/import/`) genera **`catalog.sqlite`** (artefacto derivado, no se commitea); la app PHP lo consume en vivo.
  - Búsqueda con **FTS5** si el hosting lo permite (se confirma en Fase 0); fallback: búsqueda simple sobre SQLite.
- **Frontend:** se conserva la identidad (tema oscuro slate, Inter) construida con **Tailwind 4** (build local → assets estáticos; ya migramos la toolchain). Interactividad del timeline con JS vanilla (sin librerías pesadas; se evalúa en concepto).
- **Estructura del repo (patrón kofro):** `app/` (bundle desplegable: PHP + assets compilados + `data/`), `ops/` (importador, `deploy.py`, sondas, tests), `docs/` (esta carpeta). El código Astro de v2 queda accesible en el tag `v2.0.0`.
- **Despliegue:** FTP con `ops/deploy.py` propio (resuelve `software-tools_FTP_*` vía BWS — patrón estándar del ecosistema; verificación por tamaños + sha). GitHub sigue siendo la fuente del **código**; ya no publica el sitio.
- **Retiro de GitHub Pages:** se reemplaza por una página final de aviso/enlace al nuevo dominio y se desactiva el workflow (decisión abierta #5).

### Esquema SQLite propuesto (se refina en SPEC v3)

```sql
-- Núcleo (espejo del frontmatter actual)
tools(id PK, slug UNIQUE, name, year, used_until, category, context, image,
      successor_slug, published, updated_at)
-- Clasificación
categories(id PK, slug UNIQUE, name)
tags(id PK, name UNIQUE)
tool_tags(tool_id FK, tag_id FK, PK(tool_id, tag_id))
tool_relations(tool_id FK, related_tool_id FK, PK(...))
-- Búsqueda
tools_fts(FTS5: name, context, tags)  -- contentless/linked table
-- Timeline (derivable de year/used_until; "eras" solo si se aprueban)
eras(id PK, label, start_year, end_year, sort)   -- ej. "MS-DOS", "Docencia", "Web" (opcional)
```

## 4. El timeline como eje (requiere decisión de concepto)

El sitio dejará de ser "catálogo con un timeline" para ser **el timeline**. Antes de diseñar detalles se define **una metáfora visual fuerte** (acuerdo explícito). Direcciones a explorar (2–3 maquetas desechables):

- **A — "Línea de vida":** el timeline como biografía profesional; scroll horizontal por décadas, cada herramienta un tramo inicio→fin; hitos.
- **B — "Sucesiones":** visualizar las cadenas de reemplazo (Eudora → Outlook, …) como ríos/linajes que se bifurcan.
- **C — "Estratos / archivo":** épocas como capas sedimentarias; tono museo-arqueológico, encaja con el "archivo histórico".

Y una pieza de interacción sugerida: un **scrubber de años** ("en 1997 usabas: …").

## 5. Fases del plan de acción

| # | Fase | Entregable | Criterio de salida |
|---|------|-----------|--------------------|
| 0 | **Verificación del hosting** | sonda PHP subida por FTP (versión PHP, `pdo_sqlite`, FTS5, límites); docroot confirmado; SSL activo | Informe de sonda ✔ |
| 1 | **Concepto y metáfora** (con el usuario) | 2–3 maquetas de concepto + dirección elegida | **Tu OK** |
| 2 | **SPEC v3 + ADR-005** | esquema de BD definitivo, rutas, contratos, criterios de aceptación | **Tu OK** |
| 3 | **Migración de datos** | importador markdown → `catalog.sqlite` + validación (28 fichas, integridad, duplicados) | Script + DB de prueba ✔ |
| 4 | **Prototipo local** (Docker php + sqlite) | home-timeline + ficha + búsqueda con datos reales | **Tu revisión del prototipo** |
| 5 | **Implementación completa** | app v3 completa (todas las secciones) | Tests + paridad de contenido ✔ |
| 6 | **Despliegue y verificación en vivo** | sitio en `software-tools.pcabrera.com` + retiro de GitHub Pages | Deploy verificado (FTP + tu navegador) ✔ |
| 7 | **Cierre** | tag `v3.0.0` bien documentado + docs-as-built + actualización de registros (_index) | Tag publicado ✔ |

**Nota de verificación:** el hosting de `pcabrera.com` responde a clientes automáticos con una página anti-bot; la verificación en vivo combinará comprobaciones por FTP + tu confirmación visual en un navegador real.

## 6. Decisiones abiertas (necesito tu respuesta)

1. ¿Stack **PHP 8 + SQLite** en el hosting — de acuerdo? (se confirma en Fase 0).
2. Timeline: ¿desarrollo **2–3 direcciones de concepto con maquetas** para elegir, o tienes una idea/metáfora en mente?
3. Autoría de contenido en v3: ¿seguimos en **Markdown + git** (recomendado) o quieres **edición directa en la BD**? ¿Panel de administración ahora o después?
4. ¿Se mantienen los **comentarios (Giscus)** en v3? (depende de GitHub; alternativas: quitarlos o sistema propio).
5. ¿Qué hacemos con `cazucito.github.io/software-tools`? Opciones: **(a)** página final de aviso/enlace + retirar workflow (recomendado), **(b)** dejarlo congelado tal cual, **(c)** redirección.
6. ¿El sitio sigue **solo en español**? ¿Incluimos analytics/SEO desde el inicio?

## 7. Riesgos y mitigaciones

- **Sin FTS5 o sin PDO_SQLite en el hosting** → fallback de búsqueda simple; confirmar en Fase 0 antes de decidir el diseño de búsqueda.
- **Bot-check del hosting** dificulta la verificación automática → verificación por FTP + navegador humano (ya contemplado).
- **Retiro de GitHub Pages** deja la URL vieja sin contenido → mitigar con página de aviso y conservar `v2.0.0` restaurable.
- **Espejo de validación md↔BD** puede desincronizarse → el importador valida esquema y falla ruidosamente; la BD nunca se edita a mano en v3.0.

## 8. Lo que NO cambia

- Contenido e idioma (español), slugs estables, tema oscuro y tono del catálogo.
- El repo en GitHub como fuente del código; el flujo de agentes (AGENTS.md, docs/).
- ADR-003: Markdown como fuente de autoría (el importador es su evolución natural).

---

*Siguiente paso al aprobar: Fase 0 (sonda de hosting) + Fase 1 (concepto).*
