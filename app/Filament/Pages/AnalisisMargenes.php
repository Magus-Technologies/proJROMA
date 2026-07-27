<?php

namespace App\Filament\Pages;

use App\Models\Categoria;
use App\Models\ProductoVenta;
use App\Models\User;
use App\Models\Venta;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class AnalisisMargenes extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrow-trending-up';
    protected static ?string $navigationLabel = 'Análisis de Márgenes';
    protected static string|\UnitEnum|null $navigationGroup = 'Finanzas';
    protected static ?int $navigationSort = 4;

    protected static ?string $title = 'Análisis de Márgenes';
    protected string $view = 'filament.pages.analisis-margenes';

    public function getData(): array
    {
        $empresa = (int) session('id_empresa', 0);
        $sucursal = (int) session('sucursal', 1);
        $month = now()->month;
        $year = now()->year;

        $ventas = Venta::deEmpresa($empresa)->deSucursal($sucursal)->activas()
            ->whereMonth('fecha_emision', $month)->whereYear('fecha_emision', $year)
            ->sum('total');

        $productos = ProductoVenta::join('ventas as v', 'v.id_venta', '=', 'productos_ventas.id_venta')
            ->join('productos as p', 'p.id_producto', '=', 'productos_ventas.id_producto')
            ->where('v.id_empresa', $empresa)
            ->where('v.sucursal', $sucursal)
            ->where('v.estado', '1')
            ->whereMonth('v.fecha_emision', $month)->whereYear('v.fecha_emision', $year)
            ->selectRaw('
                p.descripcion,
                p.id_categoria,
                SUM(productos_ventas.cantidad) as cantidad,
                SUM(' . ProductoVenta::sqlTotalLinea() . ') as venta,
                SUM(productos_ventas.costo * productos_ventas.cantidad) as costo
            ')
            ->groupBy('p.id_producto', 'p.descripcion', 'p.id_categoria')
            ->get();

        $totalCosto = $productos->sum('costo');
        $totalVenta = $productos->sum('venta');
        $margenGeneral = $totalVenta > 0 ? round((($totalVenta - $totalCosto) / $totalVenta) * 100, 1) : 0;

        $topProductos = $productos->map(fn ($p) => [
            'descripcion' => $p->descripcion,
            'venta' => $p->venta,
            'costo' => $p->costo,
            'margen' => $p->venta > 0 ? round((($p->venta - $p->costo) / $p->venta) * 100, 1) : 0,
            'cantidad' => $p->cantidad,
        ])->sortByDesc('margen')->values();

        $productosBajoMargen = $topProductos->filter(fn ($p) => $p['margen'] < 10 && $p['margen'] >= 0)
            ->sortBy('margen')->take(10)->values();

        $productosNegativos = $topProductos->filter(fn ($p) => $p['margen'] < 0)
            ->sortBy('margen')->take(10)->values();

        $porVendedor = Venta::deEmpresa($empresa)->deSucursal($sucursal)->activas()
            ->selectRaw('id_vendedor, SUM(total) as venta')
            ->whereMonth('fecha_emision', $month)->whereYear('fecha_emision', $year)
            ->groupBy('id_vendedor')
            ->get()->map(function ($v) {
                $vendedor = User::find($v->id_vendedor);
                $costo = ProductoVenta::join('ventas as v2', 'v2.id_venta', '=', 'productos_ventas.id_venta')
                    ->where('v2.id_vendedor', $v->id_vendedor)
                    ->whereMonth('v2.fecha_emision', now()->month)->whereYear('v2.fecha_emision', now()->year)
                    ->selectRaw('SUM(productos_ventas.costo * productos_ventas.cantidad) as total')
                    ->value('total') ?? 0;
                return [
                    'vendedor' => $vendedor?->nombre_completo ?: 'Sin asignar',
                    'venta' => $v->venta,
                    'costo' => $costo,
                    'margen' => $v->venta > 0 ? round((($v->venta - $costo) / $v->venta) * 100, 1) : 0,
                ];
            })->sortByDesc('venta')->values();

        $comprasMes = \App\Models\Compra::deEmpresa($empresa)
            ->whereMonth(DB::raw('STR_TO_DATE(fecha_emision, "%Y-%m-%d")'), $month)
            ->whereYear(DB::raw('STR_TO_DATE(fecha_emision, "%Y-%m-%d")'), $year)
            ->sum(DB::raw('CAST(total AS DECIMAL(12,2))'));

        return [
            'ventas' => $ventas,
            'costo_total' => $totalCosto,
            'margen_general' => $margenGeneral,
            'compras_mes' => $comprasMes,
            'top_productos' => $topProductos->take(15),
            'bajo_margen' => $productosBajoMargen,
            'negativos' => $productosNegativos,
            'por_vendedor' => $porVendedor,
        ];
    }
}
