# HANDOFF — cómo continuar

## Estado

El **prototipo (Fase 4) está EN VIVO** en `https://software-tools.pcabrera.com/proto/` esperando la revisión del dueño. Siguiente fase: **Fase 5 — app completa** (comentarios, panel admin, descargas con clave, assets, SEO), cuando llegue su feedback.

## Recetas

```bash
# Flujo de contenido (cualquier cambio de fichas)
python3 ops/tools/import_catalog.py && python3 ops/tools/verify_parity.py   # PARIDAD OK obligatorio
npm run build                                                               # v2 sigue construyendo

# Prototipo v3 en local (Docker php:8.1 + SQLite)
docker run --rm -d --name st-proto -p 127.0.0.1:8091:8091 -v "$PWD":/srv -w /srv php:8.1-cli php -S 0.0.0.0:8091 -t app/public
# → http://127.0.0.1:8091/index.php?modo=linea

# Redesplegar el prototipo al subdominio (solo complementa; credenciales BWS en runtime)
python3 /mnt/thoth/_TEMP/software-tools-sketches/cap/deploy_proto.py
# subir UN archivo suelto:  python3 /mnt/thoth/_TEMP/software-tools-sketches/cap/upload_one.py put <local> proto/<ruta>
```

## Cuidados

- **No mover `src/content/tools/`** hasta la reestructura final (el v2 build lo lee y está en vivo).
- **No romper el deploy v2**: cualquier push a `main` reconstruye `cazucito.github.io` (workflow de Pages).
- La BD **se regenera**, nunca se edita a mano; los cambios de contenido van a las fichas .md.
- Secretos (FTP, admin, claves) **solo en BWS**; nada en el repo.
- El prototipo vive en `/proto/` (noindex, `data/` con deny); el placeholder del subdominio y `/conceptos/` se reemplazan/retiran en la Fase 7.
- Debug en vivo: subir `proto/server/config.local.php` con `['debug' => true]` (patrón de override), y **borrarlo después**.
- La estructura de deploy difiere de la local: en el hosting `index.php`, `server/` y `data/` son hermanos dentro de `proto/`; el controlador ya autodetecta ambos layouts.
