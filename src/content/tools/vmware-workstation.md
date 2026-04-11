---
name: "VMware Workstation"
slug: "vmware-workstation"
year: 1999
category: "infra"
tags: ["virtualization", "vmware", "virtual-machine", "x86", "testing"]
context: "La revolución de la virtualización x86. Correr Windows dentro de Windows, o Linux dentro de Windows, sin reiniciar. Cambió mi forma de probar software y sistemas operativos."
usedUntil: 2015
successor: "VMware Workstation Pro / VirtualBox"
successorSlug: "virtualbox"
related: ["windows-95", "norton-ghost"]
published: true
---

VMware Workstation fue mi primera experiencia seria con virtualización. En 1999, la idea de correr un sistema operativo completo dentro de una ventana parecía ciencia ficción.

## La revolución x86

Antes de VMware, probar otro sistema operativo requería:

1. Particionar el disco duro
2. Instalar el segundo SO en otra partición
3. Configurar un bootloader dual
4. Reiniciar para cambiar entre sistemas

VMware cambió eso: múltiples sistemas operativos corriendo simultáneamente en ventanas.

## Casos de uso

Usé VMware para:

**Testing**: Probar software en Windows 98, 2000, XP sin máquinas físicas

**Desarrollo**: Entornos de desarrollo aislados que podía destruir y recrear

**Seguridad**: Analizar malware en VMs desechables

**Capacitación**: Crear labs con múltiples máquinas virtuales en una sola PC

**Legacy**: Correr aplicaciones DOS que no funcionaban en Windows XP+

## Las snapshots

La característica más valiosa: snapshots.

- Tomabas una "foto" del estado de la VM
- Hacías cambios, instalabas software, probabas
- Si algo fallaba, restaurabas el snapshot
- Era como Norton Ghost pero instantáneo

## El overhead

Las VMs tenían costo:

- **RAM**: Cada VM necesitaba su propia memoria
- **Disco**: Archivos de varios GB por VM
- **CPU**: Emulación tenía penalty de performance

En 1999, con 256MB de RAM, solo podía correr una VM a la vez. Hoy día, con 32GB, corro 5-6 VMs simultáneamente.

## Contexto personal

Usé VMware Workstation desde 1999 hasta 2015, cuando VirtualBox (gratuito) y luego hypervisors cloud (AWS, Azure) tomaron relevancia.

VMware representa la democratización de la virtualización: hardware escaso convertido en recursos abundantes y flexibles.