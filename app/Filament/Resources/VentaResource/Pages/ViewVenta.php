<?php

namespace App\Filament\Resources\VentaResource\Pages;

use App\Filament\Resources\VentaResource;
use App\Models\Venta;
use Filament\Actions\Action;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ViewVenta extends ViewRecord
{
    protected static string $resource = VentaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('pdf_a4')
                ->label('PDF A4')
                ->icon('heroicon-o-document-text')
                ->color('danger')
                ->url(fn (): string => url("/venta/comprobante/pdf/{$this->record->id_venta}"))
                ->openUrlInNewTab(),

            Action::make('voucher')
                ->label('Voucher 8cm')
                ->icon('heroicon-o-printer')
                ->color('gray')
                ->url(fn (): string => url("/venta/pdf/voucher/8cm/{$this->record->id_venta}"))
                ->openUrlInNewTab(),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Datos de la Venta')
                    ->schema([
                        TextEntry::make('documento_completo')
                            ->label('Documento'),

                        TextEntry::make('tipoDocumento.tipo_doc')
                            ->label('Tipo')
                            ->placeholder('—'),

                        TextEntry::make('fecha_emision')
                            ->label('Fecha')
                            ->date('d/m/Y'),

                        TextEntry::make('cliente.datos')
                            ->label('Cliente'),

                        TextEntry::make('vendedor.nombre_completo')
                            ->label('Vendedor')
                            ->placeholder('—'),

                        TextEntry::make('total')
                            ->label('Total')
                            ->money('PEN'),

                        TextEntry::make('estado_sunat')
                            ->label('Estado SUNAT')
                            ->badge()
                            ->getStateUsing(fn (Venta $record): string =>
                                $record->sunat?->estado_sunat ?? 'NO ENVIADO')
                            ->color(fn (string $state): string => match ($state) {
                                'ACEPTADO'   => 'success',
                                'NO ENVIADO' => 'danger',
                                default      => 'warning',
                            }),

                        TextEntry::make('estado')
                            ->label('Estado')
                            ->badge()
                            ->formatStateUsing(fn (string $state): string => $state === '1' ? 'Activa' : 'Anulada')
                            ->color(fn (string $state): string => $state === '1' ? 'success' : 'gray'),

                        TextEntry::make('observacion')
                            ->label('Observación')
                            ->placeholder('—')
                            ->columnSpanFull(),
                    ])
                    ->columns(4),

                Section::make('Productos')
                    ->schema([
                        RepeatableEntry::make('productosVenta')
                            ->hiddenLabel()
                            ->schema([
                                TextEntry::make('descripcion')
                                    ->label('Descripción')
                                    ->columnSpan(2),

                                TextEntry::make('cantidad')
                                    ->label('Cantidad'),

                                TextEntry::make('precio')
                                    ->label('P. Unit')
                                    ->money('PEN'),

                                TextEntry::make('total')
                                    ->label('Total')
                                    ->money('PEN'),
                            ])
                            ->columns(5),
                    ]),
            ]);
    }
}
