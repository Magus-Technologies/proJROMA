<?php

namespace App\Filament\Resources\TipoVehiculoResource\Pages;

use App\Filament\Resources\TipoVehiculoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTipoVehiculos extends ListRecords
{
    protected static string $resource = TipoVehiculoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->mutateDataUsing(function (array $data): array {
                    $data['id_empresa'] = (int) session('id_empresa');
                    $data['estado']     = 1;

                    return $data;
                }),
        ];
    }
}
