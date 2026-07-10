<?php

namespace App\Filament\Resources\KardexResource\Pages;

use App\Filament\Resources\KardexResource;
use App\Filament\Resources\KardexResource\Widgets\KardexStats;
use Filament\Resources\Pages\ListRecords;

class ListKardex extends ListRecords
{
    protected static string $resource = KardexResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            KardexStats::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
