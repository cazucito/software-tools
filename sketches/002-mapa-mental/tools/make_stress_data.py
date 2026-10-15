#!/usr/bin/env python3
"""Genera stress-data.js: clon sintético de data.js para probar la flexibilidad del motor.

Mutación determinista (sin azar, sin reloj) sobre data.js:
  - 14 categorías en total (6 reales + 8 sintéticas).
  - la familia real «diagramacion» crece a 40 herramientas.
  - «robotica»: categoría plana con 25 herramientas.
  - «ciencia»: 12 familias (anillo de familias denso).
  - nombres largos, categorías sin `color` (prueba el fallback del motor).
  - categoría plana pequeña, vacía de familias.

Uso (tras prep_data.py):
  python3 sketches/002-mapa-mental/tools/make_stress_data.py
Abrir: b-dendrita/?data=../stress-data.js
"""
import json
from pathlib import Path

BASE = Path(__file__).resolve().parents[1]
SRC = BASE / 'data.js'
OUT = BASE / 'stress-data.js'

PALETTE = ['#fb923c', '#2dd4bf', '#a3e635', '#38bdf8', '#e879f9', '#f87171']
LONG = ['Suite integrada de ingeniería asistida por computadora',
        'Entorno de desarrollo y depuración multiplataforma extendido',
        'Gestor documental corporativo con flujos de aprobación']


def load(path):
    s = path.read_text(encoding='utf-8')
    return json.loads(s[s.index('{'):s.rindex('}') + 1])


def synth_tool(n, name, cat, sub, tags):
    return {
        'slug': f'stress-{n:03d}', 'name': name, 'year': 1991 + (n * 7) % 35,
        'until': None if n % 3 else 1995 + (n * 5) % 30, 'cat': cat, 'sub': sub,
        'ctx': f'Herramienta sintética #{n} para probar el motor con volumen. '
               f'No existe en el catálogo real; solo ejercita el layout.',
        'tags': tags, 'rel': [], 'succ': None,
    }


def main():
    d = load(SRC)
    cats = {c['slug']: c for c in d['categories']}
    tools = d['tools']
    n = 0

    # 1) familia real «diagramacion» → 40 herramientas
    fam = next(s for s in cats['tool']['subs'] if s['slug'] == 'diagramacion')
    for k in range(40 - fam['count']):
        n += 1
        name = LONG[k % len(LONG)] if k % 9 == 0 else f'Diagrama sintético {k + 1}'
        tools.append(synth_tool(n, name, 'tool', 'diagramacion', ['stress', 'diagrama']))
        fam['count'] += 1
        cats['tool']['count'] += 1

    # 2) categoría plana con 25 herramientas
    cats_new = [{'slug': 'robotica', 'label': 'Robótica y control', 'color': PALETTE[0],
                 'count': 25, 'subs': []}]
    for k in range(25):
        n += 1
        name = LONG[k % len(LONG)] if k % 8 == 0 else f'Robot {k + 1}'
        tools.append(synth_tool(n, name, 'robotica', None, ['stress', 'robot']))

    # 3) categoría con 12 familias (30 herramientas), sin color → fallback del motor
    subs = []
    for f in range(12):
        size = 1 + (f * 5) % 4
        slug = f'ciencia-fam-{f + 1:02d}'
        for k in range(size):
            n += 1
            tools.append(synth_tool(n, f'Instrumento {f + 1}.{k + 1}', 'ciencia', slug, ['stress']))
        subs.append({'slug': slug, 'label': f'Familia científica {f + 1}', 'count': size})
    cats_new.append({'slug': 'ciencia', 'label': 'Ciencia de datos', 'count': sum(s['count'] for s in subs),
                     'subs': subs})

    # 4) seis categorías planas pequeñas (hasta 14 en total)
    for i in range(6):
        slug = f'extra-{i + 1}'
        size = 2 + i
        for k in range(size):
            n += 1
            tools.append(synth_tool(n, f'Extra {i + 1}-{k + 1}', slug, None, ['stress']))
        c = {'slug': slug, 'label': f'Categoría extra {i + 1}', 'count': size, 'subs': []}
        if i % 2:
            c['color'] = PALETTE[(i + 1) % len(PALETTE)]
        cats_new.append(c)

    d['categories'] = d['categories'] + cats_new
    OUT.write_text(
        '// Generado por tools/make_stress_data.py (sintético) — no editar a mano.\n'
        'window.ST_DATA = ' + json.dumps(d, ensure_ascii=False, indent=1) + ';\n',
        encoding='utf-8')
    nsubs = sum(len(c['subs']) for c in d['categories'])
    print(f'stress-data.js: {len(d["categories"])} categorías, {len(tools)} herramientas, '
          f'{nsubs} familias → {OUT}')


if __name__ == '__main__':
    main()
