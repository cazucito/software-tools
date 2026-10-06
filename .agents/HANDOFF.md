# HANDOFF — cómo continuar

## Siguiente fase: Fase 4 — prototipo local

1. Crear el prototipo en `app/` (PHP 8.1; `php -S 127.0.0.1:8080 -t app/public` o contenedor).
2. Implementar: home-timeline con las 3 modalidades (las maquetas de `sketches/001-direcciones-visuales/` son la base visual), ficha, búsqueda FTS5.
3. Usar `app/data/catalog.sqlite` (ya generada) como fuente de lectura.
4. Al terminar: presentar al dueño para revisión (gate de la fase).

## Recetas

```bash
# Flujo de contenido (cualquier cambio de fichas)
python3 ops/tools/import_catalog.py && python3 ops/tools/verify_parity.py   # PARIDAD OK obligatorio
npm run build                                                               # v2 sigue construyendo

# Servidores locales (v2)
npm run dev
```

## Cuidados

- **No mover `src/content/tools/`** hasta la reestructura final (el v2 build lo lee y está en vivo).
- **No romper el deploy v2**: cualquier push a `main` reconstruye `cazucito.github.io` (workflow de Pages).
- La BD **se regenera**, nunca se edita a mano; los cambios de contenido van a las fichas .md.
- Secretos (FTP, admin, claves) **solo en BWS**; nada en el repo.
- El placeholder del subdominio (`site/underconstruction/`) se reemplaza en la Fase 7; `/conceptos/` se retira al cerrar la v3.
