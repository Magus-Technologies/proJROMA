<?php

namespace App\Filament\Resources\VentaResource\Pages;

use App\Filament\Resources\VentaResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;
use Filament\Schemas\Components;

class ViewVenta extends ViewRecord
{
    protected static string $resource = VentaResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Components\Section::make('Datos de la Venta')
                    ->schema([
                        Components\TextEntry::make('documento_completo')
                            ->label('Documento'),
                        Components\TextEntry::make('cliente.datos')
                            ->label('Cliente'),
                        Components\TextEntry::make('fecha_emision')
                            ->label('Fecha')
                            ->date('d/m/Y'),
                        Components\TextEntry::make('total')
                            ->label('Total')
                            ->money('PEN'),
                        Components\TextEntry::make('estado')
                            ->label('Estado')
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                '1' => 'Activa',
                                '0' => 'Anulada',
                                default => $state,
                            }),
                    ])
                    ->columns(3),
            ]);
    }
}
