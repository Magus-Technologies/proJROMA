<?php

namespace App\Filament\Resources\CuentaPorCobrarResource\Pages;

use App\Filament\Resources\CuentaPorCobrarResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListCuentasPorCobrar extends ListRecords
{
    protected static string $resource = CuentaPorCobrarResource::class;

    public function getTabs(): array
    {
        return [
            'pendientes' => Tab::make('Pendientes')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('dias_ventas.estado', '0')),

            'vencidas' => Tab::make('Vencidas')
                ->modifyQueryUsing(fn (Builder $query) => $query
                    ->where('dias_ventas.estado', '0')
                    ->whereDate('dias_ventas.fecha', '<', now()->toDateString())),

            'pagadas' => Tab::make('Pagadas')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('dias_ventas.estado', '1')),

            'todas' => Tab::make('Todas'),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
