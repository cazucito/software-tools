# CLAUDE.md - Contexto para Claude

## Visión del proyecto

software-tools es mi archivo personal de herramientas utilizadas en más de 20 años de docencia e ingeniería de software. No es un catálogo genérico: es una línea de tiempo evolutiva con mi historia personal.

### Lo que lo hace único

Cada herramienta incluye **contexto personal**:
- "Chi Writer lo usé para mi tesis de licenciatura porque era el único que manejaba caracteres chinos correctamente..."
- "Dejé Eclipse después de 8 años cuando IntelliJ me demostró que el refactoring podía ser instantáneo..."
- "VS Code llegó tarde a mi workflow, pero ahora es indispensable gracias a..."

Este contexto es el valor diferencial. Sin él, es solo otro listado de software.

## Features clave

### 1. Estructura de contenido
- **Un post por herramienta**: Atomizar permite enlazar entre sí
- **Tags fluidos**: No jerarquías rígidas, descubrimiento orgánico
- **Timeline por décadas**: 1990s, 2000s, 2010s, 2020s

### 2. Búsqueda con Fuse.js
- Búsqueda instantánea en nombre, tags, categoría, año
- Fuzzy matching (tolerancia a errores tipográficos)
- Índice generado en build time

### 3. Comentarios con Giscus
- Cada herramienta tiene su discussion en GitHub
- Los visitantes pueden compartir sus propias experiencias
- Moderación nativa de GitHub

### 4. Imágenes locales
- Cada herramienta puede tener screenshot o logo
- Almacenadas en `/public/images/tools/`
- Fallback elegante si no hay imagen

## Esquema de frontmatter

```yaml
---
name: "Chi Writer"                    # Nombre legible
slug: "chi-writer"                    # Identificador único (kebab-case)
year: 1995                            # Año de primer uso (número)
decade: "1990s"                       # Auto-generado desde year
category: "editor"                    # editor|lenguaje|database|infra|otro
tags: ["dos", "scientific-writing", "thesis", "chinese-characters"]
image: "/images/tools/chi-writer.jpg"  # Opcional pero recomendado
context: >                            # Historia personal (obligatorio)
  Lo usé para mi tesis de licenciatura en 1995-1998.
  Era el único editor que manejaba correctamente los caracteres
  chinos que necesitaba para citar fuentes en mandarín.
usedUntil: 1998                       # Cuándo lo dejé de usar
successor: "LaTeX"                    # Qué usé después
successorSlug: "latex"                # Slug de la sucesora
related: ["dos-6.22", "word-perfect"] # Slugs de herramientas relacionadas
published: true                       # true para publicar, false para draft
---
```

## Decisiones de contenido

### ¿Qué incluir?
- Herramientas que usé profesionalmente (no solo probé)
- Con contexto de uso memorable
- Desde Chi Writer (1995) hasta lo que use hoy

### ¿Qué NO incluir?
- Listados exhaustivos ("todos los editores de 2024")
- Herramientas sin historia personal
- Contenido genérico que pueda encontrarse en Wikipedia

### Tono
- Personal pero profesional
- Nostálgico cuando corresponde
- Crítico constructivo cuando una herramienta falló
- Celebratorio cuando una herramienta cambió mi workflow

## Catálogo inicial propuesto

### Era universitaria (1990s)
- Chi Writer
- Turbo C
- Borland Pascal
- DOS 6.22

### Era corporativa temprana (2000s)
- JBuilder
- Eclipse
- Visual Basic 6
- Oracle Forms

### Era Oracle docente (2005-2018)
- NetBeans
- JDK 5/6/7/8
- Oracle SQL Developer
- VirtualBox

### Era moderna (2018-presente)
- IntelliJ IDEA
- VS Code
- Docker
- Kubernetes

## Metadatos de herramientas

Al agregar una herramienta, considerar:

1. **Slug**: Único, descriptivo, kebab-case
2. **Año**: Preciso si se conoce, aproximado si no
3. **Categoría**: La más relevante si cruza categorías
4. **Tags**: 3-7 tags que describan características clave
5. **Contexto**: Mínimo 2-3 oraciones de historia personal
6. **Relaciones**: Qué herramientas son predecesoras/sucesoras

## Reglas específicas para Claude

1. **Priorizar calidad sobre cantidad**: Mejor 5 herramientas bien documentadas que 20 incompletas
2. **Mantener voz consistente**: Primera persona, experiencia personal
3. **Conectar herramientas**: Usar `related`, `successor`, `successorSlug` para crear grafos de navegación
4. **Validar slugs**: Nunca repetir, siempre kebab-case
5. **Preguntar ante la duda**: Si no tengo contexto suficiente para escribir sobre una herramienta, preguntar en lugar de inventar

## Historia del proyecto

- **2005-2018**: Sitio Moodle con enlaces de descarga para cursos Oracle
- **2018-2025**: GitHub Pages con Jekyll (en desuso)
- **2025**: Rebirth como archivo histórico personal (v2.0.0)

Estado anterior preservado en tag `v1.0.0-legacy`.

## Recursos

- Repositorio: https://github.com/cazucito/software-tools
- Sitio: https://cazucito.github.io/software-tools (próximamente)
- Discussions: Habilitadas para comentarios Giscus
