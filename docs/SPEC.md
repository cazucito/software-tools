# Especificación — software-tools v3

**Versión:** 3.0 (borrador de implementación) · **Fecha:** 2026-10-05
**Estado:** vigente. Sustituye a la especificación v2 (era Astro/GitHub Pages — preservada en la historia de git y en el tag `v2.0.0`).
**Documentos rectores:** [ADRs 005–008](adr/) · [Plan v3 v1.3](plans/2026-10-05-v3-propuesta-arquitectura.md).

---

## 1. Resumen

| Aspecto | Descripción |
|---|---|
| **Propósito** | Archivo personal del software usado por el dueño (**48 herramientas, 1991 → hoy**): timeline navegable, fichas, búsqueda, comentarios y descargas personales; lo redistribuible se ofrece público |
| **Hosting** | `software-tools.pcabrera.com` — PHP **8.1** + **SQLite** (LiteSpeed; FTS5 verificado en Fase 0) |
| **Autoría** | Markdown en git (**fuente**) → importador → `catalog.sqlite` (espejo de lectura) + panel admin (operación diaria, ADR-008) |
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
│   └── data/        # catalog.sqlite — GENERADO, no se commitea
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
`src/content/tools/*.md` (git = fuente) → `ops/tools/import_catalog.py` → `app/data/catalog.sqlite` → app (lectura). Escrituras operativas (comentarios, descargas, claves) directo a la BD vía admin; **export a Markdown** para volcar al repo (ADR-008).

## 4. Modelo de datos (SQLite)

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
CREATE TABLE tool_assets(id INTEGER PRIMARY KEY,
  tool_id INTEGER NOT NULL REFERENCES tools(id) ON DELETE CASCADE,
  kind TEXT NOT NULL CHECK(kind IN ('icon','logo','cover')), file TEXT NOT NULL,
  source TEXT, license_note TEXT);
CREATE TABLE downloads(id INTEGER PRIMARY KEY,
  tool_id INTEGER NOT NULL REFERENCES tools(id) ON DELETE CASCADE,
  filename TEXT NOT NULL, relpath TEXT NOT NULL, bytes INTEGER, sha256 TEXT,
  visibility TEXT NOT NULL DEFAULT 'clave' CHECK(visibility IN ('publico','clave','enlace')),
  license_note TEXT, source_url TEXT, created_at TEXT, UNIQUE(tool_id, filename));
CREATE TABLE download_keys(
  tool_id INTEGER PRIMARY KEY REFERENCES tools(id) ON DELETE CASCADE,
  pass_hash TEXT NOT NULL, updated_at TEXT);
CREATE TABLE comments(id INTEGER PRIMARY KEY,
  tool_id INTEGER NOT NULL REFERENCES tools(id) ON DELETE CASCADE,
  lang TEXT NOT NULL DEFAULT 'es', author TEXT NOT NULL, body TEXT NOT NULL,
  created_at TEXT NOT NULL, status TEXT NOT NULL DEFAULT 'approved'
    CHECK(status IN ('pending','approved','spam')), ip_hash TEXT);
CREATE INDEX idx_comments_tool ON comments(tool_id, status);
CREATE VIRTUAL TABLE tools_fts USING fts5(name, context, body, tags);
```

Notas: `used_until`/`successor*` nulos = «en uso hoy» (estado formal). `next_version` recoge las 4 versiones siguientes (`maple-6`, `mathematica-3`, `matlab-r14`, `quickbasic-4-5`) referenciadas por las fichas base. `tools_fts` se puebla en la importación (rowid = tool id).

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
| `/admin/…` | Panel (login; ADR-008) |

URLs **estables** heredadas de v2 (`/tools/…`, `/tags/…`) para no romper enlaces. i18n: ES en raíz, EN bajo `/en/`, con `hreflang` + `x-default` ES.

## 6. Features y criterios de aceptación

- **6.1 Timeline-home + modalidades** — cada modo (cinta/línea/máquina) presenta las 48 fichas como recorrido navegable; selector persistente (`localStorage` + `?modo=`); **solo se carga el bundle del modo activo**; `prefers-reduced-motion` → versión estática; sin JS → contenido legible. Default: `linea`.
- **6.2 Fichas** — todos los campos (name, year, usedUntil «en uso» si null, categoría, tags, contexto, cuerpo, sucesora/siguiente versión, relacionadas, imagen/cover). Sección «Descargas» cuando existan archivos; comentarios al final. Navegación anterior/siguiente por año.
- **6.3 Búsqueda** — FTS5 sobre nombre/contexto/cuerpo/tags; resultados en tiempo real (fetch + debounce); resaltado; ≥95% de consultas <150 ms en local.
- **6.4 Índices** — tools/tags/categories/decades listan, cuentan y enlazan.
- **6.5 Comentarios** — formulario (nombre + texto + honeypot + time-trap); rate-limit por IP-hash; **publicación directa con moderación retroactiva** (recomendado; configurable a cola previa); escape estricto.
- **6.6 Descargas** — según [ADR-007](adr/007-descargas.md): sin URL directa; clave por software (argon2id, sesión corta); tiers `publico|clave|enlace`; sha256 y tamaño visibles; subidas solo admin (chunked) o FTP.
- **6.7 Assets** — `icon`/`logo`/`cover` reales y trazables; fallback monograma tipográfico; carga diferida.
- **6.8 i18n** — cadenas UI en `locales/{es,en}.json`; contenido traducido en `tool_i18n`; switcher; el inglés no se publica hasta la revisión del dueño (Fase 6).
- **6.9 SEO** — sitemap bilingüe, canonical por idioma, `hreflang`, JSON-LD (WebSite, ItemList, SoftwareApplication), Open Graph.
- **6.10 Admin** — según [ADR-008](adr/008-admin.md): login con rate-limit; catálogo + descargas + comentarios; **export a Markdown** cuyo resultado debe ser idéntico a las fuentes (diff = 0 tras re-importar).
- **6.11 Configuración** — `config.php` con flags: modos habilitados, comentarios on/off, idiomas, límites.

## 7. Seguridad

Pass admin en **BWS** (nunca en repo); argon2id; sesiones server-side cortas; CSRF en todos los POST; rate-limits (login, comentarios, claves de descarga); headers (`X-Content-Type-Options: nosniff`, `Referrer-Policy`); descargas nunca por URL directa; sin listados de directorio; errores genéricos en producción; **prepared statements** en toda consulta; escape de salida (XSS); uploads solo admin con validación de tipo.

## 8. Operación

- **Local:** `php -S 127.0.0.1:8080 -t app/public` (o contenedor php:8.1 en Fase 4) + importador.
- **Deploy:** `ops/deploy/deploy.py` (ftplib + BWS; **solo complementa, nunca borra**); verificación por tamaños + sha.
- **Respaldos:** snapshot programado del SQLite (y de `downloads/` según crecimiento) → rutas de respaldo del ecosistema (patrón kofro).
- **Tests:** `ops/tests/` (lint PHP, smoke con curl, paridad de contenido); matriz UI mínima: 3 modos × 2 idiomas × desktop/móvil.

## 9. Rendimiento (límites del hosting)

Subida PHP 2 MB → **chunking**; ejecución 30 s → nada pesado en request; JS diferido y por modo; imágenes optimizadas; cache headers para assets; el contenido renderiza antes de cualquier animación.

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

TOTP en el admin · RSS · más modos · API pública de solo lectura · métricas propias sin terceros.
