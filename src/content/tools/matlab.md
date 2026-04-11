---
name: "MATLAB"
slug: "matlab"
year: 1994
category: "tool"
tags: ["matlab", "math", "engineering", "simulation", "matrix"]
context: "El estándar para computación numérica en ingeniería. Resolví sistemas de ecuaciones, hice simulaciones y grafiqué funciones complejas sin escribir código de bajo nivel."
usedUntil: 2000
successor: "MATLAB R14"
successorSlug: "matlab-r14"
related: ["mathematica", "maple-v"]
published: true
---

MATLAB fue mi herramienta principal durante la carrera de ingeniería. Mientras otros luchaban con Fortran para resolver matrices, yo escribía `A\b` y MATLAB resolvía el sistema de ecuaciones.

## ¿Por qué MATLAB?

"MATrix LABoratory" - el nombre lo dice todo. Estaba diseñado específicamente para:

- Álgebra lineal (matrices, vectores)
- Análisis numérico
- Simulación de sistemas
- Visualización de datos
- Procesamiento de señales

## Lo que resolví con MATLAB

Durante la carrera usé MATLAB para:

**Análisis de circuitos**: Resolver redes complejas con matrices de impedancia

**Control**: Simular respuestas de sistemas de control con `step()`, `impulse()`, `lsim()`

**Señales**: Transformadas de Fourier, análisis espectral, filtros digitales

**Métodos numéricos**: Integración, derivación, solución de ecuaciones diferenciales

## La interfaz

La ventana de MATLAB tenía:

- **Command Window**: Donde escribías comandos interactivos
- **Workspace**: Variables en memoria
- **Command History**: Comandos previos
- **Editor**: Para scripts `.m`

```matlab
% Resolver sistema Ax = b
A = [1 2; 3 4];
b = [5; 6];
x = A\b  % Backslash operator - mágico
```

## Toolboxes

MATLAB tenía "toolboxes" especializados:

- Signal Processing Toolbox
- Control System Toolbox
- Image Processing Toolbox
- Optimization Toolbox

Cada toolbox costaba lo mismo que una computadora completa, así que usábamos los que la universidad tenía licenciados.

## Contexto personal

Usé MATLAB desde 1994 hasta 2000, cuando terminé la carrera. En mi trabajo posterior usé más Excel y luego Python con NumPy, pero MATLAB sigue siendo el estándar en ingeniería.

Recuerdo las noches en el laboratorio de cómputo esperando mi turno para usar una licencia de MATLAB. Había más estudiantes que licencias disponibles.

MATLAB representa la especialización: una herramienta hecha específicamente para ingenieros, no para programadores generales.