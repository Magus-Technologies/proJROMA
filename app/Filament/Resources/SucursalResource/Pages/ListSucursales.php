<?php

namespace App\Filament\Resources\SucursalResource\Pages;

use App\Filament\Resources\SucursalResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSucursales extends ListRecords
{
    protected static string $resource = SucursalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->mutateDataUsing(function (array $data): array {
                    $data['empresa_id'] = (int) session('id_empresa');

                    return $data;
                }),
        ];
    }
}
