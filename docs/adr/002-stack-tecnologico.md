# ADR-002: Stack Tecnológico

## Estado
Aprobado

## Contexto
Se requiere un stack que:
- Sea compatible con GitHub Pages (hosting estático)
- Permita generación de sitios desde Markdown
- Soporte búsqueda client-side
- Sea mantenible por agentes de IA

## Opciones consideradas

| Opción | Pros | Contras |
|--------|------|---------|
| Jekyll (actual) | Nativo GitHub Pages | Limitado, Ruby, difícil para agentes |
| Hugo | Rápido, Go | Templating complejo (Go templates) |
| **Astro** | **Islands architecture, MDX nativo, agent-friendly** | Build más lento que Hugo |
| Next.js | Flexible | Overkill para contenido estático |
| 11ty | Simple | Menos ecosistema que Astro |

## Decisión
Usar **Astro 4.x** como framework principal.

## Justificación
1. **Content-first**: Diseñado específicamente para sitios de contenido
2. **Islands Architecture**: Hidrata solo lo necesario (búsqueda, comentarios)
3. **Markdown nativo**: Todos los agentes entienden MD frontmatter
4. **TypeScript**: Tipado ayuda a mantener consistencia
5. **GitHub Pages**: Build estático compatible

## Dependencias principales
```json
{
  "astro": "^4.x",
  "@astrojs/tailwind": "^5.x",
  "fuse.js": "^7.x",
  "gray-matter": "^4.x"
}
```

## Consecuencias
- Migración completa desde Jekyll (tema slate)
- Nuevo flujo de build con GitHub Actions
- Reestructuración total del repositorio
