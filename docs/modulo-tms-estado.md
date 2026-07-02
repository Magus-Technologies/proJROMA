# Módulo TMS — Estado actual y brechas

> Fecha: 2026-07-01
> Basado en análisis del código fuente y el archivo `CUADRO DE PROGRAMACION.xlsx`

---

## 1. Resumen

El módulo TMS (Transportation Management System) está implementado en su lógica
principal pero **carece de datos reales** y de algunos componentes de integración
necesarios para operar con la información del negocio.

---

## 2. Lo implementado (funcional)

| Componente | Archivos / Rutas | Estado |
|---|---|---|
| **Mercados CRUD** | Migración, Modelo (`TmsMercado`), API (`TmsMercadoApiController`), Vista (`tms.mercados`), Filament (`MercadoResource`) | ✅ |
| **Vehículos CRUD** | Migración, Modelo (`TmsVehiculo`), API (`TmsVehiculoApiController`), Vista (`tms.vehiculos`) | ✅ |
| **Conductores CRUD** | Migración, Modelo (`TmsConductor`), API (`TmsConductorApiController`), Vista (`tms.conductores`) | ✅ |
| **Rutas + Puntos** | Migración, Modelos (`TmsRuta`, `TmsRutaPunto`), API (`TmsRutaApiController`), Vista (`tms.rutas`) | ✅ |
| **Armar Despacho** | Vista (`tms.armar-despacho`) con JS + API `pedidosPendientes()` | ✅ |
| **Despachos** | API (`TmsDespachoApiController`), Vista (`tms.despachos`), Modelos (`TmsDespacho`, `TmsDespachoPedido`, `TmsDespachoCosto`) | ✅ |
| Máquina de estados | `PLANIFICADO → CARGADO → EN_RUTA → CERRADO / ANULADO` | ✅ |
| Registro de entregas | `ENTREGADO / RECHAZADO / PARCIAL` por punto | ✅ |
| Reordenar puntos de visita | API `reordenar()` | ✅ |
| Costos de viaje | CRUD costos + integración con movimientos de Caja | ✅ |
| Reportes | Por artículo + por cliente, vía API `reporte()` | ✅ |
| Sidebar | Grupo "Transporte (TMS)" con todas las rutas | ✅ |

---

## 3. Lo que falta (brechas)

### 3.1 Datos reales de mercados desde el Excel

El archivo `CUADRO DE PROGRAMACION.xlsx` contiene las zonas de reparto con
nombres descriptivos. La migración `2026_06_29_000002_seed_tms_mercados_from_clientes.php`
solo crea registros genéricos "Mercado N".

**Códigos de zona detectados en el Excel:**

| Código | Posible nombre |
|---|---|
| `01UNICACHI` | Unicachi |
| `02COMAS` | Comas |
| `03CONZAC` | Conzac |
| `04COMAS` | Comas |
| `05COMAS` | Comas |
| `06COMAS` | Comas |
| `07CARABAYLLO` | Carabayllo |
| `08CARABAYLLO` | Carabayllo |
| `21UNICACHI` | Unicachi |
| `03MEPRO` | Mepro |
| `04CONZAC` | Conzac |
| `05CONZAC` | Conzac |
| `MEPROL01` | Mepro |
| `HUAMAN09` | Huamantanga |
| `HUAMAN17` | Huamantanga |
| `03CARABAYLLO` | Carabayllo |
| `04CARABAYLLO` | Carabayllo |
| `08TRES` | Tres |
| `22UNICACHI` | Unicachi |

**Acción requerida:**
- Crear un comando `php artisan tms:import-mercados` o seed que poble `tms_mercados`
  con los nombres reales.
- O bien agregar un importador desde Excel.

### 3.2 Asignación de mercado al cliente (CRUD Clientes)

Hoy `clientes.mercado` es un `int` sin FK ni interfaz de edición. No hay un select
en el CRUD de clientes para elegir el `tms_mercados` al que pertenece cada cliente.

**Impacto:** Sin esta relación, el algoritmo "Armar Despacho" no encuentra los pedidos
de los clientes al seleccionar una ruta.

**Archivos involucrados:**
- `app/Http/Controllers/Api/ClientesApiController.php`
- `resources/views/clientes/` (vistas del CRUD)
- `app/Models/Clientes.php` (campo `mercado`)

### 3.3 Importación del Excel al sistema

El `CUADRO DE PROGRAMACION.xlsx` tiene 3 hojas útiles:
- **CUADRO DE PROGRAMACION**: pedidos/ventas con cliente, monto, IGV, detalle de productos
- **REGISTRO DE PAGOS**: cobranzas con importes y depositantes
- **REGISTRO DE DESPACHO**: control de guías despachadas

Actualmente no hay ningún proceso batch para cargar estos datos. Esto es necesario
para la migración inicial o carga periódica.

### 3.4 Filament Resources incompletos

Solo existe `MercadoResource.php`. Faltan:

| Resource | Estado |
|---|---|
| `MercadoResource` | ✅ |
| `VehiculoResource` | ❌ |
| `ConductorResource` | ❌ |
| `RutaResource` | ❌ |
| `DespachoResource` | ❌ |

### 3.5 Generación de Guía de Remisión (fase 2)

El diseño original (`modulo-tms.md`, §10) contempla generar la **Guía de Remisión**
(`guia_remision`) por pedido/cliente al entregar, reutilizando vehículo y conductor
del despacho. No implementado.

### 3.6 Alertas de documentos vencidos

El controlador `TmsDespachoApiController::guardar()` ya advierte por SOAT,
revisión técnica y licencia vencidos, pero no hay:
- Dashboard visual con próximos vencimientos
- Bloqueo opcional (hoy solo es advertencia)

### 3.7 Validación por volumen (fase 2)

Actualmente solo se compara `peso_total` contra `capacidad_kg`. Las tablas tienen
`capacidad_m3`, `largo_m`, `ancho_m`, `alto_m` pero no se usan porque los productos
no tienen volumen registrado.

---

## 4. Dependencias entre brechas

```
Importar Excel ──► Poblar tms_mercados ──► Asignar mercado a clientes
                                               │
                                               ▼
                                        Armar Despacho funciona
                                               │
                                               ▼
                                        Despachos → Guía de Remisión
```

Las brechas **3.1 y 3.2** son requisito para que el flujo principal opere.

---

## 5. Tablas TMS en base de datos

| Tabla | Propósito | Migración |
|---|---|---|
| `tms_mercados` | Maestro de mercados | ✅ |
| `tms_vehiculos` | Flota vehicular | ✅ |
| `tms_conductores` | Conductores | ✅ |
| `tms_rutas` | Rutas de reparto | ✅ |
| `tms_ruta_puntos` | Puntos (mercado/tienda) por ruta | ✅ |
| `tms_despachos` | Cabecera de despacho | ✅ |
| `tms_despacho_pedidos` | Pedidos jalados al despacho | ✅ |
| `tms_despacho_costos` | Costos del viaje | migración separada |

---

## 6. APIs existentes

Todas bajo `api/tms/` con middleware `auth` + `check.empresa`:

```
GET/POST   /tms/mercados[/{id}]          → TmsMercadoApiController
GET/POST   /tms/vehiculos[/{id}]         → TmsVehiculoApiController
GET/POST   /tms/conductores[/{id}]       → TmsConductorApiController
GET/POST   /tms/rutas[/{id}]             → TmsRutaApiController
GET/POST   /tms/rutas/{id}/puntos        → TmsRutaApiController
GET/POST   /tms/despachos[/{id}]         → TmsDespachoApiController
POST        /tms/despachos/pedidos-pendientes → jalar pedidos
POST        /tms/despachos/estado        → cambiar estado
POST        /tms/despachos/entrega       → registrar entrega
POST        /tms/despachos/reordenar     → reordenar puntos
POST        /tms/despachos/costos        → agregar/quitar costo
```

---

## 7. Prioridad sugerida

| Prioridad | Brecha | Depende de |
|---|---|---|
| 🔴 **Alta** | 3.1 — Poblar mercados con nombres reales | — |
| 🔴 **Alta** | 3.2 — Asignar mercado a clientes | 3.1 |
| 🟡 **Media** | 3.4 — Filament Resources faltantes | — |
| 🟡 **Media** | 3.3 — Importador de Excel | — |
| 🟢 **Baja** | 3.5 — Guía de Remisión | Despacho funcional |
| 🟢 **Baja** | 3.6 — Alertas vencimientos | — |
| 🟢 **Baja** | 3.7 — Validación por volumen | Productos con volumen |
