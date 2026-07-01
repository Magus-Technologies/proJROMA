<?php

namespace App\Filament\Resources\CompraResource\Pages;

use App\Filament\Resources\CompraResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListCompras extends ListRecords
{
    protected static string $resource = CompraResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('nueva_compra')
                ->label('Nueva Compra')
                ->icon('heroicon-m-plus')
                ->color('primary')
                ->url(url('/compras/add')),
        ];
    }
}
