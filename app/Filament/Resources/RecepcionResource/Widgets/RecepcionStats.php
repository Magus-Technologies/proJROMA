<?php

namespace App\Filament\Resources\RecepcionResource\Widgets;

use App\Models\Recepcion;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class RecepcionStats extends StatsOverviewWidget
{
    protected ?string $pollingInterval = null;
    protected int | array | null $columns = 3;

    protected function getStats(): array
    {
        $empresa = (int) session('id_empresa');

        $delMes = Recepcion::where('id_empresa', $empresa)
            ->whereMonth('fecha', now()->month)
            ->whereYear('fecha', now()->year)
            ->count();

        $deHoy = Recepcion::where('id_empresa', $empresa)
            ->whereDate('fecha', now()->toDateString())
            ->count();

        $itemsMes = \Illuminate\Support\Facades\DB::table('recepcion_detalle as rd')
            ->join('recepciones as r', 'r.id_recepcion', '=', 'rd.id_recepcion')
            ->where('r.id_empresa', $empresa)
            ->whereMonth('r.fecha', now()->month)
            ->whereYear('r.fecha', now()->year)
            ->sum('rd.cantidad') ?? 0;

        return [
            Stat::make('Recepciones del Mes', number_format($delMes))
                ->description('Totales registradas')
                ->icon('heroicon-o-inbox-arrow-down')
                ->color('info'),

            Stat::make('Recepciones de Hoy', number_format($deHoy))
                ->description(now()->translatedFormat('d \d\e F'))
                ->icon('heroicon-o-calendar')
                ->color('success'),

            Stat::make('Ítems Recibidos', number_format($itemsMes))
                ->description('Unidades recibidas este mes')
                ->icon('heroicon-o-cube')
                ->color('warning'),
        ];
    }
}
