<?php

namespace App\Filament\Resources\ArqueoDiarioResource\Pages;

use App\Filament\Resources\ArqueoDiarioResource;
use Filament\Resources\Pages\ListRecords;

class ListArqueoDiario extends ListRecords
{
    protected static string $resource = ArqueoDiarioResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
