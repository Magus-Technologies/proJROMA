<?php

namespace App\Filament\Resources\UnidadMedidaResource\Pages;

use App\Filament\Resources\UnidadMedidaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListUnidadesMedida extends ListRecords
{
    protected static string $resource = UnidadMedidaResource::class;

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
