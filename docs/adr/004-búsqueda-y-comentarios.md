# ADR-004: Búsqueda y Comentarios

## Estado
Aprobado

## Contexto
Se requieren dos features interactivas sin backend propio:
1. Búsqueda de herramientas
2. Comentarios por herramienta

## Decisión 1: Búsqueda
**Fuse.js** para búsqueda client-side con índice estático.

### Implementación
- Índice generado en build: `/public/search-index.json`
- Librería: Fuse.js v7.x
- Búsqueda en: nombre, tags, categoría, año
- UI: Página dedicada `/search` + componente en header

### Por qué Fuse.js
- Ligero (~10KB)
- Fuzzy search (tolerancia a errores tipográficos)
- No requiere backend
- Fácil integración con Astro Islands

## Decisión 2: Comentarios
**Giscus** usando GitHub Discussions como backend.

### Implementación
- Cada herramienta tiene su propia discussion
- Mapeo por pathname: `/tools/chi-writer` → discussion específica
- Tema: oscuro (match con diseño)
- Reacciones habilitadas

### Por qué Giscus
- Gratis (usa GitHub Discussions)
- Sin backend propio que mantener
- Moderación nativa de GitHub
- Sincronización bidireccional

## Consecuencias
- Tamaño de bundle aumenta (Fuse.js + Giscus)
- GitHub Discussions debe estar habilitado en el repo
- Las discussions se crean automáticamente al primer comentario
