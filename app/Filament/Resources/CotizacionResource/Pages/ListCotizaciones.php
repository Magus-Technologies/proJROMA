<?php

namespace App\Filament\Resources\CotizacionResource\Pages;

use App\Filament\Pages\CrearCotizacion;
use App\Filament\Resources\CotizacionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCotizaciones extends ListRecords
{
    protected static string $resource = CotizacionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('nueva_cotizacion')
                ->label('Nueva Cotización')
                ->icon('heroicon-o-plus')
                ->color('primary')
                ->url(CrearCotizacion::getUrl()),
        ];
    }
}
