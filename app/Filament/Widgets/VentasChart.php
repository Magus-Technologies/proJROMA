<?php

namespace App\Filament\Widgets;

use App\Models\Venta;
use Carbon\Carbon;
use Filament\Widgets\LineChartWidget;

class VentasChart extends LineChartWidget
{
    protected static ?int $sort = 2;
    protected int|string|array $columnSpan = 2;
    protected ?string $heading = 'Ventas — Últimos 30 días';
    protected ?string $maxHeight = '260px';
    protected string $color = 'info';

    protected function getData(): array
    {
        $empresa  = (int) (session('id_empresa') ?: auth()->user()?->id_empresa ?? 0);
        $sucursal = (int) (session('sucursal')   ?: auth()->user()?->sucursal   ?? 1);

        $rows = Venta::deEmpresa($empresa)->deSucursal($sucursal)->activas()
            ->where('fecha_emision', '>=', now()->subDays(29))
            ->selectRaw('DATE(fecha_emision) as fecha, SUM(total) as total')
            ->groupBy('fecha')->orderBy('fecha')
            ->get();

        $labels = $rows->map(fn ($r) => Carbon::parse($r->fecha)->format('d/m'))->toArray();
        $totals = $rows->map(fn ($r) => (float) $r->total)->toArray();

        return [
            'datasets' => [
                [
                    'label'           => 'Ventas (S/)',
                    'data'            => $totals,
                    'fill'            => true,
                    'backgroundColor' => 'rgba(59,130,246,.08)',
                    'borderColor'     => '#3b82f6',
                    'borderWidth'     => 2,
                    'tension'         => 0.4,
                    'pointRadius'     => 3,
                    'pointBackgroundColor' => '#3b82f6',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => ['display' => false],
                'tooltip' => [
                    'callbacks' => [
                        'label' => \Filament\Support\RawJs::make("(c) => 'S/ ' + c.parsed.y.toFixed(2)"),
                    ],
                ],
            ],
            'scales' => [
                'x' => ['grid' => ['display' => false]],
                'y' => [
                    'grid' => ['color' => '#f1f5f9'],
                    'ticks' => ['callback' => \Filament\Support\RawJs::make("(v) => 'S/ ' + v.toLocaleString()")],
                ],
            ],
        ];
    }
}
