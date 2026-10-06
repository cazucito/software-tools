#!/usr/bin/env python3
"""Verificador de paridad: Markdown (fuente) ↔ catalog.sqlite (espejo v3).

Comprueba que NADA se perdió en la importación y que la BD es fiel a las fuentes:
campos por ficha, conteos, checksums normalizados, referencias y simetría.

Uso (desde la raíz del repo):
    python3 ops/tools/verify_parity.py [--source src/content/tools] [--db app/data/catalog.sqlite]
Salida: reporte en stdout + archivo en ops/reports/ · exit 0 = paridad OK.
"""
import argparse
import glob
import hashlib
import os
import re
import sqlite3
import sys
from datetime import date

import yaml

SKIP_SLUGS = {'nombre-de-la-herramienta', '_template'}
VERSION_SLUGS = {'maple-6', 'mathematica-3', 'matlab-r14', 'quickbasic-4-5'}


def parse_md(path):
    txt = open(path, encoding='utf-8').read()
    m = re.match(r'^---\n(.*?)\n---\n?(.*)$', txt, re.S)
    if not m:
        raise ValueError(f'frontmatter ausente: {path}')
    return yaml.safe_load(m.group(1)) or {}, (m.group(2) or '').strip()


def norm(s):
    return re.sub(r'\s+', ' ', (s or '')).strip()


def sha12(s):
    return hashlib.sha256(norm(s).encode('utf-8')).hexdigest()[:12]


def main():
    ap = argparse.ArgumentParser()
    ap.add_argument('--source', default='src/content/tools')
    ap.add_argument('--db', default='app/data/catalog.sqlite')
    ap.add_argument('--report', default='ops/reports/2026-10-05-paridad.md')
    args = ap.parse_args()

    # 1) fuente markdown
    md = {}
    for f in sorted(glob.glob(os.path.join(args.source, '*.md'))):
        if os.path.basename(f).startswith('_'):
            continue
        fm, body = parse_md(f)
        slug = (fm.get('slug') or '').strip()
        if not slug or slug in SKIP_SLUGS:
            continue
        md[slug] = {'fm': fm, 'body': body}

    # 2) espejo sqlite
    con = sqlite3.connect(f'file:{args.db}?mode=ro', uri=True)
    db = {}
    for row in con.execute('SELECT slug, name, year, used_until, category, context, body, successor_slug, next_version, published FROM tools'):
        db[row[0]] = dict(zip(['slug', 'name', 'year', 'used_until', 'category', 'context', 'body',
                               'successor_slug', 'next_version', 'published'], row))
    db_related = {}
    for tool_id, rel in con.execute('SELECT tool_id, related_slug FROM tool_relations'):
        db_related.setdefault(tool_id, set()).add(rel)
    id_by_slug = {r[0]: r[1] for r in con.execute('SELECT slug, id FROM tools')}
    db_tags = {}
    for slug, tag in con.execute(
            'SELECT t.slug, g.name FROM tools t JOIN tool_tags tt ON tt.tool_id=t.id JOIN tags g ON g.id=tt.tag_id'):
        db_tags.setdefault(slug, set()).add(tag)
    fts_count = con.execute('SELECT COUNT(*) FROM tools_fts').fetchone()[0]

    errs, warns, rows = [], [], []

    # 3) paridad ficha a ficha
    if set(md) != set(db):
        errs.append(f'conjuntos de slugs distintos: solo-md={sorted(set(md)-set(db))} solo-db={sorted(set(db)-set(md))}')
    for slug, m in sorted(md.items()):
        d = db.get(slug)
        if not d:
            continue
        fm = m['fm']
        checks = {
            'name': fm.get('name', '') == d['name'],
            'year': int(fm.get('year') or 0) == d['year'],
            'used_until': (fm.get('usedUntil') or None) == d['used_until'],
            'category': fm.get('category', '') == d['category'],
            'context': norm(fm.get('context')) == norm(d['context']),
            'body': norm(m['body']) == norm(d['body']),
            'successor_slug': (fm.get('successorSlug') or None) == d['successor_slug'],
            'next_version': (d['successor_slug'] if d['successor_slug'] in VERSION_SLUGS else None) == d['next_version'],
        }
        bad = [k for k, v in checks.items() if not v]
        if bad:
            errs.append(f'{slug}: campos no coinciden → {", ".join(bad)}')
        md_rel = set(fm.get('related') or [])
        if md_rel != db_related.get(id_by_slug[slug], set()):
            errs.append(f'{slug}: related distinto md={sorted(md_rel)} db={sorted(db_related.get(id_by_slug[slug], set()))}')
        if set(fm.get('tags') or []) != db_tags.get(slug, set()):
            errs.append(f'{slug}: tags distintos')
        rows.append((slug, len(re.findall(r'\w+', m['body'])), sha12(fm.get('context', '') + '\n' + m['body'])))

    # 4) referencias
    dangling_rel = sorted({r for rs in db_related.values() for r in rs if r not in md})
    dangling_succ = sorted({d['successor_slug'] for d in db.values()
                            if d['successor_slug'] and d['successor_slug'] not in md})
    unexpected_succ = [s for s in dangling_succ if s not in VERSION_SLUGS]
    if dangling_rel:
        errs.append(f'related sin resolver: {dangling_rel}')
    if unexpected_succ:
        errs.append(f'successorSlug sin resolver NO esperados: {unexpected_succ}')

    # 5) simetría
    asym = 0
    for slug, m in md.items():
        for r in (m['fm'].get('related') or []):
            if r in md and slug not in (md[r]['fm'].get('related') or []):
                asym += 1
                errs.append(f'asimetría: {slug} → {r}')
    if asym:
        pass

    # 6) FTS
    fts_tests = {}
    for q in ['eudora', 'contenedores', 'turbo', 'oracle', 'ascii']:
        c = con.execute('SELECT COUNT(*) FROM tools_fts WHERE tools_fts MATCH ?', (q,)).fetchone()[0]
        fts_tests[q] = c
        if c == 0:
            errs.append(f'FTS sin resultados para «{q}»')
    if fts_count != len(db):
        errs.append(f'tools_fts filas={fts_count} ≠ tools={len(db)}')

    con.close()

    # 7) reporte
    ok = not errs
    L = []
    L.append('# Reporte de paridad — Markdown ↔ catalog.sqlite')
    L.append('')
    L.append(f'**Fecha:** {date.today().isoformat()} · **Fuente:** `{args.source}` · **BD:** `{args.db}`')
    L.append('')
    L.append(f'## Resultado: {"✅ PARIDAD OK" if ok else "❌ CON ERRORES"} ({len(md)} fichas)')
    L.append('')
    L.append(f'- Fichas en Markdown: **{len(md)}** · en BD: **{len(db)}** · FTS: **{fts_count}**')
    L.append(f'- Referencias `related` sin resolver: {len(dangling_rel)} {dangling_rel if dangling_rel else "(ninguna)"}')
    L.append(f'- `successorSlug` sin resolver: {len(dangling_succ)} {dangling_succ} — esperados (versiones siguientes → `next_version`)')
    L.append(f'- Asimetrías de `related`: {asym} (esperado 0)')
    L.append(f'- Consultas FTS de prueba: ' + ', '.join(f'«{k}»={v}' for k, v in fts_tests.items()))
    L.append('')
    if errs:
        L.append('## Errores')
        L.extend(f'- {e}' for e in errs)
        L.append('')
    L.append('## Checksums por ficha (sha256-12 de `contexto + cuerpo` normalizados)')
    L.append('')
    L.append('| slug | palabras | sha12 |')
    L.append('|---|---|---|')
    for slug, words, s in rows:
        L.append(f'| `{slug}` | {words} | `{s}` |')
    L.append('')
    report = '\n'.join(L)
    os.makedirs(os.path.dirname(args.report), exist_ok=True)
    open(args.report, 'w', encoding='utf-8').write(report)

    print(report[:1200])
    print(f'\n[... reporte completo en {args.report}]')
    print(f'\nRESULTADO: {"PARIDAD OK" if ok else "ERRORES: " + str(len(errs))}')
    sys.exit(0 if ok else 1)


if __name__ == '__main__':
    main()
