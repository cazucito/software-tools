# Especificación Funcional: software-tools v2

## 1. Resumen

| Aspecto | Descripción |
|---------|-------------|
| **Nombre** | software-tools |
| **Versión** | 2.0.0 |
| **Propósito** | Archivo histórico de herramientas utilizadas en 20+ años de docencia |
| **Público objetivo** | Estudiantes, colegas, curiosos de la evolución tecnológica |
| **Hosting** | GitHub Pages |

## 2. Features

### 2.1 Core
- [ ] **Timeline visual**: Navegación por décadas (1990s, 2000s, 2010s, 2020s)
- [ ] **Página por herramienta**: Contenido completo con contexto personal
- [ ] **Índice por tags**: Nube de tags navegable
- [ ] **Índice por categoría**: Editores, lenguajes, bases de datos, infraestructura

### 2.2 Interactivas
- [ ] **Búsqueda global**: Fuse.js con fuzzy matching
- [ ] **Comentarios**: Giscus en cada herramienta
- [ ] **Navegación relacionada**: "Siguiente/anterior" por año o categoría

### 2.3 Contenido
- [ ] **Markdown + frontmatter**: Formato de autoría
- [ ] **Imágenes locales**: `/public/images/tools/`
- [ ] **Enlaces oficiales**: Sin descargas directas

## 3. Estructura de URLs

| Ruta | Descripción |
|------|-------------|
| `/` | Timeline por décadas |
| `/search` | Búsqueda con resultados |
| `/tools` | Lista completa de herramientas |
| `/tools/{slug}` | Página individual de herramienta |
| `/tags` | Nube de tags |
| `/tags/{tag}` | Herramientas filtradas por tag |
| `/categories/{category}` | Herramientas por categoría |

## 4. Esquema de datos

### 4.1 Frontmatter (Markdown)
```yaml
name: string           # Nombre de la herramienta (requerido)
slug: string           # Identificador único, kebab-case (requerido)
year: number           # Año de primer uso (requerido)
decade: string         # "1990s", "2000s", etc. (auto-generado)
category: enum         # "editor", "lenguaje", "database", "infra" (requerido)
tags: string[]         # Tags para clasificación
image: string          # Ruta a imagen (opcional)
context: string        # Historia personal de uso (requerido)
usedUntil: number      # Año de último uso (opcional)
successor: string      # Nombre de herramienta sucesora (opcional)
successorSlug: string  # Slug de sucesora (opcional)
related: string[]      # Slugs de herramientas relacionadas
published: boolean     # Mostrar/ocultar (requerido)
```

### 4.2 JSON Generado
```typescript
interface ToolsData {
  tools: Tool[];
  tags: Record<string, { count: number; tools: string[] }>;
  timeline: Record<string, string[]>; // decade -> slugs
  categories: Record<string, string[]>;
}
```

## 5. UI/UX

### 5.1 Diseño
- **Tema**: Oscuro (continuidad con jekyll-theme-slate)
- **Tipografía**: Sans-serif moderna
- **Colores**: Gris oscuro base, acentos por categoría

### 5.2 Componentes principales
- `Timeline`: Visualización cronológica
- `ToolCard`: Tarjeta resumen en listados
- `TagCloud`: Nube de tags con tamaño por frecuencia
- `SearchBox`: Input con resultados en tiempo real
- `Comments`: Embed de Giscus

## 6. Flujo de datos

```
Markdown (autoría)
    ↓
generate-data.js (build)
    ↓
tools.json (datos consolidados)
    ↓
Astro build
    ↓
HTML estático + índice de búsqueda
    ↓
GitHub Pages
```

## 7. Scripts npm

```json
{
  "dev": "astro dev",
  "build": "npm run generate-data && astro build",
  "generate-data": "node scripts/generate-data.js",
  "preview": "astro preview"
}
```

## 8. CI/CD

GitHub Actions:
1. Checkout
2. Setup Node 20
3. `npm ci`
4. `npm run generate-data`
5. `npm run build`
6. Deploy a GitHub Pages

## 9. Multi-agente

### Archivos de contexto
- `AGENTS.md`: Guidelines generales para todos los agentes
- `CLAUDE.md`: Contexto específico para Claude
- `docs/adr/`: Decisiones arquitectónicas

### Convenciones
- Commits: `content:`, `feat:`, `fix:`, `data:`, `style:`
- Un agente puede hacer cualquier tarea siguiendo guidelines
- Handoff mediante archivos de estado

## 10. Roadmap

### Fase 1: Fundación
- [ ] Setup Astro + Tailwind
- [ ] Script generate-data.js
- [ ] Estructura de carpetas
- [ ] Componentes base

### Fase 2: Contenido
- [ ] 3-5 herramientas de ejemplo
- [ ] Página timeline
- [ ] Página individual

### Fase 3: Features
- [ ] Búsqueda Fuse.js
- [ ] Comentarios Giscus
- [ ] Índices por tag/categoría

### Fase 4: Población
- [ ] Migrar/memorializar era Oracle
- [ ] Agregar herramientas históricas (Chi Writer, Turbo C)
- [ ] Agregar herramientas actuales

### Fase 5: Polish
- [ ] SEO
- [ ] RSS
- [ ] Analytics
