---
name: "IntelliJ IDEA"
slug: "intellij-idea"
year: 2007
category: "editor"
tags: ["ide", "java", "jetbrains", "refactoring", "productivity"]
context: "El IDE que me hizo dejar Eclipse después de 8 años. Cuando probé el refactoring automático supe que no volvería atrás."
usedUntil: null
successor: null
successorSlug: null
related: ["eclipse", "netbeans-8-2", "vscode"]
published: true
---

IntelliJ IDEA llegó a mi vida en 2007 y cambió mi forma de desarrollar. Antes había usado JBuilder, luego Eclipse durante 8 años, y pensaba que Eclipse era lo mejor posible. IntelliJ me demostró que estaba equivocado.

## El momento "wow"

Recuerdo exactamente cuándo sucedió. Estaba trabajando en un proyecto Java con Eclipse, haciendo un refactoring manual de renombrar una variable en múltiples archivos. Llegó un colega, abrió IntelliJ, presionó `Shift+F6`, y el IDE renombró la variable en todo el proyecto incluyendo comentarios y strings.

Eso me llevó a probar IntelliJ. Una semana después, Eclipse ya no estaba en mi máquina.

## Características que cambiaron mi workflow

### Refactoring inteligente
- Rename que entiende scope
- Extract method que sugiere nombres
- Inline variable cuando es obvio
- Change signature con preview de todos los cambios

### Navegación
- `Ctrl+B` para ir a definición
- `Ctrl+Alt+B` para implementaciones
- `Ctrl+Shift+F` para buscar en todo el proyecto (indexado, instantáneo)
- "Recent Files" con `Ctrl+E`

### Code completion
El autocompletado de IntelliJ no solo sugiere métodos, sino que entiende el contexto. Si escribes `if (obj != null)`, te sugiere `obj.` con todos los métodos disponibles.

### Análisis estático en tiempo real
Las inspecciones de IntelliJ detectan bugs antes de compilar. Null pointer posibles, recursos no cerrados, código muerto — todo subrayado mientras escribes.

## Contexto personal

Empecé con la Community Edition (gratuita) y eventualmente mi empresa pagó la Ultimate. La diferencia en productividad fue inmediata y mensurable. Tareas que tomaban horas en Eclipse se reducían a minutos.

Lo más valioso fue la confianza para hacer refactoring. En Eclipse, renombrar algo en un proyecto grande era arriesgado. En IntelliJ, el IDE te muestra exactamente qué va a cambiar y te permite previsualizar antes de confirmar.

## El ecosistema JetBrains

IntelliJ me introdujo al ecosistema JetBrains:
- WebStorm para JavaScript
- PyCharm para Python
- DataGrip para bases de datos
- Rider para .NET (cuando tuve que tocar C#)

Todos comparten la misma filosofía: el IDE entiende tu código a un nivel semántico, no solo sintáctico.

## Hasta hoy

Sigo usando IntelliJ IDEA como mi IDE principal para Java y Kotlin. He probado VS Code con extensiones Java, y es competente, pero IntelliJ sigue siendo superior para proyectos grandes. La inversión en aprender sus shortcuts y features ha pagado dividendos durante casi 20 años.