# ops/deploy/ — despliegue al hosting

`deploy.py` (Fase 7): sube `app/public/` (+ `app/data/catalog.sqlite`) por FTP al docroot de `software-tools.pcabrera.com`.

Reglas (patrón del ecosistema):

- Credenciales **solo desde BWS** en runtime (`software-tools_FTP_*`); nunca en argv ni en el repo.
- **Solo complementa, nunca borra** (sin `--delete` en mirrors).
- Verificación post-subida por tamaño/sha; el estado real se confirma por FTP.
- Subidas grandes (descargas) por FTP vía agente; el admin usa chunking para ≤2 MB ([ADR-007](../../docs/adr/007-descargas.md)).
