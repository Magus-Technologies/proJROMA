<?php

namespace App\Filament\Resources\VentaResource\Pages;

use App\Filament\Resources\VentaResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListVentas extends ListRecords
{
    protected static string $resource = VentaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('nueva_venta')
                ->label('Nueva Venta')
                ->icon('heroicon-o-plus')
                ->color('primary')
                ->url(url('/ventas/productos')),

            Action::make('excel')
                ->label('Excel')
                ->icon('heroicon-o-table-cells')
                ->color('success')
                ->url(url('/reporte/excel/' . now()->format('Y-m')))
                ->openUrlInNewTab(),

            Action::make('pdf')
                ->label('PDF')
                ->icon('heroicon-o-document-text')
                ->color('danger')
                ->url(url('/reporte/ventas'))
                ->openUrlInNewTab(),
        ];
    }
}
