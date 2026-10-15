#!/usr/bin/env python3
"""Genera data.js para la maqueta 002 (mapa mental) desde catalog.sqlite.

Dos anillos: categorías → familias → herramientas.

Regla global y determinista: una categoría con más de THRESHOLD herramientas
se subdivide en familias curadas (TAXONOMY); el resto muestra sus herramientas
directas. Validación estricta: cada herramienta de una categoría subdividida
debe estar exactamente en una familia, y cada slug de TAXONOMY debe existir en
su categoría. Si algo no cuadra, el script aborta sin escribir.

Re-generar tras import_catalog.py:
  python3 sketches/002-mapa-mental/tools/prep_data.py
"""
import json
import sqlite3
import sys
from pathlib import Path

ROOT = Path(__file__).resolve().parents[3]  # repo/sketches/002-mapa-mental/tools/
OUT = Path(__file__).resolve().parents[1] / 'data.js'

THRESHOLD = 6  # > THRESHOLD herramientas en una categoría → se subdivide

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

# Familias (segundo anillo). Solo para categorías que superan THRESHOLD.
TAXONOMY = {
    'tool': [
        ('navegadores', 'Navegadores',
         ['netscape-navigator', 'internet-explorer', 'mozilla-firefox', 'google-chrome']),
        ('correo', 'Correo', ['eudora', 'microsoft-outlook']),
        ('archivos-y-respaldo', 'Archivos y respaldo',
         ['pkzip', 'winzip', 'norton-commander', 'windows-explorer', 'norton-ghost', 'acronis-true-image']),
        ('diagramacion', 'Diagramación y conocimiento',
         ['axon-idea-processor', 'balsamiq-mockups', 'mindmanager', 'personal-brain', 'thebrain-8', 'umlet']),
        ('calculo-e-ingenieria', 'Cálculo e ingeniería',
         ['maple-v', 'mathematica', 'matlab', 'electronic-workbench', 'multisim']),
        ('ofimatica', 'Ofimática', ['lotus-1-2-3', 'microsoft-excel']),
        ('arte-ascii', 'Arte ASCII', ['bannermania', 'figlet']),
        ('mantenimiento', 'Mantenimiento', ['system-mechanic']),
        ('agentes-ia', 'Agentes de IA', ['openclaw']),
    ],
    'editor': [
        ('ides', 'IDEs', ['eclipse', 'gel', 'intellij-idea', 'netbeans', 'visual-studio']),
        ('editores-de-codigo', 'Editores de código', ['atom', 'sublime-text', 'visual-studio-code']),
        ('escritura-cientifica', 'Escritura científica', ['chi-writer', 'latex']),
    ],
    'lenguaje': [
        ('borland', 'Borland', ['turbo-c', 'borland-cpp', 'borland-pascal-7', 'delphi']),
        ('basic', 'BASIC', ['qbasic', 'visual-basic-6', 'visual-basic-net']),
    ],
}

con = sqlite3.connect(ROOT / 'app/data/catalog.sqlite')
con.row_factory = sqlite3.Row

cats = []
sub_of = {}  # slug de herramienta -> slug de familia
for r in con.execute("SELECT slug, name FROM categories ORDER BY name"):
    cslug = r['slug']
    slugs = {x['slug'] for x in con.execute(
        "SELECT slug FROM tools WHERE category = ?", (cslug,))}
    count = len(slugs)
    subs = []
    if count > THRESHOLD and slugs:
        tax = TAXONOMY.get(cslug)
        if not tax:
            sys.exit(f'ERROR: {cslug} tiene {count} herramientas y no hay TAXONOMY')
        seen = set()
        for sslug, slabel, members in tax:
            bad = [m for m in members if m not in slugs]
            if bad:
                sys.exit(f'ERROR: {cslug}/{sslug} referencia slugs inexistentes: {bad}')
            dup = [m for m in members if m in seen]
            if dup:
                sys.exit(f'ERROR: slugs duplicados en {cslug}: {dup}')
            seen.update(members)
            for m in members:
                sub_of[m] = sslug
            subs.append({'slug': sslug, 'label': slabel, 'count': len(members)})
        missing = slugs - seen
        if missing:
            sys.exit(f'ERROR: {cslug} sin familia asignada: {sorted(missing)}')
    cats.append({'slug': cslug, 'label': LABELS.get(cslug, r['name']),
                 'count': count, 'subs': subs})

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
