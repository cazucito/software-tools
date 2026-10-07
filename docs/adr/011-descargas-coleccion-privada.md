# ADR-011 — Descargas: colección privada y sitio oficial

**Estado:** aceptado (2026-10-07). **Contexto:** el propósito del sitio no es
ser un archivo de versiones: la ficha debe orientar al visitante hacia la
**fuente oficial** y, aparte, el dueño conserva una **colección privada** de
instaladores (piloto: NetBeans, 44 archivos / 6.8 GB) que solo él y quien
conozca la contraseña pueden descargar. El hosting compartido impone límites
reales: subida directa de 2 MB, respuestas PHP largas (~240 s), sin Range en el
streamer.

## Decisión: el catálogo guía; la colección es privada

- **Dos secciones en cada ficha con descargas:**
  1. **Sitio oficial** (pública): referencias a la fuente oficial por versión
     (solo las adecuadas y verificadas; nada inventado). Orden: versión ↓ →
     año ↓ → nombre ↑.
  2. **Colección privada** (oculta por defecto): no se renderizan filas hasta
     desbloquear con la **contraseña general** (cookie firmada 2 h, rate-limit
     5 intentos → 10 min). Antes del desbloqueo solo se muestran la barra de
     chips de versiones y un **tooltip contextual** por chip (año · era ·
     nº de archivos · variantes). Tras desbloquear, la lista completa con
     grupos.

## Decisión: taxonomía de cuatro ejes (obra / versión / variante / era) + año

- **Obra** = la ficha (una por software). **Versión** = release (capítulo/grupo).
  **Variante** = edición/pack (bundle JDK, Enterprise Pack, …). **Era** =
  vendor (Sun / Oracle / Apache), automática por versión (`st_era()`).
  **Año** del grupo = año mínimo de sus filas (año de lanzamiento de la
  versión).
- El archivo físico **nunca** define la ficha: la fila de `downloads` declara
  `version` / `variant` / `year`; la ficha decide a qué grupo pertenece.
- **Orden global**: versión ↓ → año ↓ → variante ↑ → nombre ↑ (criterio único,
  sin excepciones por ficha).

## Decisión: acceso con contraseña general y visibilidad binaria

- `settings.downloads_pass_hash` (argon2id, mín. 8 caracteres) reemplaza la
  clave por software (`download_keys` eliminada del schema). Una sesión
  desbloqueada vale para **todo** el sitio.
- Visibilidad reducida a 2 estados: **`clave`** = «Alojado (con contraseña)»
  (normaliza cualquier valor antiguo) y **`enlace`** = «Solo enlace oficial»
  (no se sube archivo; `size=1`, `sha256=0…0`).
- El streamer bloquea con **403 todo lo alojado** sin cookie (incluidos los
  registros que antes eran «públicos»); los `enlace` no tienen `download/`.
- Subida directa máxima 2 MB (límite del hosting); archivos grandes →
  **registro manual** (`no_file=1`) + FTP a `downloads/<slug>/`. `ALLOWED_EXT`
  incluye `sh` y `nbm`.

## Decisión: resiliencia (inventario de respaldo)

- `ops/tools/downloads_inventory.py` exporta el catálogo de descargas (filas,
  sha256, licencias, visibilidad) desde el panel admin a
  `ops/reports/downloads-inventory.json`, sin tocar datos.
- Los originales en `incoming/` se borraron **solo después** de verificar
  byte-a-byte (sha256) contra el servidor (41/41 + variantes 3/3); el
  inventario permite re-obtener y verificar cualquier archivo perdido.
- **Range/reanudación de descarga: diferido** (feature futura) por límites del
  hosting; no bloquea el alcance actual.

## Consecuencias

- Migración `ALTER TABLE downloads` (+`version`, `variant`, `year`); tabla
  `settings(k,v)`; locales `downloads.*` nuevos (es/en).
- Panel admin: fieldset «Contraseña general de descargas», selects binarios,
  campos de año; resumen por herramienta usa `dlHasPass` (se corrigió un 500
  heredado del refactor que llamaba a `Ops::keyHash()` — eliminada).
- E2E 61/61 incluye: ficha bloqueada sin grupos/archivos, desbloqueo global,
  streamer 403/200, chips con tooltip context.
- Alcance: mecanismo **general por herramienta** (NetBeans como piloto con
  datos reales; las demás fichas lo usan cuando tengan descargas).

## Alternativas descartadas

- Archivo público de versiones: contradice el propósito del sitio.
- Clave individual por software: fricción y mantenimiento innecesarios.
- Pestañas por versión: peor UX con 26 grupos; se eligieron chips con anclas
  y contadores.
- Descarga pública sin contraseña: la colección es del dueño, no del público.