# Estado Actual del Módulo de Caja — Documento Completo

## 1. Arquitectura General

### 1.1 Tablas (migraciones)

| Tabla | Propósito |
|-------|-----------|
| `cajas` | Maestro de cajas (GENERAL, CHICA, VENDEDOR) |
| `caja_movimientos` | Movimientos unificados INGRESO/EGRESO |
| `caja_instrumentos` | Asignación de instrumentos por caja |
| `caja_chica_rendiciones` | Rendiciones de caja chica (fondo fijo) |
| `arqueo_detalle` | Desglose por instrumento en arqueo |
| `billetera_tipos` | Tipos dinámicos de billetera digital |
| `caja_empresa_old_schema` | Tabla antigua renombrada (solo respaldo) |
| `ingreso_egreso_old_schema` | Tabla antigua renombrada (solo respaldo) |

### 1.2 Tipos de Caja

| Tipo | Descripción | Requisitos |
|------|-------------|------------|
| `GENERAL` | Caja principal de la empresa | Ninguno |
| `CHICA` | Caja chica con fondo fijo | `id_caja_padre` (GENERAL), `monto_fondo_fijo` |
| `VENDEDOR` | Caja individual por usuario | `id_usuario_responsable` |

## 2. Flujo de Movimientos (CajaService)

### 2.1 `registrarMovimiento()` — Core

```
CajaService::registrarMovimiento($data)
├── DB::transaction()
│   ├── cajas.lockForUpdate()  ← Bloquea la fila de la caja
│   ├── Calcula saldo_anterior → saldo_posterior (+/-)
│   ├── INSERT caja_movimientos  (id_caja, fecha, tipo, categoria, monto,
│   │                            instrumento_tipo, instrumento_id,
│   │                            saldo_anterior, saldo_posterior, origen_tipo,
│   │                            origen_id, id_usuario, estado='CONFIRMADO')
│   └── UPDATE cajas.saldo_actual = saldo_posterior
└── Retorna ID del movimiento
```

### 2.2 `anularMovimiento()` — Reversión

```
CajaService::anularMovimiento($idMov)
├── DB::transaction()
│   ├── caja_movimientos.lockForUpdate()
│   ├── cajas.lockForUpdate()
│   ├── Restaura saldo_anterior en caja.saldo_actual
│   └── UPDATE movimiento: estado='ANULADO', saldo_posterior=saldo_anterior
```

### 2.3 API Endpoints

#### CajaMaestroApiController

| Método | Ruta | Función |
|--------|------|---------|
| GET | `/api/cajas` | DataTable server-side de todas las cajas (con join a usuarios) |
| POST | `/api/cajas` | Crear caja |
| POST | `/api/cajas/editar` | Editar caja |
| POST | `/api/cajas/toggle` | Activar/desactivar caja |
| GET | `/api/cajas/opciones` | Lista cajas activas + usuarios (para selects) |

#### CajaMovimientoApiController

| Método | Ruta | Función |
|--------|------|---------|
| GET | `/api/caja-movimientos/{idCaja}` | DataTable con filtros (instrumento, categoria, fecha_desde, fecha_hasta) |
| POST | `/api/caja-movimientos` | `guardar()` — registra movimiento manual |
| POST | `/api/caja-movimientos/anular` | `anular()` — reversión |

#### CajaInstrumentoApiController

| Método | Ruta | Función |
|--------|------|---------|
| GET | `/api/caja-instrumentos/{idCaja}` | DataTable de instrumentos asignados |
| GET | `/api/caja-instrumentos/disponibles/{idCaja}` | Catálogo de instrumentos NO asignados aún (globales de la empresa) |
| GET | `/api/caja-instrumentos/por-caja/{idCaja}` | IDs de instrumentos activos en la caja |
| POST | `/api/caja-instrumentos/asignar` | Asignar instrumento a caja |
| POST | `/api/caja-instrumentos/quitar` | Quitar instrumento de caja |

#### RendicionApiController

| Método | Ruta | Función |
|--------|------|---------|
| GET | `/api/rendiciones/activa/{idCaja}` | Obtiene o crea rendición ABIERTA, calcula total_gastado |
| POST | `/api/rendiciones/solicitar` | Cambia estado a PENDIENTE_APROBACION |
| POST | `/api/rendiciones/aprobar` | Genera EGRESO en caja padre + INGRESO en caja chica |
| GET | `/api/rendiciones/historial/{idCaja}` | DataTable de rendiciones anteriores |

### 2.4 Instrumentos (PagoInstrumentoApiController)

| Método | Ruta | Función |
|--------|------|---------|
| GET | `/api/pago-instrumento/bancos` | Lista bancos |
| GET | `/api/pago-instrumento/cuentas` | Lista cuentas bancarias (con nombre banco) |
| GET | `/api/pago-instrumento/tarjetas` | Lista tarjetas (con banco+ultimos_4) |
| GET | `/api/pago-instrumento/billeteras` | Lista billeteras (con tipo+cuenta_vinculada) |
| POST | `/api/pago-instrumento/cuenta/guardar` | Crear/editar cuenta bancaria |
| POST | `/api/pago-instrumento/tarjeta/guardar` | Crear/editar tarjeta |
| POST | `/api/pago-instrumento/billetera/guardar` | Crear/editar billetera digital |
| POST | `/api/pago-instrumento/billetera/editar` | Editar billetera |
| POST | `/api/pago-instrumento/toggle-estado` | Activar/desactivar |
| GET | `/api/pago-instrumento/billetera-tipos` | Lista tipos de billetera (DataTable) |
| GET | `/api/pago-instrumento/billetera-tipos-dt` | DataTable server-side |
| POST | `/api/pago-instrumento/billetera-tipo` | Crear tipo |
| POST | `/api/pago-instrumento/billetera-tipo/editar` | Editar tipo |
| POST | `/api/pago-instrumento/billetera-tipo/toggle` | Activar/desactivar tipo |

## 3. Flujo de Caja Chica

### 3.1 Ciclo de vida de una rendición

```
1. Gestión de Cajas → Crear caja tipo CHICA
   ├── nombre, responsable, caja padre (GENERAL), fondo fijo

2. Rendiciones (/caja/rendiciones)
   ├── Seleccionar caja chica
   ├── Se crea automáticamente rendición ABIERTA (o se recupera la activa)
   ├── Panel muestra: Fondo fijo | Total gastado | Saldo disponible
   └── Tabla inferior: gastos de la caja chica (movimientos EGRESO)

3. Se registran EGRESOS manuales contra la caja chica
   ├── Desde Movimientos (/caja/movimientos) o Mi Caja
   ├── Tipo: EGRESO, Categoría: MANUAL
   └── Se reflejan en "Total gastado"

4. Solicitar aprobación
   ├── Cambia estado a PENDIENTE_APROBACION
   ├── Congela periodo_fin y total_gastado

5. Aprobar rendición
   ├── DB::transaction()
   │   ├── CajaService::registrarMovimiento() → EGRESO en caja padre
   │   │   (categoria=REPOSICION, desc="Reposición fondo caja chica: ...")
   │   ├── CajaService::registrarMovimiento() → INGRESO en caja chica
   │   │   (categoria=REPOSICION, desc="Reintegro fondo aprobado")
   │   └── UPDATE rendicion: estado=APROBADA, id_movimiento_reposicion
   └── Se crea NUEVA rendición ABIERTA para el próximo período
```

### 3.2 Reglas

- Solo puede haber UNA rendición activa (ABIERTA o PENDIENTE_APROBACION) por caja chica
- Al crear la rendición, `monto_fondo` se copia del `monto_fondo_fijo` de la caja
- `total_gastado` se calcula como `SUM(monto)` de movimientos tipo EGRESO + estado CONFIRMADO + fecha >= periodo_inicio
- Al aprobar, se repone el fondo COMPLETO (monto_fondo), no el gastado

## 4. Vistas

### 4.1 Gestión de Cajas (`/caja/gestion`)

```
┌─────────────────────────────────────────────────────────────┐
│ [Nueva Caja]                                                │
├──────────┬───────┬────────────┬──────┬──────┬──────┬───────┤
│ Nombre   │ Tipo  │ Responsable│Saldo │Fondo │Estado│Acción │
├──────────┼───────┼────────────┼──────┼──────┼──────┼───────┤
│ Caja Pr. │General│ -          │S/ 0  │ -    │Activa│✎ 🔄 💳│
│ Caja Chi.│Chica  │ -          │S/ 0  │S/ 500│Activa│✎ 🔄 💳│
└──────────┴───────┴────────────┴──────┴──────┴──────┴───────┘
```

Botones de acción: ✎ Editar | 🔄 Activar/Desactivar | 💳 Asignar instrumentos

Modal "Asignar Instrumentos":
```
┌──────────────────────────────────────┐
│ [Select: — Selecciona —] [Agregar]   │
├──────────────────────────────┬───────┤
│ Instrumento asignado         │ Acción│
├──────────────────────────────┼───────┤
│ Efectivo                     │ 🗑️    │
│ BCP - Cta Corriente 12345   │ 🗑️    │
└──────────────────────────────┴───────┘
```

### 4.2 Movimientos (`/caja/movimientos/{idCaja?}`)

```
┌──────────────────────────────────────────────────────────────────┐
│ [Select: Caja Principal] [Saldo: S/ 0] [Ingreso] [Egreso]       │
│ [Todos los métodos ▼] [Todas las categorías ▼]                  │
├────────┬────────┬─────────┬────────────┬────────┬──────┬───────┤
│ Fecha  │ Tipo   │Categoría│Descripción │Instr.  │Monto │Saldo  │
├────────┼────────┼─────────┼────────────┼────────┼──────┼───────┤
│ 2025.. │⚠ Ing. │ MANUAL  │Apertura    │Efectivo│+S/500│S/ 500 │
└────────┴────────┴─────────┴────────────┴────────┴──────┴───────┘
```

### 4.3 Rendiciones (`/caja/rendiciones`)

```
┌────────────────────────────────────────────────────────────────┐
│ [Select: Caja Chica ▼]                                        │
├────────────────────────────────────────────────────────────────┤
│ ┌──────────┐ ┌──────────────┐ ┌──────────────────┐            │
│ │Fondo fijo│ │Total gastado │ │Saldo disponible   │            │
│ │ S/ 500   │ │  S/ 120      │ │  S/ 380           │            │
│ └──────────┘ └──────────────┘ └──────────────────┘            │
│ [Solicitar aprobación]                                         │
├────────────────────────────────────────────────────────────────┤
│ Gastos de Caja Chica (DataTable)                              │
├────────┬────────────────┬────────────┬──────────┬────────────┤
│ Fecha  │ Descripción    │Instrumento │ Monto    │ Usuario    │
├────────┼────────────────┼────────────┼──────────┼────────────┤
└────────┴────────────────┴────────────┴──────────┴────────────┘

┌───────────────────────────────────────────────────┐
│ Historial de rendiciones (DataTable)               │
├─────────────┬───────┬────────┬────────────────────┤
│ Período     │ Fondo │Gastado │ Estado             │
├─────────────┼───────┼────────┼────────────────────┤
│ 2025-01-01..│ S/500 │ S/120  │ Aprobada          │
└─────────────┴───────┴────────┴────────────────────┘
```

### 4.4 Mi Caja (`/caja/mi-caja`)

Busca automáticamente la caja del usuario:

1. Caja tipo `VENDEDOR` con `id_usuario_responsable = auth()->user()->usuario_id`
2. Fallback: caja tipo `CHICA` con mismo responsable
3. Fallback: primera caja tipo `GENERAL`
4. Si no encuentra nada: mensaje "No tienes caja asignada"

```
┌──────────────────────────────────────────────────────┐
│ Mi Caja — Saldo actual: S/ 1,200                    │
│ [Ingreso] [Egreso]                                   │
├────────┬────────┬─────────┬────────┬────────┬───────┤
│ Fecha  │Tipo    │Categoría│Instr.  │ Monto  │ Saldo │
├────────┼────────┼─────────┼────────┼────────┼───────┤
└────────┴────────┴─────────┴────────┴────────┴───────┘
```

## 5. Pago Instrumento (Billetera Digital rediseñada)

### 5.1 Tabla `billetera_tipos`

```sql
CREATE TABLE billetera_tipos (
    id            INTEGER PRIMARY KEY AUTO_INCREMENT,
    id_empresa    INTEGER NOT NULL,
    nombre        VARCHAR(100) NOT NULL,  -- ej: Yape, Plin, Tunki
    estado        VARCHAR(20) DEFAULT 'ACTIVO',
    UNIQUE (id_empresa, nombre)
);
```

Auto-seed: si la empresa no tiene tipos, se crean: Yape, Plin, Tunki, Lukita.

### 5.2 Tabla `billeteras_digitales`

```sql
CREATE TABLE billeteras_digitales (
    id_billetera         INTEGER PRIMARY KEY AUTO_INCREMENT,
    id_empresa           INTEGER NOT NULL,
    id_billetera_tipo    INTEGER NOT NULL,     -- FK → billetera_tipos.id
    id_cuenta_bancaria   INTEGER NOT NULL,     -- FK → cuentas_bancarias.id_cuenta
    titular              VARCHAR(150),
    telefono             VARCHAR(20),
    estado               VARCHAR(20) DEFAULT 'ACTIVO',
    FOREIGN KEY (id_billetera_tipo) REFERENCES billetera_tipos(id),
    FOREIGN KEY (id_cuenta_bancaria) REFERENCES cuentas_bancarias(id_cuenta)
);
```

### 5.3 Formulario en vista

```
┌──────────────────────────────────────┐
│ ┌──────────────┐ ┌────────────────┐ │
│ │ Tipo (select)│ │Cuenta bancaria │ │
│ │ Yape         │ │BCP Cte 123456  │ │
│ │ [+ Nuevo]    │ │                │ │
│ └──────────────┘ └────────────────┘ │
│ ┌──────────────┐ ┌────────────────┐ │
│ │ Teléfono     │ │ Titular        │ │
│ │ 999888777    │ │ Juan Pérez     │ │
│ └──────────────┘ └────────────────┘ │
│ Estado: [ON/OFF]                    │
└──────────────────────────────────────┘
```

Selectores en Caja vistas muestran `cuenta_vinculada` para billeteras.

## 6. Rutas

### 6.1 API (`routes/api.php`)

```php
// Cajas
Route::get('/cajas',               [CajaMaestroApiController::class, 'listar']);
Route::post('/cajas',              [CajaMaestroApiController::class, 'guardar']);
Route::post('/cajas/editar',       [CajaMaestroApiController::class, 'editar']);
Route::post('/cajas/toggle',       [CajaMaestroApiController::class, 'toggle']);
Route::get('/cajas/opciones',      [CajaMaestroApiController::class, 'opciones']);

// Movimientos
Route::get('/caja-movimientos/{idCaja}',  [CajaMovimientoApiController::class, 'listar']);
Route::post('/caja-movimientos',          [CajaMovimientoApiController::class, 'guardar']);
Route::post('/caja-movimientos/anular',   [CajaMovimientoApiController::class, 'anular']);

// Instrumentos por caja
Route::get('/caja-instrumentos/{idCaja}',              [CajaInstrumentoApiController::class, 'listar']);
Route::get('/caja-instrumentos/disponibles/{idCaja}',  [CajaInstrumentoApiController::class, 'disponibles']);
Route::get('/caja-instrumentos/por-caja/{idCaja}',     [CajaInstrumentoApiController::class, 'porCaja']);
Route::post('/caja-instrumentos/asignar',              [CajaInstrumentoApiController::class, 'asignar']);
Route::post('/caja-instrumentos/quitar',               [CajaInstrumentoApiController::class, 'quitar']);

// Rendiciones
Route::get('/rendiciones/activa/{idCaja}',    [RendicionApiController::class, 'activa']);
Route::post('/rendiciones/solicitar',         [RendicionApiController::class, 'solicitarAprobacion']);
Route::post('/rendiciones/aprobar',           [RendicionApiController::class, 'aprobar']);
Route::get('/rendiciones/historial/{idCaja}', [RendicionApiController::class, 'historial']);
```

### 6.2 Web (`routes/web.php`)

```php
Route::prefix('caja')->group(function () {
    Route::get('/gestion',        [CajaController::class, 'gestion'])->name('caja.gestion');
    Route::get('/movimientos/{idCaja?}', [CajaController::class, 'movimientos'])->name('caja.movimientos');
    Route::get('/rendiciones',    [CajaController::class, 'rendiciones'])->name('caja.rendiciones');
    Route::get('/mi-caja',        [CajaController::class, 'miCaja'])->name('caja.mi-caja');
});
```

## 7. Sidebar

```
<li class="sidebar-label">CAJAS</li>
<li><a href="{{ route('caja.gestion') }}"><i class="ti ti-building-bank"></i>Gestión de Cajas</a></li>
<li><a href="{{ route('caja.movimientos') }}"><i class="ti ti-arrows-exchange"></i>Movimientos</a></li>
<li><a href="{{ route('caja.rendiciones') }}"><i class="ti ti-report-money"></i>Caja Chica</a></li>
<li><a href="{{ route('caja.arqueo') }}"><i class="ti ti-calculator"></i>Arqueo Diario</a></li>
<li><a href="{{ route('caja.mi-caja') }}"><i class="ti ti-wallet"></i>Mi Caja</a></li>
<li><a href="{{ route('pagos.instrumentos') }}"><i class="ti ti-credit-card"></i>Métodos de Pago</a></li>
```

## 8. Controllers

### CajaController (Web)

```php
class CajaController extends Controller
{
    public function gestion()                    { return view('caja.gestion'); }
    public function movimientos(int $idCaja = 0) { return view('caja.movimientos', ['idCaja' => $idCaja]); }
    public function rendiciones()                 { return view('caja.rendiciones'); }
    public function miCaja()                      { return view('caja.micaja'); }
}
```

## 9. Modelos

### CajaEmpresa.php (deprecated, ya no se usa)

```php
class CajaEmpresa extends Model
{
    protected $table = 'caja_empresa';
    protected $primaryKey = null;  // Sin PK real
    public $timestamps = false;
}
```

### BilleteraTipo.php

```php
class BilleteraTipo extends Model
{
    protected $table = 'billetera_tipos';
    public $timestamps = false;
}
```

## 10. Archivos del proyecto

### Backend
- `app/Services/CajaService.php` — Core de transacciones
- `app/Http/Controllers/CajaController.php` — Web
- `app/Http/Controllers/Api/CajaMaestroApiController.php` — CRUD cajas
- `app/Http/Controllers/Api/CajaMovimientoApiController.php` — Movimientos
- `app/Http/Controllers/Api/CajaInstrumentoApiController.php` — Instrumentos por caja
- `app/Http/Controllers/Api/RendicionApiController.php` — Rendiciones
- `app/Http/Controllers/Api/PagoInstrumentoApiController.php` — Catálogo instrumentos
- `app/Models/CajaEmpresa.php` — Deprecated
- `app/Models/BilleteraTipo.php` — Modelo billetera_tipos

### Frontend
- `resources/views/caja/gestion.blade.php`
- `resources/views/caja/movimientos.blade.php`
- `resources/views/caja/rendiciones.blade.php`
- `resources/views/caja/micaja.blade.php`
- `resources/views/pagos/instrumentos.blade.php`

### Migraciones
- `*_fix_schema_to_caja_empresa.php` — Renombra tabla vieja
- `*_create_billetera_tipos_table.php`
- `*_add_billetera_tipo_and_cuenta_to_billeteras.php`
- `*_create_cajas_table.php`
- `*_create_caja_movimientos_table.php`
- `*_create_caja_instrumentos_table.php`
- `*_create_caja_chica_rendiciones_table.php`
- `*_create_arqueo_detalle_table.php`
- `*_alter_arqueos_diarios_add_id_caja.php`
- `*_seed_caja_move_from_old_schema.php`

### Documentación
- `doc/contabilidad/requisitos-caja.md` — Especificaciones
- `doc/contabilidad/logica-actual-caja.md` — Lógica original
- `doc/contabilidad/logica-bancos-billeteras.md` — Lógica instrumentos
- `doc/contabilidad/ESTADO-ACTUAL-COMPLETO.md` — Este archivo

## 11. Próximos pasos

1. **Conectar ventas/compras** a `caja_movimientos` (origen=Venta, origen=Compra) para que los movimientos se automaticen
2. **Arqueo Diario** — integrar `arqueo_detalle` para desglose por instrumento (EFECTIVO, tarjetas, billeteras, etc.)
3. **Mi Caja** — actualizar para crear caja VENDEDOR automáticamente si el usuario no tiene una
4. **Exportar datos** de caja_movimientos a Excel/PDF
5. **Multi-moneda** — extender `moneda` en movimientos
6. **Permisos/roles** — controlar quién puede ver/qué caja, aprobar rendiciones, etc.
