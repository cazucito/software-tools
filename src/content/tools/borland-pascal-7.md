---
name: "Borland Pascal 7.0"
slug: "borland-pascal-7"
year: 1993
category: "lenguaje"
tags: ["pascal", "dos", "ide", "borland", "oop", "objects"]
context: "La evolución de Turbo Pascal con soporte para objetos. Aprendí programación orientada a objetos con este IDE azul legendaria."
usedUntil: 1996
successor: "Delphi"
successorSlug: "delphi"
related: ["turbo-c", "ms-dos-5-1"]
published: true
---

Borland Pascal 7.0 fue mi puerta de entrada a la programación orientada a objetos. Si Turbo Pascal me enseñó procedimientos y funciones, BP7 me enseñó clases, herencia y polimorfismo.

## La evolución desde Turbo Pascal

Turbo Pascal 7.0 (la versión anterior) ya era excelente, pero Borland Pascal 7.0 añadió:

- Soporte completo para Object Pascal
- Unidades con objetos (classes en el lenguaje)
- IDE mejorado con ventanas flotantes
- Compilador tanto para DOS como para Windows
- Turbo Vision (framework para interfaces de texto)

## Aprendiendo OOP

Recuerdo el momento en que entendí la herencia. Estaba haciendo un programa de gestión de biblioteca y tenía:

```pascal
type
  TPublicacion = object
    titulo: string;
    anio: integer;
    procedure Mostrar;
  end;

  TLibro = object(TPublicacion)
    autor: string;
    isbn: string;
    procedure Mostrar; virtual;
  end;
```

El concepto de que un `TLibro` ES una `TPublicacion` fue revelador. Antes tenía que duplicar campos en registros separados.

## Turbo Vision

TP7 venía con Turbo Vision, un framework para crear interfaces de usuario en modo texto que parecían gráficas:

- Ventanas con marcos y sombras
- Menús desplegables
- Diálogos con botones
- Sistema de eventos

Mi primer "aplicación seria" fue un sistema de inventario con interfaz Turbo Vision. Se veía profesional aunque corría en modo texto.

## Contexto personal

Usé BP7 desde 1993 hasta 1996, cuando Delphi cambió mi mundo. La transición fue natural porque Delphi usaba Object Pascal, pero con una interfaz gráfica real y componentes visuales.

BP7 fue el último gran compilador Pascal para DOS. Representó el final de una era y el inicio de otra.