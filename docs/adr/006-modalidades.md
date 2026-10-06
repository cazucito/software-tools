# ADR-006 — Modalidades: «tres lenguajes, un archivo»

**Estado:** aceptada (2026-10-05) · **Relacionada:** [SPEC v3](../SPEC.md) · Fase 1 (maquetas en `sketches/001-direcciones-visuales/`)

## Contexto

La Fase 1 produjo tres direcciones visuales (Cinta, Línea, Máquina) con scroll narrativo. El dueño las aprobó **las tres** y pidió poder seleccionarlas al gusto (decisión registrada en el plan v3).

## Decisión

1. Las tres direcciones conviven como **modos de presentación** del mismo sitio: misma estructura, mismos datos, tres lenguajes visuales y de interacción.
2. **Selector de modo** visible y discreto; la preferencia se recuerda (`localStorage`, clave `st.modo`) y es compartible por URL (`?modo=cinta|linea|maquina`).
3. **Default: `linea`** (decidido por el dueño, 2026-10-05).
4. **Un solo contenido y un solo SEO**: canonical única, mismo HTML semántico; el modo es una capa de presentación (JS + tokens de estilo). Cada modo carga **solo su bundle** (code-splitting).
5. Los modos pueden **activarse/desactivarse desde configuración** (el dueño decide cuáles se ofrecen).
6. Alcance: la experiencia-home (timeline) es la firma de cada modo; las páginas internas comparten estructura y reciben los *tokens* del modo activo (paleta, tipografía, acabado).
7. Accesibilidad: `prefers-reduced-motion` degrada cada modo a versión estática.

## Alternativas consideradas

- **Una sola dirección**: descartada por el dueño (las quiere todas).
- **Modos como simples temas de color**: insuficiente — cada dirección es un lenguaje completo (motion + estructura), no una paleta.
- **URLs separadas indexables por modo** (`/cinta/...`, `/linea/...`): descartada por duplicación de contenido y peso SEO.

## Consecuencias

- ✔️ Máxima riqueza sin duplicar contenido; el modo es reversible y configurable.
- ⚠️ Costo de front triple en la home (tres capas de motion) → mitigación: capa común de datos/render + módulos de modo aislados y cargados bajo demanda.
- ⚠️ Los QA de UI deben cubrir los tres modos × dos idiomas (matriz de pruebas en SPEC §12).
