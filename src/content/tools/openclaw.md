---
name: "OpenClaw"
slug: "openclaw"
year: 2025
category: "tool"
tags: ["ai", "agentes", "open-source", "asistente-personal", "clawdbot"]
context: "Asistente personal de IA open source: corre en tu propia máquina y usa tus apps de mensajería como interfaz. Lo usé desde su publicación hasta el 22 de abril de 2026."
usedUntil: 2026
successor: null
successorSlug: null
related: []
published: true
---

OpenClaw es un asistente personal de IA —un agente autónomo— de código abierto, creado por Peter Steinberger. A diferencia de los asistentes alojados en la nube, corre en tu propia máquina: configuración e historial viven en local, y se maneja desde tus aplicaciones de mensajería (Signal, Telegram, Discord, WhatsApp) como si fuera un contacto más. Ejecuta tareas por sí mismo —correo, archivos, recordatorios, otras herramientas— y se extiende con *skills* (carpetas con un `SKILL.md`). Es uno de los proyectos de más rápido crecimiento en GitHub (para octubre de 2026 ronda las 390.000 estrellas) y una fundación sin fines de lucro, la OpenClaw Foundation, lo mantiene bajo licencia MIT.

## La cadena de nombres

Se publicó el 24 de noviembre de 2025 como **Warelay** y pasó por varios nombres en apenas dos meses: **CLAWDIS**, **Clawdbot** (2 de enero de 2026), **Moltbot** (27 de enero, tras una reclamación de marca de Anthropic) y finalmente **OpenClaw** (30 de enero de 2026). Alrededor de este ecosistema nació también Moltbook, la red social de agentes.

## Qué ofrecía

- **Local-first**: datos, memoria y credenciales en tu propio equipo.
- **Interfaz conversacional**: las apps de mensajería como interfaz natural.
- **Cron propio** (OpenClaw Scheduler) para tareas programadas.
- **Multi-modelo**: Claude, GPT, DeepSeek y otros.
- **Multiplataforma**: macOS, Linux y Windows; después iOS y Android.

Su diseño de permisos amplios lo puso también bajo escrutinio de seguridad (inyección de prompts, skills de terceros): un recordatorio del poder —y del cuidado— que exigen los agentes autónomos.

## Contexto personal

Lo seguí desde su publicación, atravesando toda la cadena de nombres, y lo usé como asistente personal hasta el **22 de abril de 2026**. En esos meses vivía en un workspace propio (`~/.openclaw/workspace/`), con su memoria en un vault de Obsidian (`openclaw-obsidian/`): notas diarias, fichas de proyectos y memoria de largo plazo. Le sumé skills propias —captura de notas con imágenes, transcripción de audio en Discord con Whisper local— y dejé trabajos programados en el OpenClaw Scheduler: monitoreo de varias cuentas de Gmail con alertas a un canal de Discord y backups diarios del vault.
