#!/usr/bin/env python3
"""Sube la aplicación v3 (Fase 5) al subdominio → software-tools.pcabrera.com/proto/.

- Credenciales SOLO desde BWS en runtime (nunca en argv ni en salida).
- SOLO complementa: nunca borra nada del servidor.
- Verifica tamaño local == tamaño remoto por archivo.
- Excluye: config.local.php (secretos vivos), ops.sqlite (datos operativos),
  downloads/ (archivos del dueño) y assets/tools/ (subidos desde el admin).
- El catálogo solo se sube con --catalog (evita pisar ediciones vivas del admin).
"""
import argparse
import json
import os
import ssl
import subprocess
import tempfile
from ftplib import FTP, FTP_TLS, error_perm

ROOT = os.path.dirname(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))
REMOTE = 'proto'

# (carpeta local, prefijo remoto dentro de proto/)
TREES = [
    ('app/public', ''),
    ('app/server', 'server'),
]
# Nombres que jamás se suben (secretos / datos vivos / archivos del dueño)
EXCLUDE_NAMES = {'config.local.php', 'ops.sqlite', 'ops.sqlite-wal', 'ops.sqlite-shm'}
# Carpetas de la app que no se despliegan
EXCLUDE_DIRS = {'downloads', 'tools'}

SQLITE = ('app/data/catalog.sqlite', 'data/catalog.sqlite')

HTACCESS = """# software-tools v3 - datos generados: sin acceso web directo.
<IfModule mod_authz_core.c>
    Require all denied
</IfModule>
<IfModule !mod_authz_core.c>
    Order deny,allow
    Deny from all
</IfModule>
"""


REWRITE = """# software-tools v3 — URLs limpias (LiteSpeed/nginx compatible).
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php?p=$1 [QSA,L]
"""


def bws_value(key):
    out = subprocess.run(['bws', 'secret', 'list', '-o', 'json'], capture_output=True, text=True, check=True)
    data = json.loads(out.stdout)
    sid = next(s['id'] for s in data if s['key'] == key)
    out2 = subprocess.run(['bws', 'secret', 'get', sid, '-o', 'json'], capture_output=True, text=True, check=True)
    return json.loads(out2.stdout)['value']


def connect():
    host = bws_value('software-tools_FTP_SERVER')
    port = int(bws_value('software-tools_FTP_PORT'))
    user = bws_value('software-tools_FTP_USERNAME')
    pwd = bws_value('software-tools_FTP_PASSWORD')
    ctx = ssl.create_default_context()
    ctx.check_hostname = False
    ctx.verify_mode = ssl.CERT_NONE
    try:
        ftp = FTP_TLS(context=ctx)
        ftp.connect(host, port, timeout=30)
        ftp.login(user, pwd)
        ftp.prot_p()
        return ftp
    except Exception as exc:
        print('TLS fallo:', str(exc)[:70], '-> intento plano')
        ftp = FTP()
        ftp.connect(host, port, timeout=30)
        ftp.login(user, pwd)
        return ftp


def ensure_dir(ftp, path):
    cur = ''
    for part in path.split('/'):
        cur = f'{cur}/{part}' if cur else part
        try:
            ftp.mkd(cur)
        except error_perm:
            pass


def rename_if_exists(ftp, src, dst):
    try:
        ftp.rename(src, dst)
        print(f'renombrado: {src} -> {dst}')
    except error_perm:
        print(f'ok (sin cambios): {src}')


def main():
    global REMOTE
    ap = argparse.ArgumentParser()
    ap.add_argument('--root', action='store_true', help='desplegar a la raíz del dominio (Fase 7)')
    ap.add_argument('--catalog', action='store_true', help='subir catalog.sqlite (solo tras exportar ediciones vivas)')
    args = ap.parse_args()
    if args.root:
        REMOTE = ''

    results = []
    ftp = connect()

    # Fase 7: apartar placeholder y maquetas (renombrar, nunca borrar)
    if args.root:
        rename_if_exists(ftp, 'index.html', '_bak-v2-placeholder-index.html')
        rename_if_exists(ftp, 'conceptos', '_bak-conceptos-2026-10-05')

    ensure_dir(ftp, REMOTE)

    # ---- árboles de la app ----
    for local_base, remote_base in TREES:
        local_root = os.path.join(ROOT, local_base)
        for root, dirs, files in os.walk(local_root):
            dirs[:] = [d for d in dirs if d not in EXCLUDE_DIRS]
            rel = os.path.relpath(root, local_root).replace(os.sep, '/')
            rdir = REMOTE + (f'/{remote_base}' if remote_base else '')
            if rel != '.':
                rdir = f'{rdir}/{rel}'
            ensure_dir(ftp, rdir)
            for name in sorted(files):
                if name in EXCLUDE_NAMES:
                    continue
                lp = os.path.join(root, name)
                rp = f'{rdir}/{name}'
                with open(lp, 'rb') as fh:
                    ftp.storbinary(f'STOR {rp}', fh)
                results.append((rp, os.path.getsize(lp), ftp.size(rp)))

    # ---- base de datos (solo con bandera) ----
    if args.catalog:
        local_sqlite, remote_sqlite = SQLITE
        ensure_dir(ftp, f'{REMOTE}/data')
        with open(os.path.join(ROOT, local_sqlite), 'rb') as fh:
            ftp.storbinary(f'STOR {REMOTE}/{remote_sqlite}', fh)
        results.append((f'{REMOTE}/{remote_sqlite}', os.path.getsize(os.path.join(ROOT, local_sqlite)), ftp.size(f'{REMOTE}/{remote_sqlite}')))

    # ---- .htaccess de protección de data/ y downloads/ ----
    for sub in ('data', 'downloads'):
        ensure_dir(ftp, f'{REMOTE}/{sub}'.strip('/'))
        tmp = tempfile.NamedTemporaryFile('wb', suffix='.htaccess', delete=False)
        tmp.write(HTACCESS.encode('utf-8'))
        tmp.close()
        rp = f'{REMOTE}/{sub}/.htaccess'.strip('/')
        with open(tmp.name, 'rb') as fh:
            ftp.storbinary(f'STOR {rp}', fh)
        results.append((rp, os.path.getsize(tmp.name), ftp.size(rp)))
        os.unlink(tmp.name)

    # Fase 7: rewrite de URLs limpias solo en la raíz
    if args.root:
        tmp = tempfile.NamedTemporaryFile('wb', suffix='.htaccess', delete=False)
        tmp.write(REWRITE.encode('utf-8'))
        tmp.close()
        with open(tmp.name, 'rb') as fh:
            ftp.storbinary('STOR .htaccess', fh)
        results.append(('.htaccess', os.path.getsize(tmp.name), ftp.size('.htaccess')))
        os.unlink(tmp.name)

    try:
        ftp.quit()
    except Exception:
        ftp.close()

    ok = sum(1 for _, ls, rs in results if ls == rs)
    print(f'subidos OK: {ok}/{len(results)}')
    for rp, ls, rs in results:
        if ls != rs:
            print('MISMATCH', rp, 'local=', ls, 'remoto=', rs)
    print('deploy done (' + ('raíz' if REMOTE == '' else 'proto') + ')')


if __name__ == '__main__':
    main()