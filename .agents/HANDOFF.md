# HANDOFF — cómo continuar

## Estado

**Fase 5 desplegada en vivo** en `https://software-tools.pcabrera.com/proto/` (2026-10-05). E2E local **54/54** + smoke en vivo **OK**. Siguiente: Fase 6 (inglés) / Fase 7 (raíz + retiro Pages) cuando el dueño lo pida.

## Recetas

```bash
# Flujo de contenido (cualquier cambio de fichas)
python3 ops/tools/import_catalog.py && python3 ops/tools/verify_parity.py   # PARIDAD OK obligatorio

# v3 en local (Docker php:8.1 + SQLite)
docker run --rm -d --name st-proto -p 127.0.0.1:8091:8091 -v "$PWD":/srv -w /srv php:8.1-cli php -S 0.0.0.0:8091 -t app/public
# → http://127.0.0.1:8091/index.php?modo=linea   (admin local: config.local.php)

# E2E local (arranca su propio servidor en 8091... exige 8090 libre; receta en el propio script)
E2E_ADMIN_PASS=... bash ops/tests/e2e.sh            # esperar 54/54

# Redesplegar (solo complementa; credenciales BWS en runtime; NUNCA sube secretos)
python3 /mnt/thoth/_TEMP/software-tools-sketches/cap/deploy_proto.py
# subir catálogo fresco:        ... --catalog    (¡solo tras exportar ediciones vivas del admin!)
# subir archivo suelto:         python3 .../upload_one.py put <local> proto/<ruta>
# regenerar config.local.php vivo (nueva pass/secret): editar write_live_config.py → python3 .../write_live_config.py
```

## Cuidados

- **`deploy_proto.py` sin `--catalog` no toca la BD del servidor** — nunca pisar ediciones vivas del admin sin exportarlas antes.
- `downloads/` y `assets/tools/` del servidor son del dueño: **no se suben ni se borran**; los archivos grandes entran por FTP manual.
- La BD de catálogo **se regenera** desde las fichas .md; nunca editarla a mano.
- Secretos (FTP, admin, app_secret) **solo en BWS** o `config.local.php` (gitignored, excluido del deploy); nada en el repo.
- Round-trip del export: las fichas deben **terminar en newline** (`ops/tools/fix_trailing_newlines.py` si alguna no); tags/related se ordenan por `rowid` (orden del Markdown).
- El e2e **reimporta** el catálogo antes de la sección 7 (aisla el round-trip del test de edición); `ops.sqlite` hay que borrarlo antes de correr (receta en el script) por el rate-limit de login.
- En el hosting, `index.php`, `server/` y `data/` son hermanos dentro de `/proto/`; el controlador autodetecta ambos layouts.
- El login renderiza el token CSRF dos veces (login + logout del layout, mismo valor): al extraer con grep usar `head -1`.
- GitHub Pages **sigue activo** mientras la raíz sea la v2; retirarlo en Fase 7 (workflow Pages reconstruye en cada push a main).