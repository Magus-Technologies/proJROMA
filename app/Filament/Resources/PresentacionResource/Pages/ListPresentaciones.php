<?php

namespace App\Filament\Resources\PresentacionResource\Pages;

use App\Filament\Resources\PresentacionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPresentaciones extends ListRecords
{
    protected static string $resource = PresentacionResource::class;

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
