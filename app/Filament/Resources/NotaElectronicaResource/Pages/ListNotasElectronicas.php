<?php

namespace App\Filament\Resources\NotaElectronicaResource\Pages;

use App\Filament\Resources\NotaElectronicaResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListNotasElectronicas extends ListRecords
{
    protected static string $resource = NotaElectronicaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('nueva_nota')
                ->label('Nueva Nota')
                ->icon('heroicon-o-plus')
                ->color('primary')
                ->url(url('/nota/electronica')),
        ];
    }
}
