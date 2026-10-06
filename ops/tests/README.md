# ops/tests/ — pruebas

- **Paridad de contenido:** `python3 ops/tools/verify_parity.py` (exit 0 = OK) — cubre la regla «nada se pierde».
- **v2 build:** `npm run build` debe construir sin errores mientras v2 siga vivo.
- **Smoke v3 (a partir de Fase 4):** lint PHP (`php -l`), arranque local (`php -S 127.0.0.1:8080 -t app/public`), rutas clave por curl.
- **Matriz UI mínima (Fase 5):** 3 modalidades × 2 idiomas × desktop/móvil, incl. `prefers-reduced-motion` y sin JS.
