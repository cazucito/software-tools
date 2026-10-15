# MEMORY — lecciones del repo

- **El deploy v2 a Pages está RETIRADO** (workflow disabled + Pages retirado por el dueño, 2026-10-15): `main` ya no publica nada; la producción es el deploy FTP de la v3.
- **La paridad es la regla dura**: tras tocar fichas, correr import + verify (exit 0). El reporte en `ops/reports/` es la prueba de que nada se perdió.
- **Nombres de descarga**: solo `[A-Za-z0-9._-]` — comas/paréntesis mueren en `safeRel`; los espacios (%20) los bloquea el WAF del hosting (403 HTML del proveedor vs `403` plano de la app). El nombre físico y el registrado deben coincidir: renombrar antes, no después.
- **E2E login dev**: el hash de `app/server/config.local.php` debe validar `dev-admin-2026` (default de `ops/tests/e2e.sh`); si no → 28 fallos en cascada desde «login correcto». Alinear el hash y reiniciar st-proto.
- **Rate bucket `dlkey`** (unlock global): 5/600 s por ip; las pruebas del streamer comparten cubeta → un unlock y muchas lecturas.
- **Catálogo vivo**: subir SIEMPRE a `data/catalog.sqlite.uploading` + rename; `deploy.py --catalog` sube directo (truncado si se corta a mitad).
- **Slugs = enlaces**: son estables; renombrar uno rompe enlaces del sitio en vivo y de las referencias cruzadas.
- **Años de las fichas**: reflejan cuándo el dueño empezó a usar la herramienta (no la fecha de lanzamiento del software).
- **Fechas clave del dueño** (anclas históricas): Eudora→1998 Outlook · Lotus→1996 Excel · Netscape→1998 IE · 95→1998 Win98 · Turbo C/BP7→1996 Delphi/Borland C++ · VB6→2002 VB.NET · NC→1998 Explorer · Ghost→2005 Acronis · SQL Developer→2018 DataGrip · EW→2000 Multisim · Bannermania→1996 FIGlet · Chi Writer→1998 LaTeX.
- **`maple-6`, `mathematica-3`, `matlab-r14`, `quickbasic-4-5`** son las 4 referencias de «versión siguiente» (no fichas): viven en `next_version`, no en `successor_slug` resuelto.
- **Hosting frugal**: PHP limita subidas a 2 MB (usar chunking de ~1.5 MB estilo kofro); nada de procesos largos (30 s de ejecución).
- **Redacción con cuidado**: los cuerpos de las fichas llevan voz personal del dueño; en borradores, no inventar intimidades — derivar de lo ya escrito y marcar lo dudoso para su revisión.
