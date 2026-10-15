#!/usr/bin/env python3
"""Genera el lote «_diagram» de fichas nuevas (7 herramientas, 2026-10-15).

Herramientas de pensamiento visual y diagramación conservadas en
/mnt/thoth/incoming/_diagram: Axon Idea Processor, Balsamiq Mockups,
MindManager, PersonalBrain, TheBrain 8, UMLet y System Mechanic.

- Escribe content/tools/<slug>.md (frontmatter + cuerpo) en español.
- Aborta sin escribir NADA si algún slug ya existe (seguridad anti-pisado).
- Los años de primer uso son BORRADORES inferidos de la edición conservada
  (pendientes de revisión del dueño); las notas de fecha en cada ficha
  distinguen dato REAL vs estimado.
"""

from pathlib import Path

ROOT = Path(__file__).resolve().parents[2]  # ops/tools/ → raíz del repo
OUT_DIR = ROOT / 'content' / 'tools'

FICHAS = []


def ficha(name, slug, year, category, tags, context, used_until, successor,
          successor_slug, related, body):
    FICHAS.append({
        'name': name, 'slug': slug, 'year': year, 'category': category,
        'tags': tags, 'context': context, 'usedUntil': used_until,
        'successor': successor, 'successorSlug': successor_slug,
        'related': related, 'body': body.strip(),
    })


ficha(
    name="Axon Idea Processor",
    slug="axon-idea-processor",
    year=2004,
    category="tool",
    tags=["ideas", "mapas-mentales", "prolog", "visualización", "shareware", "windows"],
    context="Procesador de ideas en 3-D escrito en Prolog por Axon Research (Singapur): mapas mentales y conceptuales con color, forma y profundidad. Edición 2004.",
    used_until=None,
    successor=None,
    successor_slug=None,
    related=["mindmanager", "thebrain-8"],
    body="""
Axon fue un procesador de ideas —*idea processor*— de escritorio para Windows desarrollado por **Axon Research** (Singapur). Su rasgo más distintivo era estar escrito en **Prolog**, un lenguaje de inteligencia artificial: el programa trataba las ideas como objetos y explotaba atributos visuales como el color, la forma, el tamaño y la **profundidad** — de ahí su modo de visualización 3-D, capaz de mostrar grandes cantidades de información en una sola pantalla, con hasta 500 niveles por archivo. Soportaba tanto jerarquías (mapas mentales) como redes sin raíz única (mapas conceptuales).

## Qué ofrecía

- **Visualización 3-D**: sombras proyectables sobre piso, techo o paredes para acentuar la profundidad.
- **Herramientas de pensamiento integradas**: Checklists, Generador de ideas, Analizador de texto, Secuenciador, Simulador y Ticker.
- **Plantillas y ejemplos** (`.xon`, `.l`) para modelar y analizar sistemas complejos.

## Distribución y conservación

Axon se distribuía en dos ediciones: **Lite** (freeware, redistribuible sin modificaciones, limitada a guardar archivos pequeños) y **Plus** (de pago). El sitio oficial — `web.singnet.com.sg/~axon2000` — está inactivo desde hace años. La línea de versiones sobrevive en el **Internet Archive** (edición R3.2 Lite, año 2000) y su última compilación publicada (2013 R2.20) puede encontrarse en catálogos de descarga de terceros.

## Presencia en esta colección

Se conserva la **edición 2004** completa — 362 archivos, 5.1 MB — con el ejecutable `axon2004.exe`, el manual de ayuda (`main04.hlp`), la carpeta del manual *Getting Started* y las plantillas de ejemplo (`Mindmap.xon`, `brainstorm.xon`, `simulate_inventory.xon`…).

> **Nota de fechas**: la edición (2004) se identifica por el binario y el manual incluidos — dato **real**. El año de primer uso registrado es una **estimación** a partir de la edición conservada.
""",
)

ficha(
    name="Balsamiq Mockups",
    slug="balsamiq-mockups",
    year=2017,
    category="tool",
    tags=["wireframes", "prototipado", "ui", "diseño", "windows"],
    context="Wireframes de baja fidelidad con estética de boceto dibujado a mano, hoy «Balsamiq Wireframes». Se conserva el instalador de escritorio 3.5.14.",
    used_until=None,
    successor=None,
    successor_slug=None,
    related=["umlet"],
    body="""
Balsamiq Mockups es una herramienta de **wireframes de baja fidelidad**: controles de interfaz con estética de boceto dibujado a mano que se arrastran sobre un lienzo para armar pantallas antes de programar nada. Su gracia nunca fue la fidelidad, sino lo contrario — parecer un borrador, para no enamorarse del diseño antes de discutirlo.

## Qué ofrecía

- Catálogo de controles pre-dibujados: botones, menús, tablas, navegadores, móviles…
- Rejillas, alineación y **prototipos navegables** (enlazar pantallas entre sí).
- Exportación a PNG/PDF.
- Licencia de escritorio con **14 días de prueba**; ver archivos siempre es gratis.

## Historia y conservación

Balsamiq (fundada por Giacomo Guilizzoni en 2008) publicó **Mockups 3** como la renovación más ambiciosa de su línea de escritorio; de esa generación se conserva aquí el instalador **3.5.14**. Después llegó el renombre a **Balsamiq Wireframes** (4.x) y, en 2025, el anuncio del **retiro del escritorio**: ventas y soporte terminan el 31 de diciembre de 2026, con Balsamiq Cloud como sucesor. El sitio oficial mantiene un **archivo de builds** histórico en `balsamiq.com/product/desktop/archives/`.

> **Nota de fechas**: la versión 3.5.14 está confirmada por el propio instalador (marca «Balsamiq SRL»; compilación fechada el 2017-07-06) — dato **real**. El año de primer uso registrado (2017) es una **estimación** a partir de esa compilación.
""",
)

ficha(
    name="MindManager",
    slug="mindmanager",
    year=2020,
    category="tool",
    tags=["mapas-mentales", "gestión", "productividad", "windows", "comercial"],
    context="El estándar corporativo de los mapas mentales (Mindjet, hoy Corel): planear y presentar información en mapas interactivos. Edición 2020.",
    used_until=None,
    successor=None,
    successor_slug=None,
    related=["axon-idea-processor", "personal-brain", "thebrain-8"],
    body="""
MindManager es una de las herramientas de **mapas mentales** más veteranas del software de oficina. Nacida a mediados de los años 90 en el seno de **Mindjet**, convirtió la técnica del mapa mental en software corporativo: ramas jerárquicas, notas, enlaces, filtros — y la capacidad de colgar de un mapa datos de negocio reales.

## Qué ofrecía (edición 2020)

- **Co-edición en vivo**: varias personas sobre el mismo mapa, la gran novedad de la edición.
- **Integración con Excel**: visualizar y editar datos de la hoja dentro del mapa.
- **Planificación**: Gantt, líneas base, costos, recursos y gestión del trabajo.
- **Diagramas de flujo** y despliegue de procesos.
- **Snap Capture** para adjuntar contenido rápidamente.

## Distribución y conservación

Producto **comercial** (Corel, que adquirió Mindjet en 2016, mantiene la marca en `mindmanager.com`), con licencia perpetua y suscripción de servicios. La edición **MindManager 2020** es de **abril de 2020**.

## Presencia en esta colección

Se conserva el instalador MSI (306 MB), con la metadata original grabada en el paquete: *«MindManager 2020 Installer», Corel Corporation, 16 de abril de 2020*.

> **Nota de fechas**: la compilación del MSI está fechada el 2020-04-16 por el propio instalador — dato **real**.
""",
)

ficha(
    name="PersonalBrain",
    slug="personal-brain",
    year=2004,
    category="tool",
    tags=["mapas-mentales", "conocimiento", "bases-de-conocimiento", "windows"],
    context="El «brain» como base de conocimiento personal: pensamientos en red con foco dinámico. La herramienta de TheBrain Technologies antes de llamarse TheBrain.",
    used_until=None,
    successor="TheBrain 8",
    successor_slug="thebrain-8",
    related=["thebrain-8", "mindmanager"],
    body="""
PersonalBrain proponía una metáfora distinta a la del mapa mental clásico: en vez de un árbol estático que ves entero, un **«cerebro» de pensamientos en red** del que solo ves el pensamiento activo y su vecindario inmediato — el foco se re-centra contigo al navegar y el contexto no se pierde. Cada pensamiento podía ser a la vez padre, hijo y hermano de otros (red, no jerarquía pura); cada brain era un archivo portátil.

## Qué ofrecía

- **Interfaz gráfica dinámica**: navegación «plex» con el pensamiento activo al centro.
- **Pensamientos (thoughts)** que agrupan notas, archivos, enlaces y correo.
- **Búsqueda instantánea** en brains de miles de pensamientos.
- **Exportación** de brains y reportes (PBexporter incluido).

## Historia y conservación

PersonalBrain nació en **TheBrain Technologies** (Los Ángeles) a finales de los años 90. La versión **3.02** — la conservada aquí — se publicó el **4 de marzo de 2004**, tras la beta de 3.0 (diciembre de 2002): trajo integración con Outlook, nuevos índices de búsqueda y brains «hasta 32 veces más grandes» que en versiones previas. Con los años la compañía acortó el nombre del producto a **TheBrain**, que sigue publicándose en `thebrain.com`.

> **Nota de fechas**: la fecha 2004-03-04 es **real** (aparece en el histórico de versiones distribuido con el producto); la guía incluida corresponde a la era 2.0 (septiembre de 2001).

## Presencia en esta colección

El zip conserva la distribución de 3.02 tal como circulaba: el ejecutable, la guía de usuario en PDF, el exportador y la página *What's new* con el historial completo de versiones.
""",
)

ficha(
    name="TheBrain 8",
    slug="thebrain-8",
    year=2013,
    category="tool",
    tags=["mapas-mentales", "conocimiento", "bases-de-conocimiento", "windows"],
    context="La octava generación del brain de TheBrain Technologies: búsqueda inteligente, nueva interfaz y servicios en la nube. Instalador 8.0.1.3.",
    used_until=None,
    successor=None,
    successor_slug=None,
    related=["personal-brain", "mindmanager", "axon-idea-processor"],
    body="""
TheBrain 8 fue la entrega de 2013 de la herramienta de bases de conocimiento personal de TheBrain Technologies — la misma línea nacida como **PersonalBrain**. Mantenía la metáfora del pensamiento activo con su vecindario y añadía encima una capa de búsqueda y servicios.

## Qué ofrecía

- **Búsqueda inteligente** y navegación fluida por teclado.
- **Interfaz renovada**, con más de 50 funciones de mapeo, gestión de tiempo y productividad.
- **Servicios y sincronización** (suscripción Pro Services).
- **Compatibilidad** con brains heredados de la línea PersonalBrain.

## Distribución y conservación

Producto **comercial** (`thebrain.com`): aplicación de escritorio con licencia y modelo de suscripción para servicios. TheBrain 8 se anunció el **20 de noviembre de 2013** como sucesor de la generación 7; la línea continúa viva hoy.

## Presencia en esta colección

Se conserva el instalador de Windows **8.0.1.3**.

> **Nota de fechas**: el lanzamiento de la versión 8 (20 de noviembre de 2013) es **real** (anuncio de la compañía); el año de primer uso registrado se estima a partir de esa generación.
""",
)

ficha(
    name="UMLet",
    slug="umlet",
    year=2016,
    category="tool",
    tags=["uml", "diagramas", "java", "open-source", "eclipse"],
    context="Editor UML minimalista y open source: diagramas de clases, secuencia y actividad con edición textual, rápido y sin distracciones. Stand-alone 14.2.",
    used_until=None,
    successor=None,
    successor_slug=None,
    related=["balsamiq-mockups", "eclipse"],
    body="""
UMLet es una herramienta **UML** de las verdaderamente minimalistas: sin cientos de menús, el flujo consiste en arrastrar elementos y editar sus atributos en un panel de texto. Los diagramas de secuencia y actividad se escriben casi como texto plano, y el resultado se exporta a SVG, PDF, EPS, JPG o al portapapeles. Corre en Windows, macOS y Linux — **stand-alone o como plug-in de Eclipse**.

## Qué ofrecía

- Diagramas de clases, secuencia, actividad y casos de uso.
- **Edición textual de elementos** (sintaxis simple tipo `+método(): tipo`) en vez de diálogos interminables.
- **Elementos personalizados**: definir tipos de UML propios y reutilizarlos.
- Exportación vectorial y **modo batch**.
- Código abierto bajo **GPLv3**, mantenido en `github.com/umlet`.

## Historia y conservación

UMLet publicó su v0.1 el **25 de enero de 2001**, llegó a la 1.0 el **21 de junio de 2002** y abrió su código bajo GPL en la serie 1.8. Es un proyecto vivo: su línea 15.x se distribuye desde `umlet.com`. Aquí se conserva el **stand-alone 14.2**.

## Presencia en esta colección

El zip conservado es **byte-idéntico al archivo oficial** que publica el proyecto — `umlet-standalone-14.2.zip`, sha256 verificado — y se redistribuye bajo **GPLv3**.

> **Nota de fechas**: la versión 14.2 es de **principios de 2016** (archivos internos de febrero–abril; el paquete portátil de la comunidad actualizó a 14.2 en mayo de 2016).
""",
)

ficha(
    name="System Mechanic",
    slug="system-mechanic",
    year=2000,
    category="tool",
    tags=["windows", "mantenimiento", "utilidades", "iolo", "comercial"],
    context="Suite de mantenimiento de Windows de iolo technologies: registro, disco y rendimiento. Se conserva el instalador de la versión 3.2d (2000).",
    used_until=None,
    successor=None,
    successor_slug=None,
    related=[],
    body="""
System Mechanic es la suite de **mantenimiento y optimización de Windows** de iolo technologies (Los Ángeles): limpieza y reparación del registro, eliminación de archivos innecesarios, comprobación del disco, control de programas de inicio y un repertorio de utilidades para mantener un PC «en forma».

## Qué ofrecía (serie 3.x)

- Limpieza y reparación del **registro** de Windows.
- Eliminación de **archivos innecesarios** y reparación de accesos directos.
- **Mantenimiento de disco**: comprobación y defragmentación.
- **Administración del arranque** (programas de inicio).

## Historia y conservación

El producto sigue publicándose hoy (System Mechanic Ultimate Defense, `iolo.com`). De su primera era — cuando era una aplicación de escritorio para los Windows de finales de los 90 — se conserva aquí el instalador de **3.2d** (febrero de 2000); el **Internet Archive** preserva también la **3.5e** del mismo año, que confirma la serie.

## Presencia en esta colección

Se conserva el instalador original `System Mechanic 3.2d - Feb 8, 00.exe`: un instalador de **16 bits** (chequeo «Windows 3.1 o superior»), estampa de su época.

> **Nota de fechas**: la fecha 2000-02-08 proviene del **nombre original del archivo** (no hay fecha embebida legible en el binario) y de la serie 3.x documentada en el Internet Archive — es la mejor evidencia disponible.
""",
)


def render(f):
    lines = ['---']
    lines.append(f'name: "{f["name"]}"')
    lines.append(f'slug: "{f["slug"]}"')
    lines.append(f'year: {f["year"]}')
    lines.append(f'category: "{f["category"]}"')
    lines.append('tags: [' + ', '.join(f'"{t}"' for t in f['tags']) + ']')
    lines.append(f'context: "{f["context"]}"')
    lines.append('usedUntil: null' if f['usedUntil'] is None else f'usedUntil: {f["usedUntil"]}')
    lines.append('successor: null' if f['successor'] is None else f'successor: "{f["successor"]}"')
    lines.append('successorSlug: null' if f['successorSlug'] is None else f'successorSlug: "{f["successorSlug"]}"')
    lines.append('related: [' + ', '.join(f'"{r}"' for r in f['related']) + ']')
    lines.append('published: true')
    lines.append('---')
    lines.append('')
    lines.append(f['body'])
    lines.append('')
    return '\n'.join(lines)


def main():
    slugs = [f['slug'] for f in FICHAS]
    assert len(slugs) == len(set(slugs)), 'slugs duplicados en el lote'

    existing = [s for s in slugs if (OUT_DIR / f'{s}.md').exists()]
    if existing:
        print(f'ABORTO: ya existen fichas con estos slugs: {existing}')
        raise SystemExit(1)

    for f in FICHAS:
        path = OUT_DIR / f'{f["slug"]}.md'
        text = render(f)
        path.write_text(text, encoding='utf-8')
        print(f'escrita {path.name} ({len(text.encode("utf-8"))} bytes)')

    print(f'\nTotal: {len(FICHAS)} fichas en {OUT_DIR}')


if __name__ == '__main__':
    main()
