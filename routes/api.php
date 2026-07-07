<?php

use App\Http\Controllers\Api\ClientesApiController;
use App\Http\Controllers\Api\ComprasApiController;
use App\Http\Controllers\Api\CotizacionesApiController;
use App\Http\Controllers\Api\NotaElectronicaApiController;
use App\Http\Controllers\Api\PagoInstrumentoApiController;
use App\Http\Controllers\Api\VentasApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — endpoints que consumen los formularios POS en Blade que
| Filament aún enlaza (compras/add, nota/electronica, cotizaciones/editar)
| y el módulo TMS. Todo lo demás vive en el panel de Filament.
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

    // ── TMS (Transporte / Despacho) ──────────────────────────────────────────
    Route::prefix('tms')->group(function () {
        // Mercados
        Route::get('/mercados',        [\App\Http\Controllers\Api\TmsMercadoApiController::class, 'listar']);
        Route::post('/mercados',       [\App\Http\Controllers\Api\TmsMercadoApiController::class, 'guardar']);
        Route::post('/mercados/editar',[\App\Http\Controllers\Api\TmsMercadoApiController::class, 'editar']);
        Route::post('/mercados/toggle',[\App\Http\Controllers\Api\TmsMercadoApiController::class, 'toggle']);

        // Vehículos
        Route::get('/vehiculos',        [\App\Http\Controllers\Api\TmsVehiculoApiController::class, 'listar']);
        Route::post('/vehiculos',       [\App\Http\Controllers\Api\TmsVehiculoApiController::class, 'guardar']);
        Route::post('/vehiculos/editar',[\App\Http\Controllers\Api\TmsVehiculoApiController::class, 'editar']);
        Route::post('/vehiculos/toggle',[\App\Http\Controllers\Api\TmsVehiculoApiController::class, 'toggle']);

        // Conductores
        Route::get('/conductores',        [\App\Http\Controllers\Api\TmsConductorApiController::class, 'listar']);
        Route::post('/conductores',       [\App\Http\Controllers\Api\TmsConductorApiController::class, 'guardar']);
        Route::post('/conductores/editar',[\App\Http\Controllers\Api\TmsConductorApiController::class, 'editar']);
        Route::post('/conductores/toggle',[\App\Http\Controllers\Api\TmsConductorApiController::class, 'toggle']);

        // Rutas + puntos
        Route::get('/rutas',            [\App\Http\Controllers\Api\TmsRutaApiController::class, 'listar']);
        Route::post('/rutas',           [\App\Http\Controllers\Api\TmsRutaApiController::class, 'guardar']);
        Route::post('/rutas/editar',    [\App\Http\Controllers\Api\TmsRutaApiController::class, 'editar']);
        Route::post('/rutas/toggle',    [\App\Http\Controllers\Api\TmsRutaApiController::class, 'toggle']);
        Route::get('/rutas/{idRuta}/puntos', [\App\Http\Controllers\Api\TmsRutaApiController::class, 'puntos']);
        Route::post('/rutas/puntos',         [\App\Http\Controllers\Api\TmsRutaApiController::class, 'agregarPunto']);
        Route::post('/rutas/puntos/quitar',  [\App\Http\Controllers\Api\TmsRutaApiController::class, 'quitarPunto']);
        Route::get('/mercados-opciones',     [\App\Http\Controllers\Api\TmsRutaApiController::class, 'mercados']);
        Route::get('/clientes-buscar',       [\App\Http\Controllers\Api\TmsRutaApiController::class, 'buscarClientes']);

        // Despachos
        Route::get('/despachos/opciones',          [\App\Http\Controllers\Api\TmsDespachoApiController::class, 'opciones']);
        Route::post('/despachos/pedidos-pendientes',[\App\Http\Controllers\Api\TmsDespachoApiController::class, 'pedidosPendientes']);
        Route::get('/despachos',                   [\App\Http\Controllers\Api\TmsDespachoApiController::class, 'listar']);
        Route::post('/despachos',                  [\App\Http\Controllers\Api\TmsDespachoApiController::class, 'guardar']);
        Route::get('/despachos/{id}',              [\App\Http\Controllers\Api\TmsDespachoApiController::class, 'detalle']);
        Route::post('/despachos/estado',           [\App\Http\Controllers\Api\TmsDespachoApiController::class, 'cambiarEstado']);
        Route::post('/despachos/reordenar',        [\App\Http\Controllers\Api\TmsDespachoApiController::class, 'reordenar']);
        Route::post('/despachos/entrega',          [\App\Http\Controllers\Api\TmsDespachoApiController::class, 'registrarEntrega']);
        Route::get('/despachos/{id}/costos',       [\App\Http\Controllers\Api\TmsDespachoApiController::class, 'costos']);
        Route::get('/despachos/{id}/reporte',      [\App\Http\Controllers\Api\TmsDespachoApiController::class, 'reporte']);
        Route::post('/despachos/costos',           [\App\Http\Controllers\Api\TmsDespachoApiController::class, 'agregarCosto']);
        Route::post('/despachos/costos/quitar',    [\App\Http\Controllers\Api\TmsDespachoApiController::class, 'quitarCosto']);
    });
});
