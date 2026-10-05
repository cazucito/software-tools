# Fichas pendientes — auditoría del estado actual (28 fichas)

**Fecha:** 2026-10-05 · **Contexto:** indicación del dueño — «la v3 trabaja primero con lo ya registrado; incluso trata de localizar la información faltante». Esta auditoría cubre lo registrado y cataloga lo que falta. Es referencia de [la propuesta v3](../plans/2026-10-05-v3-propuesta-arquitectura.md) (§3, punto 7).

## 1. Resumen

Las **28 fichas** tienen contenido sólido (198–374 palabras, 4–7 secciones cada una; «Contexto personal» presente en 25 de 28). No hay riesgo de pérdida: lo que falta es *completar*, no corregir.

| # | Qué falta | Estado | Acción propuesta |
|---|-----------|--------|------------------|
| 1 | **Imágenes del software** | 27/28 sin imagen; `chi-writer` tiene una **referencia huérfana** (`/images/tools/chi-writer.jpg` nunca existió) | Fase de assets (§2.9 del plan): capturas del dueño + extracción de sus copias |
| 2 | **Íconos/logos** | 0 registrados | Idem — esquema `tool_assets` ya contemplado |
| 3 | **Herramientas referenciadas sin ficha** | **26 slugs colgantes** (salen de `successorSlug`/`related` de las propias fichas) | Ver Anexo A — decidir cuáles se registran |
| 4 | **Relaciones `related` asimétricas** | 24 pares (A→B sin B→A) | Ver Anexo B — completar reciprocidad |
| 5 | **Typos de slugs que rompen enlaces** | `vscode` y `netbeans-8-2` en `intellij-idea.related` (existen `visual-studio-code` y `netbeans`) | Corregir (pasada de datos) |
| 6 | **Estado «en uso hoy»** | 4 fichas (docker, intellij-idea, kubernetes, visual-studio-code) sin `usedUntil`/`successor` — **correcto**, son actuales | Formalizar como estado explícito en el esquema v3 |

## 2. Anexo A — 26 referencias colgantes (candidatas)

Todas provienen de las **propias fichas del dueño**: son herramientas que su historia ya menciona como sucesoras o relacionadas.

### A.1 Herramientas distintas — candidatas a ficha propia (20)

| Slug | Referenciada desde | Rol |
|------|--------------------|-----|
| `microsoft-outlook` | eudora | sucesora |
| `microsoft-excel` | lotus-1-2-3 | sucesora |
| `winzip` | pkzip | sucesora |
| `google-chrome` | mozilla-firefox | sucesora |
| `internet-explorer` | netscape-navigator (+firefox) | sucesora/relacionada |
| `windows-98` | windows-95 | sucesor |
| `virtualbox` | vmware-workstation | sucesora |
| `delphi` | borland-pascal-7 | sucesor |
| `borland-cpp` | turbo-c | sucesor |
| `visual-basic-net` | visual-basic-6 | sucesor |
| `windows-explorer` | norton-commander | sucesor |
| `acronis-true-image` | norton-ghost | sucesora |
| `datagrip` | oracle-sql-developer | sucesora |
| `ms-access` | visual-basic-6 | relacionada |
| `oracle-database` | oracle-sql-developer | relacionada |
| `multisim` | electronic-workbench | sucesor |
| `figlet` | bannermania | sucesora |
| `latex` | chi-writer | sucesora |
| `atom` | visual-studio-code | relacionada |
| `sublime-text` | visual-studio-code | relacionada |

### A.2 Versiones siguientes del mismo software (4) — *no deberían ser ficha aparte*

`maple-6` (de maple-v), `mathematica-3` (de mathematica), `matlab-r14` (de matlab), `quickbasic-4-5` (de qbasic).

→ Propuesta: el esquema v3 formaliza «versión siguiente» como dato de la ficha base (p. ej. `next_version`), no como ficha nueva.

### A.3 Typos de slugs (2) — corregibles ya

| En | Dice | Debería decir |
|----|------|---------------|
| `intellij-idea.related` | `"vscode"` | `"visual-studio-code"` |
| `intellij-idea.related` | `"netbeans-8-2"` | `"netbeans"` |

## 3. Anexo B — 24 relaciones asimétricas (falta la recíproca)

`bannermania → ms-dos-5-1` · `borland-pascal-7 → turbo-c` · `borland-pascal-7 → ms-dos-5-1` · `docker → vmware-workstation` · `electronic-workbench → matlab` · `electronic-workbench → mathematica` · `eudora → windows-95` · `intellij-idea → eclipse` · `kubernetes → vmware-workstation` · `lotus-1-2-3 → ms-dos-5-1` · `lotus-1-2-3 → windows-95` · `mozilla-firefox → netscape-navigator` · `netscape-navigator → windows-95` · `norton-commander → ms-dos-5-1` · `norton-ghost → ms-dos-5-1` · `norton-ghost → norton-commander` · `pkzip → ms-dos-5-1` · `qbasic → ms-dos-5-1` · `qbasic → turbo-c` · `visual-studio-code → intellij-idea` · `visual-studio → visual-basic-6` · `vmware-workstation → windows-95` · `vmware-workstation → norton-ghost` · `windows-95 → ms-dos-5-1`

## 4. Plan de localización de la información faltante

1. **Investigación por herramienta (agente):** estado oficial (activo / descontinuado / liberado), **¿es redistribuible?**, enlaces oficiales y de archivo, datos históricos clave. Una ronda por las 28 + las nuevas → alimenta Descargas (§2.8) y enriquece fichas.
2. **Del dueño:** qué herramientas colgantes registramos (y su mini-contexto personal), capturas de pantalla de cada software en uso, y las copias del software para el inventario de descargas.
3. **Assets:** extracción de íconos desde las copias del dueño + búsqueda de logos oficiales/comunitarios para clásicos.
4. **Pasada de correcciones de datos** (previa confirmación): reciprocidad de los 24 `related` + los 2 typos de slugs + definir «en uso hoy» en las 4 fichas actuales.

## 5. Lo que no se toca sin confirmación

- Los cuerpos de las fichas (contenido editorial) — solo se completan campos vacíos y referencias roscas, con revisión del dueño.
- Nada se elimina: las fichas huérfanas se *añaden*, las versiones se *anotan*.
