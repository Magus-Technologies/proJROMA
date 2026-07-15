<?php

namespace App\Filament\Widgets;

use App\Models\Cliente;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class ClientesEvolucion extends ChartWidget
{
    protected static ?int $sort = 7;
    protected int|string|array $columnSpan = 1;
    protected ?string $heading = 'Clientes Registrados (6 meses)';
    protected ?string $maxHeight = '280px';

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        $empresa = (int) (session('id_empresa') ?: auth()->user()?->id_empresa ?? 0);

        $months = collect(range(5, 0))->map(fn ($i) => now()->subMonths($i));

        $counts = $months->map(function ($date) use ($empresa) {
            $end = $date->copy()->endOfMonth();
            return Cliente::deEmpresa($empresa)
                ->where('ultima_venta', '<=', $end)
                ->count();
        });

        $increments = [];
        foreach ($counts as $i => $count) {
            $prev = $i > 0 ? $counts[$i - 1] : 0;
            $increments[] = max(0, $count - $prev);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Nuevos clientes',
                    'data' => $increments,
                    'fill' => true,
                    'backgroundColor' => 'rgba(16,185,129,.12)',
                    'borderColor' => '#10b981',
                    'borderWidth' => 2,
                    'tension' => 0.4,
                    'pointRadius' => 3,
                ],
            ],
            'labels' => $months->map(fn ($d) => $d->translatedFormat('M'))->toArray(),
        ];
    }

    protected function getOptions(): array
    {
        return [
            'maintainAspectRatio' => false,
            'plugins' => ['legend' => ['display' => false]],
            'scales' => [
                'x' => ['grid' => ['display' => false]],
                'y' => ['beginAtZero' => true, 'grid' => ['color' => '#f1f5f9']],
            ],
        ];
    }
}
