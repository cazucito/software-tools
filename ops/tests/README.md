# ops/tests/ — pruebas

- **Paridad de contenido:** `python3 ops/tools/verify_parity.py` (exit 0 = OK) — cubre la regla «nada se pierde».
- **e2e:** `rm -f app/data/ops.sqlite && bash ops/tests/e2e.sh` — 0 fallos (incluye home = mapa con 57 herramientas / 14 familias).
- **Smoke v3 (a partir de Fase 4):** lint PHP (`php -l`), arranque local (`php -S 127.0.0.1:8080 -t app/public`), rutas clave por curl.
- **Matriz UI mínima:** home (mapa) y páginas de contenido × desktop/móvil, incl. `prefers-reduced-motion` y sin JS (`<noscript>` con el archivo completo).
