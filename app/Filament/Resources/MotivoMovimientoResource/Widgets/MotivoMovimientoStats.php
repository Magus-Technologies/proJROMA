<?php

namespace App\Filament\Resources\MotivoMovimientoResource\Widgets;

use App\Models\MotivoMovimiento;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class MotivoMovimientoStats extends StatsOverviewWidget
{
    protected ?string $pollingInterval = null;
    protected int | array | null $columns = 3;

    protected function getStats(): array
    {
        $empresa = (int) session('id_empresa');

        $total   = MotivoMovimiento::where('id_empresa', $empresa)->where('estado', 1)->count();
        $ingresos = MotivoMovimiento::where('id_empresa', $empresa)->where('estado', 1)
            ->whereIn('tipo', ['I', 'A'])->count();
        $salidas  = MotivoMovimiento::where('id_empresa', $empresa)->where('estado', 1)
            ->whereIn('tipo', ['S', 'A'])->count();

        return [
            Stat::make('Motivos Activos', number_format($total))
                ->description('Totales disponibles')
                ->icon('heroicon-o-list-bullet')
                ->color('info'),

            Stat::make('Para Ingresos', number_format($ingresos))
                ->description('Motivos de tipo Ingreso')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success'),

            Stat::make('Para Salidas', number_format($salidas))
                ->description('Motivos de tipo Salida')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('danger'),
        ];
    }
}
