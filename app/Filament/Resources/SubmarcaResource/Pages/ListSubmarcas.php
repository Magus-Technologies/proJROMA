<?php

namespace App\Filament\Resources\SubmarcaResource\Pages;

use App\Filament\Resources\SubmarcaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSubmarcas extends ListRecords
{
    protected static string $resource = SubmarcaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->mutateDataUsing(function (array $data): array {
                    $data['id_empresa'] = (int) session('id_empresa');
                    $data['estado']     = '1';

                    return $data;
                }),
        ];
    }
}
