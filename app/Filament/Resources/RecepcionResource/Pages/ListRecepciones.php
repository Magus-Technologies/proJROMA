<?php

namespace App\Filament\Resources\RecepcionResource\Pages;

use App\Filament\Resources\RecepcionResource;
use Filament\Resources\Pages\ListRecords;

class ListRecepciones extends ListRecords
{
    protected static string $resource = RecepcionResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
