---
name: "Mathematica"
slug: "mathematica"
year: 1995
category: "tool"
tags: ["mathematica", "symbolic", "math", "wolfram", "algebra"]
context: "Computación simbólica que parecía magia. Resolver integrales simbólicamente, simplificar expresiones algebraicas complejas, y obtener resultados exactos en lugar de aproximaciones numéricas."
usedUntil: 1998
successor: "Mathematica 3.0"
successorSlug: "mathematica-3"
related: ["matlab", "maple-v"]
published: true
---

Mathematica fue mi introducción a la computación simbólica. Mientras MATLAB daba resultados numéricos (0.707106...), Mathematica daba resultados exactos (√2/2).

## Computación simbólica vs numérica

La diferencia fundamental:

**MATLAB** (numérico):
```matlab
integral = 3.1415926535  % Aproximación
```

**Mathematica** (simbólico):
```mathematica
integral = Pi  % Exacto, simbólico
```

## Lo que podía hacer

Mathematica manejaba:

- **Álgebra simbólica**: Expandir `(a+b)^3` → `a^3 + 3a^2b + 3ab^2 + b^3`
- **Cálculo simbólico**: Derivadas, integrales, límites con resultados exactos
- **Resolución de ecuaciones**: Soluciones algebraicas, no numéricas
- **Gráficos**: Funciones 2D, 3D, superficies complejas
- **Manipulación de fórmulas**: Simplificar expresiones enormes

## El lenguaje

El lenguaje de Mathematica era funcional y basado en patrones:

```mathematica
Integrate[x^2, x]           (* Integral de x^2 *)
D[Sin[x]^2, x]              (* Derivada *)
Solve[x^2 - 2x + 1 == 0, x] (* Resolver ecuación *)
```

Todo era una expresión. Todo podía manipularse.

## Contexto personal

Usé Mathematica principalmente para:

- Verificar resultados de cálculo vectorial
- Graficar funciones complejas para entender su comportamiento
- Resolver integrales que no podía hacer a mano
- Preparar figuras para reportes técnicos

La licencia de Mathematica era cara y la universidad tenía pocas. Solía ir a la biblioteca donde había computadoras con acceso.

Mathematica representa el poder de la computación simbólica: la computadora como asistente matemático, no solo como calculadora.