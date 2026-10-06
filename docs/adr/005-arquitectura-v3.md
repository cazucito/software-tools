# ADR-005 — Arquitectura de la v3: PHP + SQLite en el subdominio

**Estado:** aceptada (2026-10-05) · **Sustituye a:** ADR-002 y ADR-003 (era v2; preservados en la historia de git y el tag `v2.0.0`) · **Refina:** ADR-001 (visión del archivo, era v2) · **Detalle:** [SPEC v3](../SPEC.md)

## Contexto

La v2 (sitio estático generado con Astro en GitHub Pages) llegó a su límite técnico para lo que viene: búsqueda real, comentarios, descargas con clave, panel de administración e internacionalización. El subdominio `software-tools.pcabrera.com` fue verificado en Fase 0: **PHP 8.1.34 (LiteSpeed)**, **PDO SQLite + FTS5 funcionando**, escritura en disco ✓, con límites de hosting compartido (subida PHP 2 MB, ejecución 30 s, memoria 512 MB).

## Decisión

1. **Aplicación PHP 8.1 (compatible) renderizada en servidor + SQLite** como única base de datos. Sin frameworks PHP ni dependencias externas.
2. **Frontend con JS progresivo**: HTML semántico desde PHP (SEO primero) + capa de experiencia con librerías vendored (GSAP + ScrollTrigger + Lenis) y módulos ES, servidas localmente.
3. **Estructura de repo estilo kofro**: `app/` (public, server, data), `ops/` (deploy, tests, tools, reports), `docs/`, `.agents/`.
4. **Markdown = fuente de autoría.** Un **importador** (`ops/tools/import_catalog.py`) genera `app/data/catalog.sqlite` (artefacto derivado, no commiteado). La app lee de la BD; escribe solo en tablas operativas (comentarios, descargas, claves).
5. **Assets por herramienta** en `app/public/assets/tools/<slug>/` (ícono/logo/cover).
6. El sitio v2 permanece en vivo (GitHub Pages) hasta el deploy de la v3 (Fase 7); entonces se retira.

## Alternativas consideradas

- **Astro + API externa**: hosting adicional/contratos; descartado.
- **MySQL**: innecesario para la escala; SQLite simplifica backups y deploy.
- **Framework PHP (Laravel/Slim)**: peso y dependencias sin beneficio para este tamaño; descartado.
- **SQLite solo en el cliente (WASM)**: imposibilita comentarios/descargas compartidas; descartado.
- **BD como fuente primaria de autoría**: rompe el flujo del dueño y la trazabilidad git; descartado (ver ADR-008 para el equilibrio con el admin).

## Consecuencias

- ✔️ Control total, cero costo de infraestructura, backups triviales (un archivo), búsqueda FTS5 de primera.
- ✔️ El flujo de autoría sigue siendo git (Markdown), con agentes y revisión.
- ⚠️ Se programa para **PHP 8.1** (sin sintaxis 8.2+).
- ⚠️ Límites del hosting: subidas PHP 2 MB → los archivos grandes van por FTP o por subida fragmentada (chunking, patrón kofro).
- ⚠️ Hay que mantener el importador + verificador de paridad (`ops/tools/`).
- ⚠️ La migración no debe romper URLs: se conservan los esquemas actuales (`/tools/<slug>`, `/tags/<tag>`, etc.), con inglés bajo `/en/`.
