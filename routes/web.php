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
    Route::get('/venta/comprobante/pdf/{venta}',        [ReportesController::class, 'comprobanteVenta'])->name('venta.comprobante')->middleware('can:ventas.pdf');
    Route::get('/venta/comprobante/pdf/ma4/{venta}',    [ReportesController::class, 'comprobanteVentaMa4'])->name('venta.comprobante.ma4')->middleware('can:ventas.pdf');
    Route::get('/venta/pdf/voucher/8cm/{voucher}',      [ReportesController::class, 'voucher8cm'])->name('venta.voucher.8cm')->middleware('can:ventas.pdf');
    Route::get('/venta/pdf/voucher/5.6cm/{voucher}',    [ReportesController::class, 'voucher56cm'])->name('venta.voucher.56cm')->middleware('can:ventas.pdf');
    Route::get('/guia/remision/pdf/{guia}',             [ReportesController::class, 'guiaRemisionPdf'])->name('guia.pdf')->middleware('can:guias.pdf');
    Route::get('/traslado/pdf/{traslado}',              [ReportesController::class, 'trasladoPdf'])->name('traslado.pdf')->middleware('can:almacen_traslados.ver');
    Route::get('/nota/electronica/pdf/{nota}',          [ReportesController::class, 'notaElectronicaPdf'])->name('nota.pdf')->middleware('can:notas.pdf');
    Route::get('/files/facturacion/xml/{ruc}/{archivo}', [ReportesController::class, 'verXml'])->name('facturacion.xml')->middleware('can:ventas.sunat');
    Route::get('/r/cotizaciones/reporte/{coti}',        [ReportesController::class, 'comprobanteCotizacion'])->name('cotizacion.reporte')->middleware('can:cotizaciones.pdf');
    Route::get('/r/cotizaciones/reporteA4/{coti}',      [ReportesController::class, 'comprobanteCotizacionA4'])->name('cotizacion.reporte.a4')->middleware('can:cotizaciones.pdf');
    Route::get('/r/pedidos/reporte/{coti}',             [ReportesController::class, 'comprobantePedidos'])->name('pedidos.reporte')->middleware('can:cotizaciones.pdf');
    Route::get('/tms/despacho/pdf/{despacho}',          [ReportesController::class, 'despachoReportePdf'])->name('tms.despacho.pdf')->middleware('can:tms_despachos.pdf');
    Route::get('/tms/despacho/pdf/{despacho}/mercado/{mercado}', [ReportesController::class, 'despachoReportePdf'])->name('tms.despacho.pdf.mercado')->middleware('can:tms_despachos.pdf');
    Route::get('/tms/despacho/guias/{despacho}',        [ReportesController::class, 'despachoGuiasPdf'])->name('tms.despacho.guias')->middleware('can:tms_despachos.pdf');
    Route::get('/tms/despacho/comprobantes/{despacho}', [ReportesController::class, 'despachoComprobantesPdf'])->name('tms.despacho.comprobantes')->middleware('can:tms_despachos.pdf');
    Route::get('/tms/despacho/guias-remision/{despacho}', [ReportesController::class, 'despachoGuiasRemisionPdf'])->name('tms.despacho.guias.remision')->middleware('can:tms_despachos.pdf');
    Route::get('/escanear/codigobarra/{empresa}/{sucursal}', [ProductosController::class, 'escanearBarra'])->name('scanner.barra')->middleware('can:productos.ver');
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

    Route::get('/nota/electronica',         [VentasController::class, 'notaElectronica'])->name('nota.electronica')->middleware('can:notas.crear');
    Route::get('/cotizaciones/editar/{id}', [CotizacionesController::class, 'edit'])->name('cotizaciones.edit')->middleware('can:cotizaciones.editar');
    Route::get('/compras/add',              [ComprasController::class, 'create'])->name('compras.create')->middleware('can:compras.crear');

    // ── TMS (Transporte / Despacho) ────────────────────────────────────────
    Route::prefix('tms')->name('tms.')->group(function () {
        Route::get('/mercados',       [TmsController::class, 'mercados'])->name('mercados')->middleware('can:tms_mercados.ver');
        Route::get('/vehiculos',      [TmsController::class, 'vehiculos'])->name('vehiculos')->middleware('can:tms_vehiculos.ver');
        Route::get('/conductores',    [TmsController::class, 'conductores'])->name('conductores')->middleware('can:tms_conductores.ver');
        Route::get('/rutas',          [TmsController::class, 'rutas'])->name('rutas')->middleware('can:tms_rutas.ver');
        Route::get('/armar-despacho', [TmsController::class, 'armarDespacho'])->name('armar')->middleware('can:tms_despachos.crear');
        Route::get('/despachos',      [TmsController::class, 'despachos'])->name('despachos')->middleware('can:tms_despachos.ver');
    });

    // ── Compras ───────────────────────────────────────────────────────────
    Route::prefix('compras')->name('compras.')->group(function () {
        Route::get('/',     [ComprasController::class, 'index'])->name('index')->middleware('can:productos.ver');
        Route::get('/add',  [ComprasController::class, 'create'])->name('create')->middleware('can:productos.crear');
    });

    // ── Inventario ────────────────────────────────────────────────────────
    Route::prefix('almacen')->name('almacen.')->group(function () {
        Route::get('/productos',     [ProductosController::class, 'index'])->name('index')->middleware('can:productos.ver');      // Registro de Productos
        Route::get('/productos/add', [ProductosController::class, 'create'])->name('create')->middleware('can:productos.crear');
        Route::get('/recepcion',     [ProductosController::class, 'recepcion'])->name('recepcion')->middleware('can:almacen_recepcion.ver');// Recepción
        Route::get('/existencias',   [ProductosController::class, 'almacen'])->name('almacen')->middleware('can:almacen_existencias.ver');   // Almacén
        Route::get('/kardex',        [ProductosController::class, 'kardex'])->name('kardex')->middleware('can:productos.kardex');     // Kardex
        Route::get('/ajustes',       [ProductosController::class, 'ajustes'])->name('ajustes')->middleware('can:almacen_ajustes.ver');   // Cuadres / Ajustes
        Route::get('/traslado',      [ProductosController::class, 'traslado'])->name('traslado')->middleware('can:almacen_traslados.ver'); // Traslado de Stock
        Route::get('/prestamos',     [ProductosController::class, 'prestamos'])->name('prestamos')->middleware('can:almacen_prestamos.ver');// Préstamos de Productos
    });

    // ── Maestros ──────────────────────────────────────────────────────────
    Route::get('/clientes',     [ClientesController::class,   'index'])->name('clientes.index')->middleware('can:clientes.ver');
    Route::get('/proveedores',  [ProveedoresController::class, 'index'])->name('proveedores.index')->middleware('can:proveedores.ver');

    // ── Admin ─────────────────────────────────────────────────────────────
    // UsuariosController/SucursalController ya no existen (módulos migrados a
    // Filament); se conservan los nombres de ruta para el layout Blade viejo.
    Route::middleware('auth')->group(function () {
        Route::get('/usuarios',             fn () => redirect(url('/panel/usuarios')))->name('usuarios.index')->middleware('can:usuarios.ver');
        Route::get('/sucursales',           fn () => redirect(url('/panel/sucursales')))->name('admin.sucursales')->middleware('can:sucursales.ver');
        Route::get('/administrarempresas',  fn () => redirect(url('/panel/empresas')))->name('admin.empresas')->middleware('can:empresas.ver');
    });

    // ── Reportes ──────────────────────────────────────────────────────────
    // ── Métodos de pago (Bancos, Cuentas, Tarjetas, Billeteras) ──────────
    Route::permanentRedirect('/pago-instrumentos', '/panel/metodos-de-pago');

    // ── Reportes / Exports (enlazados desde Filament) ─────────────────────
    Route::prefix('reporte')->name('reporte.')->group(function () {
        Route::get('/ventas',           [ReportesController::class, 'ventasPdf'])->name('ventas')->middleware('can:reportes_ventas.pdf');
        Route::get('/ventas/avanzado',  [ReportesController::class, 'reporteVentasAvanzado'])->name('ventas.avanzado')->middleware('can:reportes_ventas.pdf');
        Route::get('/excel/{fecha}',    [ReportesController::class, 'exportarExcel'])->name('excel')->middleware('can:reportes.exportar');
        Route::get('/compras/pdf/{id}', [ReportesController::class, 'reporteCompra'])->name('compra.pdf')->middleware('can:compras.pdf');
        Route::get('/clientes/{id}',    [ReportesController::class, 'reporteCliente'])->whereNumber('id')->name('cliente')->middleware('can:reportes_clientes.pdf');
        Route::get('/clientes/xls',     [ClientesController::class, 'exportarExcel'])->name('clientes.xls')->middleware('can:clientes.exportar');
        Route::get('/clientes/plantilla', [ClientesController::class, 'descargarPlantilla'])->name('clientes.plantilla')->middleware('can:clientes.crear');
        Route::get('/productos/plantilla', [ProductosController::class, 'descargarPlantilla'])->name('productos.plantilla')->middleware('can:productos.crear');
        Route::get('/cotizaciones',     [ReportesController::class, 'reporteCotizaciones'])->name('cotizaciones')->middleware('can:cotizaciones.pdf');
        Route::get('/cuentas-por-cobrar', [ReportesController::class, 'reporteCuentasPorCobrar'])->name('cuentas.cobrar')->middleware('can:cobranzas.ver');
        Route::get('/proveedores/xls',  [ProveedoresController::class, 'exportarExcel'])->name('proveedores.xls')->middleware('can:proveedores.exportar');
        Route::get('/ingresos/egresos/{id}', [ReportesController::class, 'ingresosEgresos'])->name('ingresos.egresos')->middleware('can:caja.ver');
        Route::get('/utilidades/pdf',   [ReportesController::class, 'utilidadesPdf'])->name('utilidades.pdf')->middleware('can:finanzas.utilidades');
        Route::get('/utilidades/xls',   [ReportesController::class, 'utilidadesExcel'])->name('utilidades.xls')->middleware('can:finanzas.utilidades');
        Route::get('/indicadores/pdf',  [ReportesController::class, 'indicadoresPdf'])->name('indicadores.pdf')->middleware('can:finanzas.indicadores');
        Route::get('/indicadores/xls',  [ReportesController::class, 'indicadoresExcel'])->name('indicadores.xls')->middleware('can:finanzas.indicadores');
    });
});

// TEMPORAL — solo para depurar la barra lateral en local. BORRAR.
Route::get('/__debug-barra', function () {
    abort_unless(app()->environment('local'), 404);
    $u = \App\Models\User::where('id_rol', 1)->first();
    auth()->login($u);
    session(['id_empresa' => $u->id_empresa, 'sucursal' => $u->sucursal ?? 1]);

    return redirect(url('/panel'));
});
