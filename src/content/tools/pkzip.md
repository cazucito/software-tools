---
name: "PKZIP"
slug: "pkzip"
year: 1991
category: "tool"
tags: ["compression", "zip", "dos", "file-format", "phil-katz"]
context: "El estándar de compresión que definió el formato ZIP. Antes de WinZip, antes de Windows, estaba Phil Katz y su PKZIP en línea de comandos."
usedUntil: 1998
successor: "WinZip"
successorSlug: "winzip"
related: ["ms-dos-5-1", "norton-commander"]
published: true
---

PKZIP fue mi herramienta de compresión durante toda la era DOS. Creada por Phil Katz, definió el formato ZIP que aún usamos hoy.

## El problema: espacio limitado

En los 90s, el espacio en disco era oro:

- Discos duros de 40MB, 80MB, 120MB
- Disquetes de 1.44MB para transferir archivos
- Banda ancha no existía, todo se compartía en floppy

Comprimir archivos no era opcional, era necesidad.

## La línea de comandos

PKZIP era un programa de DOS:

```
PKZIP archivo.zip *.txt          ; Comprimir todos los .txt
PKUNZIP archivo.zip              ; Extraer
PKZIP -a archivo.zip nuevo.txt   ; Agregar archivo
```

La sintaxis era críptica pero poderosa. Aprendías los switches o consultabas el manual.

## El formato ZIP

Phil Katz creó el formato ZIP como respuesta al formato ARC, que tenía licencias restrictivas. ZIP fue:

- Libre de patentes
- Abierto
- Eficiente

El éxito fue inmediato. ZIP se convirtió en el estándar de facto.

## Contexto personal

Usé PKZIP desde 1991 hasta que WinZip y Windows 95 hicieron la compresión gráfica mainstream. Pero seguí usando la línea de comandos para scripts batch.

Recuerdo el drama de Phil Katz: su batalla legal con ARC, su alcoholismo, su muerte prematura en 2000. Era un genio trágico del software.

PKZIP representa la era de los utilitarios de DOS que hacían una sola cosa, pero la hacían perfectamente.