#!/usr/bin/env python3
"""Normaliza: garantiza newline final en las fichas Markdown del catálogo."""
import glob
import os

ROOT = os.path.dirname(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))
fixed = []
for path in sorted(glob.glob(os.path.join(ROOT, 'src/content/tools', '*.md'))):
    with open(path, 'rb') as fh:
        data = fh.read()
    if not data.endswith(b'\n'):
        with open(path, 'wb') as fh:
            fh.write(data + b'\n')
        fixed.append(os.path.basename(path))
print(f'fijadas {len(fixed)}: ', ', '.join(fixed) if fixed else '(ninguna)')