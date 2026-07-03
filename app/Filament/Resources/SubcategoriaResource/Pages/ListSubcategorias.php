<?php

namespace App\Filament\Resources\SubcategoriaResource\Pages;

use App\Filament\Resources\SubcategoriaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSubcategorias extends ListRecords
{
    protected static string $resource = SubcategoriaResource::class;

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
