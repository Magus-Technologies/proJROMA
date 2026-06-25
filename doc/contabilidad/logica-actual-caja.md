# Módulo Caja — Lógica Actual

## Tablas y Modelos

| Tabla | Modelo | Estado |
|---|---|---|
| `caja_empresa` | `CajaEmpresa` | **Huérfano** — no se usa en ningún controlador |
| `ingreso_egreso` | `IngresoEgreso` | **Huérfano** — no se usa en ningún controlador |
| `arqueos_diarios` | `ArqueoDiario` | **Activo** — usado por `ArqueoApiController` |

> No existen migraciones para ninguna de las 3 tablas.

---

## Submódulos y su estado

### 1. Registro de Caja (`/caja/registros`)
- **Vista:** `caja/registros.blade.php`
- **Controlador:** `CajaController@registros` (solo retorna la vista)
- **Estado:** ROTO
- **Problema:** La vista tiene un DataTable y modal para Ingresos/Egresos, pero los 3 endpoints API que consume NO existen:
  - `POST /api/caja/registros` — fuente de datos del DataTable
  - `POST /api/caja/ingreso` — guardar ingreso
  - `POST /api/caja/egreso` — guardar egreso

### 2. Caja Chica / Flujo (`/caja/flujo`)
- **Vista:** `caja/flujo.blade.php`
- **Estado:** Placeholder — solo muestra "Módulo en construcción"

### 3. Mi Caja (`/caja/mi-caja`)
- **Vista:** `caja/micaja.blade.php`
- **Estado:** Placeholder — solo muestra "Módulo en construcción"

### 4. Arqueo Diario (`/caja/arqueo-diario`) ✅ Funcional
- **Vista:** `arqueo/index.blade.php`
- **API Controller:** `ArqueoApiController` (3 endpoints funcionales)
- **Rutas API:**
  - `POST /api/arqueo/cobros-dia` — consulta cobros del día por vendedor
  - `POST /api/arqueo/guardar` — guarda/actualiza arqueo
  - `POST /api/arqueo/get` — recupera arqueo existente
- **Flujo:**
  1. Usuario selecciona fecha y hace clic en "Consultar"
  2. Se consultan `dias_ventas` + `ventas` + `cuotas_cotizacion` + `cotizaciones`
  3. Se agrupa por vendedor y tipo de pago (efectivo vs bancos/digital)
  4. Se renderizan tarjetas por vendedor con totales
  5. Usuario puede guardar el arqueo (persiste en `arqueos_diarios`)
  6. El arqueo guardado incluye diferencias y flags de "cuadra"

### 5. Reporte Ingresos/Egresos (`/reporte/ingresos/egresos/{id}`)
- **Vista:** `reportes/ingresos-egresos.blade.php`
- **Estado:** Placeholder

### 6. Reporte Excel Caja (`/reporte/caja/excel/{id}`)
- **Controlador:** `ReportesController@exportarExcelCaja`
- **Estado:** Placeholder — retorna "Excel en desarrollo"

---

## Relaciones entre entidades

No hay relaciones Eloquent entre los modelos. Son tablas independientes.

- `caja_empresa` y `ingreso_egreso` tienen los mismos campos: `id_empresa`, `sucursal`, `fecha`, `tipo`, `descripcion`, `monto`, `id_usuario`
- `arqueos_diarios` almacena **resúmenes** por vendedor/día (no transacciones individuales)

---

## Endpoints API existentes

| Método | Ruta | Controlador | Funciona |
|---|---|---|---|
| GET | `/caja/registros` | `CajaController@registros` | ✅ Vista |
| GET | `/caja/flujo` | `CajaController@flujo` | ✅ Vista (placeholder) |
| GET | `/caja/mi-caja` | `CajaController@miCaja` | ✅ Vista (placeholder) |
| GET | `/caja/arqueo-diario` | `ArqueoDiarioController@index` | ✅ Vista |
| POST | `/api/arqueo/cobros-dia` | `ArqueoApiController@obtenerCobrosDia` | ✅ |
| POST | `/api/arqueo/guardar` | `ArqueoApiController@guardar` | ✅ |
| POST | `/api/arqueo/get` | `ArqueoApiController@get` | ✅ |
| POST | `/api/caja/registros` | **NO EXISTE** | ❌ |
| POST | `/api/caja/ingreso` | **NO EXISTE** | ❌ |
| POST | `/api/caja/egreso` | **NO EXISTE** | ❌ |

---

## Resumen de problemas

1. **Modelos `CajaEmpresa` e `IngresoEgreso` huérfanos** — las tablas existen pero ningún controlador las usa
2. **3 rutas API faltantes** — el frontend de Registro de Caja las necesita pero no están definidas
3. **Sin migraciones** — las tablas existen pero no hay registro en migraciones
4. **3 vistas placeholder** — Falta, Caja Chica, Mi Caja, Reporte Ingresos/Egresos
5. **Solo Arqueo Diario funcional** — es el único submódulo operativo
