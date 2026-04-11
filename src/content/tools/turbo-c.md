---
name: "Turbo C"
slug: "turbo-c"
year: 1992
category: "lenguaje"
tags: ["c", "dos", "compiler", "borland", "ide"]
context: "Mi primer entorno de desarrollo C. Aprendí punteros, memoria y estructuras en esa pantalla azul con texto amarillo."
usedUntil: 1996
successor: "Borland C++"
successorSlug: "borland-cpp"
related: ["ms-dos-5-1", "chi-writer"]
published: true
---

Turbo C fue mi puerta de entrada al mundo de la programación de sistemas. Lo usé desde 1992 hasta 1996, aproximadamente desde segundo año de universidad hasta que descubrí las clases de C++.

## El entorno

Quien no recuerda esa interfaz azul con texto amarillo, el editor integrado, y la compilación con Alt+F9. Era un IDE completo para la época:

- Editor con resaltado básico de sintaxis
- Compilador integrado (Alt+F9 para compilar, Ctrl+F9 para ejecutar)
- Debugger con breakpoints
- Help integrado (F1 sobre cualquier función)

## Lo que aprendí

En Turbo C entendí conceptos fundamentales:

- **Punteros**: Cuando haces `int *p = &x;` y ves la dirección de memoria, es cuando realmente entiendes qué es un puntero
- **Memoria segmentada**: Los famosos `near` y `far` pointers del modelo de memoria DOS
- **Graphics.h**: Dibujar líneas, círculos y crear animaciones rudimentarias en modo VGA

## Contexto personal

Mi primer programa serio fue un sistema de control de inventario para un proyecto de la facultad. Recuerdo la frustración cuando el programa crasheaba por escribir más allá de los límites de un array, y la satisfacción cuando finalmente funcionó.

El código era spaghetti puro — `goto` everywhere, variables globales, funciones de 200 líneas — pero funcionaba y aprendí más de ese código que de cualquier libro.

## La transición

En 1996 migré a Borland C++ cuando necesité clases para un proyecto más grande. Turbo C quedó atrás, pero los fundamentos que aprendí ahí (punteros, manejo de memoria, estructuras) siguen siendo relevantes hoy.