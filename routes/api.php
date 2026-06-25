<?php

use App\Http\Controllers\Api\VentasApiController;
use App\Http\Controllers\Api\ClientesApiController;
use App\Http\Controllers\Api\ProductosApiController;
use App\Http\Controllers\Api\CatalogoApiController;
use App\Http\Controllers\Api\ComprasApiController;
use App\Http\Controllers\Api\AlmacenApiController;
use App\Http\Controllers\Api\MovimientoApiController;
use App\Http\Controllers\Api\RecepcionApiController;
use App\Http\Controllers\Api\PrestamoApiController;
use App\Http\Controllers\Api\MotivoApiController;
use App\Http\Controllers\Api\SucursalApiController;
use App\Http\Controllers\Api\TrasladoApiController;
use App\Http\Controllers\Api\ArqueoApiController;
use App\Http\Controllers\Api\CajaApiController;
use App\Http\Controllers\Api\FlujoApiController;
use App\Http\Controllers\Api\MiCajaApiController;
use App\Http\Controllers\Api\PagoInstrumentoApiController;
use App\Http\Controllers\Api\CajaMaestroApiController;
use App\Http\Controllers\Api\CajaInstrumentoApiController;
use App\Http\Controllers\Api\CajaMovimientoApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — todas con auth:sanctum,web + check.empresa
|--------------------------------------------------------------------------
| Nota: Los controladores ya usan #[Middleware] en Laravel 13,
| pero también están protegidos aquí a nivel de grupo como doble capa.
|--------------------------------------------------------------------------
*/

Route::middleware(['web', 'auth', 'check.empresa'])->group(function () {

    // ── Ventas ───────────────────────────────────────────────────────────
    Route::prefix('ventas')->group(function () {
        Route::get('/',                       [VentasApiController::class, 'listar']);
        Route::post('/add',                   [VentasApiController::class, 'guardar']);
        Route::post('/anular',                [VentasApiController::class, 'anular']);
        Route::post('/detalle',               [VentasApiController::class, 'detalle']);
        Route::post('/tipo',                  [VentasApiController::class, 'tipoVenta']);
        Route::post('/productos/edit',        [VentasApiController::class, 'editProducto']);
        Route::post('/servicios/edit',        [VentasApiController::class, 'editServicio']);
        Route::post('/ingreso/almacen',       [VentasApiController::class, 'ingresoAlmacen']);
        Route::post('/egreso/almacen',        [VentasApiController::class, 'egresoAlmacen']);
        Route::get('/cargar/productos/{id}',  [VentasApiController::class, 'buscarProducto']);
        Route::get('/cargar/productos',       [VentasApiController::class, 'buscarProductoCoti']);
        Route::post('/cargar/venta/productos',[VentasApiController::class, 'cargarVentaProductos']);
        Route::post('/cargar/venta/servicios',[VentasApiController::class, 'cargarVentaServicios']);
        Route::post('/cargar/venta/info',     [VentasApiController::class, 'cargarVentaDetalles']);
    });

    // ── Clientes ──────────────────────────────────────────────────────────
    Route::prefix('clientes')->group(function () {
        Route::get('/',              [ClientesApiController::class, 'listar']);
        Route::post('/add',          [ClientesApiController::class, 'insertar']);
        Route::post('/add/lista',    [ClientesApiController::class, 'insertarXLista']);
        Route::post('/render',       [ClientesApiController::class, 'render']);
        Route::post('/get-one',      [ClientesApiController::class, 'getOne']);
        Route::post('/editar',       [ClientesApiController::class, 'editar']);
        Route::post('/borrar',       [ClientesApiController::class, 'borrar']);
        Route::get('/buscar/datos',  [ClientesApiController::class, 'buscarDatos']);
    });

    // ── Productos ─────────────────────────────────────────────────────────
    Route::prefix('productos')->group(function () {
        Route::get('/',              [ProductosApiController::class, 'listar']);
        Route::get('/serverside',    [ProductosApiController::class, 'serverside']);
        Route::get('/catalogo',      [ProductosApiController::class, 'catalogo']);
        Route::post('/add',          [ProductosApiController::class, 'guardar']);
        Route::post('/add/lista',    [ProductosApiController::class, 'agregarPorLista']);
        Route::post('/editar',       [ProductosApiController::class, 'editar']);
        Route::post('/borrar',       [ProductosApiController::class, 'borrar']);
        Route::post('/get-one',      [ProductosApiController::class, 'getOne']);
        Route::post('/imagen',       [ProductosApiController::class, 'subirImagen']);
        Route::get('/razon-social',  [ProductosApiController::class, 'porRazonSocial']);
    });

    // ── Catálogo: Categorías / Subcategorías / Marcas / Submarcas ──────────
    Route::prefix('catalogo')->group(function () {
        Route::get('/{tipo}',         [CatalogoApiController::class, 'listar']);
        Route::post('/{tipo}',        [CatalogoApiController::class, 'guardar']);
        Route::post('/{tipo}/editar', [CatalogoApiController::class, 'editar']);
        Route::post('/{tipo}/toggle', [CatalogoApiController::class, 'toggle']);
        Route::post('/{tipo}/borrar', [CatalogoApiController::class, 'borrar']);
    })->where('tipo', 'categorias|subcategorias|marcas|submarcas');

    // ── Compras ────────────────────────────────────────────────────────────
    Route::get('/compras',         [ComprasApiController::class, 'listar']);
    Route::post('/compras',        [ComprasApiController::class, 'guardar']);
    Route::post('/compras/editar', [ComprasApiController::class, 'editar']);

    // ── Instrumentos de pago (bancos, cuentas, tarjetas, billeteras) ──────
    Route::prefix('pago-instrumento')->group(function () {
        Route::get('/bancos',      [PagoInstrumentoApiController::class, 'bancos']);
        Route::get('/cuentas',     [PagoInstrumentoApiController::class, 'cuentasBancarias']);
        Route::get('/tarjetas',    [PagoInstrumentoApiController::class, 'tarjetas']);
        Route::get('/billeteras',  [PagoInstrumentoApiController::class, 'billeteras']);
        Route::get('/bancos-dt',   [PagoInstrumentoApiController::class, 'bancosDt']);
        Route::get('/cuentas-dt',  [PagoInstrumentoApiController::class, 'cuentasDt']);
        Route::get('/tarjetas-dt', [PagoInstrumentoApiController::class, 'tarjetasDt']);
        Route::get('/billeteras-dt',[PagoInstrumentoApiController::class, 'billeterasDt']);
        Route::post('/banco',        [PagoInstrumentoApiController::class, 'guardarBanco']);
        Route::post('/banco/editar', [PagoInstrumentoApiController::class, 'editarBanco']);
        Route::post('/banco/toggle', [PagoInstrumentoApiController::class, 'toggleBanco']);
        Route::post('/cuenta',        [PagoInstrumentoApiController::class, 'guardarCuenta']);
        Route::post('/cuenta/editar', [PagoInstrumentoApiController::class, 'editarCuenta']);
        Route::post('/cuenta/toggle', [PagoInstrumentoApiController::class, 'toggleCuenta']);
        Route::post('/tarjeta',        [PagoInstrumentoApiController::class, 'guardarTarjeta']);
        Route::post('/tarjeta/editar', [PagoInstrumentoApiController::class, 'editarTarjeta']);
        Route::post('/tarjeta/toggle', [PagoInstrumentoApiController::class, 'toggleTarjeta']);
        Route::post('/billetera',        [PagoInstrumentoApiController::class, 'guardarBilletera']);
        Route::post('/billetera/editar', [PagoInstrumentoApiController::class, 'editarBilletera']);
        Route::post('/billetera/toggle', [PagoInstrumentoApiController::class, 'toggleBilletera']);
        Route::get('/billetera-tipos',     [PagoInstrumentoApiController::class, 'billeteraTipos']);
        Route::get('/billetera-tipos-dt',  [PagoInstrumentoApiController::class, 'billeteraTiposDt']);
        Route::post('/billetera-tipo',        [PagoInstrumentoApiController::class, 'guardarBilleteraTipo']);
        Route::post('/billetera-tipo/editar', [PagoInstrumentoApiController::class, 'editarBilleteraTipo']);
        Route::post('/billetera-tipo/toggle', [PagoInstrumentoApiController::class, 'toggleBilleteraTipo']);
    });

    // ── Almacenes (maestro) ──────────────────────────────────────────────────
    Route::prefix('almacenes')->group(function () {
        Route::get('/',        [AlmacenApiController::class, 'listar']);
        Route::post('/',       [AlmacenApiController::class, 'guardar']);
        Route::post('/editar', [AlmacenApiController::class, 'editar']);
        Route::post('/toggle', [AlmacenApiController::class, 'toggle']);
        Route::post('/borrar', [AlmacenApiController::class, 'borrar']);
    });

    // ── Movimientos de Inventario (Ingresos / Salidas) ───────────────────────
    Route::prefix('movimientos')->group(function () {
        Route::get('/',          [MovimientoApiController::class, 'listar']);
        Route::get('/ajustes',   [MovimientoApiController::class, 'ajustes']);
        Route::get('/motivos',   [MovimientoApiController::class, 'motivos']);
        Route::get('/productos', [MovimientoApiController::class, 'productosAlmacen']);
        Route::post('/',         [MovimientoApiController::class, 'guardar']);
        Route::post('/traslado', [MovimientoApiController::class, 'traslado']);
        Route::post('/anular',   [MovimientoApiController::class, 'anular']);
    });

    // ── Recepción de compras ─────────────────────────────────────────────────
    Route::prefix('recepcion')->group(function () {
        Route::get('/pendientes',  [RecepcionApiController::class, 'pendientes']);
        Route::get('/lineas',      [RecepcionApiController::class, 'lineas']);
        Route::get('/registro',    [RecepcionApiController::class, 'registro']);
        Route::get('/detalle-recepcion', [RecepcionApiController::class, 'detalleRecepcion']);
        Route::get('/historial',   [RecepcionApiController::class, 'historial']);
        Route::post('/recepcionar', [RecepcionApiController::class, 'recepcionar']);
        Route::post('/deshacer',    [RecepcionApiController::class, 'deshacer']);
        Route::post('/eliminar',    [RecepcionApiController::class, 'eliminar']);
    });

    // ── Préstamos de productos ───────────────────────────────────────────────
    Route::prefix('prestamos')->group(function () {
        Route::get('/',          [PrestamoApiController::class, 'listar']);
        Route::get('/detalle',   [PrestamoApiController::class, 'detalle']);
        Route::get('/lineas-devolucion', [PrestamoApiController::class, 'lineasDevolucion']);
        Route::post('/',         [PrestamoApiController::class, 'guardar']);
        Route::post('/devolver', [PrestamoApiController::class, 'devolver']);
    });

    // ── Motivos de movimiento (maestro) ──────────────────────────────────────
    Route::prefix('motivos')->group(function () {
        Route::get('/',        [MotivoApiController::class, 'listar']);
        Route::post('/',       [MotivoApiController::class, 'guardar']);
        Route::post('/editar', [MotivoApiController::class, 'editar']);
        Route::post('/toggle', [MotivoApiController::class, 'toggle']);
        Route::post('/borrar', [MotivoApiController::class, 'borrar']);
    });

    // ── Traslados (cabecera + detalle) ───────────────────────────────────────
    Route::prefix('traslados')->group(function () {
        Route::get('/',        [TrasladoApiController::class, 'listar']);
        Route::get('/detalle', [TrasladoApiController::class, 'detalle']);
        Route::post('/',       [TrasladoApiController::class, 'guardar']);
    });

    // ── Sucursales (maestro) ─────────────────────────────────────────────────
    Route::prefix('sucursales')->group(function () {
        Route::get('/',        [SucursalApiController::class, 'listar']);
        Route::post('/',       [SucursalApiController::class, 'guardar']);
        Route::post('/editar', [SucursalApiController::class, 'editar']);
        Route::post('/toggle', [SucursalApiController::class, 'toggle']);
        Route::post('/borrar', [SucursalApiController::class, 'borrar']);
    });

    // ── Cajas (nuevo maestro) ─────────────────────────────────────────────
    Route::prefix('cajas')->group(function () {
        Route::get('/',               [CajaMaestroApiController::class, 'listar']);
        Route::post('/',              [CajaMaestroApiController::class, 'guardar']);
        Route::post('/editar',        [CajaMaestroApiController::class, 'editar']);
        Route::post('/toggle',        [CajaMaestroApiController::class, 'toggle']);
        Route::get('/opciones',       [CajaMaestroApiController::class, 'opciones']);
    });

    // ── Caja Movimientos (unificado) ─────────────────────────────────────
    Route::prefix('caja-movimientos')->group(function () {
        Route::get('/{idCaja}',       [CajaMovimientoApiController::class, 'listar']);
        Route::post('/',              [CajaMovimientoApiController::class, 'guardar']);
        Route::post('/editar',        [CajaMovimientoApiController::class, 'editar']);
        Route::post('/anular',        [CajaMovimientoApiController::class, 'anular']);
    });

    // ── Caja Instrumentos ─────────────────────────────────────────────────
    Route::prefix('caja-instrumentos')->group(function () {
        Route::get('/{idCaja}',              [CajaInstrumentoApiController::class, 'listar']);
        Route::get('/disponibles/{idCaja}',  [CajaInstrumentoApiController::class, 'disponibles']);
        Route::get('/por-caja/{idCaja}',     [CajaInstrumentoApiController::class, 'porCaja']);
        Route::post('/asignar',              [CajaInstrumentoApiController::class, 'asignar']);
        Route::post('/quitar',               [CajaInstrumentoApiController::class, 'quitar']);
    });

    // ── Cierres y Consolidado de Cajas ────────────────────────────────────
    Route::prefix('cierres')->group(function () {
        Route::get('/balance/{idCaja}',   [\App\Http\Controllers\Api\CierreCajaApiController::class, 'balanceSistema']);
        Route::post('/cerrar',            [\App\Http\Controllers\Api\CierreCajaApiController::class, 'cerrar']);
        Route::get('/consolidado',        [\App\Http\Controllers\Api\CierreCajaApiController::class, 'consolidado']);
        Route::post('/aprobar',           [\App\Http\Controllers\Api\CierreCajaApiController::class, 'aprobar']);
        Route::get('/historial/{idCaja}', [\App\Http\Controllers\Api\CierreCajaApiController::class, 'historial']);
    });

    // ── Arqueo Diario ─────────────────────────────────────────────────────
    Route::prefix('arqueo')->group(function () {
        Route::post('/cobros-dia',   [ArqueoApiController::class, 'obtenerCobrosDia']);
        Route::post('/guardar',      [ArqueoApiController::class, 'guardar']);
        Route::post('/get',          [ArqueoApiController::class, 'get']);
    });

    // ── TXT Libro Ventas ──────────────────────────────────────────────────
    Route::post('/generar/txt/ventareporte', [VentasApiController::class, 'generarTextLibroVentas']);
});
