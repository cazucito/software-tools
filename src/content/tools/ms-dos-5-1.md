---
name: "MS-DOS 5.1"
slug: "ms-dos-5-1"
year: 1991
category: "os"
tags: ["dos", "os", "command-line", "batch", "memory-management"]
context: "El sistema operativo donde aprendí que la computadora hace exactamente lo que le dices, no lo que quieres."
usedUntil: 1997
successor: "Windows 95"
successorSlug: "windows-95"
related: ["turbo-c", "chi-writer"]
published: true
---

MS-DOS 5.1 fue mi sistema operativo durante toda la carrera universitaria. No fue mi primer sistema operativo — antes tuve CP/M en una compu sin disco duro — pero DOS fue donde realmente aprendí cómo funciona una computadora desde adentro.

## El prompt C:\>

Aprender DOS era aprender a pensar en comandos. No había mouse, no había ventanas, solo:

```
C:\> dir /w
C:\> cd \programas\tc
C:\> edit autoexec.bat
```

Cada comando se quedaba grabado. Todavía recuerdo:

- `mem /c` para ver cómo estaba dividida la memoria convencional y extendida
- `himem.sys` y `emm386.exe` para gestionar memoria
- `config.sys` y `autoexec.bat` — los archivos más importantes del sistema
- `batch files` para automatizar tareas repetitivas

## Contexto personal

Configurar DOS para correr Chi Writer, Turbo C y algún juego requería planificar la memoria. Tenías 640KB de memoria convencional y todo tenía que caber ahí. Era como un rompecabezas:

```
DEVICE=C:\DOS\HIMEM.SYS
DEVICE=C:\DOS\EMM386.EXE NOEMS
DOS=HIGH,UMB
```

Recuerdo pasar horas optimizando el `config.sys` para liberar los últimos 10KB de memoria que necesitaba un programa.

## El file system

En DOS aprendí sobre:

- FAT12/FAT16 y por qué existen los clusters desperdiciados
- Archivos ocultos y atributos (attrib +h archivo.txt)
- Subdirectorios y por qué `..` sube un nivel
- La diferencia entre `copy` y `xcopy /s`

## La transición

Windows 95 llegó con su "plug and play" y muchos olvidaron DOS. Pero yo seguí abriendo ventanas de DOS para tareas rápidas, y todavía hoy prefiero la línea de comandos para muchas operaciones. Los fundamentos de DOS — el filesystem, los pipes, el redireccionamiento — son la base de cualquier terminal moderna.