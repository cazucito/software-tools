# Especificación — software-tools v3

**Versión:** 3.0 (borrador de implementación) · **Fecha:** 2026-10-05
**Estado:** vigente. Sustituye a la especificación v2 (era Astro/GitHub Pages — preservada en la historia de git y en el tag `v2.0.0`).
**Documentos rectores:** [ADRs 005–009](adr/) · [Plan v3 v1.3](plans/2026-10-05-v3-propuesta-arquitectura.md).

---

## 1. Resumen

| Aspecto | Descripción |
|---|---|
| **Propósito** | Archivo personal del software usado por el dueño (**48 herramientas, 1991 → hoy**): timeline navegable, fichas, búsqueda, comentarios y descargas personales; lo redistribuible se ofrece público |
| **Hosting** | `software-tools.pcabrera.com` — PHP **8.1** + **SQLite** (LiteSpeed; FTS5 verificado en Fase 0) |
| **Autoría** | Markdown en git (**fuente**) → importador → `catalog.sqlite` (espejo de lectura) · datos operativos en `ops.sqlite` (ADR-009) · panel admin (ADR-008) |
| **Idiomas** | Español (raíz) · Inglés (`/en/`) |
| **Modos** | `cinta` · `linea` (default) · `maquina` — seleccionables (ADR-006) |
| **Fuera de** | GitHub Pages (se retira al desplegar), subidas públicas, cuentas de usuario |

## 2. Alcance

**Dentro:** timeline-home con 3 modos + selector · fichas · índices (herramientas, etiquetas, categorías, décadas) · búsqueda FTS instantánea · comentarios moderados · descargas con clave individual por software (ADR-007) · assets por herramienta (ícono/logo/cover) · i18n es/en · SEO · panel admin (catálogo/descargas/comentarios) · respaldos.

**Fuera:** subidas de visitantes (**regla**), cuentas públicas, e-commerce, analytics de terceros, notificaciones por correo (v1), RSS (futuro opcional).

## 3. Arquitectura

Estructura objetivo (estilo kofro; ver [ADR-005](adr/005-arquitectura-v3.md)):

```
software-tools/
├── app/
│   ├── public/      # docroot desplegable: index.php, assets/, locales/, js/, downloads/ (protegido)
│   ├── server/      # PHP: lib/ (módulos namespaced), controladores, config.php
│   └── data/        # catalog.sqlite (generado) + ops.sqlite (operativo) — no se commitean
├── ops/
│   ├── deploy/      # deploy.py (ftplib + BWS; solo complementa, nunca borra)
│   ├── tests/       # smoke/e2e
│   ├── tools/       # importador, verificador de paridad
│   └── reports/     # reportes generados (paridad, QA)
├── docs/            # SPEC, ADRs, planes, notas
├── .agents/         # continuidad para agentes
└── AGENTS.md · README.md
```

**Transición:** durante la v3 en curso, las fichas permanecen en `src/content/tools/` (donde el sitio v2 las lee). `content/` y el retiro del código Astro se ejecutan en la reestructura final (Fase 5–7). El importador acepta `--source`.

**Flujo de datos:**
`src/content/tools/*.md` (git = fuente) → `ops/tools/import_catalog.py` → `app/data/catalog.sqlite` → app (lectura). Los **datos operativos** (comentarios, descargas, claves, assets, ediciones del admin) viven aparte en `app/data/ops.sqlite`, que se auto-inicializa y **nunca lo toca el importador ni el deploy** ([ADR-009](adr/009-datos-operativos.md)); **export a Markdown** para volcar al repo ([ADR-008](adr/008-admin.md)).

## 4. Modelo de datos (SQLite)

**Dos archivos** ([ADR-009](adr/009-datos-operativos.md)).

**`app/data/catalog.sqlite`** — generado por el importador (reemplazable; la app solo lee):

```sql
CREATE TABLE schema_meta(key TEXT PRIMARY KEY, value TEXT);
CREATE TABLE tools(
  id INTEGER PRIMARY KEY,
  slug TEXT UNIQUE NOT NULL, name TEXT NOT NULL, year INTEGER NOT NULL,
  used_until INTEGER, category TEXT NOT NULL, context TEXT NOT NULL, body TEXT NOT NULL DEFAULT '',
  image TEXT, successor TEXT, successor_slug TEXT, next_version TEXT,
  published INTEGER NOT NULL DEFAULT 1, created_at TEXT, updated_at TEXT);
CREATE TABLE tool_i18n(
  tool_id INTEGER NOT NULL REFERENCES tools(id) ON DELETE CASCADE,
  lang TEXT NOT NULL, context TEXT, body TEXT, PRIMARY KEY(tool_id, lang));
CREATE TABLE categories(slug TEXT PRIMARY KEY, name TEXT NOT NULL);
CREATE TABLE tags(id INTEGER PRIMARY KEY, name TEXT UNIQUE NOT NULL);
CREATE TABLE tool_tags(tool_id INTEGER NOT NULL REFERENCES tools(id) ON DELETE CASCADE,
  tag_id INTEGER NOT NULL REFERENCES tags(id), PRIMARY KEY(tool_id, tag_id));
CREATE TABLE tool_relations(tool_id INTEGER NOT NULL REFERENCES tools(id) ON DELETE CASCADE,
  related_slug TEXT NOT NULL, PRIMARY KEY(tool_id, related_slug));
CREATE VIRTUAL TABLE tools_fts USING fts5(name, context, body, tags);
```

**`app/data/ops.sqlite`** — operativo (lo escribe la app; se auto-inicializa; referencias por **slug**):

```sql
CREATE TABLE IF NOT EXISTS comments(
  id INTEGER PRIMARY KEY, tool_slug TEXT NOT NULL, lang TEXT NOT NULL DEFAULT 'es',
  author TEXT NOT NULL, body TEXT NOT NULL, created_at TEXT NOT NULL,
  status TEXT NOT NULL DEFAULT 'approved' CHECK(status IN ('pending','approved','spam')),
  ip_hash TEXT);
CREATE INDEX IF NOT EXISTS idx_comments_tool ON comments(tool_slug, status);
CREATE TABLE IF NOT EXISTS downloads(
  id INTEGER PRIMARY KEY, tool_slug TEXT NOT NULL, filename TEXT NOT NULL, relpath TEXT NOT NULL,
  bytes INTEGER, sha256 TEXT,
  visibility TEXT NOT NULL DEFAULT 'clave' CHECK(visibility IN ('publico','clave','enlace')),
  license_note TEXT, source_url TEXT, created_at TEXT, UNIQUE(tool_slug, filename));
CREATE TABLE IF NOT EXISTS download_keys(
  tool_slug TEXT PRIMARY KEY, pass_hash TEXT NOT NULL, updated_at TEXT);
CREATE TABLE IF NOT EXISTS tool_assets(
  id INTEGER PRIMARY KEY, tool_slug TEXT NOT NULL,
  kind TEXT NOT NULL CHECK(kind IN ('icon','logo','cover')), file TEXT NOT NULL,
  source TEXT, license_note TEXT, created_at TEXT, UNIQUE(tool_slug, kind));
CREATE TABLE IF NOT EXISTS rate_events(
  id INTEGER PRIMARY KEY, bucket TEXT NOT NULL, key TEXT NOT NULL, created_at INTEGER NOT NULL);
CREATE INDEX IF NOT EXISTS idx_rate ON rate_events(bucket, key, created_at);
CREATE TABLE IF NOT EXISTS catalog_dirty(tool_slug TEXT PRIMARY KEY, changed_at TEXT NOT NULL);
CREATE TABLE IF NOT EXISTS meta(key TEXT PRIMARY KEY, value TEXT);
```

Notas: `used_until`/`successor*` nulos = **sin fecha de fin registrada** (posible uso actual o desconocido — revisión de datos pendiente). `next_version` recoge las 4 versiones siguientes (`maple-6`, `mathematica-3`, `matlab-r14`, `quickbasic-4-5`). `tools_fts` se puebla en la importación (rowid = tool id).

## 5. Rutas (mapa)

| Ruta | Qué |
|---|---|
| `/` | Home: timeline con el modo activo |
| `/tools` | Índice completo de herramientas |
| `/tools/<slug>` | Ficha de herramienta |
| `/tags` · `/tags/<tag>` | Índices por etiqueta |
| `/category/<cat>` · `/decade/<década>` | Filtros |
| `/search` | Búsqueda (FTS5) |
| `/api/search?q=` | JSON para búsqueda instantánea |
| `/download/<slug>/<archivo>` | Streamer PHP de descargas (ADR-007) |
| `/en/<ruta>` | Prefijo inglés de todas las anteriores |
| `/admin/…` | Panel: login, comentarios, descargas, catálogo, export (ADR-008) |

URLs **estables** heredadas de v2 (`/tools/…`, `/tags/…`) para no romper enlaces. i18n: ES en raíz, EN bajo `/en/`, con `hreflang` + `x-default` ES.

## 6. Features y criterios de aceptación

- **6.1 Timeline-home + modalidades** — cada modo (cinta/línea/máquina) presenta las 48 fichas como recorrido navegable; selector persistente (`localStorage` + `?modo=`); **solo se carga el bundle del modo activo**; `prefers-reduced-motion` → versión estática; sin JS → contenido legible. Default: `linea`.
- **6.2 Fichas** — todos los campos (name, year, usedUntil «en uso» si null, categoría, tags, contexto, cuerpo, sucesora/siguiente versión, relacionadas, imagen/cover). Sección «Descargas» cuando existan archivos; comentarios al final. Navegación anterior/siguiente por año.
- **6.3 Búsqueda** — FTS5 sobre nombre/contexto/cuerpo/tags; resultados en tiempo real (fetch + debounce); resaltado; ≥95% de consultas <150 ms en local.
- **6.4 Índices** — tools/tags/categories/decades listan, cuentan y enlazan.
- **6.5 Comentarios** — formulario (nombre + texto + honeypot + time-trap); rate-limit por IP-hash; **publicación directa con moderación retroactiva** (recomendado; configurable a cola previa); escape estricto.
- **6.6 Descargas** — según [ADR-007](adr/007-descargas.md): sin URL directa; clave por software (argon2id, sesión corta); tiers `publico|clave|enlace`; sha256 y tamaño visibles; subidas solo admin (subida directa ≤1.5 MB, registro de archivos subidos por FTP, o FTP para grandes).
- **6.7 Assets** — `icon`/`logo`/`cover` reales y trazables; fallback monograma tipográfico; carga diferida.
- **6.8 i18n** — cadenas UI en `locales/{es,en}.json` (módulo listo; extracción completa de cadenas en Fase 6); contenido traducido en `tool_i18n`; switcher cuando el inglés esté publicado (Fase 6).
- **6.9 SEO** — canonical por idioma, `hreflang`, JSON-LD (WebSite, SoftwareApplication), Open Graph, sitemap; el prototipo permanece `noindex` hasta la Fase 7.
- **6.10 Admin** — según [ADR-008](adr/008-admin.md): login con rate-limit; comentarios (moderar), descargas (archivos + claves + visibilidad), catálogo (editar/despublicar) y **export a Markdown**; las ediciones marcan `catalog_dirty` hasta exportarse.
- **6.11 Configuración** — `config.php` con flags: modos habilitados, comentarios on/off, idiomas, límites; secretos en `config.local.php` (no versionado).

## 7. Seguridad

Pass admin en **BWS/config.local.php** (nunca en repo); argon2id (con fallback bcrypt); sesiones server-side cortas; CSRF en todos los POST del admin; rate-limits (login, comentarios, claves de descarga); headers (`X-Content-Type-Options: nosniff`, `Referrer-Policy`); descargas nunca por URL directa; sin listados de directorio; errores genéricos en producción; **prepared statements** en toda consulta; escape de salida (XSS); uploads solo admin con validación de tipo y extensión.

## 8. Operación

- **Local:** `docker run … php:8.1-cli php -S 127.0.0.1:8091 -t app/public` (o cualquier PHP 8.1) + importador.
- **Deploy:** `ops/deploy/deploy.py` (ftplib + BWS; **solo complementa, nunca borra**); el catálogo se sube solo con bandera explícita; `ops.sqlite` y `config.local.php` **nunca** se suben.
- **Respaldos:** snapshot programado del SQLite (patrón kofro): `ops.sqlite` (preciado) + `catalog.sqlite` (reconstruible).
- **Tests:** `ops/tests/` (lint PHP, smoke con curl, paridad de contenido); matriz UI mínima: 3 modos × 2 idiomas × desktop/móvil.

## 9. Rendimiento (límites del hosting)

Subida PHP 2 MB → subida directa ≤1.5 MB; archivos grandes por FTP; ejecución 30 s → nada pesado en request; JS diferido y por modo; imágenes optimizadas; cache headers para assets; el contenido renderiza antes de cualquier animación.

## 10. Criterios de aceptación globales (gate previo al deploy, Fase 7)

- [ ] **Paridad 48/48** verificada (`ops/reports/`, checksum por ficha) — nada del contenido v2 se pierde.
- [ ] 3 modalidades funcionando (desktop + móvil, reduced-motion, sin JS).
- [ ] Búsqueda, índices, comentarios, descargas con clave y admin según §6.
- [ ] i18n operativa (ES completo; EN tras revisión).
- [ ] SEO ✓; **URLs de v2 intactas**.
- [ ] Seguridad §7 verificada; cero accesos directos a archivos.
- [ ] Deploy reproducible y **restauración de backup probada**.
- [ ] GitHub Pages retirado; placeholder reemplazado.

## 11. Futuro (v3.x, no bloqueante)

TOTP en el admin · RSS · más modos · API pública de solo lectura · métricas propias sin terceros · subida fragmentada (chunking) en el admin.
