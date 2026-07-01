<?php

namespace App\Filament\Widgets;

use App\Models\Cliente;
use App\Models\Compra;
use App\Models\Cotizacion;
use App\Models\Venta;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;
    protected int|string|array $columnSpan = 'full';

    private function empresaId(): int
    {
        $id = (int) session('id_empresa', 0);
        return $id ?: (int) (auth()->user()?->id_empresa ?? 0);
    }

    private function sucursalId(): int
    {
        $id = (int) session('sucursal', 0);
        return $id ?: (int) (auth()->user()?->sucursal ?? 1);
    }

    protected function getStats(): array
    {
        $empresa  = $this->empresaId();
        $sucursal = $this->sucursalId();

        $ventasMes    = Venta::deEmpresa($empresa)->deSucursal($sucursal)->activas()->delMes()->sum('total');
        $comprasMes   = Compra::deEmpresa($empresa)->delMes()->sum('total');
        $clientes     = Cliente::deEmpresa($empresa)->count();
        $pedidosPend  = Cotizacion::deEmpresa($empresa)->pendientes()->count();

        $ventasTrend = Venta::deEmpresa($empresa)->deSucursal($sucursal)->activas()
            ->where('fecha_emision', '>=', now()->subDays(6))
            ->selectRaw('DATE(fecha_emision) as d, SUM(total) as t')
            ->groupBy('d')->orderBy('d')
            ->pluck('t')->toArray();

        return [
            Stat::make('Ventas del Mes', 'S/ ' . number_format($ventasMes, 2))
                ->description(now()->translatedFormat('F Y'))
                ->icon('heroicon-o-arrow-trending-up')
                ->color('info')
                ->chart($ventasTrend),

            Stat::make('Compras del Mes', 'S/ ' . number_format($comprasMes, 2))
                ->description(now()->translatedFormat('F Y'))
                ->icon('heroicon-o-shopping-cart')
                ->color('warning'),

            Stat::make('Clientes', number_format($clientes))
                ->description('Total registrados')
                ->icon('heroicon-o-users')
                ->color('success'),

            Stat::make('Pedidos Pendientes', number_format($pedidosPend))
                ->description('Sin convertir')
                ->icon('heroicon-o-clipboard-document-list')
                ->color('danger'),
        ];
    }
}
