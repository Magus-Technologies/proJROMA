<?php

use App\Http\Controllers\Api\VentasApiController;
use App\Http\Controllers\Api\ClientesApiController;
use App\Http\Controllers\Api\ProductosApiController;
use App\Http\Controllers\Api\CatalogoApiController;
use App\Http\Controllers\Api\ArqueoApiController;
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
        Route::post('/add',          [ProductosApiController::class, 'guardar']);
        Route::post('/add/lista',    [ProductosApiController::class, 'agregarPorLista']);
        Route::post('/editar',       [ProductosApiController::class, 'editar']);
        Route::post('/borrar',       [ProductosApiController::class, 'borrar']);
        Route::post('/get-one',      [ProductosApiController::class, 'getOne']);
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

    // ── Arqueo Diario ─────────────────────────────────────────────────────
    Route::prefix('arqueo')->group(function () {
        Route::post('/cobros-dia',   [ArqueoApiController::class, 'obtenerCobrosDia']);
        Route::post('/guardar',      [ArqueoApiController::class, 'guardar']);
        Route::post('/get',          [ArqueoApiController::class, 'get']);
    });

    // ── TXT Libro Ventas ──────────────────────────────────────────────────
    Route::post('/generar/txt/ventareporte', [VentasApiController::class, 'generarTextLibroVentas']);
});
