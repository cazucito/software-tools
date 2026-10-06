# app/public/ — docroot desplegable

Todo lo que se sube al hosting (Fase 7). Contendrá:

- `index.php` — front controller (render server-side; SEO primero).
- `assets/` — CSS/JS/fuentes/libs vendored (GSAP, Lenis) + `assets/tools/<slug>/` (íconos/logos/covers).
- `locales/` — cadenas de UI: `es.json`, `en.json`.
- `js/` — módulos ES de la capa de experiencia (un bundle por modalidad).
- `downloads/` — archivos de descarga (**protegida**; se sirven solo por PHP, nunca por URL directa — [ADR-007](../../docs/adr/007-descargas.md)).

*Se construye en las Fases 4–5. Hasta entonces, aquí no hay código de producción.*
