<?php

namespace App\Filament\Resources\GuiaRemisionResource\Pages;

use App\Filament\Resources\GuiaRemisionResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListGuiasRemision extends ListRecords
{
    protected static string $resource = GuiaRemisionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('nueva_guia')
                ->label('Nueva Guía')
                ->icon('heroicon-o-plus')
                ->color('primary')
                ->url(route('guias.create')),
        ];
    }
}
