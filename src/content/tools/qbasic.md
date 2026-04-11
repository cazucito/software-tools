---
name: "QBasic"
slug: "qbasic"
year: 1991
category: "lenguaje"
tags: ["basic", "microsoft", "dos", "learning", "beginner"]
context: "Mi primera experiencia con programación. El intérprete incluido en DOS que me enseñó qué era un bucle, una variable, y la satisfacción de hacer que la computadora hiciera lo que yo quería."
usedUntil: 1993
successor: "QuickBASIC 4.5"
successorSlug: "quickbasic-4-5"
related: ["ms-dos-5-1", "turbo-c"]
published: true
---

QBasic fue mi primer lenguaje de programación. Venía incluido con MS-DOS 5.0 y versiones posteriores, y fue donde escribí mis primeras líneas de código.

## El inicio

Recuerdo abrir QBasic por primera vez:

1. En el prompt de DOS: `QBASIC`
2. Aparecía el IDE azul con menús
3. Escribí el clásico:

```basic
PRINT "Hola, Mundo!"
```

Presioné F5, y funcionó. Ese momento de ver la computadora obedecer mis instrucciones fue revelador.

## Lo que aprendí

En QBasic aprendí los fundamentos:

- **Variables**: `DIM nombre AS STRING`
- **Condiciones**: `IF...THEN...ELSE`
- **Bucles**: `FOR...NEXT`, `WHILE...WEND`
- **Subrutinas**: `SUB...END SUB`, `FUNCTION...END FUNCTION`
- **Gráficos**: `SCREEN 13`, `PSET`, `LINE`, `CIRCLE`

## El modo gráfico

QBasic incluía soporte para gráficos VGA (modo 320x200 con 256 colores). Pude hacer:

- Animaciones simples
- Dibujos geométricos
- Un juego de la serpiente muy básico

```basic
SCREEN 13
LINE (10, 10)-(100, 100), 15, BF
CIRCLE (50, 50), 30, 4
```

## Limitaciones

QBasic era un intérprete, no un compilador:

- Los programas corrían lentamente
- No podías crear archivos .EXE independientes
- El código fuente era visible para cualquiera

Por eso eventualmente migré a QuickBASIC 4.5 (que sí compilaba) y luego a Turbo Pascal.

## Contexto personal

Usé QBasic desde 1991 hasta 1993. Fue mi puerta de entrada a la programación. Esos años aprendiendo conceptos básicos me dieron fundamentos sólidos para cuando llegaron lenguajes más serios.

Todavía tengo un floppy disk con mis programas de QBasic. Son horribles, pero los conservo con cariño.

QBasic representa la democratización de la programación: venía gratis con DOS, cualquiera podía aprender.