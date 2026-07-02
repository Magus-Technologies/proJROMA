<?php

namespace App\Filament\Resources\ReporteDeudaResource\Pages;

use App\Filament\Resources\ReporteDeudaResource;
use Filament\Resources\Pages\ListRecords;

class ListReporteDeudas extends ListRecords
{
    protected static string $resource = ReporteDeudaResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
