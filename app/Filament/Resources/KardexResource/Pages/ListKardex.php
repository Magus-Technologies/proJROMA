<?php

namespace App\Filament\Resources\KardexResource\Pages;

use App\Filament\Resources\KardexResource;
use Filament\Resources\Pages\ListRecords;

class ListKardex extends ListRecords
{
    protected static string $resource = KardexResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
