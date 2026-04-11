---
# TEMPLATE PARA NUEVAS HERRAMIENTAS
# Copia este archivo, renómbralo con el slug y completa los campos
# Ejemplo: mi-herramienta.md

# ============================================
# FRONTMATTER (METADATOS OBLIGATORIOS)
# ============================================

name: "Nombre de la Herramienta"           # Nombre legible (requerido)
slug: "nombre-de-la-herramienta"            # Identificador único en kebab-case (requerido)
year: 1995                                  # Año de primer uso, número entero (requerido)
category: "editor"                          # Categoría principal (requerido)
                                            # Opciones: editor, lenguaje, database, infra, 
                                            #           framework, tool, os, other

tags:                                       # Array de tags para clasificación
  - "tag1"
  - "tag2"
  - "tag3"

# ============================================
# FRONTMATTER (METADATOS OPCIONALES)
# ============================================

image: "/images/tools/slug-de-la-herramienta.jpg"  # Ruta a imagen (opcional pero recomendado)

context: >                                  # Descripción corta del contexto de uso (obligatorio)
  Breve historia personal de por qué y cómo usaste esta herramienta.
  Esta frase aparece en listados y búsquedas.

usedUntil: 1998                             # Año en que dejaste de usarla (opcional)

successor: "Nombre del Sucesor"             # Herramienta que reemplazó a esta (opcional)
successorSlug: "slug-del-sucesor"           # Slug de la herramienta sucesora (opcional)

related:                                    # Slugs de herramientas relacionadas (opcional)
  - "herramienta-relacionada-1"
  - "herramienta-relacionada-2"

published: true                             # true para publicar, false para draft (requerido)

---

# Nombre de la Herramienta

Aquí va el contenido detallado en Markdown. Estructura sugerida:

## El problema / Contexto

¿Qué necesidad resolvía esta herramienta? ¿Por qué la elegiste?

## Características principales

- Lista de features importantes
- Lo que la hacía especial
- Comparación con alternativas de la época

## Contexto personal

Tu historia específica con esta herramienta:
- En qué proyecto la usaste
- Anécdotas memorables
- Frustraciones o "aha moments"

## Transición

¿Por qué la dejaste de usar? ¿Qué vino después?

---

## CHECKLIST ANTES DE PUBLICAR

- [ ] El slug es único (no existe otro archivo con el mismo nombre)
- [ ] El año es un número (no string)
- [ ] La categoría está en la lista permitida
- [ ] El contexto tiene entre 50-200 caracteres
- [ ] Hay al menos 2-3 tags relevantes
- [ ] El contenido tiene tu voz personal (no genérico tipo Wikipedia)
- [ ] Si hay sucesor, el slug del sucesor existe o está planeado
- [ ] Las herramientas related existen o se agregarán después

## COMANDOS POST-AGREGADO

Después de crear el archivo, ejecuta:

```bash
npm run generate-data      # Regenera el JSON de datos
npm run build              # Verifica que el build funcione
```

Luego haz commit:

```bash
git add src/content/tools/tu-herramienta.md
git commit -m "content: agrega [Nombre de la Herramienta]"
git push origin main
```

El sitio se actualizará automáticamente en GitHub Pages.
