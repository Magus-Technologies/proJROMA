<?php

namespace App\Filament\Resources\GestionCajasResource\Pages;

use App\Filament\Resources\GestionCajasResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListGestionCajas extends ListRecords
{
    protected static string $resource = GestionCajasResource::class;

    public function getTabs(): array
    {
        return [
            'principales' => Tab::make('Cajas Principales')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNull('id_caja_padre')),

            'hijas' => Tab::make('Cajas Hijas')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNotNull('id_caja_padre')),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->mutateDataUsing(function (array $data): array {
                    $data['id_empresa']   = (int) session('id_empresa');
                    $data['sucursal']     = (int) session('sucursal');
                    $data['saldo_actual'] = $data['saldo_actual'] ?? 0;
                    $data['moneda']       = 'PEN';

                    return $data;
                }),
        ];
    }
}
