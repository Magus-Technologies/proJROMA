# Módulo TMS — Estado actual y brechas

> Fecha: 2026-07-01 · **Actualizado: 2026-07-02**
> Basado en análisis del código fuente y el archivo `CUADRO DE PROGRAMACION.xlsx`

---

## 0. Actualización 2026-07-02 — Migración a Filament

El módulo se **reconstruyó en el panel Filament** (`/panel`), que es hacia donde va
todo el sistema. Con esto se cerraron varias brechas de la versión anterior:

- ✅ **Todos los Filament Resources** creados: `Mercado`, `Vehiculo`, `Conductor`,
  `Ruta` (con Repeater de puntos), `Despacho` (armar, estados, entregas, reporte,
  **costos**). Además catálogos `UnidadMedida` y `Presentacion`.
- ✅ **Asignar mercado al cliente**: `ClienteResource` ahora tiene un Select
  "Mercado / Zona (TMS)" (brecha §3.2 resuelta).
- ✅ **Costos del despacho en Filament**: acciones "Agregar costo" (con egreso a
  caja) y "Ver costos".
- ✅ Catálogo de productos: unidades de medida y presentaciones como CRUD, integrados
  al formulario de Producto.

La versión Blade antigua (`/tms/*`) queda como legado y puede eliminarse.

**Brechas que siguen abiertas:** nombres reales de mercados (§3.1), importador de
Excel (§3.3), Guía de Remisión SUNAT (§3.5), dashboard de vencimientos (§3.6),
validación por volumen (§3.7).

---

## 1. Resumen

El módulo TMS (Transportation Management System) está **implementado y operativo en
Filament**. El flujo principal funciona; lo que resta es sobre todo **carga de datos
reales** (nombres de mercados, importación del Excel) e integraciones de fase 2.

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

### 3.2 Asignación de mercado al cliente (CRUD Clientes) — ✅ RESUELTO (2026-07-02)

`ClienteResource` (Filament) ahora incluye un Select **"Mercado / Zona (TMS)"** que
guarda `clientes.mercado`, más una columna en la tabla. El modelo `Cliente` tiene la
relación `mercadoTms()`. Con esto "Armar Despacho" ya puede encontrar los pedidos de
los clientes de una ruta (siempre que se les asigne su mercado).

**Pendiente operativo:** asignar el mercado a los clientes existentes (dato), ya sea a
mano desde el CRUD o vía el importador del Excel (§3.3).

### 3.3 Importación del Excel al sistema

El `CUADRO DE PROGRAMACION.xlsx` tiene 3 hojas útiles:
- **CUADRO DE PROGRAMACION**: pedidos/ventas con cliente, monto, IGV, detalle de productos
- **REGISTRO DE PAGOS**: cobranzas con importes y depositantes
- **REGISTRO DE DESPACHO**: control de guías despachadas

Actualmente no hay ningún proceso batch para cargar estos datos. Esto es necesario
para la migración inicial o carga periódica.

### 3.4 Filament Resources — ✅ RESUELTO (2026-07-02)

Todos los Resources creados:

| Resource | Estado |
|---|---|
| `MercadoResource` | ✅ |
| `VehiculoResource` | ✅ |
| `ConductorResource` | ✅ |
| `RutaResource` (Repeater de puntos) | ✅ |
| `DespachoResource` (armar, estados, entregas, reporte, costos) | ✅ |
| `UnidadMedidaResource` / `PresentacionResource` (catálogo productos) | ✅ |

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
| ✅ hecho | 3.2 — Campo mercado en cliente (Filament) | — |
| ✅ hecho | 3.4 — Filament Resources | — |
| 🔴 **Alta** | 3.1 — Poblar mercados con nombres reales (o renombrar en el CRUD) | — |
| 🔴 **Alta** | Asignar mercado a los clientes existentes (dato) | 3.1 |
| 🟡 **Media** | 3.3 — Importador de Excel | — |
| 🟢 **Baja** | 3.5 — Guía de Remisión | decisión SUNAT |
| 🟢 **Baja** | 3.6 — Alertas vencimientos | — |
| 🟢 **Baja** | 3.7 — Validación por volumen | Productos con volumen |
