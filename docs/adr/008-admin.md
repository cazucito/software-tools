# ADR-008 — Panel de administración (catálogo + descargas + comentarios)

**Estado:** aceptada (2026-10-05) · **Detalle:** [SPEC v3 §6.10](../SPEC.md) · **Relacionada:** [ADR-005](005-arquitectura-v3.md) (Markdown como fuente de autoría)

## Contexto

El dueño preguntó si conviene un modo admin del sitio para editar el catálogo y subir archivos más directamente (sin depender del agente para lo cotidiano). Riesgo central: **deriva de fuentes** — si el admin edita la BD y el repo (Markdown) no se entera, la fuente de autoría (ADR-005) queda desincronizada.

## Decisión

1. **Sí a un panel admin único** (una sola cuenta), con alcance v1:
   - **Catálogo**: crear, editar, despublicar fichas y todos sus campos (incl. tags, relacionadas, sucesoras).
   - **Descargas**: subir archivos (chunked), definir clave por software, visibilidad, nota de licencia, enlace.
   - **Comentarios**: moderación (ver/borrar/marcar spam, aprobar).
2. **Autenticación**: pass única fuerte (argon2id; secreto gestionado en BWS y no en el repo), sesión server-side corta, **rate-limit**, **tokens CSRF**, y bloqueo a rutas admin sin sesión.
3. **Subidas solo desde el admin** (nunca públicas) o por FTP; con fragmentación para el límite de 2 MB del hosting.
4. **Export a Markdown**: el admin ofrece exportar las fichas a formato Markdown (pantalla/descarga) para volcar al repo por commit — así la BD es la **fuente operativa** y el repo conserva su papel de archivo versionado. Reconciliación documentada en la SPEC; la deriva se corrige re-importando cuando haga falta.
5. El admin **no** toca: la estructura del sitio, los modos (solo flags de configuración), ni ejecuta código arbitrario.

## Alternativas consideradas

- **Solo-agente** (estado actual): válido pero deja al dueño dependiente para cada edición cotidiana.
- **CMS completo indexado** (WordPress-like): overkill, pesado, otra superficie de ataque.
- **Escribir directo al repo desde el server**: imposible de forma limpia en hosting compartido (sin git); descartado.
- **Sin export a Markdown**: la deriva se vuelve permanente; descartado.

## Consecuencias

- ✔️ El dueño gana autonomía para el día a día; el agente sigue para lotes, investigación y cambios estructurales.
- ⚠️ Doble fuente temporal (BD ↔ Markdown) **mitigada** por el export + re-importación; se documenta el flujo en SPEC §6.10.
- ⚠️ El admin es la principal superficie de ataque del sitio → hardening listado en SPEC §7 (headers, rate-limit, sin errores verbosos, sin listados de directorio).
