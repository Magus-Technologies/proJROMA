<?php

namespace App\Filament\Widgets;

use App\Models\Compra;
use App\Models\Venta;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class VentasVsCompras extends ChartWidget
{
    protected static ?int $sort = 4;
    protected int|string|array $columnSpan = 2;
    protected ?string $heading = 'Ventas vs Compras (Últimos 6 meses)';
    protected ?string $maxHeight = '280px';

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $empresa  = (int) (session('id_empresa') ?: auth()->user()?->id_empresa ?? 0);
        $sucursal = (int) (session('sucursal')   ?: auth()->user()?->sucursal   ?? 1);

        $months = collect(range(5, 0))->map(fn ($i) => now()->subMonths($i));

        $ventas = $months->map(function ($date) use ($empresa, $sucursal) {
            return Venta::deEmpresa($empresa)->deSucursal($sucursal)->activas()
                ->whereYear('fecha_emision', $date->year)
                ->whereMonth('fecha_emision', $date->month)
                ->sum('total');
        });

        $compras = $months->map(function ($date) use ($empresa) {
            return Compra::deEmpresa($empresa)
                ->whereYear('fecha_emision', $date->year)
                ->whereMonth('fecha_emision', $date->month)
                ->sum('total');
        });

        return [
            'datasets' => [
                [
                    'label' => 'Ventas (S/)',
                    'data' => $ventas->toArray(),
                    'backgroundColor' => '#3b82f6',
                    'borderRadius' => 4,
                ],
                [
                    'label' => 'Compras (S/)',
                    'data' => $compras->toArray(),
                    'backgroundColor' => '#f59e0b',
                    'borderRadius' => 4,
                ],
            ],
            'labels' => $months->map(fn ($d) => $d->translatedFormat('M Y'))->toArray(),
        ];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'position' => 'top',
                    'labels' => ['boxWidth' => 12, 'font' => ['size' => 10]],
                ],
            ],
            'scales' => [
                'x' => ['grid' => ['display' => false]],
                'y' => [
                    'beginAtZero' => true,
                    'grid' => ['color' => '#f1f5f9'],
                ],
            ],
        ];
    }
}
