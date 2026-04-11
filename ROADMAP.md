# Roadmap: software-tools v2

## Fase 1: Fundación
> Objetivo: Estructura base funcional

- [ ] Inicializar proyecto Astro con Tailwind
- [ ] Crear script `scripts/generate-data.js`
- [ ] Definir tipos TypeScript para Tool
- [ ] Configurar estructura de carpetas
- [ ] Componentes base: Layout, Header, Footer

**Entregable**: `npm run dev` funciona con página de inicio básica

---

## Fase 2: Primeras herramientas
> Objetivo: Validar flujo de contenido

- [ ] Crear 3-5 herramientas de ejemplo:
  - [ ] Chi Writer (era 1990s)
  - [ ] Turbo C (era 1990s)
  - [ ] NetBeans 8.2 (era Oracle)
  - [ ] VS Code (era moderna)
- [ ] Componente `ToolCard`
- [ ] Página `/tools/{slug}` individual
- [ ] Página `/tools` listado completo

**Entregable**: Navegación básica entre herramientas funciona

---

## Fase 3: Timeline y navegación
> Objetivo: Experiencia de descubrimiento

- [ ] Componente `Timeline` por décadas
- [ ] Página `/` con timeline visual
- [ ] Página `/tags` con nube de tags
- [ ] Página `/tags/{tag}` filtrado
- [ ] Página `/categories/{category}` filtrado

**Entregable**: Usuario puede navegar por década, tag o categoría

---

## Fase 4: Búsqueda
> Objetivo: Encontrar cualquier herramienta

- [ ] Integrar Fuse.js
- [ ] Generar índice de búsqueda en build
- [ ] Componente `SearchBox`
- [ ] Página `/search` con resultados
- [ ] Búsqueda en header (global)

**Entregable**: Búsqueda funciona con fuzzy matching

---

## Fase 5: Comentarios
> Objetivo: Interacción con visitantes

- [ ] Habilitar GitHub Discussions
- [ ] Componente `Comments` con Giscus
- [ ] Integrar en página `/tools/{slug}`
- [ ] Configurar tema oscuro
- [ ] Probar flujo de comentario

**Entregable**: Visitantes pueden comentar en herramientas

---

## Fase 6: Población histórica
> Objetivo: Contenido sustancial

- [ ] Era 1990s:
  - [ ] DOS 6.22
  - [ ] Borland Pascal
  - [ ] WordPerfect
- [ ] Era 2000s:
  - [ ] JBuilder
  - [ ] Eclipse
  - [ ] Visual Basic 6
  - [ ] Oracle Forms
- [ ] Era Oracle (2010s):
  - [ ] JDK 5/6/7/8
  - [ ] Oracle SQL Developer
  - [ ] VirtualBox
  - [ ] WinSCP
- [ ] Era moderna:
  - [ ] IntelliJ IDEA
  - [ ] Docker
  - [ ] Kubernetes
  - [ ] Cursor/Winds

**Entregable**: 20+ herramientas documentadas

---

## Fase 7: Deploy y polish
> Objetivo: Producción lista

- [ ] Configurar GitHub Actions
- [ ] Deploy a GitHub Pages
- [ ] SEO básico (meta tags, sitemap)
- [ ] RSS feed
- [ ] Favicon y manifest
- [ ] Analytics (opcional)

**Entregable**: Sitio público en cazucito.github.io/software-tools

---

## Fase 8: Post-launch
> Objetivo: Mejoras continuas

- [ ] Link checking automatizado
- [ ] Prettier/eslint consistente
- [ ] Tests de build
- [ ] Dark/light mode toggle
- [ ] Mejoras de performance (lazy loading, etc.)

**Entregable**: Proceso de mantenimiento sostenible

---

## Estado actual

**Fase**: 0 - Planificación completada
**Próximo**: Fase 1 - Fundación

## Historial

- 2025-04-11: Aprobada arquitectura v2
- 2025-04-11: Creada documentación (ADRs, SPEC, AGENTS, CLAUDE)
- 2025-04-11: Rama main establecida, tags legacy creados
