# STATE — fase actual

**Fase 5 completada (2026-10-05): app completa DESPLEGADA y verificada en vivo** → `https://software-tools.pcabrera.com/proto/` (admin en `/proto/index.php?p=admin/login`, cuenta única, pass en BWS/scratch del dueño).

## Entregado (Fases 0–7 de la v3)

- ✔️ Fase 0 — hosting verificado (PHP 8.1.34, SQLite 3.53.4 + FTS5; límites 2 MB/30 s/512 MB).
- ✔️ Fase 1 — maquetas de las 3 modalidades aprobadas (seleccionables; default línea).
- ✔️ Fase 2 — SPEC v3 + ADRs 005–009; specs v2 retiradas.
- ✔️ Fase 3 — importador + verificador (**PARIDAD 48/48**); `catalog.sqlite` (espejo read-only) + `ops.sqlite` operativo (ADR-009); esqueleto kofro + `.agents/`.
- ✔️ Extra — fichas 28→48; 60 recíprocos; estudio de descargas (48 herramientas).
- ✔️ Fase 4 — prototipo navegable: 3 modalidades, ficha, FTS5 (`<mark>` + `/api/search`), catálogo por décadas.
- ✔️ **Fase 5** — `Ops`/`Auth`/`I18n`/`Comments`/`Downloads`/`Admin`; comentarios (honeypot «website» + time-trap HMAC + rate-limit por ip_hash, publicación directa con moderación retroactiva); panel admin (CSRF, login rate-limit 5/900 s, sesión `st_admin` endurecida); descargas con clave individual (hash, cookie firmada por slug TTL 7200 s, niveles publico/clave/enlace, streamer PHP, sha256+tamaño, `downloads/` con deny); export a Markdown **round-trip byte a byte**; i18n es/en; SEO (canonical, hreflang es/x-default, OG, JSON-LD, sitemap bilingüe, noindex prueba).
- ✔️ **E2E local 54/54** (`ops/tests/e2e.sh`) + **smoke en vivo OK** (`ops/tests/live_smoke.sh`): login, panel, comentario publicado→visible→moderado, `data/`+`downloads/` 403.
- ✔️ Deploy tooling: `deploy_proto.py` (excluye `config.local.php`/`ops.sqlite`/`downloads/`/`assets/tools/`; catálogo solo con `--catalog`) + `write_live_config.py` (genera y sube config.local.php vivo).

## Pendiente (por fase)

- **Fase 6:** inglés de ficha/sistema (revisión del dueño; base i18n ya lista).
- **Fase 7:** deploy a la raíz de `software-tools.pcabrera.com` + retiro de GitHub Pages. **Fase 8:** tag `v3.0.0`.
- Seguridad pendiente de decisión: alerta Dependabot `http-cache-semantics` (transitiva de Astro, impacto ~0).

## Pendientes del dueño

- **Probar el sitio en vivo** (`/proto/`): modalidades, ficha, búsqueda, comentar.
- **Recibir la contraseña del admin** (definitiva, generada 2026-10-05; también en `~/.hermes/cache/scratch/st-f4/adminpass` local). Guardarla en Bitwarden (regla: credenciales en BWS).
- Compartir la **carpeta de software** para poblar `downloads/` (tamaños/hashes/clasificación).
- Íconos/imágenes reales por herramienta (fase de assets).
- Revisar los **años inferidos** de las 20 fichas nuevas.