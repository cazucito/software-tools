#!/usr/bin/env python3
"""Importador Markdown → catalog.sqlite (software-tools v3).

Fuente de autoría: src/content/tools/*.md (frontmatter YAML + cuerpo Markdown).
Genera: app/data/catalog.sqlite (artefacto derivado; NO se commitea).

Uso (desde la raíz del repo):
    python3 ops/tools/import_catalog.py [--source src/content/tools] [--db app/data/catalog.sqlite]
"""
import argparse
import glob
import os
import re
import sqlite3
import sys
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
  image TEXT, successor TEXT, successor_slug TEXT, next_version TEXT,
  published INTEGER NOT NULL DEFAULT 1, created_at TEXT, updated_at TEXT);
CREATE TABLE IF NOT EXISTS tool_i18n(
  tool_id INTEGER NOT NULL REFERENCES tools(id) ON DELETE CASCADE,
  lang TEXT NOT NULL, context TEXT, body TEXT, PRIMARY KEY(tool_id, lang));
CREATE TABLE IF NOT EXISTS categories(slug TEXT PRIMARY KEY, name TEXT NOT NULL);
CREATE TABLE IF NOT EXISTS tags(id INTEGER PRIMARY KEY, name TEXT UNIQUE NOT NULL);
CREATE TABLE IF NOT EXISTS tool_tags(tool_id INTEGER NOT NULL REFERENCES tools(id) ON DELETE CASCADE,
  tag_id INTEGER NOT NULL REFERENCES tags(id), PRIMARY KEY(tool_id, tag_id));
CREATE TABLE IF NOT EXISTS tool_relations(tool_id INTEGER NOT NULL REFERENCES tools(id) ON DELETE CASCADE,
  related_slug TEXT NOT NULL, PRIMARY KEY(tool_id, related_slug));
-- Las tablas operativas (comments, downloads, download_keys, tool_assets, rate_events…)
-- viven aparte en ops.sqlite (ADR-009): la app las auto-inicializa; el importador no las toca.
CREATE VIRTUAL TABLE IF NOT EXISTS tools_fts USING fts5(name, context, body, tags);
"""

CLEAR_ORDER = ['tool_relations', 'tool_tags', 'tags', 'categories', 'tools_fts', 'tools']


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
    ap.add_argument('--source', default='src/content/tools')
    ap.add_argument('--db', default='app/data/catalog.sqlite')
    args = ap.parse_args()

    files = sorted(glob.glob(os.path.join(args.source, '*.md')))
    if not files:
        print(f'ERROR: sin archivos en {args.source}', file=sys.stderr)
        sys.exit(1)

    os.makedirs(os.path.dirname(args.db), exist_ok=True)
    con = sqlite3.connect(args.db)
    con.executescript(SCHEMA)
    for t in CLEAR_ORDER:
        con.execute(f'DELETE FROM {t}')
    con.commit()

    now = datetime.now(timezone.utc).isoformat(timespec='seconds')
    n = 0
    for f in files:
        base = os.path.basename(f)
        if base.startswith('_'):
            continue
        fm, body = parse_md(f)
        slug = (fm.get('slug') or '').strip()
        if not slug or slug in SKIP_SLUGS:
            continue
        successor_slug = (fm.get('successorSlug') or None)
        next_version = successor_slug if successor_slug in VERSION_SLUGS else None
        published = 0 if fm.get('published') is False else 1
        cur = con.execute(
            """INSERT INTO tools(slug, name, year, used_until, category, context, body,
                                 image, successor, successor_slug, next_version, published,
                                 created_at, updated_at)
               VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)""",
            (slug, fm.get('name', ''), int(fm.get('year') or 0), fm.get('usedUntil'),
             fm.get('category', ''), fm.get('context', ''), body,
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
    con.execute("INSERT OR REPLACE INTO schema_meta(key, value) VALUES ('spec_version', '3.0-draft')")
    con.execute("INSERT OR REPLACE INTO schema_meta(key, value) VALUES ('imported_at', ?)", (now,))
    con.execute("INSERT OR REPLACE INTO schema_meta(key, value) VALUES ('source_dir', ?)", (args.source,))
    con.commit()

    counts = {}
    for t in ('tools', 'tags', 'tool_tags', 'tool_relations', 'categories', 'tool_i18n', 'tools_fts'):
        counts[t] = con.execute(f'SELECT COUNT(*) FROM {t}').fetchone()[0]
    size = os.path.getsize(args.db)
    con.close()

    print(f'OK — importadas {n} fichas → {args.db} ({size/1024:.1f} KB)')
    print('conteos:', ', '.join(f'{k}={v}' for k, v in counts.items()))


if __name__ == '__main__':
    main()
