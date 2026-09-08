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
    Route::prefix('ventas')->middleware('can:ventas.ver')->group(function () {
        Route::get('/',                       [VentasApiController::class, 'listar']);
        Route::post('/add',                   [VentasApiController::class, 'guardar'])->middleware('can:ventas.crear');
        Route::post('/anular',                [VentasApiController::class, 'anular'])->middleware('can:ventas.anular');
        Route::post('/detalle',               [VentasApiController::class, 'detalle']);
        Route::get('/tipo',                   [VentasApiController::class, 'tipoVenta']);
        Route::post('/productos/edit',        [VentasApiController::class, 'editProducto'])->middleware('can:ventas.crear');
        Route::post('/servicios/edit',        [VentasApiController::class, 'editServicio'])->middleware('can:ventas.crear');
        Route::post('/ingreso/almacen',       [VentasApiController::class, 'ingresoAlmacen'])->middleware('can:ventas.crear');
        Route::post('/egreso/almacen',        [VentasApiController::class, 'egresoAlmacen'])->middleware('can:ventas.crear');
        Route::get('/cargar/productos/{id}',  [VentasApiController::class, 'buscarProducto']);
        Route::get('/cargar/productos',       [VentasApiController::class, 'buscarProductoCoti']);
        Route::post('/cargar/venta/productos',[VentasApiController::class, 'cargarVentaProductos']);
        Route::post('/cargar/venta/servicios',[VentasApiController::class, 'cargarVentaServicios']);
        Route::post('/cargar/venta/info',     [VentasApiController::class, 'cargarVentaDetalles']);
    });

    // ── Clientes ──────────────────────────────────────────────────────────
    Route::prefix('clientes')->middleware('can:clientes.ver')->group(function () {
        Route::get('/',              [ClientesApiController::class, 'listar']);
        Route::post('/add',          [ClientesApiController::class, 'insertar'])->middleware('can:clientes.crear');
        Route::post('/add/lista',    [ClientesApiController::class, 'insertarXLista'])->middleware('can:clientes.importar');
        Route::post('/render',       [ClientesApiController::class, 'render']);
        Route::post('/get-one',      [ClientesApiController::class, 'getOne']);
        Route::post('/editar',       [ClientesApiController::class, 'editar'])->middleware('can:clientes.editar');
        Route::post('/borrar',       [ClientesApiController::class, 'borrar'])->middleware('can:clientes.borrar');
        Route::get('/buscar/datos',  [ClientesApiController::class, 'buscarDatos']);
    });

    // ── Compras ────────────────────────────────────────────────────────────
    Route::get('/compras',         [ComprasApiController::class, 'listar'])->middleware('can:compras.ver');
    Route::post('/compras',        [ComprasApiController::class, 'guardar'])->middleware('can:compras.crear');
    Route::post('/compras/editar', [ComprasApiController::class, 'editar'])->middleware('can:compras.editar');

    // ── Cotizaciones ────────────────────────────────────────────────────
    Route::prefix('cotizaciones')->middleware('can:cotizaciones.ver')->group(function () {
        Route::get('/',                  [CotizacionesApiController::class, 'listar']);
        Route::get('/tipo',              [CotizacionesApiController::class, 'tipoDocumento']);
        Route::get('/buscar/producto',   [CotizacionesApiController::class, 'buscarProducto']);
        Route::post('/add',              [CotizacionesApiController::class, 'guardar'])->middleware('can:cotizaciones.crear');
        Route::post('/editar',           [CotizacionesApiController::class, 'editar'])->middleware('can:cotizaciones.editar');
        Route::post('/anular',           [CotizacionesApiController::class, 'anular'])->middleware('can:cotizaciones.anular');
        Route::post('/detalle',          [CotizacionesApiController::class, 'detalle']);
        Route::post('/cuotas',           [CotizacionesApiController::class, 'cuotas'])->middleware('can:cotizaciones.cuotas');
        Route::post('/convertir',        [CotizacionesApiController::class, 'convertir'])->middleware('can:ventas.crear');
    });

    // ── Instrumentos de pago (bancos, cuentas, tarjetas, billeteras) ──────
    Route::prefix('pago-instrumento')->middleware('can:caja.metodos_pago')->group(function () {
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
    Route::prefix('notas')->middleware('can:notas.ver')->group(function () {
        Route::get('/',                [NotaElectronicaApiController::class, 'listar']);
        Route::get('/buscar-venta',    [NotaElectronicaApiController::class, 'buscarVenta']);
        Route::post('/cargar-venta',   [NotaElectronicaApiController::class, 'cargarVenta']);
        Route::post('/add',            [NotaElectronicaApiController::class, 'guardar'])->middleware('can:notas.crear');
        Route::post('/enviar-sunat',   [NotaElectronicaApiController::class, 'enviarSunat'])->middleware('can:notas.sunat');
        Route::post('/anular',         [NotaElectronicaApiController::class, 'anular'])->middleware('can:notas.anular');
    });

    // ── TMS (Transporte / Despacho) ──────────────────────────────────────────
    Route::prefix('tms')->group(function () {
        // Mercados
        Route::get('/mercados',        [\App\Http\Controllers\Api\TmsMercadoApiController::class, 'listar'])->middleware('can:tms_mercados.ver');
        Route::post('/mercados',       [\App\Http\Controllers\Api\TmsMercadoApiController::class, 'guardar'])->middleware('can:tms_mercados.editar');
        Route::post('/mercados/editar',[\App\Http\Controllers\Api\TmsMercadoApiController::class, 'editar'])->middleware('can:tms_mercados.editar');
        Route::post('/mercados/toggle',[\App\Http\Controllers\Api\TmsMercadoApiController::class, 'toggle'])->middleware('can:tms_mercados.editar');

        // Vehículos
        Route::get('/vehiculos',        [\App\Http\Controllers\Api\TmsVehiculoApiController::class, 'listar'])->middleware('can:tms_vehiculos.ver');
        Route::post('/vehiculos',       [\App\Http\Controllers\Api\TmsVehiculoApiController::class, 'guardar'])->middleware('can:tms_vehiculos.editar');
        Route::post('/vehiculos/editar',[\App\Http\Controllers\Api\TmsVehiculoApiController::class, 'editar'])->middleware('can:tms_vehiculos.editar');
        Route::post('/vehiculos/toggle',[\App\Http\Controllers\Api\TmsVehiculoApiController::class, 'toggle'])->middleware('can:tms_vehiculos.editar');

        // Conductores
        Route::get('/conductores',        [\App\Http\Controllers\Api\TmsConductorApiController::class, 'listar'])->middleware('can:tms_conductores.ver');
        Route::post('/conductores',       [\App\Http\Controllers\Api\TmsConductorApiController::class, 'guardar'])->middleware('can:tms_conductores.editar');
        Route::post('/conductores/editar',[\App\Http\Controllers\Api\TmsConductorApiController::class, 'editar'])->middleware('can:tms_conductores.editar');
        Route::post('/conductores/toggle',[\App\Http\Controllers\Api\TmsConductorApiController::class, 'toggle'])->middleware('can:tms_conductores.editar');

        // Rutas + puntos
        Route::get('/rutas',            [\App\Http\Controllers\Api\TmsRutaApiController::class, 'listar'])->middleware('can:tms_rutas.ver');
        Route::post('/rutas',           [\App\Http\Controllers\Api\TmsRutaApiController::class, 'guardar'])->middleware('can:tms_rutas.editar');
        Route::post('/rutas/editar',    [\App\Http\Controllers\Api\TmsRutaApiController::class, 'editar'])->middleware('can:tms_rutas.editar');
        Route::post('/rutas/toggle',    [\App\Http\Controllers\Api\TmsRutaApiController::class, 'toggle'])->middleware('can:tms_rutas.editar');
        Route::get('/rutas/{idRuta}/puntos', [\App\Http\Controllers\Api\TmsRutaApiController::class, 'puntos'])->middleware('can:tms_rutas.ver');
        Route::post('/rutas/puntos',         [\App\Http\Controllers\Api\TmsRutaApiController::class, 'agregarPunto'])->middleware('can:tms_rutas.editar');
        Route::post('/rutas/puntos/quitar',  [\App\Http\Controllers\Api\TmsRutaApiController::class, 'quitarPunto'])->middleware('can:tms_rutas.editar');
        Route::get('/mercados-opciones',     [\App\Http\Controllers\Api\TmsRutaApiController::class, 'mercados'])->middleware('can:tms_rutas.ver');
        Route::get('/clientes-buscar',       [\App\Http\Controllers\Api\TmsRutaApiController::class, 'buscarClientes'])->middleware('can:tms_rutas.ver');

        // Despachos
        Route::get('/despachos/opciones',          [\App\Http\Controllers\Api\TmsDespachoApiController::class, 'opciones'])->middleware('can:tms_despachos.ver');
        Route::post('/despachos/pedidos-pendientes',[\App\Http\Controllers\Api\TmsDespachoApiController::class, 'pedidosPendientes'])->middleware('can:tms_despachos.editar');
        Route::get('/despachos',                   [\App\Http\Controllers\Api\TmsDespachoApiController::class, 'listar'])->middleware('can:tms_despachos.ver');
        Route::post('/despachos',                  [\App\Http\Controllers\Api\TmsDespachoApiController::class, 'guardar'])->middleware('can:tms_despachos.editar');
        Route::get('/despachos/{id}',              [\App\Http\Controllers\Api\TmsDespachoApiController::class, 'detalle'])->middleware('can:tms_despachos.ver');
        Route::post('/despachos/estado',           [\App\Http\Controllers\Api\TmsDespachoApiController::class, 'cambiarEstado'])->middleware('can:tms_despachos.editar');
        Route::post('/despachos/reordenar',        [\App\Http\Controllers\Api\TmsDespachoApiController::class, 'reordenar'])->middleware('can:tms_despachos.editar');
        Route::post('/despachos/entrega',          [\App\Http\Controllers\Api\TmsDespachoApiController::class, 'registrarEntrega'])->middleware('can:tms_despachos.editar');
        Route::get('/despachos/{id}/costos',       [\App\Http\Controllers\Api\TmsDespachoApiController::class, 'costos'])->middleware('can:tms_despachos.ver');
        Route::get('/despachos/{id}/reporte',      [\App\Http\Controllers\Api\TmsDespachoApiController::class, 'reporte'])->middleware('can:tms_despachos.ver');
        Route::post('/despachos/costos',           [\App\Http\Controllers\Api\TmsDespachoApiController::class, 'agregarCosto'])->middleware('can:tms_despachos.editar');
        Route::post('/despachos/costos/quitar',    [\App\Http\Controllers\Api\TmsDespachoApiController::class, 'quitarCosto'])->middleware('can:tms_despachos.editar');
    });
});
