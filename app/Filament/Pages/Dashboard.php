<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\BajoStockWidget;
use App\Filament\Widgets\ClientesEvolucion;
use App\Filament\Widgets\CxcAgingChart;
use App\Filament\Widgets\StatsOverview;
use App\Filament\Widgets\TopClientesWidget;
use App\Filament\Widgets\TopDeudoresWidget;
use App\Filament\Widgets\TopProductos;
use App\Filament\Widgets\UltimasVentasWidget;
use App\Filament\Widgets\VentasChart;
use App\Filament\Widgets\VentasPorCategoria;
use App\Filament\Widgets\VentasVsCompras;
use BackedEnum;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-home';
    protected static ?string $navigationLabel = 'Dashboard';
    protected static ?int $navigationSort = 0;

    public function getColumns(): int|array
    {
        return 3;
    }

    public function getWidgets(): array
    {
        return [
            StatsOverview::class,
            VentasChart::class,
            TopProductos::class,
            VentasVsCompras::class,
            VentasPorCategoria::class,
            CxcAgingChart::class,
            TopDeudoresWidget::class,
            TopClientesWidget::class,
            ClientesEvolucion::class,
            BajoStockWidget::class,
            UltimasVentasWidget::class,
        ];
    }
}
