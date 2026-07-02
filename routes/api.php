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

    // ── Apertura de Caja ─────────────────────────────────────────────────
    Route::prefix('aperturas')->group(function () {
        Route::get('/cajas-disponibles', [\App\Http\Controllers\Api\AperturaCajaApiController::class, 'cajasDisponibles']);
        Route::post('/guardar',          [\App\Http\Controllers\Api\AperturaCajaApiController::class, 'guardar']);
        Route::get('/historial/{idCaja}',[\App\Http\Controllers\Api\AperturaCajaApiController::class, 'historial']);
        Route::get('/ultima/{idCaja}',   [\App\Http\Controllers\Api\AperturaCajaApiController::class, 'ultima']);
    });

    // ── Arqueo Diario ─────────────────────────────────────────────────────
    Route::prefix('arqueo')->group(function () {
        Route::post('/cobros-dia',   [ArqueoApiController::class, 'obtenerCobrosDia']);
        Route::post('/guardar',      [ArqueoApiController::class, 'guardar']);
        Route::post('/get',          [ArqueoApiController::class, 'get']);
    });

    // ── TXT Libro Ventas ──────────────────────────────────────────────────
    Route::post('/generar/txt/ventareporte', [VentasApiController::class, 'generarTextLibroVentas']);

    // ── Usuarios ───────────────────────────────────────────────────────────
    Route::prefix('usuarios')->group(function () {
        Route::post('/render', [UsuariosApiController::class, 'render']);
    });

    // ── TMS: Transporte / Despacho ─────────────────────────────────────────
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
