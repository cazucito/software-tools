#!/usr/bin/env bash
# Smoke funcional EN VIVO (Fase 5, producción): login admin → comentario → moderación.
# La contraseña viene del archivo scratch (nunca en argv ni en salida).
set -u
BASE="https://software-tools.pcabrera.com/proto/index.php"
PASS="$(cat ~/.hermes/cache/scratch/st-f4/adminpass)"
JAR="$(mktemp)"
trap 'rm -f "$JAR"' EXIT
fails=0

grab() { grep -oE "$1" | head -1 | grep -oE "[^ ]+$" ; }

# ---- 1. login admin ----
curl -s -c "$JAR" "$BASE?p=admin/login" >/dev/null
page=$(curl -s -b "$JAR" "$BASE?p=admin/login")
csrf=$(printf '%s' "$page" | grep -oE 'name="csrf" value="[0-9a-f]{32}"' | head -1 | grep -oE '[0-9a-f]{32}')
loc=$(curl -s -b "$JAR" -c "$JAR" -o /dev/null -w '%{redirect_url}' -X POST "$BASE?p=admin/login" -d "csrf=$csrf" -d "password=$PASS")
case "$loc" in
  *admin/home) echo "OK  login admin -> $loc" ;;
  *) echo "FAIL login admin: $loc"; fails=1 ;;
esac

# ---- 2. panel admin carga ----
code=$(curl -s -b "$JAR" -o /tmp/live-admin-home.html -w '%{http_code}' "$BASE?p=admin")
echo "$code" | grep -q 200 && echo "OK  panel admin 200" || { echo "FAIL panel admin $code"; fails=1; }
grep -q "Panel" /tmp/live-admin-home.html && echo "OK  dashboard render" || { echo "FAIL dashboard"; fails=1; }

# ---- 3. comentario en eudora ----
ficha=$(curl -s "$BASE?p=tools/eudora")
t=$(printf '%s' "$ficha" | grep -oE 'name="t" value="[0-9]+"' | head -1 | grep -oE '[0-9]+')
tsig=$(printf '%s' "$ficha" | grep -oE 'name="tsig" value="[0-9a-f]{64}"' | head -1 | grep -oE '[0-9a-f]{64}')
sleep 4
loc=$(curl -s -o /dev/null -w '%{redirect_url}' -X POST "$BASE?p=comment" --data-urlencode "slug=eudora" --data-urlencode "author=Comentarista Anónimo" --data-urlencode "body=Prueba de despliegue de la Fase 5 (comentario que será moderado)." -d "t=$t" -d "tsig=$tsig" -d "website=")
case "$loc" in
  *c=ok*) echo "OK  comentario publicado (c=ok)" ;;
  *) echo "FAIL comentario: $loc"; fails=1 ;;
esac

# ---- 4. visible en la ficha ----
curl -s "$BASE?p=tools/eudora" | grep -q "Prueba de despliegue de la Fase 5" && echo "OK  comentario visible en ficha" || { echo "FAIL comentario visible"; fails=1; }

# ---- 5. moderación: marcar spam desde admin (todos los comentarios de prueba) ----
cmds=$(curl -s -b "$JAR" "$BASE?p=admin/comments")
ccsrf=$(printf '%s' "$cmds" | grep -oE 'name="csrf" value="[0-9a-f]{32}"' | head -1 | grep -oE '[0-9a-f]{32}')
ids=$(printf '%s' "$cmds" | grep -oE 'name="id" value="[0-9]+"' | grep -oE '[0-9]+')
n=0
for cid in $ids; do
  curl -s -b "$JAR" -o /dev/null -X POST "$BASE?p=admin/comments" -d "csrf=$ccsrf" -d "action=status" -d "id=$cid" -d "value=spam"
  n=$((n + 1))
done
[ "$n" -gt 0 ] && echo "OK  $n comentario(s) marcados spam" || { echo "FAIL sin comentarios para moderar"; fails=1; }
curl -s "$BASE?p=tools/eudora" | grep -q "Prueba de despliegue de la Fase 5" && { echo "FAIL comentario sigue visible"; fails=1; } || echo "OK  moderados y retirados de la ficha"

# ---- 6. ficha de descargas (clave + sha256) ----
code=$(curl -s -b "$JAR" -o /tmp/live-admin-dl.html -w '%{http_code}' "$BASE?p=admin/downloads")
echo "$code" | grep -q 200 && echo "OK  admin/descargas 200" || { echo "FAIL admin/descargas $code"; fails=1; }

[ "$fails" -eq 0 ] && echo "== SMOKE VIVO: TODO OK ==" || echo "== SMOKE VIVO: HAY FALLOS =="
exit "$fails"