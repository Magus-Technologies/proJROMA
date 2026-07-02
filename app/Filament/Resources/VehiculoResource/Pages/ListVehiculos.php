<?php

namespace App\Filament\Resources\VehiculoResource\Pages;

use App\Filament\Resources\VehiculoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListVehiculos extends ListRecords
{
    protected static string $resource = VehiculoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->mutateDataUsing(function (array $data): array {
                    $data['id_empresa'] = (int) session('id_empresa');
                    $data['sucursal']   = (int) session('sucursal');
                    $data['estado']     = 1;
                    if (!empty($data['placa'])) {
                        $data['placa'] = strtoupper(trim($data['placa']));
                    }

                    return $data;
                }),
        ];
    }
}
