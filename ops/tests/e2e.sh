#!/usr/bin/env bash
# ============================================================================
# e2e de la Fase 5 — comentarios, admin, descargas con clave, export.
# Uso:   E2E_ADMIN_PASS=<pass> bash ops/tests/e2e.sh [http://127.0.0.1:8091]
# Corre contra el prototipo local (php -S). Necesita: curl, python3.
# ============================================================================
set -u
BASE="${1:-http://127.0.0.1:8091}"
ROOT="$(cd "$(dirname "$0")/../.." && pwd)"
JAR=$(mktemp); JAR2=$(mktemp); TMP=$(mktemp -d)
PASS_SHA=$(printf '%s' e2e-prueba | sha256sum | cut -c1-8)
PASS="${E2E_ADMIN_PASS:-dev-admin-2026}"

PASSED=0; FAILED=0
ok()   { PASSED=$((PASSED+1)); echo "  ok   – $1"; }
fail() { FAILED=$((FAILED+1)); echo "  FAIL – $1"; }
assert_eq() { # label expected actual
  if [ "$2" = "$3" ]; then ok "$1"; else fail "$1 (esperado «$2», obtenido «$3»)"; fi
}
assert_contains() { # label needle haystack
  if printf '%s' "$3" | grep -q -- "$2"; then ok "$1"; else fail "$1 (no contiene «$2»)"; fi
}
grab() { printf '%s' "$2" | grep -oE "$1" | head -1; }
H() { curl -s -o "$TMP/body.html" -w '%{http_code}' "$@"; }

echo "== 1. Páginas base =="
code=$(H "$BASE/index.php");                assert_eq "home 200" 200 "$code"
code=$(H "$BASE/index.php?modo=cinta");     assert_eq "home cinta 200" 200 "$code"
code=$(H "$BASE/index.php?p=tools");        assert_eq "catálogo 200" 200 "$code"
code=$(H "$BASE/index.php?p=search&q=contenedores"); assert_eq "búsqueda 200" 200 "$code"
code=$(H "$BASE/index.php?p=admin/login");  assert_eq "admin login 200" 200 "$code"
body=$(curl -s "$BASE/index.php")
assert_contains "home con footer i18n (fase 5)" 'fase 5' "$body"
assert_contains "home con canonical" 'rel="canonical"' "$body"
assert_contains "home con JSON-LD WebSite" 'application/ld+json' "$body"
sitemap=$(curl -s "$BASE/index.php?p=sitemap.xml")
assert_contains "sitemap con 48 loc" '<loc>' "$sitemap"
nloc=$(printf '%s' "$sitemap" | grep -c '<loc>')
assert_eq "sitemap 52 urls (home+tools+search+49)" 52 "$nloc"

echo "== 2. Ficha (monograma, comentarios, JSON-LD) =="
code=$(H "$BASE/index.php?p=tools/eudora&modo=linea"); assert_eq "ficha 200" 200 "$code"
body=$(cat "$TMP/body.html")
assert_contains "ficha con monograma (fallback st-art)" 'st-art--mono' "$body"
assert_contains "ficha con formulario de comentarios" 'st-cform' "$body"
assert_contains "ficha con honeypot" 'name="website"' "$body"
assert_contains "ficha con JSON-LD SoftwareApplication" 'SoftwareApplication' "$body"
assert_contains "ficha con og:type" 'og:type' "$body"

echo "== 3. Comentarios =="
ficha=$(curl -s "$BASE/index.php?p=tools/eudora")
T=$(grab 'name="t" value="[0-9]+"' "$ficha" | grep -oE '[0-9]+')
TSIG=$(grab 'name="tsig" value="[0-9a-f]{64}"' "$ficha" | grep -oE '[0-9a-f]{64}')
# envío inmediato (<3s) → too_fast
loc=$(curl -s -o /dev/null -w '%{redirect_url}' -X POST "$BASE/index.php?p=comment" \
      -d "slug=eudora" -d "author=BotRápido" -d "body=prueba de freno" -d "t=$T" -d "tsig=$TSIG" -d "website=")
assert_contains "time-trap: envío instantáneo rechazado" 'c=too_fast' "$loc"
# honeypot relleno → generic
loc=$(curl -s -o /dev/null -w '%{redirect_url}' -X POST "$BASE/index.php?p=comment" \
      -d "slug=eudora" -d "author=X" -d "body=spam puro" -d "website=http://spam.example" -d "t=$T" -d "tsig=$TSIG")
assert_contains "honeypot: campo website relleno rechazado" 'c=generic' "$loc"
# envío válido (tras esperar la ventana)
sleep 4
loc=$(curl -s -o /dev/null -w '%{redirect_url}' -X POST "$BASE/index.php?p=comment" \
      -d "slug=eudora" -d "author=Tester E2E" -d "body=Comentario de prueba de la Fase 5." -d "t=$T" -d "tsig=$TSIG" -d "website=")
assert_contains "comentario válido publicado" 'c=ok' "$loc"
ficha=$(curl -s "$BASE/index.php?p=tools/eudora")
assert_contains "comentario visible en la ficha" 'Tester E2E' "$ficha"
assert_contains "comentario visible: texto" 'Comentario de prueba de la Fase 5.' "$ficha"
n=$(python3 -c "
import sqlite3
c=sqlite3.connect('$ROOT/app/data/ops.sqlite')
print(c.execute(\"SELECT COUNT(*) FROM comments WHERE tool_slug='eudora' AND status='approved' AND author='Tester E2E'\").fetchone()[0])")
assert_eq "1 comentario en ops.sqlite (aprobado)" 1 "$n"

echo "== 4. Admin: login =="
login=$(curl -s -c "$JAR" "$BASE/index.php?p=admin/login")
CSRF=$(grab 'name="csrf" value="[0-9a-f]{32}"' "$login" | grep -oE '[0-9a-f]{32}')
loc=$(curl -s -c "$JAR" -b "$JAR" -o /dev/null -w '%{redirect_url}' -X POST "$BASE/index.php?p=admin/login" -d "csrf=$CSRF" -d "password=clave-incorrecta")
res=$(curl -s -b "$JAR" -X POST "$BASE/index.php?p=admin/login" -d "csrf=$CSRF" -d "password=clave-incorrecta")
assert_contains "login con clave mala → error visible" 'Contraseña incorrecta' "$res"
loc=$(curl -s -c "$JAR" -b "$JAR" -o /dev/null -w '%{redirect_url}' -X POST "$BASE/index.php?p=admin/login" -d "csrf=$CSRF" -d "password=$PASS")
assert_contains "login correcto → admin/home" 'admin/home' "$loc"
code=$(H -b "$JAR" "$BASE/index.php?p=admin/home"); assert_eq "dashboard 200" 200 "$code"
assert_contains "dashboard con tarjetas" 'Comentarios' "$(cat "$TMP/body.html")"
# sin sesión no se entra
code=$(H "$BASE/index.php?p=admin/comments")
assert_eq "sin sesión → redirect login" 302 "$code"

echo "== 5. Admin: descargas (clave + archivo + streamer) =="
manage=$(curl -s -b "$JAR" "$BASE/index.php?p=admin/downloads/eudora")
CSRF=$(grab 'name="csrf" value="[0-9a-f]{32}"' "$manage" | grep -oE '[0-9a-f]{32}')
loc=$(curl -s -b "$JAR" -o /dev/null -w '%{redirect_url}' -X POST "$BASE/index.php?p=admin/downloads/eudora" \
      -d "csrf=$CSRF" -d "action=set_dl_pass" -d "dl_pass=e2e-clave")
assert_contains "contraseña general definida → vuelve a gestión" 'admin/downloads/eudora' "$loc"
assert_contains "flash ok contraseña" 'actualizada' "$(curl -s -b "$JAR" "$BASE/index.php?p=admin/downloads/eudora")"
# subida directa
printf 'prueba-e2e-fase5\n' > "$TMP/e2e-prueba.txt"
loc=$(curl -s -b "$JAR" -o /dev/null -w '%{redirect_url}' -X POST "$BASE/index.php?p=admin/downloads/eudora" \
      -F "csrf=$CSRF" -F "action=upload" -F "visibility=clave" -F "license=prueba" -F "source_url=" -F "version=10.0" -F "variant=IDE" -F "file=@$TMP/e2e-prueba.txt")
assert_contains "archivo subido y registrado → vuelve a gestión" 'admin/downloads/eudora' "$loc"
n=$(python3 -c "
import sqlite3
c=sqlite3.connect('$ROOT/app/data/ops.sqlite')
print(c.execute(\"SELECT COUNT(*) FROM downloads WHERE tool_slug='eudora' AND filename='e2e-prueba.txt'\").fetchone()[0])")
assert_eq "fila de descarga en ops.sqlite" 1 "$n"
# ficha bloqueada: form visible, SOLO chips, lista oculta
ficha=$(curl -s "$BASE/index.php?p=tools/eudora")
assert_contains "ficha con sección descargas" 'st-dlkey' "$ficha"
assert_eq "ficha bloqueada: sin grupos de versión" 0 "$(printf '%s' "$ficha" | grep -c 'st-dlgroup__title')"
assert_eq "ficha bloqueada: sin nombre de archivo" 0 "$(printf '%s' "$ficha" | grep -c 'e2e-prueba.txt')"
# evitar el streamer sin cookie
code=$(H "$BASE/index.php?p=download/eudora/e2e-prueba.txt"); assert_eq "stream sin acceso → 403" 403 "$code"
# contraseña incorrecta (general)
loc=$(curl -s -b "$JAR2" -o /dev/null -w '%{redirect_url}' -X POST "$BASE/index.php?p=download/eudora" -d "dl_pass=clave-mala")
assert_contains "desbloqueo con contraseña mala → d=wrong" 'd=wrong' "$loc"
# contraseña correcta (general, una para todo el sitio)
loc=$(curl -s -c "$JAR2" -b "$JAR2" -o /dev/null -w '%{redirect_url}' -X POST "$BASE/index.php?p=download/eudora" -d "dl_pass=e2e-clave")
assert_contains "desbloqueo correcto → d=ok" 'd=ok' "$loc"
code=$(H -b "$JAR2" "$BASE/index.php?p=download/eudora/e2e-prueba.txt")
assert_eq "stream con cookie → 200" 200 "$code"
assert_eq "contenido del archivo íntegro" "prueba-e2e-fase5" "$(cat "$TMP/body.html")"
# sha256 visible coincide con disco
sha=$(sha256sum "$TMP/e2e-prueba.txt" | cut -c1-12)
ficha=$(curl -s -b "$JAR2" "$BASE/index.php?p=tools/eudora")
assert_contains "ficha desbloqueada: grupos por versión" 'st-dlgroup__title' "$ficha"
assert_contains "ficha desbloqueada: nombre de archivo" 'e2e-prueba.txt' "$ficha"
assert_contains "sha256 mostrado en ficha" "$sha" "$ficha"
# ---- alojado (normalizado a clave) → sigue requiriendo contraseña general ----
loc=$(curl -s -b "$JAR" -o /dev/null -w '%{redirect_url}' -X POST "$BASE/index.php?p=admin/downloads/eudora" \
      -d "csrf=$CSRF" -d "action=update_file" -d "id=$(python3 -c "
import sqlite3
print(sqlite3.connect('$ROOT/app/data/ops.sqlite').execute(\"SELECT id FROM downloads WHERE tool_slug='eudora' AND filename='e2e-prueba.txt'\").fetchone()[0])")" -d "visibility=publico" -d "license=prueba" -d "source_url=")
code=$(H "$BASE/index.php?p=download/eudora/e2e-prueba.txt")
assert_eq "stream alojado sin cookie → 403 (aunque se pida publico)" 403 "$code"
# borrado (registro + archivo)
loc=$(curl -s -b "$JAR" -o /dev/null -w '%{redirect_url}' -X POST "$BASE/index.php?p=admin/downloads/eudora" \
      -d "csrf=$CSRF" -d "action=delete_file" -d "id=$(python3 -c "
import sqlite3
print(sqlite3.connect('$ROOT/app/data/ops.sqlite').execute(\"SELECT id FROM downloads WHERE tool_slug='eudora'\").fetchone()[0])")" -d "with_file=1")
assert_contains "borrado → vuelve a gestión" 'admin/downloads/eudora' "$loc"
n=$(python3 -c "
import sqlite3
print(sqlite3.connect('$ROOT/app/data/ops.sqlite').execute(\"SELECT COUNT(*) FROM downloads WHERE tool_slug='eudora'\").fetchone()[0])")
assert_eq "registro borrado" 0 "$n"
[ -f "$ROOT/app/public/downloads/eudora/e2e-prueba.txt" ] && fail "archivo sigue en disco" || ok "archivo borrado de disco"

# ---- estándar de subcarpetas: filename = ruta relativa segura ----
docker run --rm -v "$ROOT":/srv php:8.1-cli sh -c 'mkdir -p /srv/app/public/downloads/eudora/sub-plugins && printf plug-e2e > /srv/app/public/downloads/eudora/sub-plugins/e2e-plug.txt' >/dev/null 2>&1
apage=$(curl -s -b "$JAR" "$BASE/index.php?p=admin/downloads/eudora")
csrf=$(echo "$apage" | grep -o 'name="csrf" value="[0-9a-f]\{32\}"' | head -1 | grep -o '[0-9a-f]\{32\}')
loc=$(curl -s -b "$JAR" -o /dev/null -w '%{redirect_url}' -X POST "$BASE/index.php?p=admin/downloads/eudora" \
  --data-urlencode "csrf=$csrf" --data-urlencode 'action=register' \
  --data-urlencode 'filename=sub-plugins/e2e-plug.txt' --data-urlencode 'variant=Subcarpeta (estándar)' \
  --data-urlencode 'version=10.1' --data-urlencode 'year=2025' --data-urlencode 'visibility=clave')
assert_contains "registro con subruta aceptado" 'admin/downloads/eudora' "$loc"
ficha=$(curl -s -b "$JAR2" "$BASE/index.php?p=tools/eudora")
assert_contains "ficha desbloqueada muestra subruta" 'sub-plugins/e2e-plug.txt' "$ficha"
curl -s -b "$JAR2" "$BASE/index.php?p=download/eudora/sub-plugins/e2e-plug.txt" -o "$TMP/sub.bin"
assert_eq "stream con subcarpeta devuelve bytes" 8 "$(wc -c < "$TMP/sub.bin")"
# traversal: nunca se sale de downloads/<slug>/
code=$(H "$BASE/index.php?p=download/eudora/..%2F..%2Fetc%2Fpasswd")
assert_eq "traversal (encoded) bloqueado → 404" 404 "$code"
code=$(H "$BASE/index.php?p=download/eudora/../../etc/passwd")
assert_eq "traversal (literal) bloqueado → 404" 404 "$code"
# limpieza del fixture de subcarpeta
docker run --rm -v "$ROOT":/srv php:8.1-cli sh -c 'rm -f /srv/app/public/downloads/eudora/sub-plugins/e2e-plug.txt && rmdir /srv/app/public/downloads/eudora/sub-plugins' >/dev/null 2>&1 || true

echo "== 6. Admin: catálogo (toggle + edición + dirty) =="
code=$(H -b "$JAR" "$BASE/index.php?p=admin/catalog"); assert_eq "catálogo admin 200" 200 "$code"
csrf_cat=$(grab 'name="csrf" value="[0-9a-f]{32}"' "$(cat "$TMP/body.html")" | grep -oE '[0-9a-f]{32}')
loc=$(curl -s -b "$JAR" -o /dev/null -w '%{redirect_url}' -X POST "$BASE/index.php?p=admin/catalog" -d "csrf=$csrf_cat" -d "action=toggle" -d "slug=eudora")
code=$(H "$BASE/index.php?p=tools/eudora"); assert_eq "ficha oculta tras toggle → 404" 404 "$code"
loc=$(curl -s -b "$JAR" -o /dev/null -w '%{redirect_url}' -X POST "$BASE/index.php?p=admin/catalog" -d "csrf=$csrf_cat" -d "action=toggle" -d "slug=eudora")
code=$(H "$BASE/index.php?p=tools/eudora"); assert_eq "ficha visible tras toggle → 200" 200 "$code"
# guardado (no-op semántico) marca dirty
EDIT=$(curl -s -b "$JAR" "$BASE/index.php?p=admin/catalog/eudora")
csrf_e=$(grab 'name="csrf" value="[0-9a-f]{32}"' "$EDIT" | grep -oE '[0-9a-f]{32}')
PYCSRF="$csrf_e" python3 - "$ROOT" > "$TMP/save-body.txt" <<'PY'
import sqlite3, sys, urllib.parse, os
root, slug = sys.argv[1], 'eudora'
c = sqlite3.connect(f'{root}/app/data/catalog.sqlite')
r = c.execute("SELECT name,year,used_until,category,context,body,image,successor,successor_slug,published FROM tools WHERE slug=?", (slug,)).fetchone()
keys = ['name','year','used_until','category','context','body','image','successor','successor_slug','published']
d = dict(zip(keys, r))
d['action'] = 'save'; d['csrf'] = os.environ['PYCSRF']
d['tags'] = ', '.join(x[0] for x in c.execute(
    "SELECT g.name FROM tool_tags tt JOIN tags g ON g.id=tt.tag_id JOIN tools t ON t.id=tt.tool_id WHERE t.slug=?", (slug,)))
d['related'] = ', '.join(x[0] for x in c.execute(
    "SELECT related_slug FROM tool_relations r JOIN tools t ON t.id=r.tool_id WHERE t.slug=?", (slug,)))
print(urllib.parse.urlencode({k: ('' if v is None else str(v)) for k, v in d.items()}))
PY
loc=$(curl -s -b "$JAR" -o /dev/null -w '%{redirect_url}' -X POST -H "Content-Type: application/x-www-form-urlencoded" --data-binary @"$TMP/save-body.txt" "$BASE/index.php?p=admin/catalog/eudora")
assert_contains "guardado de ficha ok → vuelve a edición" 'admin/catalog/eudora' "$loc"
n=$(python3 -c "
import sqlite3
c=sqlite3.connect('$ROOT/app/data/ops.sqlite')
print(c.execute(\"SELECT COUNT(*) FROM catalog_dirty WHERE tool_slug='eudora'\").fetchone()[0])")
assert_eq "eudora marcada como pendiente de export" 1 "$n"

echo "== 7. Export a Markdown (round-trip) =="
# Regenera el catálogo desde las fuentes: el test de edición pudo reordenar tags.
(cd "$ROOT" && python3 ops/tools/import_catalog.py) >/dev/null 2>&1 || true
curl -s -b "$JAR" -o "$TMP/export-figlet.md" "$BASE/index.php?p=admin/export/figlet"
if diff -u "$ROOT/src/content/tools/figlet.md" "$TMP/export-figlet.md" > "$TMP/diff.txt" 2>&1; then
  ok "round-trip figlet: byte a byte idéntico"
else
  fail "round-trip figlet difiere"; head -20 "$TMP/diff.txt"
fi
curl -s -b "$JAR" -o "$TMP/export-eudora.md" "$BASE/index.php?p=admin/export/eudora"
if diff -u "$ROOT/src/content/tools/eudora.md" "$TMP/export-eudora.md" > /dev/null 2>&1; then
  ok "round-trip eudora: byte a byte idéntico"
else
  fail "round-trip eudora difiere"
fi
bundle=$(curl -s -b "$JAR" "$BASE/index.php?p=admin/export&bundle=1")
nb=$(printf '%s' "$bundle" | grep -c '^slug:')
assert_eq "bundle contiene 49 fichas" 49 "$nb"
exportpage=$(curl -s -b "$JAR" "$BASE/index.php?p=admin/export")
assert_contains "pendiente visible en Export" 'eudora' "$exportpage"
csrf_x=$(grab 'name="csrf" value="[0-9a-f]{32}"' "$exportpage" | grep -oE '[0-9a-f]{32}')
loc=$(curl -s -b "$JAR" -o /dev/null -w '%{redirect_url}' -X POST "$BASE/index.php?p=admin/export" -d "csrf=$csrf_x" -d "action=mark_exported")
n=$(python3 -c "
import sqlite3
print(sqlite3.connect('$ROOT/app/data/ops.sqlite').execute('SELECT COUNT(*) FROM catalog_dirty').fetchone()[0])")
assert_eq "pendientes limpiados tras marcar exportado" 0 "$n"

echo "== 7b. Escala (ADR-010): hero, filtros, arte =="
home=$(curl -s "$BASE/")
assert_contains "home: buscador hero presente" 'st-hero-search' "$home"
catp=$(curl -s "$BASE/index.php?p=tools")
assert_contains "catálogo: barra de filtros" 'st-filters' "$catp"
catd=$(curl -s "$BASE/index.php?p=tools&decada=1990")
assert_contains "catálogo: filtro por década aplicado" '1990s' "$catd"
ficha=$(curl -s "$BASE/index.php?p=tools/eudora")
assert_contains "ficha: arte de década (monograma era)" 'st-era-1990' "$ficha"

echo "== 8. Logout =="
loc=$(curl -s -b "$JAR" -o /dev/null -w '%{redirect_url}' -X POST "$BASE/index.php?p=admin/logout" -d "csrf=$csrf_x")
code=$(H -b "$JAR" "$BASE/index.php?p=admin/home"); assert_eq "tras logout → redirect login" 302 "$code"

echo
echo "RESULTADO: $PASSED ok, $FAILED fallos"
rm -f "$JAR" "$JAR2"; rm -rf "$TMP"
[ "$FAILED" = "0" ]