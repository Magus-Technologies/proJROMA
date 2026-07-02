<?php

namespace App\Filament\Resources\MercadoResource\Pages;

use App\Filament\Resources\MercadoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMercados extends ListRecords
{
    protected static string $resource = MercadoResource::class;

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
