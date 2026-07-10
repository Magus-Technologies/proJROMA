<?php

namespace App\Filament\Resources\PrestamoResource\Widgets;

use App\Models\Prestamo;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PrestamoStats extends StatsOverviewWidget
{
    protected ?string $pollingInterval = null;
    protected int | array | null $columns = 3;

    protected function getStats(): array
    {
        $empresa = (int) session('id_empresa');

        $activos  = Prestamo::where('id_empresa', $empresa)->where('estado', 1)->count();
        $delMes   = Prestamo::where('id_empresa', $empresa)
            ->whereMonth('fecha', now()->month)
            ->whereYear('fecha', now()->year)
            ->count();

        $prestamos = Prestamo::where('id_empresa', $empresa)
            ->where('tipo', 'P')
            ->where('estado', 1)
            ->count();

        $recibidos = Prestamo::where('id_empresa', $empresa)
            ->where('tipo', 'R')
            ->where('estado', 1)
            ->count();

        return [
            Stat::make('Préstamos del Mes', number_format($delMes))
                ->description('Totales registrados')
                ->icon('heroicon-o-plus-circle')
                ->color('info'),

            Stat::make('Prestados', number_format($prestamos))
                ->description('Stock prestado a terceros')
                ->icon('heroicon-o-arrow-right-circle')
                ->color('warning'),

            Stat::make('Recibidos', number_format($recibidos))
                ->description('Stock recibido de terceros')
                ->icon('heroicon-o-arrow-left-circle')
                ->color('success'),
        ];
    }
}
