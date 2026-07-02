<?php

namespace App\Filament\Resources\MisCobroResource\Pages;

use App\Filament\Resources\MisCobroResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListMisCobros extends ListRecords
{
    protected static string $resource = MisCobroResource::class;

    public function getTabs(): array
    {
        return [
            'hoy' => Tab::make('Hoy')
                ->modifyQueryUsing(fn (Builder $query) => $query
                    ->whereRaw('DATE(IFNULL(dias_ventas.fecha_pago_real, dias_ventas.fecha)) = ?', [now()->toDateString()])),

            'semana' => Tab::make('Esta semana')
                ->modifyQueryUsing(fn (Builder $query) => $query
                    ->whereRaw('DATE(IFNULL(dias_ventas.fecha_pago_real, dias_ventas.fecha)) BETWEEN ? AND ?', [
                        now()->startOfWeek()->toDateString(),
                        now()->endOfWeek()->toDateString(),
                    ])),

            'mes' => Tab::make('Este mes')
                ->modifyQueryUsing(fn (Builder $query) => $query
                    ->whereRaw('DATE(IFNULL(dias_ventas.fecha_pago_real, dias_ventas.fecha)) BETWEEN ? AND ?', [
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
