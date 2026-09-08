<?php

namespace App\Filament\Resources\MisCobroResource\Pages;

use App\Filament\Resources\MisCobroResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListMisCobros extends ListRecords
{
    protected static string $resource = MisCobroResource::class;

    /**
     * El recurso pasó de listar cuotas (dias_ventas) a listar abonos
     * (cxc_abonos), pero las pestañas seguían filtrando por
     * dias_ventas.fecha_pago_real, una tabla que esta consulta no une: MySQL
     * cortaba con "Unknown column" y la pantalla no abría.
     *
     * En cxc_abonos la fecha del abono ES la fecha del cobro, que es
     * justamente lo que estas pestañas quieren separar.
     */
    public function getTabs(): array
    {
        return [
            'hoy' => Tab::make('Hoy')
                ->modifyQueryUsing(fn (Builder $query) => $query
                    ->whereDate('cxc_abonos.fecha', now()->toDateString())),

            'semana' => Tab::make('Esta semana')
                ->modifyQueryUsing(fn (Builder $query) => $query
                    ->whereBetween('cxc_abonos.fecha', [
                        now()->startOfWeek()->toDateString(),
                        now()->endOfWeek()->toDateString(),
                    ])),

            'mes' => Tab::make('Este mes')
                ->modifyQueryUsing(fn (Builder $query) => $query
                    ->whereBetween('cxc_abonos.fecha', [
                        now()->startOfMonth()->toDateString(),
                        now()->endOfMonth()->toDateString(),
                    ])),

            'todos' => Tab::make('Todos'),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
