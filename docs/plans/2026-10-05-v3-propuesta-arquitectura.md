# Plan de acción — software-tools v3 (propuesta)

**Versión:** v1.3 · 2026-10-05 *(v1.2: modalidades + placeholder + descargas · v1.3: assets visuales por herramienta + descargas afinadas: clave por software, sin subidas, rutas)*
**Estado:** borrador para revisión — pendiente tu OK final antes de SPEC v3 y código.
**Objetivo:** cambio de arquitectura — de sitio estático (Astro, GitHub Pages) a **aplicación PHP + SQLite en `software-tools.pcabrera.com`**, con un **timeline scroll-driven** como corazón del producto.

---

## 0. Decisiones registradas (2026-10-05)

| # | Tema | Decisión |
|---|------|----------|
| 1 | Stack | **PHP 8 + SQLite** ✔ (confirmado por Fase 0) |
| 2 | Experiencia | **Scroll narrativo estilo páginas de producto de Apple**; se autoriza proponer librerías JS para riqueza visual |
| 3 | Carácter | **Más dinamismo, tipo webapp** (interacciones, transiciones, búsqueda instantánea) |
| 4 | Comentarios | **Sí** — infraestructura propia propuesta (ver §2.5) |
| 5 | Repo | **Repositorio tradicional**; **no existirá GitHub Pages** (se retira el workflow; sin página de aviso) |
| 6 | Idiomas + SEO | **Internacionalizable desde el inicio (es + en)**, con SEO cuidado |
| 7 | Modalidades | **Las tres direcciones conviven como modos seleccionables** («al gusto»; default **Línea**) — detalle en §2.7 |
| 8 | Placeholder | Página «en construcción» **ya desplegada** en la raíz del subdominio |
| 9 | Descargas | Uso **personal primero** + redistribuible público; **clave individual por software**; **sin subidas** de usuarios; rutas y streamer por PHP (§2.8) |
| 10 | Íconos/imágenes | **Sí** — assets por herramienta (ícono/logo/imagen), reales y trazables (§2.9) |
| 11 | Panel admin | **Sí (propuesto)** — catálogo + descargas + comentarios; edita en BD y **exporta a Markdown** para el repo; subidas solo admin (chunked estilo kofro); ADR-006 en Fase 2 |
| — | Regla dura | **No se pierde información** del contenido actual (ver §3) |

## 1. Fase 0 — Resultados (COMPLETADA ✔)

Sonda subida, leída y eliminada desde `software-tools.pcabrera.com` (verificado). Docroot real confirmado.

| Elemento | Resultado |
|---|---|
| PHP | **8.1.34** (LiteSpeed, compat Apache) |
| pdo_sqlite / sqlite3 | **SÍ** (SQLite lib **3.53.4**) |
| **FTS5** | **SÍ** — probado con tabla virtual + match real |
| Extensiones | intl, gd, zip, curl, mbstring, openssl, dom, iconv ✔ |
| Escritura en docroot / SQLite | **SÍ** (creación, escritura y borrado de archivos) |
| HTTPS | Activo |
| Límites | upload 2 MB · post 8 MB · ejecución **30 s** · memoria 512 MB |
| Docroot | `/home/coraz6/public_html/software-tools` |

**Implicaciones:** programar con compatibilidad **PHP 8.1**; búsqueda **FTS5 confirmada** (plan A, sin fallback); requests ligeros (<30 s); assets estáticos optimizados. *Si el cPanel ofrece selector de PHP ≥8.2 conviene evaluarlo (opcional); 8.1 basta para este diseño.*

## 2. Arquitectura propuesta

### 2.1 Estructura del repo (calcada de kofro)

```
software-tools/
├── app/
│   ├── public/          # lo desplegable (docroot): index.php, assets/, locales/, js/
│   ├── server/          # PHP: front controllers + lib/ (módulos namespaced) + config
│   └── data/            # catalog.sqlite (+comments), no se commitea (se genera/despliega)
├── ops/
│   ├── deploy/          # deploy.py (lftp, creds BWS en runtime; jamás en argv)
│   ├── tests/           # e2e/smoke + contrato de datos
│   └── tools/           # importador md→sqlite · verificador de paridad · sondas
├── docs/                # SPEC, ADRs, plans/ (este documento), guías
├── .agents/             # continuidad para agentes (CONTEXT/HANDOFF/STATE…)
├── AGENTS.md · SPEC.md · PLAN.md · README.md
└── (el sitio Astro v2 se retira de main; queda íntegro en el tag v2.0.0 y en la historia de git)
```

### 2.2 Stack y módulos

- **Servidor:** PHP 8.1, render server-side; `lib/` con módulos namespaced (Catalog, Timeline, Search, I18n, Comments, Seo). Sin framework ni dependencias PHP externas.
- **Datos:** un SQLite (`catalog.sqlite`) generado por el importador; la app solo lee para el contenido público; escribe únicamente en comentarios/moderación.
- **Cliente:** HTML renderizado por PHP (SEO primero) + capa JS progresiva (la experiencia).

### 2.3 Experiencia visual (dirección: scroll estilo Apple)

- **GSAP + ScrollTrigger** (pin, scrub, reveals) + **Lenis** (scroll inercial). Librerías **fijadas por versión y servidas localmente** (sin CDN — ethos del ecosistema).
- **View Transitions API** nativa para transiciones entre vistas (sensación webapp).
- Módulos ES organizados (no SPA): timeline, buscador, comentarios… un módulo por pieza.
- `prefers-reduced-motion` respetado; degradación elegante sin JS (el sitio funciona).
- **Boceto inicial de narrativa del home** (se evoluciona en Fase 1 con maquetas):
  1. **Hero** — "20+ años de software" con contador animado.
  2. **La línea** — recorrido 1985→hoy con pin por décadas y scrub.
  3. **Eras / spotlights** — escenas pegadas con las herramientas clave de cada época y su contexto personal.
  4. **Sucesiones** — el "río de reemplazos" (Eudora→Outlook, …) como visualización de linajes.
  5. **Cierre** — entrada al catálogo y buscador.
- Riqueza extra a evaluar en Fase 1 según el concepto ganador: micro-interacciones, tipografía cinética, canvas puntual — **nada que hipoteque performance** en hosting compartido.

### 2.4 i18n (es + en desde el inicio)

- **URLs:** español en la raíz (`/`, `/tool/eudora`), inglés bajo `/en/…`; `hreflang` + `x-default`; canonical por idioma.
- **UI:** cadenas en `app/public/locales/{es,en}.json` (patrón kofro; cero strings hardcodeados).
- **Contenido:** tabla `tool_i18n(tool_id, lang, context, …)`; el importador carga ES; las traducciones EN se producen en Fase 6 (borrador asistido + tu revisión) sin bloquear el desarrollo.
- Idioma por defecto: **es**; detección de navegador con recordado (patrón kofro).

### 2.5 Comentarios (propuesta de infraestructura propia)

Se propone **construirlos sobre el SQLite del proyecto** (coherente con "integral y autónomo"; Giscus queda descartado: ata al ecosistema GitHub y pierde control de los datos).

- Tabla `comments(id, tool_id, lang, author, body, created_at, status[pending|approved|spam], ip_hash)`.
- **Anti-spam sin cuentas:** honeypot + time-trap + rate-limit por `ip_hash` + longitud máxima + escape estricto (formato mínimo seguro, nunca HTML libre).
- **Moderación:** mini-panel `admin.php` con pass en BWS (`software-tools_ADMIN_PASS` — la creas tú); lista/borra/aprueba.
- **Respaldo:** snapshot periódico del `.sqlite` (script en `ops/tools/`) — los comentarios son datos vivos, a diferencia del catálogo.
- Plantilla HTML server-side (SEO neutro); sin notificaciones por correo en v1.

### 2.6 SEO

Título/descripción por página e idioma · `hreflang`/canonical · **sitemap.xml bilingüe** · `robots.txt` · Open Graph/Twitter · **JSON-LD** (WebSite; timeline como `ItemList`; fichas como `SoftwareApplication`/`CreativeWork`) · slugs estables · performance (JS diferido, fuentes optimizadas, caché de assets).

### 2.7 Modalidades — «tres lenguajes, un archivo» (aprobado 2026-10-05)

Las tres direcciones de la Fase 1 (Cinta / Línea / Máquina) conviven como **modos de presentación** del mismo sitio: una sola estructura y una sola base de datos; tres lenguajes visuales y de interacción que el visitante puede cambiar **al gusto**.

- **Selector de modo** visible y discreto; la preferencia se recuerda (`localStorage`) y es compartible por URL (`?modo=linea`); los modos pueden activarse/desactivarse desde configuración (el dueño decide cuáles están disponibles).
- **Un solo contenido:** los modos no duplican información ni SEO (canonical única, mismo HTML semántico); solo se descarga el código del modo activo (code-splitting).
- **Alcance:** la experiencia-home (el timeline) es la firma de cada modo; las páginas internas comparten estructura y reciben los *tokens* del modo activo (paleta, tipografía, acabado).
- **Default:** **Línea** (decidido 2026-10-05). El visitante puede cambiarlo al gusto en todo momento.

### 2.8 Descargas — archivos por herramienta (ajustado 2026-10-05)

**Propósito:** uso **personal primero** (el dueño descarga su propio software desde cualquier lugar); además, todo lo que sea redistribuible se ofrece público. **Los visitantes no pueden subir nada** — regla explícita del proyecto: los archivos los gestiona el dueño (vía agente/FTP).

- **Rutas (consideradas desde el diseño):** cada archivo tiene ruta lógica estable **servida por PHP**: `/descarga/<slug>/<archivo>`; la carpeta `downloads/<slug>/` queda protegida (`.htaccess` deny) y **nunca** hay URL directa a los ficheros. La ficha muestra su sección «Descargas» con tamaño y sha256 de cada archivo.
- **Tabla:** `downloads(tool_id, filename, bytes, sha256, visibility[publico|clave|enlace], license_note, source_url)`.
- **Clave individual por software** (así lo pidió el dueño): cada herramienta con descargas privadas tiene **su propia pass** (se guarda hasheada en la BD; se define desde el mini-panel de administración). Al validarla se abre una sesión corta *para esa herramienta* y se desbloquean sus archivos; rate-limit contra fuerza bruta.
- **Niveles:** 1) **Público** — redistribuible (freeware/liberado/permiso), con nota de origen; 2) **Clave** — personal o dudoso (pass por software); 3) **Solo enlace** — la ficha apunta a la fuente oficial.
- **Almacenamiento:** subida por FTP (sin el tope PHP de 2 MB), servido por PHP con `Content-Disposition: attachment` y `nosniff`. Revisar la cuota de disco del hosting antes de poblar.
- **Legal (regla de oro):** alojar solo lo redistribuible; ante duda, nivel 3. Nada con copyright vigente se aloja.
- **Pendiente del dueño:** inventario de archivos (tamaños/hashes/clasificación) — se hará cuando los comparta.

### 2.9 Íconos, logos e imágenes por herramienta (aprobado 2026-10-05)

**Sí** — cada herramienta podrá tener material visual propio, con **procedencia trazable**:

- **Tipos:** `icon` (marca pequeña para listas/fotogramas, 64–512 px), `logo` (vector preferente para fondos oscuros), `cover` (imagen o captura del software en uso, para la ficha y los spotlight de las modalidades).
- **Esquema:** `tool_assets(tool_id, kind[icon|logo|cover], file, source, license_note)` + carpeta `assets/tools/<slug>/` en el repo.
- **Política de fuentes:** assets **reales** (extraídos del software que el dueño tiene, capturas propias, kits oficiales o archivos comunitarios); **no se inventan** iconos a mano — si no existe un ícono auténtico, se usa un monograma tipográfico de la identidad del sitio. Los logos son marcas de sus respectivos dueños; uso identificativo.
- **Uso:** los íconos alimentan las tres modalidades (fotogramas de la cinta, estaciones de la línea, ventanas del directorio): más riqueza visual sin tocar el contenido.
- **Rendimiento:** SVG cuando exista; PNG/WebP optimizados con tamaños declarados; carga diferida.

## 3. Preservación del contenido (regla dura)

1. **Inventario de partida:** 28 fichas reales + plantilla `_template.md` (la plantilla no se publica en v3; se conserva en el repo — *decisión menor B*). Sin imágenes en el sitio hoy: 1 referencia huérfana detectada (`chi-writer` apunta a `/images/tools/chi-writer.jpg`, archivo que nunca existió en el repo) — el campo se preserva y se resolverá o marcará durante la importación; el esquema v3 mantiene soporte de imagen.
2. **Importador con verificación campo por campo** (name, slug, year, usedUntil, category, tags[], context, successor, successorSlug, related[], published) → filas SQLite + **reporte automático md↔BD**.
3. **Checksums de contenido:** conteo de palabras + hash normalizado de cada `context` — cualquier pérdida falla ruidosamente.
4. **Verificación página vieja ↔ nueva:** para cada slug, el texto normalizado de la ficha v2 debe aparecer íntegro en la v3 (caza de frases perdidas en el rediseño).
5. **Respaldos permanentes:** tag `v2.0.0`, bundle local y `tools.json` histórico; el Markdown sigue versionado como fuente de autoría.
6. **Nada se edita "de paso" durante la migración:** mejoras editoriales van en commits separados y revisables DESPUÉS de probar la paridad 1:1.
7. **Punto de partida (indicación del dueño, 2026-10-05):** la v3 se construye primero con **lo ya registrado** (las 28 fichas); en paralelo se **localiza la información faltante** (auditoría en [../notes/2026-10-05-fichas-pendientes.md](../notes/2026-10-05-fichas-pendientes.md)).

## 4. Fases del plan de acción

| # | Fase | Entregable | Gate |
|---|------|-----------|------|
| 0 | Verificación de hosting | ✅ Completada (§1) | ✔ |
| 1 | **Concepto y dirección visual** | 2–3 maquetas de concepto (scroll Apple-like) + dirección elegida + librerías confirmadas | **Tu OK** |
| 2 | **SPEC v3 + ADR-005 (arquitectura) + esquema SQLite definitivo + sistema de modalidades + descargas (clave por software) + assets (íconos/imágenes) + reestructura del repo** | Documentos | **Tu OK** |
| 3 | Importación + verificación de paridad | Importador + `catalog.sqlite` + reporte | ✔ automático |
| 4 | **Prototipo local** (Docker php:8.1 + SQLite): home-timeline, ficha, búsqueda FTS | Prototipo navegable | **Tu revisión** |
| 5 | Implementación completa (vistas, comentarios, **admin: catálogo/descargas/comentarios**, i18n UI, **modalidades seleccionables**, descargas con clave, **assets**, SEO) | App v3 | Tests ✔ |
| 6 | Contenido EN: traducción asistida + revisión | Fichas bilingües | **Tu revisión** |
| 7 | **Deploy a producción** + verificación (FTP + navegador) + retiro de GitHub Pages (workflow eliminado) | Sitio en vivo | ✔ verificado |
| 8 | Cierre: tag `v3.0.0` (bien documentado), docs-as-built, registros | Repo completo | ✔ |

## 5. Decisiones menores pendientes (para Fase 2)

- **A.** Comentarios: ¿publicación directa con moderación retroactiva (recomendado para empezar; tráfico bajo) o cola previa a publicación?
- **B.** ¿Confirmas excluir `_template.md` de la publicación? (recomendado)
- **C.** *(Opcional)* ¿revisas en cPanel si hay selector de PHP ≥8.2?
- **D.** Libs definitivas (GSAP/Lenis/…) — se cierran con las maquetas de la Fase 1.

## 6. Riesgos y mitigaciones

- **PHP 8.1** → compatibilidad 8.1 en todo el código (ya previsto).
- **Límite de 30 s por request** → consultas simples; cache de fragmentos solo si hiciera falta (dataset diminuto: riesgo bajo).
- **Ambición visual** → gates de concepto (F1) y prototipo (F4) antes de la implementación completa; performance budget explícito.
- **Verificación HTTP en pcabrera.com** (histórico de bot-check) → la sonda respondió bien con UA de navegador; si estorba: verificación por FTP + confirmación visual tuya.
- **Traducción EN** → borrador por agente + tu revisión explícita antes de publicar nada.

## 7. Lo que NO cambia

- Contenido, tono personal, slugs y el español como idioma base (el inglés es capa nueva).
- Identidad (tema oscuro, tipografía display coherente) — evoluciona en F1, no se pierde.
- [ADR-003](../adr/003-modelo-de-datos.md): Markdown como fuente de autoría (el importador es su evolución natural).
- El repo en GitHub como fuente del código y de la historia (el tag `v2.0.0` conserva el pasado íntegro).

---

*Fases 1–3 completadas (2026-10-05): las tres maquetas aprobadas como **modos seleccionables**; **SPEC v3 + ADRs 005–008** escritos (specs v2 retiradas del repo); **`catalog.sqlite` generada con paridad 48/48 ✓** y esqueleto kofro plantado. Siguiente paso: **Fase 4** — prototipo local navegable.*
