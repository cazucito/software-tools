# AGENTS.md - Guidelines para Agentes de IA

## Sobre este proyecto

**software-tools v2** es un archivo histórico de herramientas utilizadas en 20+ años de docencia e ingeniería. Es un sitio estático generado con Astro, hospedado en GitHub Pages.

## Stack Tecnológico

| Capa | Tecnología |
|------|------------|
| Framework | Astro 4.x |
| Estilos | Tailwind CSS |
| Lenguaje | TypeScript |
| Búsqueda | Fuse.js |
| Comentarios | Giscus (GitHub Discussions) |
| Hosting | GitHub Pages |

## Estructura del proyecto

```
software-tools/
├── src/
│   ├── content/tools/     # Un .md por herramienta
│   ├── data/              # tools.json (generado)
│   ├── pages/             # Rutas de Astro
│   └── components/        # Componentes Astro
├── public/images/tools/   # Imágenes locales
├── scripts/               # generate-data.js
└── docs/adr/              # Decisiones arquitectónicas
```

## Convenciones de commits

Prefijo obligatorio:
- `content:` nueva herramienta o edición de contenido
- `feat:` nueva funcionalidad
- `fix:` corrección de bug
- `data:` cambios en generación de datos
- `style:` cambios visuales/CSS
- `docs:` documentación

Ejemplo: `content: agrega Chi Writer con contexto de tesis`

## Flujo de trabajo

### Antes de editar
1. Leer `CLAUDE.md` para contexto específico
2. Ejecutar `npm run generate-data` si trabajas con contenido
3. Verificar que el slug no exista en `/src/content/tools/`

### Al agregar una herramienta
1. Crear `/src/content/tools/{slug}.md` con frontmatter completo
2. Agregar imagen a `/public/images/tools/{slug}.{ext}` (opcional pero recomendado)
3. Ejecutar `npm run generate-data` para regenerar JSON
4. Verificar en `npm run dev` que se vea correctamente

### Frontmatter obligatorio
```yaml
---
name: "Nombre de la herramienta"
slug: "nombre-de-la-herramienta"
year: 1995
category: "editor"  # editor|lenguaje|database|infra|otro
published: true
context: "Historia personal de uso..."
---
```

## Comandos útiles

```bash
# Desarrollo
npm run dev

# Generar datos desde Markdown
npm run generate-data

# Build de producción
npm run build

# Preview local del build
npm run preview
```

## Reglas de oro

1. **Slug único**: kebab-case, sin espacios ni caracteres especiales
2. **Año como número**: para poder ordenar cronológicamente
3. **Contexto personal obligatorio**: la historia es el valor diferencial
4. **Imágenes locales preferidas**: en `/public/images/tools/`
5. **No enlaces de descarga directa**: solo referencias oficiales
6. **Un agente puede hacer cualquier tarea**: siguiendo estas guidelines

## Recursos adicionales

- `docs/adr/` - Architecture Decision Records
- `docs/SPEC.md` - Especificación funcional completa
- `CLAUDE.md` - Contexto específico para Claude

## Dudas

Si algo no está claro, preguntar antes de asumir. Este proyecto prioriza la calidad del contenido sobre la velocidad de implementación.
