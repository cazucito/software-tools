#!/usr/bin/env python3
"""Exporta el inventario completo de descargas (BD del hosting) a ops/reports/downloads-inventory.json.

Lee las filas del panel admin (que reflejan ops.sqlite), sin tocar nada: solo lectura.
Conexión: ST_BASE (URL raíz) y ST_ADMIN_PASS (contraseña admin) por entorno.
Uso: ST_BASE=https://... ST_ADMIN_PASS=... python3 ops/tools/downloads_inventory.py
"""
import html
import json
import os
import re
import sys
import urllib.parse
import urllib.request
import http.cookiejar

BASE = os.environ['ST_BASE'].rstrip('/')
PASS = os.environ['ST_ADMIN_PASS']


class Admin:
    def __init__(self):
        jar = http.cookiejar.CookieJar()
        self.op = urllib.request.build_opener(urllib.request.HTTPCookieProcessor(jar))
        self.op.addheaders = [('User-Agent', 'st-inventory (software-tools)')]

    def get(self, path: str) -> str:
        r = self.op.open(BASE + path, timeout=90)
        return r.read().decode()

    def post(self, path: str, fields: dict) -> str:
        req = urllib.request.Request(
            BASE + path, data=urllib.parse.urlencode(fields).encode(), method='POST')
        r = self.op.open(req, timeout=90)
        return r.read().decode()

    def login(self):
        page = self.get('/admin/login')
        m = re.search(r'name="csrf" value="([0-9a-f]{32})"', page)
        if not m:
            sys.exit('login: no csrf')
        self.post('/admin/login', {'csrf': m.group(1), 'password': PASS})


def parse_tool_page(page: str) -> list[dict]:
    """Fila por archivo del panel admin (name/version/variant/year/visibility/sha/license)."""
    rows = []
    for b in page.split('<strong class="ad-file">')[1:]:
        fn = html.unescape(b.split('</strong>')[0])
        get = lambda n: (m.group(1) if (m := re.search(
            rf'name="{n}"[^>]*value="([^"]*)"', b)) else '')
        sha_m = re.search(r'class="ad-note" title="([0-9a-f]{64})"', b)
        vis_m = re.search(r'<option value="(\w+)" selected', b)
        away_m = re.search(r'<span class="ad-note">([^<]*)</span>', b)
        rows.append({
            'filename': fn,
            'version': get('version'),
            'variant': get('variant'),
            'year': get('year'),
            'visibility': vis_m.group(1) if vis_m else 'clave',
            'size': away_m.group(1) if away_m else '',
            'sha256': sha_m.group(1) if sha_m else '',
            'license': get('license'),
            'source_url': get('source_url'),
        })
    return rows


def main():
    admin = Admin()
    admin.login()
    index = admin.get('/admin/downloads')
    slugs = sorted(set(re.findall(r'href="/admin/downloads/([a-z0-9-]+)"', index)))
    if not slugs:
        sys.exit('no hay enlaces a herramientas con descargas en /admin/downloads')
    tools = {}
    for slug in slugs:
        page = admin.get(f'/admin/downloads/{slug}')
        rows = parse_tool_page(page)
        if rows:
            tools[slug] = rows
    inv = {
        'generated_at': __import__('datetime').datetime.now().isoformat(timespec='seconds'),
        'base_url': BASE,
        'tools': tools,
    }
    out = os.path.join(os.path.dirname(os.path.abspath(__file__)), '..', 'reports',
                       'downloads-inventory.json')
    with open(out, 'w', encoding='utf-8') as f:
        json.dump(inv, f, ensure_ascii=False, indent=2)
    total = sum(len(rows) for rows in tools.values())
    hosted = sum(1 for rows in tools.values() for r in rows if r['visibility'] != 'enlace')
    print(f'herramientas: {len(tools)} · filas: {total} (alojadas: {hosted}) · → {out}')
    for slug, rows in tools.items():
        print(f'  {slug:<12} {len(rows):>3} filas')


if __name__ == '__main__':
    main()