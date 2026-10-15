---
name: "Program CC"
slug: "program-cc"
year: 1992
category: "tool"
tags: ["control", "cacsd", "ingeniería", "dos", "simulación"]
context: "CAD de sistemas de control para DOS: análisis clásico (Bode, Nyquist, root locus) y moderno (espacio de estados, LQG, MIMO) desde una línea de comandos con lenguaje macro. Se conserva el paquete completo de la versión 3B."
usedUntil: null
successor: null
successorSlug: null
related: ["matlab", "electronic-workbench"]
published: true
---

Program CC (**Comprehensive Control**) fue un paquete de **diseño asistido de sistemas de control** (CACSD) para PC, dirigido por comandos, obra de **Peter M. Thompson**. Cubría el análisis de sistemas lineales continuos, discretos y muestreados: desde el análisis clásico hasta el control óptimo, todo tecleado en su propia línea de comandos con macros.

## Qué ofrecía

- **Análisis clásico**: diagramas de Bode, Nyquist, Nichols y lugar de raíces; criterios de estabilidad (Routh, Schur).
- **Control moderno**: espacio de estados, estabilidad, control óptimo **LQG**, sistemas **MIMO**, asignación de polos.
- **Mundo digital**: transformadas Z, W-plane, discretización de controladores, análisis en frecuencia muestreada.
- **Lenguaje macro propio** (`STORE`, `RECALL`, `BUILD`, `CONVERT`…) con historial, editor de macros y pantallas de demostración — un antecesor directo de los *scripts* de MATLAB.
- Gráficas a pantalla, impresora o plotter (`HARDCOPY`), cálculo numérico auxiliar (incluida una «HP calculator» interna) y tutoriales por tema.

## Historia y conservación

Thompson publicó Program CC a mediados de los 80: el aviso de copyright del propio programa es de **1984–1985**, y de esta línea existió un manual de usuario (v3.0) y una edición académica (*Program CC: Introductory Version*, Thompson & Shahian). Los *papers* de la época describen su implementación de LQG y asignación de polos (Wiley, *Optimal Control Applications & Methods*, 1987). El programa corre sobre el **runtime de Microsoft BASIC** (`BASRUN`) y está repartido en **30 módulos *overlay*** (`CC.EXE` + `CC1–CC29`) — señal de la era de la memoria convencional de 640 KB.

## Presencia en esta colección

Se conserva el **paquete completo de la versión 3B** (42 archivos): el lanzador, los 30 módulos, el emulador de coprocesador matemático **EM87** (se cargaba con `EMULADOR.BAT`, «em87 /l», para máquinas sin 8087) y los archivos de estado (`$$SETUP`, `$$HIS`). Al ser software comercial de los años 80, se mantiene en la **colección privada**; su redistribución no está verificada.

> **Nota de fechas**: el primer uso estimado (**1992**) es un **borrador** pendiente de confirmación; las fechas seguras son las del aviso de copyright (1984–1985) y de la propia versión 3B.
