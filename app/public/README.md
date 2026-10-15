# app/public/ — docroot desplegable

Todo lo que se sube al hosting (Fase 7). Contendrá:

- `index.php` — front controller (render server-side; SEO primero).
- `assets/` — `css/` (`base`, `content`, `scale`, `mapa`, `admin`), `js/` (`mapa.js` motor del mapa de la home, `app.js` atajo de búsqueda) + `assets/tools/<slug>/` (íconos/logos/covers).
- `locales/` — cadenas de UI: `es.json`, `en.json`.
- `downloads/` — archivos de descarga (**protegida**; se sirven solo por PHP, nunca por URL directa — [ADR-007](../../docs/adr/007-descargas.md)).

*Se construye en las Fases 4–5. Hasta entonces, aquí no hay código de producción.*
