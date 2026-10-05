# 001 · Direcciones visuales — «tres lenguajes para el timeline»

Maquetas de concepto (**desechables**) para elegir la dirección visual de la v3.
Mismo contenido real —los 28 herramientas del catálogo, 1991 → hoy— en tres lenguajes distintos.
Es la exploración de la **Fase 1** del [plan v3](../../docs/plans/2026-10-05-v3-propuesta-arquitectura.md).

## Cómo verlas

- **En línea (temporal):**
  - A · La Cinta → https://software-tools.pcabrera.com/conceptos/a-cinta/
  - B · La Línea → https://software-tools.pcabrera.com/conceptos/b-linea/
  - C · La Máquina → https://software-tools.pcabrera.com/conceptos/c-maquina/
  *(se eliminarán del hosting al elegir dirección)*
- **Local:** abrir `a-cinta/index.html` (etc.) directamente en el navegador. Las fuentes usan Google Fonts (necesitan internet); el resto es todo local.
- **Deep-link a un punto del scroll:** añadir `?y=0.5` (0–1) a la URL — p. ej. `/a-cinta/?y=0.8`.
- Con `prefers-reduced-motion` las tres degradan a una versión estática accesible (sin pins ni scrub).

## A · La Cinta — `a-cinta/`

**Stance:** cinemateca. La vida como una cinta de film que se reproduce con el scroll.

- Cinta horizontal con **pin + scrub**: 28 fotogramas reales ubicados por año, cabezal central, contador de año, marcadores de década, escala de progreso.
- «Fotograma destacado» (Eudora) que crece desde la cinta hasta ficha viva.
- Cierre «REBOBINA» con el catálogo completo en movimiento.
- Estética: negro cálido + ámbar, Space Grotesk + IBM Plex Mono, grano de película.
- **Fuerte en:** emoción narrativa, metáfora potente, espectáculo.
- **Débil en:** densidad de información; exige scroll largo.
- **Para:** un sitio que se sienta como una historia personal.

## B · La Línea — `b-linea/`

**Stance:** editorial de lujo (scrollytelling silencioso).

- Una **línea luminosa se dibuja** de 1991 a hoy con el scroll; 10 estaciones reales se encienden a su paso (SVG generado en vivo, punto viajero).
- Estación editorial (Turbo C) con pull-quote; cierre con la constelación de herramientas vigentes.
- Estética: slate profundo + sky, Fraunces (serif display) + Inter, mucho aire.
- **Fuerte en:** elegancia y legibilidad; la más cercana a la identidad actual del sitio.
- **Débil en:** menos «wow» que A a igual longitud de scroll.
- **Para:** un portafolio profesional sereno y atemporal.

## C · La Máquina — `c-maquina/`

**Stance:** CRT retro. El archivo como una máquina que arranca.

- Secuencia **BIOS** → log de arranque con scrub (año + barra + avance) → **directorio estilo Norton Commander** — interactivo: clic en una fila inspecciona la herramienta → `EXIT`.
- Solo IBM Plex Mono; fósforo verde con scanlines, viñeta y flicker sutil.
- **Fuerte en:** personalidad máxima; el tema («software que usé») es el protagonista; el directorio demuestra el lenguaje de interacción.
- **Débil en:** choque directo con la identidad slate/sky actual; riesgo de legibilidad CRT.
- **Para:** un sitio que celebre la nostalgia digital sin disculpas.

## Comparativa

| | A · La Cinta | B · La Línea | C · La Máquina |
|---|---|---|---|
| Metáfora | cine / film | camino / estaciones | computadora que arranca |
| Tono | cinematográfico | editorial | nostálgico-hacker |
| Scroll | largo y dramático | medio y meditativo | medio, por fases |
| Interacción | scrub | scrub | clic + scrub |
| Cercanía a la identidad actual | media | **alta** | baja (a propósito) |

## Notas técnicas

- **Librerías:** GSAP 3.15 + ScrollTrigger y Lenis 1.3 en `libs/` (servidas localmente, sin CDN; licencias en [libs/README.md](libs/README.md)).
- **Datos:** `data.js` se genera desde `src/data/tools.json` con [tools/prep_data.py](tools/prep_data.py) (28 herramientas publicadas; excluye el placeholder de la plantilla).
- **Deep-link `?y=`:** pensado para capturas y para compartir estados concretos durante la revisión.
- Estas maquetas son **desechables**: la dirección aprobada se convierte en la base del prototipo real (Fase 4) y las demás se archivan.
