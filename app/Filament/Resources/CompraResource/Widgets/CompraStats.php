<?php

namespace App\Filament\Resources\CompraResource\Widgets;

use App\Models\Compra;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CompraStats extends StatsOverviewWidget
{
    protected ?string $pollingInterval = null;
    protected int | array | null $columns = 4;

    protected function getStats(): array
    {
        $empresa = (int) session('id_empresa');

        $delMes      = Compra::deEmpresa($empresa)->delMes()->count();
        $pendientes  = Compra::deEmpresa($empresa)->where('recepcionado', 0)->count();
        $recepcionadas = Compra::deEmpresa($empresa)->delMes()->where('recepcionado', 1)->count();
        $totalMes    = Compra::deEmpresa($empresa)->delMes()->sum('total') ?? 0;

        return [
            Stat::make('Compras del Mes', number_format($delMes))
                ->description('Total registradas')
                ->icon('heroicon-o-shopping-cart')
                ->color('info'),

            Stat::make('Pendientes', number_format($pendientes))
                ->description('Sin recepcionar')
                ->icon('heroicon-o-clock')
                ->color('warning'),

            Stat::make('Recepcionadas', number_format($recepcionadas))
                ->description('Completadas este mes')
                ->icon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make('Total del Mes', 'S/ ' . number_format($totalMes, 2))
                ->description('Monto acumulado')
                ->icon('heroicon-o-currency-dollar')
                ->color('primary'),
        ];
    }
}
