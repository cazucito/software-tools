# HANDOFF — cómo continuar

## Estado

**v3 EN PRODUCCIÓN en la raíz** `https://software-tools.pcabrera.com/` (2026-10-05): URLs limpias, indexable, admin en `/admin` (pass de producción rotada 2026-10-15 → BWS `H_SOFTWARE_TOOLS_ADMIN_PASS`; `app_secret` en `H_SOFTWARE_TOOLS_APP_SECRET`). `/proto/` sigue vivo como sandbox noindex. E2E **70/70**. Catálogo: **58 fichas** (lote `_diagram` 2026-10-15: Axon, Balsamiq, MindManager, PersonalBrain, TheBrain 8, UMLet, System Mechanic — más **Program CC** 2026-10-15; años borrador pendientes de tu revisión). Descargas: Sitio oficial (público) + Colección privada tras **contraseña general** (rotada 2026-10-15; el valor vive en BWS `H_SOFTWARE_TOOLS_DL_PASS` — proyecto hermes-evo) — **92 filas** (netbeans 46, gel 18, +7 nuevas ×2 alojado/enlace, +13 referencias de primera/última versión, +`program-cc/cc.zip` 2026-10-15) y 8 binarios nuevos (371 MB + CC.zip). GitHub Pages: **retirado** por el dueño (2026-10-15). Pendiente del dueño: revisar los años borrador; decidir mirror físico de `downloads/` (~7.2 GB) — el respaldo activo es el inventario con sha256.

## Recetas

```bash
# Flujo de contenido (cualquier cambio de fichas)
python3 ops/tools/import_catalog.py && python3 ops/tools/verify_parity.py   # PARIDAD OK obligatorio

# Inventario de respaldo de descargas (solo lectura; ST_ADMIN_PASS vía env/BWS)
ST_BASE=https://software-tools.pcabrera.com ST_ADMIN_PASS=... python3 ops/tools/downloads_inventory.py

# v3 en local (Docker php:8.1 + SQLite)
docker run --rm -d --name st-proto -p 127.0.0.1:8091:8091 -v "$PWD":/srv -w /srv php:8.1-cli php -S 0.0.0.0:8091 -t app/public
# → http://127.0.0.1:8091/  (home = mapa dendrita; ?static desactiva animaciones)

# E2E local (borrar ops.sqlite antes por el rate-limit de login)
rm -f app/data/ops.sqlite && bash ops/tests/e2e.sh     # esperar 70/70 (default dev-admin-2026:
# el hash dev de app/server/config.local.php DEBE validar esa pass o el login falla en cascada)

# Desplegar (credenciales BWS en runtime; NUNCA sube secretos ni datos vivos)
python3 ops/deploy/deploy.py                     # sandbox /proto/
python3 ops/deploy/deploy.py --catalog           # + catálogo (solo tras exportar ediciones vivas)
python3 ops/deploy/deploy.py --root --catalog    # producción (raíz); el catálogo sube ANTES del código (desde 173c0e2); renombra a _bak-* si reaparecen
# config viva (admin pass/app_secret): no hay script en el repo (los de cap/ se retiraron en la limpieza) —
# patrón: openssl rand → password_hash (php:8.1, env nunca argv) → config.local.php (comillas simples) → FTP; valores SIEMPRE a BWS
```

## Cuidados

- **`--catalog` solo tras exportar ediciones vivas** del admin (nunca pisar la BD del server).
- `downloads/` y `assets/tools/` del server son del dueño: no se suben ni se borran (archivos grandes por FTP manual).
- La BD de catálogo **se regenera** desde las fichas .md.
- Secretos solo en BWS o `config.local.php` (gitignored, excluido del deploy). La raíz tiene `noindex=false` + `url_style=pretty`; el sandbox `/proto/` usa defaults (noindex, query-style).
- `.htaccess` de la raíz: rewrite LiteSpeed `index.php?p=$1` (QSA). En `data/` y `downloads/`: deny total.
- Round-trip: fichas con newline final (`ops/tools/fix_trailing_newlines.py`); tags/related por rowid (orden Markdown).
- El login renderiza el token CSRF dos veces (login + logout del layout): al extraer con grep usar `head -1`.
- Arte (ADR-010): `st_tool_art()` (server) y `window.stArt(t, icons)` (JS); mapa `icons` = `Ops::iconMap()`; monogramas por década en `scale.css`. (El zoom por década vivía en Cinta/Línea — retirados con el mapa, ADR-012.)
- GitHub Pages: **retirado por el dueño** (2026-10-15; la URL responde 404). `pages-build-deployment` es workflow de sistema.
- **Nombres de archivo servidos**: solo `[A-Za-z0-9._-]`. Comas/paréntesis los rechaza `safeRel` (registro admin) y los espacios (%20) los bloquea el WAF del hosting (403 con HTML del proveedor, distinguible del `403` plano de la app). Renombrar ANTES de subir/registrar.
- **Catálogo al servidor**: STOR a `data/catalog.sqlite.uploading` + verificar tamaño + RENAME; `deploy.py --catalog` STOREA directo a `data/catalog.sqlite` — evitarlo o asumir el riesgo de truncado.
- Bucket `dlkey` (unlock de descargas): 5 intentos/600 s por ip (los bloqueados NO se registran — no gastan ventana). Para verificar el streamer: UN unlock y muchas lecturas.