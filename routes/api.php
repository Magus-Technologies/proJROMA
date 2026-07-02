<?php

use App\Http\Controllers\Api\ClientesApiController;
use App\Http\Controllers\Api\ComprasApiController;
use App\Http\Controllers\Api\CotizacionesApiController;
use App\Http\Controllers\Api\GuiaRemisionApiController;
use App\Http\Controllers\Api\NotaElectronicaApiController;
use App\Http\Controllers\Api\PagoInstrumentoApiController;
use App\Http\Controllers\Api\VentasApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — solo los endpoints que consumen los formularios POS en Blade
| que Filament aún enlaza (compras/add, guias/registrar, nota/electronica,
| cotizaciones/editar). Todo lo demás vive en el panel de Filament.
|--------------------------------------------------------------------------
*/

Route::middleware(['web', 'auth', 'check.empresa'])->group(function () {

    // ── Ventas (buscador de productos y carga usada por los POS) ──────────
    Route::prefix('ventas')->group(function () {
        Route::get('/',                       [VentasApiController::class, 'listar']);
        Route::post('/add',                   [VentasApiController::class, 'guardar']);
        Route::post('/anular',                [VentasApiController::class, 'anular']);
        Route::post('/detalle',               [VentasApiController::class, 'detalle']);
        Route::get('/tipo',                   [VentasApiController::class, 'tipoVenta']);
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

    // ── Compras ────────────────────────────────────────────────────────────
    Route::get('/compras',         [ComprasApiController::class, 'listar']);
    Route::post('/compras',        [ComprasApiController::class, 'guardar']);
    Route::post('/compras/editar', [ComprasApiController::class, 'editar']);

    // ── Cotizaciones ────────────────────────────────────────────────────
    Route::prefix('cotizaciones')->group(function () {
        Route::get('/',                  [CotizacionesApiController::class, 'listar']);
        Route::get('/tipo',              [CotizacionesApiController::class, 'tipoDocumento']);
        Route::get('/buscar/producto',   [CotizacionesApiController::class, 'buscarProducto']);
        Route::post('/add',              [CotizacionesApiController::class, 'guardar']);
        Route::post('/editar',           [CotizacionesApiController::class, 'editar']);
        Route::post('/anular',           [CotizacionesApiController::class, 'anular']);
        Route::post('/detalle',          [CotizacionesApiController::class, 'detalle']);
        Route::post('/cuotas',           [CotizacionesApiController::class, 'cuotas']);
        Route::post('/convertir',        [CotizacionesApiController::class, 'convertir']);
    });

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

    // ── Notas Electrónicas (Crédito / Débito) ───────────────────────────────
    Route::prefix('notas')->group(function () {
        Route::get('/',                [NotaElectronicaApiController::class, 'listar']);
        Route::get('/buscar-venta',    [NotaElectronicaApiController::class, 'buscarVenta']);
        Route::post('/cargar-venta',   [NotaElectronicaApiController::class, 'cargarVenta']);
        Route::post('/add',            [NotaElectronicaApiController::class, 'guardar']);
        Route::post('/enviar-sunat',   [NotaElectronicaApiController::class, 'enviarSunat']);
        Route::post('/anular',         [NotaElectronicaApiController::class, 'anular']);
    });

    // ── Guías de Remisión ────────────────────────────────────────────────────
    Route::prefix('guias')->group(function () {
        Route::get('/',                [GuiaRemisionApiController::class, 'listar']);
        Route::get('/buscar-venta',    [GuiaRemisionApiController::class, 'buscarVenta']);
        Route::post('/cargar-venta',   [GuiaRemisionApiController::class, 'cargarVenta']);
        Route::post('/add',            [GuiaRemisionApiController::class, 'guardar']);
        Route::post('/detalle',        [GuiaRemisionApiController::class, 'detalle']);
        Route::post('/anular',         [GuiaRemisionApiController::class, 'anular']);
    });
});
