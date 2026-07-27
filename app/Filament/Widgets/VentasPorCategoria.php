<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class VentasPorCategoria extends ChartWidget
{
    protected static ?int $sort = 5;
    protected int|string|array $columnSpan = 1;
    protected ?string $heading = 'Ventas por Categoría';
    protected ?string $maxHeight = '280px';

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getData(): array
    {
        $empresa  = (int) (session('id_empresa') ?: auth()->user()?->id_empresa ?? 0);
        $sucursal = (int) (session('sucursal')   ?: auth()->user()?->sucursal   ?? 1);

        $rows = DB::table('productos_ventas as pv')
            ->join('ventas as v', 'v.id_venta', '=', 'pv.id_venta')
            ->join('productos as p', 'p.id_producto', '=', 'pv.id_producto')
            ->join('categorias as c', 'c.id_categoria', '=', 'p.id_categoria')
            ->where('v.id_empresa', $empresa)
            ->where('v.sucursal', $sucursal)
            ->where('v.estado', '1')
            ->whereMonth('v.fecha_emision', now()->month)
            ->whereYear('v.fecha_emision', now()->year)
            ->selectRaw('c.nombre, SUM(' . \App\Models\ProductoVenta::sqlTotalLinea('pv') . ') as total')
            ->groupBy('c.id_categoria', 'c.nombre')
            ->orderByDesc('total')
            ->limit(6)
            ->get();

        $colors = ['#3b82f6','#f59e0b','#10b981','#ef4444','#8b5cf6','#ec4899'];

        return [
            'datasets' => [
                [
                    'data' => $rows->pluck('total')->toArray(),
                    'backgroundColor' => array_slice($colors, 0, $rows->count()),
                    'borderWidth' => 0,
                ],
            ],
            'labels' => $rows->pluck('nombre')->toArray(),
        ];
    }

    protected function getOptions(): array
    {
        return [
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                    'labels' => ['boxWidth' => 10, 'padding' => 8, 'font' => ['size' => 10]],
                ],
            ],
            'cutout' => '65%',
        ];
    }
}
