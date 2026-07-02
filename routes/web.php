<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\VentasController;
use App\Http\Controllers\ComprasController;
use App\Http\Controllers\ClientesController;
use App\Http\Controllers\ProductosController;
use App\Http\Controllers\CotizacionesController;
use App\Http\Controllers\CobranzasController;
use App\Http\Controllers\CajaController;
use App\Http\Controllers\ReportesController;
use App\Http\Controllers\UsuariosController;
use App\Http\Controllers\SucursalController;
use App\Http\Controllers\ProveedoresController;
use App\Http\Controllers\GuiaRemisionController;
use App\Http\Controllers\ArqueoDiarioController;
use Illuminate\Support\Facades\Route;

// ── Auth ──────────────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post')
        ->middleware('throttle:10.1');
});

Route::post('/logout', [LoginController::class, 'logout'])
    ->name('logout')->middleware('auth');

// ── PDFs / Comprobantes (con auth pero sin empresa check) ─────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/venta/comprobante/pdf/{venta}',        [ReportesController::class, 'comprobanteVenta'])->name('venta.comprobante');
    Route::get('/venta/comprobante/pdf/ma4/{venta}',    [ReportesController::class, 'comprobanteVentaMa4'])->name('venta.comprobante.ma4');
    Route::get('/venta/pdf/voucher/8cm/{voucher}',      [ReportesController::class, 'voucher8cm'])->name('venta.voucher.8cm');
    Route::get('/venta/pdf/voucher/5.6cm/{voucher}',    [ReportesController::class, 'voucher56cm'])->name('venta.voucher.56cm');
    Route::get('/guia/remision/pdf/{guia}',             [ReportesController::class, 'guiaRemisionPdf'])->name('guia.pdf');
    Route::get('/nota/electronica/pdf/{nota}',          [ReportesController::class, 'notaElectronicaPdf'])->name('nota.pdf');
    Route::get('/r/cotizaciones/reporte/{coti}',        [ReportesController::class, 'comprobanteCotizacion'])->name('cotizacion.reporte');
    Route::get('/r/cotizaciones/reporteA4/{coti}',      [ReportesController::class, 'comprobanteCotizacionA4'])->name('cotizacion.reporte.a4');
    Route::get('/r/pedidos/reporte/{coti}',             [ReportesController::class, 'comprobantePedidos'])->name('pedidos.reporte');
    Route::get('/escanear/codigobarra/{empresa}/{sucursal}', [ProductosController::class, 'escanearBarra'])->name('scanner.barra');
});

// ── App principal (requiere auth + empresa activa + session timeout) ───────────
Route::middleware(['auth', 'check.empresa', 'session.timeout'])->group(function () {

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
   Route::get('/home', [DashboardController::class, 'index'])->name('home');
    // ── Ventas ────────────────────────────────────────────────────────────
    Route::prefix('ventas')->name('ventas.')->group(function () {
        Route::get('/',                       [VentasController::class, 'index'])->name('index');
        Route::get('/productos',              [VentasController::class, 'formProductos'])->name('productos');
        Route::get('/servicios',              [VentasController::class, 'formServicios'])->name('servicios');
        Route::get('/editar-producto/{id}',   [VentasController::class, 'editarProducto'])->name('editar.producto');
        Route::get('/editar-servicio/{id}',   [VentasController::class, 'editarServicio'])->name('editar.servicio');
    });

    // ── Guías de Remisión ─────────────────────────────────────────────────
    Route::prefix('guias')->name('guias.')->group(function () {
        Route::get('/remision',           [GuiaRemisionController::class, 'index'])->name('index');
        Route::get('/remision/registrar', [GuiaRemisionController::class, 'create'])->name('create');
    });

    // ── Notas Electrónicas ────────────────────────────────────────────────
    Route::get('/nota/electronica',       [VentasController::class, 'notaElectronica'])->name('nota.electronica');
    Route::get('/nota/electronica/lista', [VentasController::class, 'notaElectronicaLista'])->name('nota.electronica.lista');

    // ── Cotizaciones / Pedidos ────────────────────────────────────────────
    Route::prefix('cotizaciones')->name('cotizaciones.')->group(function () {
        Route::get('/',               [CotizacionesController::class, 'index'])->name('index');
        Route::get('/add',            [CotizacionesController::class, 'create'])->name('create');
        Route::get('/editar/{id}',    [CotizacionesController::class, 'edit'])->name('edit');
        Route::get('/cuotas/{id}',    [CotizacionesController::class, 'cuotas'])->name('cuotas');
    });

    // ── Cobranzas ─────────────────────────────────────────────────────────
    Route::get('/cobranzas',        [CobranzasController::class, 'index'])->name('cobranzas.index');
    Route::get('/deudas',           [CobranzasController::class, 'deudas'])->name('cobranzas.deudas');
    Route::get('/cuentas/cobrar',   [CobranzasController::class, 'cuentasPorCobrar'])->name('cobranzas.cuentas');
    Route::get('/mis-cobros',       [CobranzasController::class, 'misCobros'])->name('cobranzas.miscobros');

    // ── Pagos ─────────────────────────────────────────────────────────────
    Route::get('/pagos',            [ComprasController::class, 'pagos'])->name('pagos.index');

    // ── Cajas ─────────────────────────────────────────────────────────────
    Route::prefix('caja')->name('caja.')->group(function () {
        Route::get('/gestion',      [\App\Http\Controllers\CajaController::class, 'gestion'])->name('gestion');
        Route::get('/movimientos/{idCaja?}', [\App\Http\Controllers\CajaController::class, 'movimientos'])->name('movimientos');
        Route::get('/rendiciones',  [\App\Http\Controllers\CajaController::class, 'rendiciones'])->name('rendiciones');
        Route::get('/arqueo-diario',[\App\Http\Controllers\ArqueoDiarioController::class,'index'])->name('arqueo');
        Route::get('/mi-caja',      [\App\Http\Controllers\CajaController::class, 'miCaja'])->name('micaja');
        Route::get('/apertura',     [\App\Http\Controllers\CajaController::class, 'apertura'])->name('apertura');
    });

    // ── Compras ───────────────────────────────────────────────────────────
    Route::prefix('compras')->name('compras.')->group(function () {
        Route::get('/',     [ComprasController::class, 'index'])->name('index');
        Route::get('/add',  [ComprasController::class, 'create'])->name('create');
    });

    // ── Inventario ────────────────────────────────────────────────────────
    Route::prefix('almacen')->name('almacen.')->group(function () {
        Route::get('/productos',     [ProductosController::class, 'index'])->name('index');      // Registro de Productos
        Route::get('/productos/add', [ProductosController::class, 'create'])->name('create');
        Route::get('/recepcion',     [ProductosController::class, 'recepcion'])->name('recepcion');// Recepción
        Route::get('/existencias',   [ProductosController::class, 'almacen'])->name('almacen');   // Almacén
        Route::get('/kardex',        [ProductosController::class, 'kardex'])->name('kardex');     // Kardex
        Route::get('/ajustes',       [ProductosController::class, 'ajustes'])->name('ajustes');   // Cuadres / Ajustes
        Route::get('/traslado',      [ProductosController::class, 'traslado'])->name('traslado'); // Traslado de Stock
        Route::get('/prestamos',     [ProductosController::class, 'prestamos'])->name('prestamos');// Préstamos de Productos
    });

    // ── Maestros ──────────────────────────────────────────────────────────
    Route::get('/clientes',     [ClientesController::class,   'index'])->name('clientes.index');
    Route::get('/proveedores',  [ProveedoresController::class, 'index'])->name('proveedores.index');

    // ── Admin ─────────────────────────────────────────────────────────────
    Route::middleware('auth')->group(function () {
        Route::get('/usuarios',             [UsuariosController::class, 'index'])->name('usuarios.index');
        Route::get('/sucursales',           [SucursalController::class, 'index'])->name('admin.sucursales');
        Route::get('/administrarempresas',  [UsuariosController::class, 'adminEmpresas'])->name('admin.empresas');
    });

    // ── Reportes ──────────────────────────────────────────────────────────
    // ── Métodos de pago (Bancos, Cuentas, Tarjetas, Billeteras) ──────────
    Route::get('/pago-instrumentos', [\App\Http\Controllers\PagoInstrumentoController::class, 'index'])->name('pago.instrumentos');

    Route::prefix('reporte')->name('reporte.')->group(function () {
        Route::get('/ventas',               [ReportesController::class, 'ventasPdf'])->name('ventas');
        Route::get('/ventas/vendedor',      [ReportesController::class, 'ventasVendedor'])->name('ventas.vendedor');
        Route::get('/deudas/cobros',        [ReportesController::class, 'deudaCobros'])->name('deudas.cobros');
        Route::get('/deudas/vendedor',      [ReportesController::class, 'deudaVendedor'])->name('deudas.vendedor');
        Route::get('/deudas/ruta',          [ReportesController::class, 'deudaRuta'])->name('deudas.ruta');
        Route::get('/excel/{fecha}',        [ReportesController::class, 'exportarExcel'])->name('excel');
        Route::get('/producto/excel',       [ReportesController::class, 'exportarExcelProducto'])->name('excel.producto');
        Route::get('/caja/excel/{id}',      [ReportesController::class, 'exportarExcelCaja'])->name('excel.caja');
        Route::get('/compras/pdf/{id}',     [ReportesController::class, 'reporteCompra'])->name('compra.pdf');
        Route::get('/clientes/{id}',        [ReportesController::class, 'reporteCliente'])->whereNumber('id')->name('cliente');
        Route::get('/clientes/diavisita/pdf',[ClientesController::class,'exportarClientesVisitaPdf'])->name('clientes.visita');
        Route::get('/cobranzas/xls',        [CobranzasController::class,'exportarExcel'])->name('cobranzas.xls');
        Route::get('/clientes/xls',         [ClientesController::class, 'exportarExcel'])->name('clientes.xls');
        Route::get('/proveedores/xls',      [ProveedoresController::class, 'exportarExcel'])->name('proveedores.xls');
        Route::get('/pedidos/camion',        [ReportesController::class,'pedidoCamion'])->name('pedidos.camion');
        Route::get('/pedido/{numero}',       [ReportesController::class,'comprobantePedido'])->name('pedido');
        Route::get('/pedido/logistico',      [ReportesController::class,'reporteLogistico'])->name('logistico');
        Route::get('/ingresos/egresos/{id}', [ReportesController::class,'ingresosEgresos'])->name('ingresos.egresos');
    });
});
