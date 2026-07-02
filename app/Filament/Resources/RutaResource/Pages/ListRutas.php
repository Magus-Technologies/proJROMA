<?php

namespace App\Filament\Resources\RutaResource\Pages;

use App\Filament\Resources\RutaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRutas extends ListRecords
{
    protected static string $resource = RutaResource::class;

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
