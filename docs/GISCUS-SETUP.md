# Configuración de Giscus para Comentarios

Esta guía explica cómo habilitar los comentarios en software-tools usando Giscus.

## ¿Qué es Giscus?

Giscus es un sistema de comentarios basado en GitHub Discussions. Es gratuito, sin publicidad, y se integra perfectamente con GitHub.

## Pasos de configuración

### 1. Habilitar GitHub Discussions

1. Ve a tu repositorio: https://github.com/cazucito/software-tools
2. Click en **Settings** (pestaña superior)
3. En la barra lateral izquierda, busca **General** → **Features**
4. Activa la casilla **Discussions**

### 2. Crear categoría de Discussions

1. Ve a la pestaña **Discussions** de tu repositorio
2. Click en **Manage categories** (botón a la derecha)
3. Click en **New category**
4. Nombre: `Tools`
5. Descripción: `Comentarios sobre herramientas`
6. Formato: **Open-ended discussion**
7. Click en **Create**

### 3. Obtener IDs de Giscus

1. Ve a https://giscus.app
2. En el formulario:
   - **Repository**: `cazucito/software-tools`
   - **Page ↔ Discussions mapping**: `Specific discussion term`
   - **Discussion term**: (deja vacío, se configura por página)
   - **Discussion category**: `Tools`
   - **Features**: Activa todas las que quieras
   - **Theme**: `Dark`

3. Click en **Generate code**
4. Copia los valores de:
   - `data-repo-id` (ejemplo: `R_kgDO...`)
   - `data-category-id` (ejemplo: `DIC_kwDO...`)

### 4. Actualizar el componente

Edita `src/components/Comments.astro`:

```astro
<script
  src="https://giscus.app/client.js"
  data-repo="cazucito/software-tools"
  data-repo-id="R_kgDO..."  <-- PEGA AQUÍ
  data-category="Tools"
  data-category-id="DIC_kwDO..."  <-- PEGA AQUÍ
  data-mapping="specific"
  data-term={slug}
  ...
>
```

### 5. Eliminar el notice

Una vez configurado, puedes eliminar el div de `giscus-setup-notice` del componente.

### 6. Redeploy

```bash
git add src/components/Comments.astro
git commit -m "config: habilita Giscus para comentarios"
git push origin main
```

Los comentarios estarán disponibles en cada página de herramienta.

## ¿Cómo funcionan?

- Cada herramienta tiene su propia discussion en GitHub
- Los comentarios se crean automáticamente cuando alguien comenta
- Puedes moderar desde GitHub Discussions
- Se sincronizan bidireccionalmente

## Personalización

### Temas disponibles
- `light` - Tema claro
- `dark` - Tema oscuro
- `dark_dimmed` - Oscuro atenuado
- `dark_high_contrast` - Alto contraste
- `preferred_color_scheme` - Respeta preferencia del sistema

### Opciones adicionales
- `data-reactions-enabled="1"` - Reacciones con emojis
- `data-emit-metadata="1"` - Metadatos en eventos
- `data-input-position="top"` - Caja de comentario arriba

## Troubleshooting

### Los comentarios no aparecen
- Verifica que Discussions esté habilitado
- Verifica que los IDs sean correctos
- Abre DevTools → Network para ver errores

### Las discussions no se crean
- Asegúrate de que la categoría "Tools" exista
- El primer comentario crea la discussion automáticamente

### Quiero cambiar el mapeo
- `data-mapping="pathname"` usa la URL completa
- `data-mapping="specific"` + `data-term` usa un valor específico (como el slug)
- Consulta https://giscus.app para más opciones
