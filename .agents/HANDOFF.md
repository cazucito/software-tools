# HANDOFF — cómo continuar

## Estado

**v3 EN PRODUCCIÓN en la raíz** `https://software-tools.pcabrera.com/` (2026-10-05): URLs limpias, indexable, admin en `/admin`. `/proto/` sigue vivo como sandbox noindex. E2E **66/66**. Fase 8 (ADR-011, 2026-10-07): descargas = Sitio oficial (público) + Colección privada tras **contraseña general** (`thoth-descargas-2026` en prod; cookie 2 h); la ficha de NetBeans es el piloto (44 archivos). Pendientes del dueño: unpublish de GitHub Pages (Settings → Pages; kaelaxiom no tiene admin); decidir mirror físico de `downloads/` (6.8 GB) — hoy el respaldo activo es el inventario con sha256.

## Recetas

```bash
# Flujo de contenido (cualquier cambio de fichas)
python3 ops/tools/import_catalog.py && python3 ops/tools/verify_parity.py   # PARIDAD OK obligatorio

# Inventario de respaldo de descargas (solo lectura; ST_ADMIN_PASS vía env/BWS)
ST_BASE=https://software-tools.pcabrera.com ST_ADMIN_PASS=... python3 ops/tools/downloads_inventory.py

# v3 en local (Docker php:8.1 + SQLite)
docker run --rm -d --name st-proto -p 127.0.0.1:8091:8091 -v "$PWD":/srv -w /srv php:8.1-cli php -S 0.0.0.0:8091 -t app/public
# → http://127.0.0.1:8091/index.php?modo=linea

# E2E local (borrar ops.sqlite antes por el rate-limit de login)
rm -f app/data/ops.sqlite && E2E_ADMIN_PASS=... bash ops/tests/e2e.sh     # esperar 66/66

# Desplegar (credenciales BWS en runtime; NUNCA sube secretos ni datos vivos)
python3 ops/deploy/deploy.py                     # sandbox /proto/
python3 ops/deploy/deploy.py --catalog           # + catálogo (solo tras exportar ediciones vivas)
python3 ops/deploy/deploy.py --root --catalog    # producción (raíz); renombra a _bak-* si reaparecen
# regenerar config viva (nueva pass/secret): cap/write_live_config.py — scratch/st-f4 se autopurga a las 24 h;
# el admin pass de producción debe conservarse en BWS
```

## Cuidados

- **`--catalog` solo tras exportar ediciones vivas** del admin (nunca pisar la BD del server).
- `downloads/` y `assets/tools/` del server son del dueño: no se suben ni se borran (archivos grandes por FTP manual).
- La BD de catálogo **se regenera** desde las fichas .md.
- Secretos solo en BWS o `config.local.php` (gitignored, excluido del deploy). La raíz tiene `noindex=false` + `url_style=pretty`; el sandbox `/proto/` usa defaults (noindex, query-style).
- `.htaccess` de la raíz: rewrite LiteSpeed `index.php?p=$1` (QSA). En `data/` y `downloads/`: deny total.
- Round-trip: fichas con newline final (`ops/tools/fix_trailing_newlines.py`); tags/related por rowid (orden Markdown).
- El login renderiza el token CSRF dos veces (login + logout del layout): al extraer con grep usar `head -1`.
- Arte (ADR-010): `st_tool_art()` (server) y `window.stArt(t, icons)` (JS); mapa `icons` = `Ops::iconMap()`; monogramas por década en `scale.css`; zoom por década en Cinta (marcadores) y Línea (`.st-dec-nav`).
- GitHub Pages sigue publicado (v2 en cazucito.github.io) hasta que el dueño haga unpublish; `pages-build-deployment` es workflow de sistema: no se puede desactivar por API.