<?php

namespace App\Filament\Resources\TrasladoResource\Widgets;

use App\Models\Traslado;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TrasladoStats extends StatsOverviewWidget
{
    protected ?string $pollingInterval = null;
    protected int | array | null $columns = 3;

    protected function getStats(): array
    {
        $empresa = (int) session('id_empresa');

        $delMes  = Traslado::where('id_empresa', $empresa)
            ->whereMonth('fecha', now()->month)
            ->whereYear('fecha', now()->year)
            ->count();

        $activos = Traslado::where('id_empresa', $empresa)
            ->where('estado', 1)
            ->count();

        $itemsMes = \Illuminate\Support\Facades\DB::table('traslado_detalle as td')
            ->join('traslados as t', 't.id_traslado', '=', 'td.id_traslado')
            ->where('t.id_empresa', $empresa)
            ->whereMonth('t.fecha', now()->month)
            ->whereYear('t.fecha', now()->year)
            ->sum('td.cantidad') ?? 0;

        return [
            Stat::make('Traslados del Mes', number_format($delMes))
                ->description('Totales registrados')
                ->icon('heroicon-o-arrows-right-left')
                ->color('info'),

            Stat::make('Activos', number_format($activos))
                ->description('Traslados en curso')
                ->icon('heroicon-o-play')
                ->color('warning'),

            Stat::make('Unidades Trasladadas', number_format($itemsMes))
                ->description('Este mes')
                ->icon('heroicon-o-cube')
                ->color('success'),
        ];
    }
}
