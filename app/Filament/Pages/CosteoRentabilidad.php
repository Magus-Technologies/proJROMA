<?php

namespace App\Filament\Pages;

use App\Models\Producto;
use App\Models\ProductoVenta;
use App\Models\Venta;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class CosteoRentabilidad extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-scale';
    protected static ?string $navigationLabel = 'Costeo y Rentabilidad';
    protected static string|\UnitEnum|null $navigationGroup = 'Finanzas';
    protected static ?int $navigationSort = 5;

    protected static ?string $title = 'Costeo y Rentabilidad';
    protected string $view = 'filament.pages.costeo-rentabilidad';

    public function getData(): array
    {
        $empresa = (int) session('id_empresa', 0);
        $month = now()->month;
        $year = now()->year;

        $productos = Producto::where('id_empresa', $empresa)
            ->where('estado', '1')
            ->selectRaw('id_producto, descripcion, costo, precio, cantidad, id_categoria')
            ->get();

        $ventasProductos = ProductoVenta::join('ventas as v', 'v.id_venta', '=', 'productos_ventas.id_venta')
            ->where('v.id_empresa', $empresa)
            ->where('v.estado', '1')
            ->whereMonth('v.fecha_emision', $month)
            ->whereYear('v.fecha_emision', $year)
            ->selectRaw('
                productos_ventas.id_producto,
                SUM(productos_ventas.cantidad) as cantidad_vendida,
                SUM(' . ProductoVenta::sqlTotalLinea() . ') as venta_total,
                SUM(productos_ventas.costo * productos_ventas.cantidad) as costo_total
            ')
            ->groupBy('productos_ventas.id_producto')
            ->get()
            ->keyBy('id_producto');

        $rentabilidad = $productos->map(function ($p) use ($ventasProductos) {
            $v = $ventasProductos->get($p->id_producto);
            $venta = $v ? $v->venta_total : 0;
            $costoVenta = $v ? $v->costo_total : 0;
            $cantidad = $v ? $v->cantidad_vendida : 0;
            $costoPromedio = $cantidad > 0 ? $costoVenta / $cantidad : $p->costo;
            $precioPromedio = $cantidad > 0 ? $venta / $cantidad : $p->precio;
            $margen = $venta > 0 ? round((($venta - $costoVenta) / $venta) * 100, 1) : 0;
            $utilidad = $venta - $costoVenta;
            $precioSugerido = $costoPromedio * 1.3;

            return [
                'id' => $p->id_producto,
                'descripcion' => $p->descripcion,
                'costo_unitario' => $p->costo,
                'precio_venta' => $p->precio,
                'precio_promedio' => round($precioPromedio, 2),
                'stock' => $p->cantidad,
                'cantidad_vendida' => $cantidad,
                'venta_total' => $venta,
                'costo_total' => $costoVenta,
                'utilidad' => $utilidad,
                'margen' => $margen,
                'precio_sugerido' => round($precioSugerido, 2),
                'diferencia_precio' => round($p->precio - $precioSugerido, 2),
            ];
        })->filter(fn ($p) => $p['cantidad_vendida'] > 0 || $p['stock'] > 0)
          ->sortByDesc('utilidad')
          ->values();

        $totalVentas = $rentabilidad->sum('venta_total');
        $totalCostos = $rentabilidad->sum('costo_total');
        $totalUtilidad = $rentabilidad->sum('utilidad');
        $margenPromedio = $totalVentas > 0 ? round(($totalUtilidad / $totalVentas) * 100, 1) : 0;

        $mejores = $rentabilidad->sortByDesc('margen')->take(10);
        $peores = $rentabilidad->filter(fn ($p) => $p['margen'] < 0)->sortBy('margen')->take(10);

        return [
            'rentabilidad' => $rentabilidad,
            'total_ventas' => $totalVentas,
            'total_costos' => $totalCostos,
            'total_utilidad' => $totalUtilidad,
            'margen_promedio' => $margenPromedio,
            'mejores' => $mejores,
            'peores' => $peores,
            'total_productos' => $rentabilidad->count(),
            'total_con_venta' => $rentabilidad->filter(fn ($p) => $p['cantidad_vendida'] > 0)->count(),
        ];
    }
}
