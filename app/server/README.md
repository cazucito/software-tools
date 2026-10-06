# app/server/ — PHP de la v3

Código PHP (objetivo **8.1**, compat hosting). Contendrá:

- `lib/` — módulos namespaced: `Catalog`, `Timeline`, `Search` (FTS5), `I18n`, `Comments`, `Downloads`, `Seo`, `Admin`.
- Controladores de rutas (ver mapa en [SPEC §5](../../docs/SPEC.md)).
- `config.example.php` — plantilla de configuración (los secretos viven en **BWS**, nunca en el repo ni en `config.php`).

Sin frameworks ni dependencias PHP externas. *Se construye en las Fases 4–5.*
