# ADR-007 — Descargas: clave individual por software, sin subidas públicas

**Estado:** aceptada (2026-10-05) · **Detalle:** [SPEC v3 §6.6](../SPEC.md) · Investigación: [descargas-estado](../notes/2026-10-05-descargas-estado.md)

## Contexto

El dueño posee la mayoría del software del catálogo y quiere poder **descargarlo él mismo desde cualquier lugar** («uso personal primero»); lo que sea redistribuible, además, se ofrece público. Requisitos explícitos: **clave individual por software**, **considerar las rutas de los archivos desde el diseño**, y **no permitir subidas de visitantes** (no es el propósito del sitio). Hosting: PHP con límite de subida de 2 MB; FTP sin ese límite.

## Decisión

1. **Tabla `downloads`** (tool_id, filename, bytes, sha256, visibility, license_note, source_url) + los archivos en `downloads/<slug>/` dentro del docroot, **protegida con deny** — nunca hay URL directa a un archivo.
2. **Rutas estables servidas por PHP**: `GET /download/<slug>/<archivo>` (streamer con `Content-Disposition: attachment` y `nosniff`). La ficha muestra tamaño y **sha256** de cada archivo.
3. **Tres niveles**: `publico` (solo redistribuible, con nota de licencia), `clave` (copia personal / licencia gris) y `enlace` (la ficha apunta a la fuente oficial).
4. **Clave individual por software**: hash **argon2id** en `download_keys` (una por herramienta), definida desde el panel admin. Al validarla se abre una **sesión corta para esa herramienta** (cookie firmada, TTL corto) y se desbloquean sus archivos; **rate-limit** contra fuerza bruta.
5. **Sin subidas públicas** (regla explícita del proyecto). Sube el dueño: desde el **admin** (fragmentado en chunks de ~1.5 MB, patrón kofro, para rebasar el límite PHP) o por **FTP** para los archivos grandes (vía agente).
6. **Regla legal (oro)**: alojar solo lo redistribuible; ante duda, nivel 3 (enlace). Nota de licencia/origen por archivo.

## Alternativas consideradas

- **Clave única global**: rechazada por el dueño (quiere individual por software).
- **Cuentas de usuario/login público**: innecesario; más superficie de ataque.
- **URLs directas a los archivos**: descartado (hotlinking, privacidad, control).
- **Subidas públicas**: excluido por el dueño desde el inicio.

## Consecuencias

- ✔️ Descarga personal desde cualquier lugar con una clave por herramienta; lo público, público.
- ⚠️ Los archivos de descargas son datos vivos → entran al respaldo periódico (snapshot del SQLite + copia de `downloads/`; ver SPEC §8).
- ⚠️ Vigilar la **cuota de disco** del hosting antes de poblar; los archivos grandes pueden requerir backup alterno.
