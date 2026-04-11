# ADR-003: Modelo de Datos y Contenido

## Estado
Aprobado

## Contexto
Se necesita balancear:
- Facilidad de edición (Markdown para humanos/agentes)
- Consultas eficientes (JSON para búsqueda/filtros)
- Consistencia de esquema

## Decisión
**Dual-source**: Markdown para autoría + JSON generado para consumo.

## Esquema de datos

### Fuente de verdad: Markdown + Frontmatter
```yaml
---
name: "Chi Writer"
slug: "chi-writer"
year: 1995
decade: "1990s"
category: "editor"
tags: ["dos", "scientific-writing", "thesis"]
image: "/images/tools/chi-writer.jpg"
context: "Licenciatura - redacción de tesis con caracteres chinos"
usedUntil: 1998
successor: "LaTeX"
successorSlug: "latex"
related: ["dos-6.22", "word-perfect"]
published: true
---
```

### Derivado: JSON consolidado
Ubicación: `/src/data/tools.json`
Generado por: `scripts/generate-data.js`

```json
{
  "tools": [...],
  "tags": { "tag": { "count": n, "tools": [...] } },
  "timeline": { "1990s": [...], "2000s": [...] },
  "categories": { "editor": [...] }
}
```

## Proceso de generación
1. Leer todos `/src/content/tools/*.md`
2. Parsear frontmatter con gray-matter
3. Validar esquema (campos obligatorios)
4. Generar estructuras derivadas (tags, timeline)
5. Escribir `tools.json`

## Campos obligatorios
- `name`: Nombre de la herramienta
- `slug`: Identificador único (kebab-case)
- `year`: Año de primer uso (número)
- `category`: Categoría principal
- `published`: Booleano

## Campos opcionales pero recomendados
- `context`: Historia personal de uso
- `tags`: Array para clasificación
- `image`: Ruta a imagen local

## Consecuencias
- Build en dos fases: generar datos → build Astro
- Posible desincronización si se edita JSON manualmente
- Necesidad de validación de esquema
