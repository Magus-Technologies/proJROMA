<?php
namespace App\Filament\Resources\GuiaRemisionResource\Pages;
use App\Filament\Resources\GuiaRemisionResource;
use Filament\Resources\Pages\ListRecords;
class ListGuiasRemision extends ListRecords {
    protected static string $resource = GuiaRemisionResource::class;
    protected function getHeaderActions(): array { return []; }
}
