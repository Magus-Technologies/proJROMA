<?php

namespace App\Filament\Resources\ProductoResource\Widgets;

use App\Models\Producto;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ProductoStats extends StatsOverviewWidget
{
    protected ?string $pollingInterval = null;
    protected int | array | null $columns = 3;

    protected function getStats(): array
    {
        $empresa = (int) session('id_empresa');

        $total       = Producto::deEmpresa($empresa)->activos()->count();
        $bajoStock   = Producto::deEmpresa($empresa)->activos()->bajoStock()->count();
        $valorizado  = Producto::deEmpresa($empresa)->activos()
            ->selectRaw('SUM(cantidad * costo) as total')
            ->value('total') ?? 0;

        return [
            Stat::make('Productos Activos', number_format($total))
                ->description('Total en inventario')
                ->icon('heroicon-o-cube')
                ->color('info'),

            Stat::make('Stock Bajo', number_format($bajoStock))
                ->description('Productos con ≤ 5 unidades')
                ->icon('heroicon-o-exclamation-triangle')
                ->color('danger'),

            Stat::make('Valorizado', 'S/ ' . number_format($valorizado, 2))
                ->description('Costo total del inventario')
                ->icon('heroicon-o-currency-dollar')
                ->color('success'),
        ];
    }
}
