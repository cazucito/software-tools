#!/usr/bin/env python3
"""Genera data.js para la maqueta 002 (mapa mental) desde catalog.sqlite.

Incluye: categorías (con etiqueta ES), y por herramienta: slug, nombre, año,
used_until, categoría, contexto, tags, relacionadas y sucesora.
Re-generar tras import_catalog.py:  python3 sketches/002-mapa-mental/tools/prep_data.py
"""
import json
import sqlite3
from pathlib import Path

ROOT = Path(__file__).resolve().parents[3]  # repo/sketches/002-mapa-mental/tools/
OUT = Path(__file__).resolve().parents[1] / 'data.js'

LABELS = {
    'editor': 'Editores',
    'lenguaje': 'Lenguajes',
    'database': 'Bases de datos',
    'infra': 'Infraestructura',
    'framework': 'Frameworks',
    'tool': 'Herramientas',
    'os': 'Sistemas operativos',
    'other': 'Otros',
}

con = sqlite3.connect(ROOT / 'app/data/catalog.sqlite')
con.row_factory = sqlite3.Row

cats = []
for r in con.execute("SELECT slug, name FROM categories ORDER BY name"):
    n = con.execute("SELECT COUNT(*) FROM tools WHERE category = ?", (r['slug'],)).fetchone()[0]
    cats.append({'slug': r['slug'], 'label': LABELS.get(r['slug'], r['name']), 'count': n})

tools = []
for r in con.execute("""SELECT id, slug, name, year, used_until, category, context,
                               successor, successor_slug
                        FROM tools ORDER BY year, name"""):
    tid = r['id']
    tags = [x[0] for x in con.execute(
        "SELECT t.name FROM tool_tags tt JOIN tags t ON t.id = tt.tag_id "
        "WHERE tt.tool_id = ? ORDER BY tt.rowid", (tid,))]
    rel = [x[0] for x in con.execute(
        "SELECT related_slug FROM tool_relations WHERE tool_id = ? ORDER BY rowid", (tid,))]
    tools.append({
        'slug': r['slug'], 'name': r['name'], 'year': r['year'],
        'until': r['used_until'], 'cat': r['category'], 'ctx': r['context'],
        'tags': tags, 'rel': rel, 'succ': r['successor_slug'],
    })

con.close()

payload = {'site': 'software-tools', 'categories': cats, 'tools': tools}
OUT.write_text(
    '// Generado por tools/prep_data.py desde catalog.sqlite — no editar a mano.\n'
    'window.ST_DATA = ' + json.dumps(payload, ensure_ascii=False, indent=1) + ';\n',
    encoding='utf-8')
print(f'data.js: {len(cats)} categorías, {len(tools)} herramientas → {OUT}')
