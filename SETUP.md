# Guía de Configuración de software-tools

Este documento contiene las instrucciones para configurar las funcionalidades que requieren pasos manuales.

---

## 1. GitHub Pages (Deploy automático)

El sitio se despliega automáticamente a GitHub Pages cuando haces push a la rama `main`.

### Activar GitHub Pages

1. Ve a tu repositorio: https://github.com/cazucito/software-tools
2. Click en **Settings** → **Pages** (en el menú lateral izquierdo)
3. En **Build and deployment**:
   - **Source**: Selecciona **GitHub Actions**
4. Guarda los cambios

### Verificar el deploy

1. Ve a la pestaña **Actions** de tu repositorio
2. Deberías ver el workflow "Deploy to GitHub Pages" ejecutándose
3. Una vez completado (✅), el sitio estará disponible en:
   **https://cazucito.github.io/software-tools**

---

## 2. Giscus (Comentarios)

Giscus usa GitHub Discussions como backend para los comentarios.

### Paso 1: Habilitar Discussions

1. Ve a tu repositorio: https://github.com/cazucito/software-tools
2. Click en **Settings** → **General**
3. En la sección **Features**, activa **Discussions**

### Paso 2: Crear categoría

1. Ve a la pestaña **Discussions** del repositorio
2. Click en **Manage categories** (botón a la derecha)
3. Click en **New category**
4. Configura:
   - **Name**: `Tools`
   - **Description**: `Comentarios sobre herramientas`
   - **Format**: **Open-ended discussion**
5. Click en **Create**

### Paso 3: Obtener IDs de Giscus

1. Ve a https://giscus.app
2. Completa el formulario:
   - **repository**: `cazucito/software-tools`
   - **Page ↔ Discussions mapping**: `Specific discussion term`
   - **Discussion category**: `Tools`
   - **Theme**: `Dark`
3. Click en **Generate code**
4. Copia los valores:
   - `data-repo-id` (ejemplo: `R_kgDO...`)
   - `data-category-id` (ejemplo: `DIC_kwDO...`)

### Paso 4: Actualizar el código

Edita el archivo `src/components/Comments.astro`:

```astro
<script
  src="https://giscus.app/client.js"
  data-repo="cazucito/software-tools"
  data-repo-id="R_kgDO...TU_REPO_ID_AQUI..."
  data-category="Tools"
  data-category-id="DIC_kwDO...TU_CATEGORY_ID_AQUI..."
  data-mapping="specific"
  data-term={slug}
  data-strict="0"
  data-reactions-enabled="1"
  data-emit-metadata="0"
  data-input-position="top"
  data-theme="dark"
  data-lang="es"
  data-loading="lazy"
  crossorigin="anonymous"
  async
></script>
```

### Paso 5: Desplegar cambios

```bash
git add src/components/Comments.astro
git commit -m "config: configura Giscus con IDs correctos"
git push origin main
```

---

## 3. Telegram (Opcional)

Para recibir notificaciones por Telegram, sigue la guía en `TELEGRAM-SETUP.md` en el workspace.

---

## Verificación final

Después de completar los pasos anteriores:

1. ✅ El sitio debería estar online en https://cazucito.github.io/software-tools
2. ✅ Cada herramienta debería tener su página propia
3. ✅ La búsqueda debería funcionar
4. ✅ Los comentarios deberían aparecer (después de configurar Giscus)

---

## Solución de problemas

### El sitio no se ve
- Verifica que GitHub Pages esté habilitado en Settings → Pages
- Verifica que el workflow en Actions se haya ejecutado correctamente

### Los comentarios no aparecen
- Verifica que Discussions esté habilitado
- Verifica que la categoría "Tools" exista
- Verifica que los IDs en Comments.astro sean correctos
- Abre DevTools (F12) → Console para ver errores

### La búsqueda no funciona
- Verifica que `public/search-index.json` exista
- Verifica que no haya errores en la consola del navegador
