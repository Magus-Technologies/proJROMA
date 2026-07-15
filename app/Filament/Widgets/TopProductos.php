<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class TopProductos extends ChartWidget
{
    protected static ?int $sort = 3;
    protected int|string|array $columnSpan = 1;
    protected ?string $heading = 'Productos Más Vendidos';
    protected ?string $maxHeight = '280px';

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $empresa  = (int) (session('id_empresa') ?: auth()->user()?->id_empresa ?? 0);
        $sucursal = (int) (session('sucursal')   ?: auth()->user()?->sucursal   ?? 1);

        $rows = DB::table('productos_ventas as pv')
            ->join('ventas as v', 'v.id_venta', '=', 'pv.id_venta')
            ->join('productos as p', 'p.id_producto', '=', 'pv.id_producto')
            ->where('v.id_empresa', $empresa)
            ->where('v.sucursal', $sucursal)
            ->where('v.estado', '1')
            ->whereMonth('v.fecha_emision', now()->month)
            ->whereYear('v.fecha_emision', now()->year)
            ->selectRaw('p.descripcion, SUM(pv.cantidad) as cantidad')
            ->groupBy('p.id_producto', 'p.descripcion')
            ->orderByDesc('cantidad')
            ->limit(8)
            ->get();

        return [
            'datasets' => [
                [
                    'data' => $rows->pluck('cantidad')->toArray(),
                    'backgroundColor' => '#10b981',
                    'borderRadius' => 3,
                ],
            ],
            'labels' => $rows->pluck('descripcion')->map(fn ($d) => str($d)->limit(18))->toArray(),
        ];
    }

    protected function getOptions(): array
    {
        return [
            'maintainAspectRatio' => false,
            'indexAxis' => 'y',
            'plugins' => [
                'legend' => ['display' => false],
            ],
            'scales' => [
                'x' => ['grid' => ['display' => false]],
                'y' => [
                    'grid' => ['display' => false],
                ],
            ],
        ];
    }
}
