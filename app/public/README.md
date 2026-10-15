# app/public/ — docroot desplegable

Lo que se despliega al hosting con `deploy.py --root`; en el servidor queda **plano** (junto a `server/`, `data/` y `downloads/`):

- `index.php` — front controller (render server-side; SEO primero).
- `assets/` — `css/` (`base`, `content`, `scale`, `mapa`, `admin`), `js/` (`mapa.js` motor del mapa de la home, `app.js` atajo de búsqueda) + `assets/tools/<slug>/` (íconos/logos/covers).
- `locales/` — cadenas de UI: `es.json`, `en.json`.
- `downloads/` — archivos de descarga (**protegida**; se sirven solo por PHP, nunca por URL directa — [ADR-007](../../docs/adr/007-descargas.md)).

En producción desde 2026-10-05 (`software-tools.pcabrera.com`). `config.local.php`, `data/` y `ops.sqlite*` están excluidos del deploy.
