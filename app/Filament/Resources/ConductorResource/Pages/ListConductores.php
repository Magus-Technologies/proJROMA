<?php

namespace App\Filament\Resources\ConductorResource\Pages;

use App\Filament\Resources\ConductorResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListConductores extends ListRecords
{
    protected static string $resource = ConductorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->mutateDataUsing(function (array $data): array {
                    $data['id_empresa'] = (int) session('id_empresa');
                    $data['sucursal']   = (int) session('sucursal');
                    $data['estado']     = 1;

                    return $data;
                }),
        ];
    }
}
