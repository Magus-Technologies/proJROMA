<?php

namespace App\Filament\Resources\MotivoMovimientoResource\Pages;

use App\Filament\Resources\MotivoMovimientoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMotivoMovimientos extends ListRecords
{
    protected static string $resource = MotivoMovimientoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->mutateDataUsing(function (array $data): array {
                    $data['id_empresa'] = (int) session('id_empresa');
                    return $data;
                }),
        ];
    }
}
