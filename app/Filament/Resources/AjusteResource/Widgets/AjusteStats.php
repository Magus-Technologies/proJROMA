<?php

namespace App\Filament\Resources\AjusteResource\Widgets;

use App\Models\InventarioMovimiento;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AjusteStats extends StatsOverviewWidget
{
    protected ?string $pollingInterval = null;
    protected int | array | null $columns = 3;

    protected function getStats(): array
    {
        $empresa = (int) session('id_empresa');

        $delMes = InventarioMovimiento::where('id_empresa', $empresa)
            ->whereMonth('fecha', now()->month)
            ->whereYear('fecha', now()->year)
            ->count();

        $ingresos = InventarioMovimiento::where('id_empresa', $empresa)
            ->where('tipo', 'I')
            ->whereMonth('fecha', now()->month)
            ->whereYear('fecha', now()->year)
            ->sum('cantidad') ?? 0;

        $salidas = InventarioMovimiento::where('id_empresa', $empresa)
            ->where('tipo', 'S')
            ->whereMonth('fecha', now()->month)
            ->whereYear('fecha', now()->year)
            ->sum('cantidad') ?? 0;

        return [
            Stat::make('Movimientos del Mes', number_format($delMes))
                ->description('Todos los movimientos')
                ->icon('heroicon-o-adjustments-horizontal')
                ->color('info'),

            Stat::make('Ingresos', number_format($ingresos))
                ->description('Unidades ingresadas')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success'),

            Stat::make('Salidas', number_format($salidas))
                ->description('Unidades salidas')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('danger'),
        ];
    }
}
