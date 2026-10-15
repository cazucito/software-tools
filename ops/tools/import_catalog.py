#!/usr/bin/env python3
"""Importador Markdown → catalog.sqlite (software-tools v3).

Fuente de autoría: content/tools/*.md (frontmatter YAML + cuerpo Markdown)
y content/taxonomy.json (umbral, etiquetas/colores de categoría, familias).
Genera: app/data/catalog.sqlite (artefacto derivado; NO se commitea). Todo lo que
el mapa necesita (familias, etiqueta y color de categoría) queda en la BD: PHP
nunca lee el JSON en tiempo de ejecución.

Reglas de la taxonomía (idénticas al generador de la maqueta 002):
  - categoría con <= threshold herramientas → plana (sin familias).
  - categoría con > threshold → familias del JSON; las herramientas no listadas
    van a la familia sintética «sin-clasificar» (aviso con sus slugs).
  - aborta SIN escribir ante slugs desconocidos, duplicados, una herramienta en la
    categoría equivocada, familias vacías o colores inválidos.

Uso (desde la raíz del repo):
    python3 ops/tools/import_catalog.py [--source content/tools] [--db app/data/catalog.sqlite]
                                        [--taxonomy content/taxonomy.json]
"""
import argparse
import glob
import os
import re
import json
import sqlite3
import sys
import zlib
from datetime import datetime, timezone

import yaml

# Slugs que no son herramientas (plantilla)
SKIP_SLUGS = {'nombre-de-la-herramienta', '_template'}
# Referencias a «versión siguiente» (mismo software) — se modelan como next_version
VERSION_SLUGS = {'maple-6', 'mathematica-3', 'matlab-r14', 'quickbasic-4-5'}

SCHEMA = """
PRAGMA foreign_keys = ON;
CREATE TABLE IF NOT EXISTS schema_meta(key TEXT PRIMARY KEY, value TEXT);
CREATE TABLE IF NOT EXISTS tools(
  id INTEGER PRIMARY KEY,
  slug TEXT UNIQUE NOT NULL, name TEXT NOT NULL, year INTEGER NOT NULL,
  used_until INTEGER, category TEXT NOT NULL, context TEXT NOT NULL, body TEXT NOT NULL DEFAULT '',
  family TEXT, image TEXT, successor TEXT, successor_slug TEXT, next_version TEXT,
  published INTEGER NOT NULL DEFAULT 1, created_at TEXT, updated_at TEXT);
CREATE TABLE IF NOT EXISTS tool_i18n(
  tool_id INTEGER NOT NULL REFERENCES tools(id) ON DELETE CASCADE,
  lang TEXT NOT NULL, context TEXT, body TEXT, PRIMARY KEY(tool_id, lang));
CREATE TABLE IF NOT EXISTS categories(slug TEXT PRIMARY KEY, name TEXT NOT NULL, label_es TEXT, color TEXT);
CREATE TABLE IF NOT EXISTS families(
  category TEXT NOT NULL, slug TEXT NOT NULL, label TEXT NOT NULL, position INTEGER NOT NULL,
  PRIMARY KEY(category, slug));
CREATE TABLE IF NOT EXISTS tags(id INTEGER PRIMARY KEY, name TEXT UNIQUE NOT NULL);
CREATE TABLE IF NOT EXISTS tool_tags(tool_id INTEGER NOT NULL REFERENCES tools(id) ON DELETE CASCADE,
  tag_id INTEGER NOT NULL REFERENCES tags(id), PRIMARY KEY(tool_id, tag_id));
CREATE TABLE IF NOT EXISTS tool_relations(tool_id INTEGER NOT NULL REFERENCES tools(id) ON DELETE CASCADE,
  related_slug TEXT NOT NULL, PRIMARY KEY(tool_id, related_slug));
-- Las tablas operativas (comments, downloads, download_keys, tool_assets, rate_events…)
-- viven aparte en ops.sqlite (ADR-009): la app las auto-inicializa; el importador no las toca.
CREATE VIRTUAL TABLE IF NOT EXISTS tools_fts USING fts5(name, context, body, tags);
"""

CLEAR_ORDER = ['tool_relations', 'tool_tags', 'tags', 'categories', 'families', 'tools_fts', 'tools']

# Columnas añadidas después de la v3 inicial: se agregan si la BD ya existía.
ADD_COLUMNS = [('tools', 'family', 'TEXT'), ('categories', 'label_es', 'TEXT'), ('categories', 'color', 'TEXT')]

UNCLASSIFIED = {'slug': 'sin-clasificar', 'label': 'Sin clasificar'}
# Paleta fija de categorías (8 colores): respaldo determinista (crc32) para slugs sin color en el JSON.
PALETTE = ['#7dd3fc', '#a78bfa', '#34d399', '#fbbf24', '#f472b6', '#fb923c', '#2dd4bf', '#a3e635']
HEX_COLOR = re.compile(r'^#[0-9a-fA-F]{6}$')


def fail(msg):
    sys.exit(f'ERROR: {msg}')


def color_of(slug, colors):
    """Color del JSON; si no hay, hash estable (crc32, no hash() de Python) sobre la paleta."""
    return colors.get(slug) or PALETTE[zlib.crc32(slug.encode('utf-8')) % len(PALETTE)]


def load_taxonomy(path):
    try:
        with open(path, encoding='utf-8') as fh:
            tax = json.load(fh)
    except (OSError, ValueError) as err:
        fail(f'no se pudo leer {path}: {err}')
    threshold = tax.get('threshold')
    if isinstance(threshold, bool) or not isinstance(threshold, int) or not 1 <= threshold <= 50:
        fail(f'taxonomy.json: threshold debe ser un entero 1..50 (recibido: {threshold!r})')
    tax.setdefault('category_labels', {})
    tax.setdefault('category_colors', {})
    tax.setdefault('families', {})
    for k, v in tax['category_colors'].items():
        if not isinstance(v, str) or not HEX_COLOR.match(v):
            fail(f'taxonomy.json: category_colors[{k!r}] debe ser #rrggbb (recibido: {v!r})')
    return tax


def plan_taxonomy(tax, tools):
    """Valida el JSON contra el catálogo y calcula la familia de cada herramienta.

    tools: lista de (slug, categoría). Devuelve (family_of, families, unclassified):
      family_of      slug → slug de familia (solo categorías subdivididas)
      families       categoría → [(slug, label)] en orden de taxonomía (+ sin-clasificar al final)
      unclassified   categoría → [slugs] sin familia asignada
    """
    cat_of = dict(tools)
    cat_slugs = set(cat_of.values())
    for k in sorted(set(tax['category_labels']) - cat_slugs):
        print(f'AVISO: category_labels «{k}» no coincide con ninguna categoría del catálogo (se ignora)')
    assigned = {}
    for cslug, fams in tax['families'].items():
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
                    fail(f'slug duplicado en taxonomy.json: {m} ({assigned[m][0]}/{assigned[m][1]} y {cslug}/{fslug})')
                assigned[m] = (cslug, fslug)

    family_of, families, unclassified = {}, {}, {}
    for cslug in sorted(cat_slugs):
        slugs = sorted(s for s, c in tools if c == cslug)
        if len(slugs) <= tax['threshold']:
            if cslug in tax['families']:
                print(f'AVISO: {cslug} tiene {len(slugs)} <= {tax["threshold"]} herramientas; '
                      'se ignoran sus familias del JSON (categoría plana)')
            continue
        fams = []
        for f in tax['families'].get(cslug, []):
            fams.append((f['slug'], f['label']))
            for m in f['tools']:
                family_of[m] = f['slug']
        rest = [m for m in slugs if m not in family_of]
        if rest:
            for m in rest:
                family_of[m] = UNCLASSIFIED['slug']
            fams.append((UNCLASSIFIED['slug'], UNCLASSIFIED['label']))
            unclassified[cslug] = rest
        families[cslug] = fams
    return family_of, families, unclassified


def parse_md(path):
    txt = open(path, encoding='utf-8').read()
    m = re.match(r'^---\n(.*?)\n---\n?(.*)$', txt, re.S)
    if not m:
        raise ValueError(f'frontmatter ausente: {path}')
    fm = yaml.safe_load(m.group(1)) or {}
    body = (m.group(2) or '').strip()
    return fm, body


def main():
    ap = argparse.ArgumentParser()
    ap.add_argument('--source', default='content/tools')
    ap.add_argument('--db', default='app/data/catalog.sqlite')
    ap.add_argument('--taxonomy', default='content/taxonomy.json')
    args = ap.parse_args()

    files = sorted(glob.glob(os.path.join(args.source, '*.md')))
    if not files:
        print(f'ERROR: sin archivos en {args.source}', file=sys.stderr)
        sys.exit(1)

    # Fase 1 (sin escribir): leer fichas y validar la taxonomía contra ellas.
    records = []
    for f in files:
        if os.path.basename(f).startswith('_'):
            continue
        fm, body = parse_md(f)
        slug = (fm.get('slug') or '').strip()
        if not slug or slug in SKIP_SLUGS:
            continue
        records.append((slug, fm, body))
    tax = load_taxonomy(args.taxonomy)
    family_of, families, unclassified = plan_taxonomy(
        tax, [(slug, fm.get('category', '')) for slug, fm, _ in records])

    # Fase 2: reconstruir la BD.
    os.makedirs(os.path.dirname(args.db), exist_ok=True)
    con = sqlite3.connect(args.db)
    con.executescript(SCHEMA)
    for table, column, ctype in ADD_COLUMNS:
        cols = [r[1] for r in con.execute(f'PRAGMA table_info({table})')]
        if column not in cols:
            con.execute(f'ALTER TABLE {table} ADD COLUMN {column} {ctype}')
    for t in CLEAR_ORDER:
        con.execute(f'DELETE FROM {t}')
    con.commit()

    now = datetime.now(timezone.utc).isoformat(timespec='seconds')
    n = 0
    for slug, fm, body in records:
        successor_slug = (fm.get('successorSlug') or None)
        next_version = successor_slug if successor_slug in VERSION_SLUGS else None
        published = 0 if fm.get('published') is False else 1
        cur = con.execute(
            """INSERT INTO tools(slug, name, year, used_until, category, family, context, body,
                                 image, successor, successor_slug, next_version, published,
                                 created_at, updated_at)
               VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)""",
            (slug, fm.get('name', ''), int(fm.get('year') or 0), fm.get('usedUntil'),
             fm.get('category', ''), family_of.get(slug), fm.get('context', ''), body,
             fm.get('image'), fm.get('successor'), successor_slug, next_version, published,
             now, now))
        tool_id = cur.lastrowid
        for tag in (fm.get('tags') or []):
            con.execute('INSERT OR IGNORE INTO tags(name) VALUES (?)', (tag,))
            tag_id = con.execute('SELECT id FROM tags WHERE name=?', (tag,)).fetchone()[0]
            con.execute('INSERT OR IGNORE INTO tool_tags(tool_id, tag_id) VALUES (?,?)', (tool_id, tag_id))
        for rel in (fm.get('related') or []):
            con.execute('INSERT OR IGNORE INTO tool_relations(tool_id, related_slug) VALUES (?,?)', (tool_id, rel))
        con.execute(
            'INSERT INTO tools_fts(rowid, name, context, body, tags) VALUES (?,?,?,?,?)',
            (tool_id, fm.get('name', ''), fm.get('context', ''), body, ' '.join(fm.get('tags') or [])))
        n += 1

    con.execute("INSERT OR IGNORE INTO categories(slug, name) SELECT DISTINCT category, category FROM tools")
    for (cslug,) in con.execute("SELECT slug FROM categories").fetchall():
        con.execute("UPDATE categories SET label_es = ?, color = ? WHERE slug = ?",
                    (tax['category_labels'].get(cslug, cslug), color_of(cslug, tax['category_colors']), cslug))
    for cslug, fams in families.items():
        for pos, (fslug, flabel) in enumerate(fams):
            con.execute("INSERT INTO families(category, slug, label, position) VALUES (?,?,?,?)",
                        (cslug, fslug, flabel, pos))
    con.execute("INSERT OR REPLACE INTO schema_meta(key, value) VALUES ('spec_version', '3.0-draft')")
    con.execute("INSERT OR REPLACE INTO schema_meta(key, value) VALUES ('imported_at', ?)", (now,))
    con.execute("INSERT OR REPLACE INTO schema_meta(key, value) VALUES ('source_dir', ?)", (args.source,))
    con.commit()

    counts = {}
    for t in ('tools', 'tags', 'tool_tags', 'tool_relations', 'categories', 'families', 'tool_i18n', 'tools_fts'):
        counts[t] = con.execute(f'SELECT COUNT(*) FROM {t}').fetchone()[0]
    size = os.path.getsize(args.db)
    con.close()

    print(f'OK — importadas {n} fichas → {args.db} ({size/1024:.1f} KB)')
    print('conteos:', ', '.join(f'{k}={v}' for k, v in counts.items()))
    for cslug, rest in unclassified.items():
        print(f'AVISO: {cslug}: {len(rest)} herramientas sin familia → «sin-clasificar»: ' + ', '.join(rest))


if __name__ == '__main__':
    main()
