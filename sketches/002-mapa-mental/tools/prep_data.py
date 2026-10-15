#!/usr/bin/env python3
"""Genera data.js para la maqueta 002 (mapa mental) desde catalog.sqlite.

Dos anillos: categorías → familias → herramientas.

La taxonomía es DATO, no código: vive en tools/taxonomy.json (umbral,
etiquetas de categoría y familias curadas). Añadir una categoría o
recategorizar = editar ese JSON (más el catálogo).

Reglas deterministas:
  - categoría con <= threshold herramientas → plana (sin familias).
  - categoría con > threshold → anillo de familias tomado del JSON. Las
    herramientas no listadas NO abortan: van a la familia sintética
    «sin-clasificar» y el script imprime sus slugs (visibilidad, no fallo).
  - aborta sin escribir si el JSON tiene slugs desconocidos (typos), slugs
    duplicados, o una herramienta asignada a una categoría que no es la suya.
  - guarda TOTAL: cada herramienta de una categoría subdividida termina en
    exactamente una familia.
  - cada categoría emite `color`: paleta fija por slug conocido y, para slugs
    nuevos, hash determinista sobre una paleta de 8 colores.

Re-generar tras import_catalog.py o al editar taxonomy.json:
  python3 sketches/002-mapa-mental/tools/prep_data.py
"""
import json
import sqlite3
import sys
import zlib
from pathlib import Path

ROOT = Path(__file__).resolve().parents[3]  # repo/sketches/002-mapa-mental/tools/
OUT = Path(__file__).resolve().parents[1] / 'data.js'
TAXONOMY_JSON = Path(__file__).resolve().parent / 'taxonomy.json'

UNCLASSIFIED = {'slug': 'sin-clasificar', 'label': 'Sin clasificar'}

CAT_COLOR = {'editor': '#7dd3fc', 'lenguaje': '#a78bfa', 'database': '#34d399',
             'infra': '#fbbf24', 'os': '#f472b6', 'tool': '#94a3b8', 'other': '#64748b'}
PALETTE = ['#7dd3fc', '#a78bfa', '#34d399', '#fbbf24', '#f472b6', '#fb923c', '#2dd4bf', '#a3e635']


def color_of(slug):
    """Color fijo si es conocido; si no, hash estable (crc32, no hash() de Python)."""
    return CAT_COLOR.get(slug) or PALETTE[zlib.crc32(slug.encode('utf-8')) % len(PALETTE)]


def fail(msg):
    sys.exit(f'ERROR: {msg}')


tax = json.loads(TAXONOMY_JSON.read_text(encoding='utf-8'))
THRESHOLD = tax['threshold']  # > THRESHOLD herramientas en una categoría → se subdivide
LABELS = tax.get('category_labels', {})
FAMILIES = tax.get('families', {})

con = sqlite3.connect(ROOT / 'app/data/catalog.sqlite')
con.row_factory = sqlite3.Row

# --- validación del JSON contra el catálogo (antes de escribir nada) ---
cat_of = {r['slug']: r['category'] for r in con.execute("SELECT slug, category FROM tools")}
cat_slugs = {r['slug'] for r in con.execute("SELECT slug FROM categories")}
assigned = {}  # slug de herramienta -> (categoría, familia) según el JSON
for cslug, fams in FAMILIES.items():
    if cslug not in cat_slugs:
        fail(f'taxonomy.json: categoría desconocida «{cslug}» en families')
    fam_slugs = set()
    for f in fams:
        fslug = f['slug']
        if fslug == UNCLASSIFIED['slug']:
            fail(f'{cslug}: el slug «{fslug}» está reservado para la familia sintética')
        if fslug in fam_slugs:
            fail(f'{cslug}: familia duplicada «{fslug}»')
        fam_slugs.add(fslug)
        if not f['tools']:
            fail(f'{cslug}/{fslug}: familia sin herramientas')
        for m in f['tools']:
            if m not in cat_of:
                fail(f'{cslug}/{fslug} referencia un slug inexistente: {m}')
            if cat_of[m] != cslug:
                fail(f'{cslug}/{fslug}: «{m}» pertenece a la categoría «{cat_of[m]}»')
            if m in assigned:
                fail(f'slug duplicado en taxonomy.json: {m} '
                     f'({assigned[m][0]}/{assigned[m][1]} y {cslug}/{fslug})')
            assigned[m] = (cslug, fslug)

cats = []
sub_of = {}  # slug de herramienta -> slug de familia
unclassified = {}  # categoría -> slugs sin familia
for r in con.execute("SELECT slug, name FROM categories ORDER BY name"):
    cslug = r['slug']
    slugs = sorted(x['slug'] for x in con.execute(
        "SELECT slug FROM tools WHERE category = ?", (cslug,)))
    count = len(slugs)
    subs = []
    if count > THRESHOLD:
        for f in FAMILIES.get(cslug, []):
            for m in f['tools']:
                sub_of[m] = f['slug']
            subs.append({'slug': f['slug'], 'label': f['label'], 'count': len(f['tools'])})
        rest = [m for m in slugs if m not in sub_of]
        if rest:
            for m in rest:
                sub_of[m] = UNCLASSIFIED['slug']
            subs.append({**UNCLASSIFIED, 'count': len(rest)})
            unclassified[cslug] = rest
        # TOTAL: cada herramienta de la categoría en exactamente una familia
        if sum(s['count'] for s in subs) != count or any(m not in sub_of for m in slugs):
            fail(f'{cslug}: las familias suman {sum(s["count"] for s in subs)} '
                 f'de {count} herramientas')
    elif cslug in FAMILIES:
        print(f'AVISO: {cslug} tiene {count} <= {THRESHOLD} herramientas; '
              f'se ignoran sus familias del JSON (categoría plana)')
    cats.append({'slug': cslug, 'label': LABELS.get(cslug, r['name']),
                 'color': color_of(cslug), 'count': count, 'subs': subs})

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
        'until': r['used_until'], 'cat': r['category'], 'sub': sub_of.get(r['slug']),
        'ctx': r['context'], 'tags': tags, 'rel': rel, 'succ': r['successor_slug'],
    })

con.close()

payload = {'site': 'software-tools', 'categories': cats, 'tools': tools}
OUT.write_text(
    '// Generado por tools/prep_data.py desde catalog.sqlite — no editar a mano.\n'
    'window.ST_DATA = ' + json.dumps(payload, ensure_ascii=False, indent=1) + ';\n',
    encoding='utf-8')
nsubs = sum(len(c['subs']) for c in cats)
print(f'data.js: {len(cats)} categorías, {len(tools)} herramientas, '
      f'{nsubs} familias → {OUT}')
for cslug, rest in unclassified.items():
    print(f'AVISO: {cslug}: {len(rest)} herramientas sin familia → «sin-clasificar»: '
          + ', '.join(rest))
