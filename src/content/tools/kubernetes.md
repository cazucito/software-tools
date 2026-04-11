---
name: "Kubernetes"
slug: "kubernetes"
year: 2015
category: "infra"
tags: ["kubernetes", "k8s", "containers", "orchestration", "devops"]
context: "El estándar de orquestación de contenedores. Cuando Docker resolvió el empaquetado, Kubernetes resolvió el despliegue a escala. Cambió cómo pienso la infraestructura."
usedUntil: null
successor: null
successorSlug: null
related: ["docker", "vmware-workstation"]
published: true
---

Kubernetes (K8s) llegó en 2015 y se convirtió en el estándar para orquestar contenedores a escala. Si Docker resolvió el "build", Kubernetes resolvió el "run at scale".

## El problema: escalar contenedores

Docker maneja contenedores individuales bien. Pero cuando necesitas:

- Correr 100 instancias de tu app
- Distribuirlas en 10 servidores
- Balancear tráfico entre ellas
- Recuperarte automáticamente si fallan
- Actualizar sin downtime

Necesitas orquestación.

## Origen Google

Kubernetes nació de Borg, el sistema interno de Google para manejar miles de contenedores. Google lo liberó como open source y donó a la Cloud Native Computing Foundation (CNCF).

## Conceptos fundamentales

- **Pods**: La unidad mínima desplegable (uno o más contenedores)
- **Services**: Exponer pods como servicios de red
- **Deployments**: Gestionar réplicas y actualizaciones
- **ConfigMaps/Secrets**: Configuración separada del código
- **Ingress**: Enrutamiento HTTP desde fuera

## Mi workflow con K8s

```yaml
# deployment.yaml
apiVersion: apps/v1
kind: Deployment
metadata:
  name: mi-app
spec:
  replicas: 3
  selector:
    matchLabels:
      app: mi-app
  template:
    metadata:
      labels:
        app: mi-app
    spec:
      containers:
      - name: mi-app
        image: mi-registry/mi-app:latest
        ports:
        - containerPort: 3000
```

```bash
kubectl apply -f deployment.yaml
kubectl get pods
kubectl logs -f deployment/mi-app
```

## La curva de aprendizaje

Kubernetes tiene fama de complejo. Y lo es. Conceptos como:

- Service meshes
- Ingress controllers
- StatefulSets vs Deployments
- Persistent Volumes
- RBAC y seguridad

Requieren tiempo para dominar.

## Cuándo usar K8s (y cuándo no)

**Usar K8s cuando**:
- Múltiples servicios que interactúan
- Equipo de DevOps dedicado
- Necesitas alta disponibilidad
- Escalas horizontalmente

**No usar K8s cuando**:
- Una sola app simple
- Equipo pequeño sin experiencia
- Prefieres PaaS (Heroku, Railway)

## Contexto personal

Empecé con Kubernetes en 2017 y lo uso para proyectos que requieren escala o alta disponibilidad. Para proyectos pequeños, prefiero Docker Compose o PaaS.

K8s representa la complejidad necesaria para operar software a escala moderna: poderoso pero con curva de aprendizaje pronunciada.