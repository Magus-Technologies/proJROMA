<?php

use App\Http\Controllers\ClientesController;
use App\Http\Controllers\ComprasController;
use App\Http\Controllers\CotizacionesController;
use App\Http\Controllers\ProductosController;
use App\Http\Controllers\ProveedoresController;
use App\Http\Controllers\ReportesController;
use App\Http\Controllers\TmsController;
use App\Http\Controllers\VentasController;
use Illuminate\Support\Facades\Route;

// ── Todo el sistema vive en el panel de Filament ──────────────────────────────
Route::redirect('/', '/panel')->name('dashboard');
Route::redirect('/home', '/panel')->name('home');

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
    Route::get('/nota/electronica/pdf/{nota}',          [ReportesController::class, 'notaElectronicaPdf'])->name('nota.pdf');
    Route::get('/r/cotizaciones/reporte/{coti}',        [ReportesController::class, 'comprobanteCotizacion'])->name('cotizacion.reporte');
    Route::get('/r/cotizaciones/reporteA4/{coti}',      [ReportesController::class, 'comprobanteCotizacionA4'])->name('cotizacion.reporte.a4');
    Route::get('/r/pedidos/reporte/{coti}',             [ReportesController::class, 'comprobantePedidos'])->name('pedidos.reporte');
    Route::get('/tms/despacho/pdf/{despacho}',          [ReportesController::class, 'despachoReportePdf'])->name('tms.despacho.pdf');
    Route::get('/tms/despacho/pdf/{despacho}/mercado/{mercado}', [ReportesController::class, 'despachoReportePdf'])->name('tms.despacho.pdf.mercado');
    Route::get('/tms/despacho/guias/{despacho}',        [ReportesController::class, 'despachoGuiasPdf'])->name('tms.despacho.guias');
    Route::get('/escanear/codigobarra/{empresa}/{sucursal}', [ProductosController::class, 'escanearBarra'])->name('scanner.barra');
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

    // ── Reportes / Exports (enlazados desde Filament) ─────────────────────
    Route::prefix('reporte')->name('reporte.')->group(function () {
        Route::get('/ventas',           [ReportesController::class, 'ventasPdf'])->name('ventas');
        Route::get('/ventas/avanzado',  [ReportesController::class, 'reporteVentasAvanzado'])->name('ventas.avanzado');
        Route::get('/excel/{fecha}',    [ReportesController::class, 'exportarExcel'])->name('excel');
        Route::get('/compras/pdf/{id}', [ReportesController::class, 'reporteCompra'])->name('compra.pdf');
        Route::get('/clientes/{id}',    [ReportesController::class, 'reporteCliente'])->whereNumber('id')->name('cliente');
        Route::get('/clientes/xls',     [ClientesController::class, 'exportarExcel'])->name('clientes.xls');
        Route::get('/cotizaciones',     [ReportesController::class, 'reporteCotizaciones'])->name('cotizaciones');
        Route::get('/proveedores/xls',  [ProveedoresController::class, 'exportarExcel'])->name('proveedores.xls');
        Route::get('/ingresos/egresos/{id}', [ReportesController::class, 'ingresosEgresos'])->name('ingresos.egresos');
    });
});
