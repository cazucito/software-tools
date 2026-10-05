#!/usr/bin/env python3
"""Genera data.js (datos reales del catálogo) para las maquetas de concepto v3.

Uso (desde la raíz del repo):
    python3 sketches/001-direcciones-visuales/tools/prep_data.py
"""
import json
from datetime import date

SRC = "src/data/tools.json"
OUT = "sketches/001-direcciones-visuales/data.js"

d = json.load(open(SRC, encoding="utf-8"))
# Excluir el placeholder de la plantilla (aparece como herramienta publicada en el build)
PLACEHOLDER_SLUGS = {"nombre-de-la-herramienta", "_template"}
tools = [t for t in d["tools"] if t.get("published") is not False and t["slug"] not in PLACEHOLDER_SLUGS]
tools.sort(key=lambda t: (t["year"] or 0, t["name"]))

years = [t["year"] for t in tools if t.get("year")]
stats = {
    "count": len(tools),
    "minYear": min(years),
    "maxYear": max(years),
    "span": max(years) - min(years),
    "today": date.today().year,
    "usedUntilMax": max([t["usedUntil"] for t in tools if t.get("usedUntil")] or [0]),
    "categories": sorted(set(t["category"] for t in tools)),
    "tagsCount": len(set(tag for t in tools for tag in (t.get("tags") or []))),
}

payload = {
    "stats": stats,
    "tools": [
        {
            "name": t["name"], "slug": t["slug"], "year": t["year"],
            "usedUntil": t.get("usedUntil"), "category": t["category"],
            "tags": t.get("tags") or [], "context": t.get("context") or "",
            "successor": t.get("successor"),
        }
        for t in tools
    ],
}

js = "// software-tools v3 — maquetas de concepto: datos REALES del catálogo\n"
js += f"// Fuente: src/data/tools.json ({date.today().isoformat()}) — generado por tools/prep_data.py, no editar a mano\n"
js += "window.ST_DATA = " + json.dumps(payload, ensure_ascii=False, indent=1) + ";\n"
with open(OUT, "w", encoding="utf-8") as fh:
    fh.write(js)

print("STATS:", json.dumps(stats, ensure_ascii=False))
print(f"OK → {OUT} ({stats['count']} herramientas)")
