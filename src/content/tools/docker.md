---
name: "Docker"
slug: "docker"
year: 2013
category: "infra"
tags: ["docker", "containers", "devops", "microservices", "virtualization"]
context: "La revolución de los contenedores. Empaquetar aplicaciones con todas sus dependencias y desplegarlas en cualquier lado. Cambió mi forma de desarrollar y desplegar software."
usedUntil: null
successor: null
successorSlug: null
related: ["vmware-workstation", "kubernetes"]
published: true
---

Docker llegó en 2013 y revolucionó cómo desarrollo y despliego software. La promesa era simple: "Build once, run anywhere" - y por primera vez, era real.

## El problema anterior

Antes de Docker, desplegar aplicaciones era un dolor:

- "Funciona en mi máquina"
- Dependencias conflictivas entre proyectos
- Instrucciones de instalación de páginas
- Entornos de desarrollo vs producción diferentes

## La solución: contenedores

Docker popularizó los contenedores Linux:

- **Aislados**: Cada app en su propio ambiente
- **Ligeros**: Comparten el kernel del host
- **Portables**: Mismo contenedor en dev, staging, prod
- **Versionables**: Dockerfiles como código

```dockerfile
FROM node:14
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
EXPOSE 3000
CMD ["node", "server.js"]
```

## Mi workflow con Docker

Desarrollo local:
```bash
docker-compose up  # Levantar app + DB + cache
```

Testing:
```bash
docker run -v $(pwd):/app myapp:test
```

Producción:
```bash
docker pull myregistry/myapp:latest
docker run -d -p 80:3000 myapp
```

## El ecosistema

Docker creó un ecosistema completo:

- **Docker Hub**: Registro de imágenes públicas
- **Docker Compose**: Múltiples contenedores
- **Docker Swarm**: Orquestación (antes de Kubernetes)
- **Dockerfile**: Infraestructura como código

## Impacto en mi trabajo

Docker cambió mi día a día:

- **Onboarding**: Nuevos desarrolladores productivos en minutos, no días
- **Consistencia**: Dev y producción idénticos
- **Escalabilidad**: Escalar horizontalmente se volvió trivial
- **Microservicios**: Arquitectura de microservicios viable

## Contexto personal

Empecé a usar Docker en 2014 y sigo usándolo hoy. Ha pasado de ser "nueva tecnología cool" a "estándar de la industria".

La transición de VMs a contenedores fue similar a la transición de bare metal a VMs: más eficiencia, más flexibilidad.

Docker representa la madurez del DevOps: infraestructura como código, inmutabilidad, y despliegues predecibles.