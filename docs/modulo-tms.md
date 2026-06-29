# Módulo TMS (Transportation Management System) — Lógica requerida

Documento de diseño funcional y técnico del módulo de gestión de transporte/despacho.
Estado: **propuesta / pendiente de implementar**.

---

## 1. Objetivo

Gestionar el ciclo **pedido → despacho → reparto → entrega**: armar rutas, asignar
vehículos y conductores, jalar los pedidos del día, cargar y repartir a los puntos de
entrega (mercados, tiendas y otros clientes), registrando el estado de cada entrega.

---

## 2. Flujo de negocio (según operación real)

```
 DÍA 1 (ej. lunes)                         DÍA 2 (mañana siguiente)
 ─────────────────                         ────────────────────────
 1. Vendedores registran PEDIDOS  ──┐
    (cotizaciones, id_tido = pedido)│
 2. Más tarde AUMENTAN pedidos      │  3. CORTE: se cierran los pedidos del día
    (agregan líneas / nuevos)       │  4. Se ARMAN DESPACHOS:
                                    │       - se eligen pedidos pendientes
                                    └──►    - se agrupan por RUTA
                                            - se asigna VEHÍCULO + CONDUCTOR
                                         5. CARGA: se confirma la mercadería cargada
                                         6. RUTA: el vehículo sale a repartir
                                         7. ENTREGA por punto:
                                            mercados / tiendas / otros
                                            → ENTREGADO | RECHAZADO | PARCIAL
                                         8. RETORNO / cierre del despacho + costos
```

Reglas clave del flujo:

- Un **pedido** se puede **crear y aumentar** mientras no haya pasado el **corte** ni
  esté asignado a un despacho cargado.
- Un **despacho** agrupa **varios pedidos** de **una ruta** en **un vehículo** con **un
  conductor**, para **una fecha de reparto**.
- Un **pedido** pertenece como máximo a **un despacho activo**.
- La **entrega** se registra por **pedido/cliente** (punto de entrega), no por todo el
  camión de golpe.

---

## 3. Qué ya existe en el sistema (reutilizar)

| Recurso | Tabla / campo | Uso en TMS |
|---|---|---|
| Pedidos | `cotizaciones` (cabecera) + `productos_cotis` (detalle) | Fuente de los pedidos a repartir. `id_tido` distingue el tipo pedido. |
| Clientes / puntos | `clientes` | Punto de entrega. Ya trae `direccion`, `distrito`, `telefono`. |
| Mercado vs tienda | `clientes.mercado` (int) | Clasifica el punto (mercado / otro). |
| Ruta del cliente | `clientes.id_ruta` | Ruta a la que pertenece el cliente. |
| Ruta ↔ vendedor | `rutas_vendedor` (id_ruta, id_usuario) | Asocia ruta con su vendedor. |
| Empresa / sucursal | `id_empresa`, `sucursal` (en todas las tablas) | Multiempresa/multisucursal. Filtrar siempre. |
| Caja (costos) | `caja_movimientos` | Registrar combustible/peajes/viáticos como egreso de caja (opcional fase 2). |

> ⚠️ **Falta una tabla maestra de RUTAS con nombre.** Hoy `id_ruta` es solo un número
> referenciado por `clientes` y `rutas_vendedor`, pero no hay una tabla `rutas` con
> nombre/zona/descripción. Hay que crearla (ver §4).

---

## 4. Modelo de datos propuesto (tablas nuevas)

Prefijo `tms_` para aislar el módulo. Todas con `id_empresa` + `sucursal`.

### 4.1 `tms_rutas` — maestro de rutas/zonas
```
id              PK
id_empresa      int
sucursal        int
nombre          varchar(120)   -- "Ruta Mercado Central", "Zona Norte"
descripcion     varchar(255)   nullable
estado          tinyint        default 1  (activo/inactivo)
```
> Si se decide reusar el `id_ruta` ya existente en `clientes`, esta tabla le pondría
> nombre a esos números. Recomendado: migrar `id_ruta` a FK de `tms_rutas`.

### 4.2 `tms_vehiculos` — maestro de flota
```
id                  PK
id_empresa          int
sucursal            int
placa               varchar(15)    -- único por empresa
marca               varchar(60)    nullable
modelo              varchar(60)    nullable
capacidad_kg        decimal(10,2)  nullable
capacidad_volumen   decimal(10,2)  nullable
soat_vence          date           nullable
rev_tecnica_vence   date           nullable
estado              tinyint        default 1
```

### 4.3 `tms_conductores` — maestro de choferes
```
id                  PK
id_empresa          int
sucursal            int
id_usuario          int            nullable  -- si el chofer también es usuario
nombres             varchar(120)
documento           varchar(15)    nullable
licencia            varchar(30)    nullable
licencia_categoria  varchar(10)    nullable
licencia_vence      date           nullable
telefono            varchar(20)    nullable
estado              tinyint        default 1
```

### 4.4 `tms_despachos` — cabecera del viaje/reparto
```
id                  PK
id_empresa          int
sucursal            int
codigo              varchar(20)    -- correlativo legible (DSP-000123)
fecha_reparto       date
id_ruta             int  FK tms_rutas
id_vehiculo         int  FK tms_vehiculos
id_conductor        int  FK tms_conductores
estado              varchar(15)    -- PLANIFICADO|CARGADO|EN_RUTA|CERRADO|ANULADO
observaciones       varchar(255)   nullable
id_usuario_creacion int
created_at / updated_at
```

### 4.5 `tms_despacho_pedidos` — pedidos dentro del despacho (detalle)
```
id                  PK
id_despacho         int  FK tms_despachos (onDelete cascade)
id_cotizacion       int  -- el pedido (cotizaciones.cotizacion_id)
id_cliente          int  -- punto de entrega (denormalizado p/ rapidez)
orden               int  -- orden de visita en la ruta
estado_entrega      varchar(15)  -- PENDIENTE|ENTREGADO|RECHAZADO|PARCIAL
monto               decimal(12,2) -- total del pedido al momento de asignar
motivo_rechazo      varchar(255)  nullable
hora_entrega        datetime      nullable
```

> **Entrega parcial / evidencia (fase 2):** si se requiere detalle por producto
> entregado, agregar `tms_entrega_detalle` (id_despacho_pedido, id_producto,
> cant_entregada, cant_devuelta). Para evidencia: `tms_entrega_evidencia`
> (foto/firma url).

---

## 5. Máquina de estados

### Pedido (cotización) respecto al TMS
```
SIN_DESPACHO ──(asignar a despacho)──► ASIGNADO ──(cargar)──► EN_RUTA
   ▲                                       │
   └──────────(quitar del despacho)────────┘
EN_RUTA ──► ENTREGADO | RECHAZADO | PARCIAL
```
Se puede derivar del `estado_entrega` en `tms_despacho_pedidos`; no requiere tocar
`cotizaciones` salvo que se quiera marcar el pedido como "despachado".

### Despacho
```
PLANIFICADO ──► CARGADO ──► EN_RUTA ──► CERRADO
     └──────────────► ANULADO (libera los pedidos)
```
Transiciones:
- **PLANIFICADO → CARGADO**: se confirma la carga. A partir de aquí los pedidos
  asignados **se bloquean** (no se pueden aumentar/editar).
- **CARGADO → EN_RUTA**: sale el vehículo.
- **EN_RUTA → CERRADO**: todas las entregas tienen estado final; se calcula resumen.
- **→ ANULADO**: solo desde PLANIFICADO/CARGADO; libera los pedidos (vuelven a
  SIN_DESPACHO).

---

## 6. Reglas de negocio (validaciones)

1. **Corte de pedidos**: definir una hora/fecha de corte. Después del corte, los pedidos
   de esa fecha entran al pool despachable y ya no se aumentan (configurable).
2. **Un pedido, un despacho activo**: al asignar, validar que el pedido no esté ya en un
   despacho no anulado.
3. **Coherencia de ruta**: al armar un despacho de la ruta X, solo listar pedidos cuyos
   clientes pertenezcan a la ruta X (`clientes.id_ruta`). Permitir override manual.
4. **Capacidad del vehículo (opcional)**: sumar peso/volumen de los pedidos y advertir si
   supera `capacidad_kg`/`capacidad_volumen`.
5. **Disponibilidad**: un vehículo/conductor no puede estar en dos despachos EN_RUTA el
   mismo `fecha_reparto`.
6. **Documentos vencidos**: advertir si SOAT/revisión técnica/licencia están vencidos a la
   `fecha_reparto`.
7. **Bloqueo por carga**: si el despacho está CARGADO o EN_RUTA, no se quitan/agregan
   pedidos ni se editan sus líneas.
8. **Multiempresa/sucursal**: todo filtrado por `id_empresa` + `sucursal` de la sesión.
9. **Anulación**: al anular un despacho, liberar pedidos y dejar traza.

---

## 7. Integración recomendada (respuesta a "no estoy seguro")

**Integrar con `cotizaciones` (pedidos)** — es donde viven las órdenes reales:

- El TMS **jala** los pedidos pendientes (`cotizaciones` del tipo pedido, sin despacho,
  por fecha y/o ruta) en la pantalla "Armar despacho".
- El **punto de entrega** sale de `clientes` (dirección, distrito, mercado).
- La **ruta sugerida** sale de `clientes.id_ruta`.
- **Opcional (fase 2):** al cerrar la entrega, **generar la Guía de Remisión**
  (`guia_remision`) por pedido/cliente, reutilizando vehículo y conductor del despacho.
- **Opcional (fase 2):** registrar **costos del viaje** (combustible, peajes, viáticos)
  como egresos en `caja_movimientos`.

No se integra con `ventas` directamente porque el reparto opera sobre el **pedido**, no
sobre el comprobante.

---

## 8. Endpoints / API propuestos

```
# Maestros
GET    /api/tms/rutas                 listar rutas
POST   /api/tms/rutas                 crear
POST   /api/tms/rutas/editar
POST   /api/tms/rutas/toggle

GET    /api/tms/vehiculos             listar
POST   /api/tms/vehiculos             crear
POST   /api/tms/vehiculos/editar
POST   /api/tms/vehiculos/toggle

GET    /api/tms/conductores           listar
POST   /api/tms/conductores           crear
POST   /api/tms/conductores/editar
POST   /api/tms/conductores/toggle

# Despacho
GET    /api/tms/pedidos-pendientes    pedidos sin despacho (filtros: fecha, id_ruta)
POST   /api/tms/despachos             crear despacho (ruta+vehiculo+conductor+pedidos)
GET    /api/tms/despachos             listar (filtros: fecha, estado)
GET    /api/tms/despachos/{id}        detalle (cabecera + pedidos + puntos)
POST   /api/tms/despachos/cargar      PLANIFICADO → CARGADO
POST   /api/tms/despachos/salir       CARGADO → EN_RUTA
POST   /api/tms/despachos/anular      → ANULADO (libera pedidos)
POST   /api/tms/despachos/reordenar   cambia orden de visita
POST   /api/tms/entregas/registrar    marca un pedido ENTREGADO|RECHAZADO|PARCIAL
```

---

## 9. Pantallas (vistas Blade) propuestas

Menú nuevo en el sidebar: **"Transporte / TMS"** con:

1. **Vehículos** — CRUD de flota (estilo `caja.gestion`).
2. **Conductores** — CRUD de choferes.
3. **Rutas** — CRUD de rutas/zonas.
4. **Armar Despacho** — pantalla principal:
   - Filtro por fecha + ruta → tabla de **pedidos pendientes** (checkbox).
   - Selección de vehículo + conductor.
   - Botón "Crear despacho" con los pedidos elegidos.
5. **Despachos** — lista con estados; al abrir uno: cabecera + lista de puntos con su
   estado de entrega y botones (Cargar / Salir / Registrar entrega / Anular).

---

## 10. Estructura de archivos (Laravel) propuesta

```
app/Http/Controllers/
  TmsController.php                 (vistas web: vehiculos, conductores, rutas, despachos)
app/Http/Controllers/Api/
  TmsVehiculoApiController.php
  TmsConductorApiController.php
  TmsRutaApiController.php
  TmsDespachoApiController.php      (pedidos-pendientes, despachos, entregas)

database/migrations/
  XXXX_create_tms_rutas_table.php
  XXXX_create_tms_vehiculos_table.php
  XXXX_create_tms_conductores_table.php
  XXXX_create_tms_despachos_table.php
  XXXX_create_tms_despacho_pedidos_table.php

resources/views/tms/
  vehiculos.blade.php
  conductores.blade.php
  rutas.blade.php
  armar-despacho.blade.php
  despachos.blade.php

routes/web.php  → grupo prefix('tms')
routes/api.php  → grupo prefix('tms')
```

---

## 11. Fases de implementación sugeridas

| Fase | Entrega |
|---|---|
| **1 — Maestros** | Migraciones + CRUD de vehículos, conductores y rutas. |
| **2 — Despacho** | Armar despacho (jalar pedidos), estados, registro de entregas. |
| **3 — Integraciones** | Generar Guía de Remisión desde la entrega; costos a caja; capacidad/vencimientos. |
| **4 — Extras** | Evidencia (foto/firma), entrega parcial por producto, reportes/mapa. |

---

## 12. Decisiones pendientes de confirmar con el cliente

1. ¿El **corte** de pedidos es por hora fija (ej. 6 p.m.) o manual?
2. ¿Los **conductores** son usuarios del sistema o solo un maestro aparte?
3. ¿Se requiere **generar Guía de Remisión** automática por entrega (fase 2)?
4. ¿Se controlará **capacidad del vehículo** (peso/volumen) desde ya?
5. ¿Reutilizamos el `id_ruta` existente de `clientes` o creamos rutas nuevas en
   `tms_rutas` y migramos?
6. ¿Entrega **parcial** a nivel de producto, o basta ENTREGADO/RECHAZADO por pedido?
```
