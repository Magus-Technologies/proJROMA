<?php

use App\Http\Controllers\ClientesController;
use App\Http\Controllers\ComprasController;
use App\Http\Controllers\ConsultaComprobanteController;
use App\Http\Controllers\CotizacionesController;
use App\Http\Controllers\ProductosController;
use App\Http\Controllers\ProveedoresController;
use App\Http\Controllers\ReportesController;
use App\Http\Controllers\TmsController;
use App\Http\Controllers\VentasController;
use Illuminate\Support\Facades\Route;

// ── Todo el sistema vive en el panel de Filament ──────────────────────────────
// Usamos url('/panel') (no una ruta literal) para que respete la subcarpeta
// del despliegue — ej. /molitalia/panel en el servidor.
Route::get('/', fn () => redirect(url('/panel')))->name('dashboard');
Route::get('/home', fn () => redirect(url('/panel')))->name('home');

// ── Consulta pública de comprobantes (sin login) ─────────────────────────────
// El cliente valida su documento con serie + número + su DNI/RUC.
Route::get('/consulta',        [ConsultaComprobanteController::class, 'index'])->name('consulta.index');
Route::post('/consulta/buscar', [ConsultaComprobanteController::class, 'buscar'])->name('consulta.buscar');
Route::get('/consulta/pdf/{tipo}/{id}', [ConsultaComprobanteController::class, 'pdf'])
    ->middleware('signed')
    ->whereIn('tipo', ['venta', 'nota', 'guia'])
    ->whereNumber('id')
    ->name('consulta.pdf');

// Login del panel servido también en /login (misma página Livewire de Filament)
Route::get('/login', \App\Filament\Pages\Auth\Login::class)
    ->middleware([
        'web',
        'guest',
        \Filament\Http\Middleware\SetUpPanel::class . ':admin',
        \Filament\Http\Middleware\DisableBladeIconComponents::class,
        \Filament\Http\Middleware\DispatchServingFilamentEvent::class,
    ])
    ->name('login');

// ── PDFs / Comprobantes (con auth pero sin empresa check) ─────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/venta/comprobante/pdf/{venta}',        [ReportesController::class, 'comprobanteVenta'])->name('venta.comprobante');
    Route::get('/venta/comprobante/pdf/ma4/{venta}',    [ReportesController::class, 'comprobanteVentaMa4'])->name('venta.comprobante.ma4');
    Route::get('/venta/pdf/voucher/8cm/{voucher}',      [ReportesController::class, 'voucher8cm'])->name('venta.voucher.8cm');
    Route::get('/venta/pdf/voucher/5.6cm/{voucher}',    [ReportesController::class, 'voucher56cm'])->name('venta.voucher.56cm');
    Route::get('/guia/remision/pdf/{guia}',             [ReportesController::class, 'guiaRemisionPdf'])->name('guia.pdf');
    Route::get('/traslado/pdf/{traslado}',              [ReportesController::class, 'trasladoPdf'])->name('traslado.pdf');
    Route::get('/nota/electronica/pdf/{nota}',          [ReportesController::class, 'notaElectronicaPdf'])->name('nota.pdf');
    Route::get('/files/facturacion/xml/{ruc}/{archivo}', [ReportesController::class, 'verXml'])->name('facturacion.xml');
    Route::get('/r/cotizaciones/reporte/{coti}',        [ReportesController::class, 'comprobanteCotizacion'])->name('cotizacion.reporte');
    Route::get('/r/cotizaciones/reporteA4/{coti}',      [ReportesController::class, 'comprobanteCotizacionA4'])->name('cotizacion.reporte.a4');
    Route::get('/r/pedidos/reporte/{coti}',             [ReportesController::class, 'comprobantePedidos'])->name('pedidos.reporte');
    Route::get('/tms/despacho/pdf/{despacho}',          [ReportesController::class, 'despachoReportePdf'])->name('tms.despacho.pdf');
    Route::get('/tms/despacho/pdf/{despacho}/mercado/{mercado}', [ReportesController::class, 'despachoReportePdf'])->name('tms.despacho.pdf.mercado');
    Route::get('/tms/despacho/guias/{despacho}',        [ReportesController::class, 'despachoGuiasPdf'])->name('tms.despacho.guias');
    Route::get('/tms/despacho/comprobantes/{despacho}', [ReportesController::class, 'despachoComprobantesPdf'])->name('tms.despacho.comprobantes');
    Route::get('/tms/despacho/guias-remision/{despacho}', [ReportesController::class, 'despachoGuiasRemisionPdf'])->name('tms.despacho.guias.remision');
    Route::get('/escanear/codigobarra/{empresa}/{sucursal}', [ProductosController::class, 'escanearBarra'])->name('scanner.barra');
});

// ── Compatibilidad: rutas nombradas del layout Blade viejo ────────────────────
// Estos módulos se migraron a Filament, pero `layouts/app.blade.php` todavía
// los enlaza por nombre. Sin estas rutas, cualquier página Blade tira error 500.
Route::middleware('auth')->group(function () {
    $aPanel = [
        'ventas.index'          => '/panel/ventas',
        'cotizaciones.index'    => '/panel/cotizacions',
        'guias.index'           => '/panel/guia-remisions',
        'nota.electronica.lista' => '/panel/notas-electronicas',
        'cobranzas.index'       => '/panel/cuenta-por-cobrars',
        'cobranzas.deudas'      => '/panel/cuenta-por-cobrars',
        'cobranzas.miscobros'   => '/panel/mis-cobros',
        'pagos.index'           => '/panel/cuentas-por-pagar',
        'pago.instrumentos'     => '/panel/metodos-de-pago',
        'caja.gestion'          => '/panel/gestion-cajas',
        'caja.movimientos'      => '/panel/movimientos-cajas',
        'caja.rendiciones'      => '/panel/cierres-cajas',
        'caja.micaja'           => '/panel/mi-caja',
    ];

    foreach ($aPanel as $nombre => $destino) {
        // url() respeta la subcarpeta del despliegue (ej. /molitalia).
        Route::get('/ir/' . str_replace('.', '-', $nombre), fn () => redirect(url($destino)))->name($nombre);
    }
});

// ── Blade aún activo (POS enlazados desde Filament + módulo TMS) ───────────────
Route::middleware(['auth', 'check.empresa', 'session.timeout'])->group(function () {

    Route::get('/nota/electronica',         [VentasController::class, 'notaElectronica'])->name('nota.electronica');
    Route::get('/cotizaciones/editar/{id}', [CotizacionesController::class, 'edit'])->name('cotizaciones.edit');
    Route::get('/compras/add',              [ComprasController::class, 'create'])->name('compras.create');

    // ── TMS (Transporte / Despacho) ────────────────────────────────────────
    Route::prefix('tms')->name('tms.')->group(function () {
        Route::get('/mercados',       [TmsController::class, 'mercados'])->name('mercados');
        Route::get('/vehiculos',      [TmsController::class, 'vehiculos'])->name('vehiculos');
        Route::get('/conductores',    [TmsController::class, 'conductores'])->name('conductores');
        Route::get('/rutas',          [TmsController::class, 'rutas'])->name('rutas');
        Route::get('/armar-despacho', [TmsController::class, 'armarDespacho'])->name('armar');
        Route::get('/despachos',      [TmsController::class, 'despachos'])->name('despachos');
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
    Route::permanentRedirect('/pago-instrumentos', '/panel/metodos-de-pago');

    // ── Reportes / Exports (enlazados desde Filament) ─────────────────────
    Route::prefix('reporte')->name('reporte.')->group(function () {
        Route::get('/ventas',           [ReportesController::class, 'ventasPdf'])->name('ventas');
        Route::get('/ventas/avanzado',  [ReportesController::class, 'reporteVentasAvanzado'])->name('ventas.avanzado');
        Route::get('/excel/{fecha}',    [ReportesController::class, 'exportarExcel'])->name('excel');
        Route::get('/compras/pdf/{id}', [ReportesController::class, 'reporteCompra'])->name('compra.pdf');
        Route::get('/clientes/{id}',    [ReportesController::class, 'reporteCliente'])->whereNumber('id')->name('cliente');
        Route::get('/clientes/xls',     [ClientesController::class, 'exportarExcel'])->name('clientes.xls');
        Route::get('/cotizaciones',     [ReportesController::class, 'reporteCotizaciones'])->name('cotizaciones');
        Route::get('/cuentas-por-cobrar', [ReportesController::class, 'reporteCuentasPorCobrar'])->name('cuentas.cobrar');
        Route::get('/proveedores/xls',  [ProveedoresController::class, 'exportarExcel'])->name('proveedores.xls');
        Route::get('/ingresos/egresos/{id}', [ReportesController::class, 'ingresosEgresos'])->name('ingresos.egresos');
    });
});
