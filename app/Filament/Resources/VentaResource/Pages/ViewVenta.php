<?php

namespace App\Filament\Resources\VentaResource\Pages;

use App\Filament\Resources\VentaResource;
use App\Models\Venta;
use Filament\Actions\Action;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\RepeatableEntry\TableColumn;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;

class ViewVenta extends ViewRecord
{
    protected static string $resource = VentaResource::class;

    public function getTitle(): string
    {
        return 'Venta ' . $this->record->documento_completo;
    }

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
                Grid::make(['default' => 1, 'xl' => 3])
                    ->columnSpanFull()
                    ->schema([
                        // ── IZQUIERDA (ancha): productos + cuotas ──
                        Group::make([
                            Section::make('Productos')
                                ->compact()
                                ->schema([
                                    RepeatableEntry::make('productosVenta')
                                        ->hiddenLabel()
                                        ->table([
                                            TableColumn::make('Descripción'),
                                            TableColumn::make('Cant.')->width('90px'),
                                            TableColumn::make('P. Unit')->width('120px'),
                                            TableColumn::make('Total')->width('120px'),
                                        ])
                                        ->schema([
                                            TextEntry::make('descripcion')
                                                ->hiddenLabel(),
                                            TextEntry::make('cantidad')
                                                ->hiddenLabel()
                                                ->numeric(),
                                            TextEntry::make('precio')
                                                ->hiddenLabel()
                                                ->money('PEN'),
                                            TextEntry::make('total')
                                                ->hiddenLabel()
                                                ->money('PEN')
                                                ->weight(FontWeight::SemiBold),
                                        ]),
                                ]),

                            Section::make('Cuotas de pago')
                                ->compact()
                                ->visible(fn (Venta $record): bool => $record->pagos->isNotEmpty())
                                ->schema([
                                    RepeatableEntry::make('pagos')
                                        ->hiddenLabel()
                                        ->table([
                                            TableColumn::make('Vencimiento'),
                                            TableColumn::make('Monto')->width('130px'),
                                            TableColumn::make('Tipo de pago')->width('140px'),
                                            TableColumn::make('Estado')->width('120px'),
                                        ])
                                        ->schema([
                                            TextEntry::make('fecha')
                                                ->hiddenLabel()
                                                ->date('d/m/Y'),
                                            TextEntry::make('monto')
                                                ->hiddenLabel()
                                                ->money('PEN'),
                                            TextEntry::make('tipo_pago')
                                                ->hiddenLabel()
                                                ->placeholder('—'),
                                            TextEntry::make('estado')
                                                ->hiddenLabel()
                                                ->badge()
                                                ->formatStateUsing(fn (string $state): string => $state === '1' ? 'Pagada' : 'Pendiente')
                                                ->color(fn (string $state): string => $state === '1' ? 'success' : 'danger'),
                                        ]),
                                ]),
                        ])->columnSpan(['default' => 1, 'xl' => 2]),

                        // ── DERECHA (angosta): comprobante, cliente, totales ──
                        Group::make([
                            Section::make('Comprobante')
                                ->compact()
                                ->columns(2)
                                ->schema([
                                    TextEntry::make('documento_completo')
                                        ->label('Documento')
                                        ->size(TextSize::Large)
                                        ->weight(FontWeight::Bold)
                                        ->color('primary'),

                                    TextEntry::make('tipoDocumento.tipo_doc')
                                        ->label('Tipo')
                                        ->placeholder('—'),

                                    TextEntry::make('fecha_emision')
                                        ->label('Emisión')
                                        ->date('d/m/Y'),

                                    TextEntry::make('fecha_vencimiento')
                                        ->label('Vencimiento')
                                        ->date('d/m/Y')
                                        ->placeholder('—')
                                        ->visible(fn (Venta $record): bool => $record->id_tipo_pago == 2),

                                    TextEntry::make('estado')
                                        ->label('Estado')
                                        ->badge()
                                        ->formatStateUsing(fn (string $state): string => match ($state) {
                                            '1'     => 'Activa',
                                            '2'     => 'Crédito',
                                            '0'     => 'Anulada',
                                            default => $state,
                                        })
                                        ->color(fn (string $state): string => match ($state) {
                                            '1'     => 'success',
                                            '2'     => 'warning',
                                            '0'     => 'gray',
                                            default => 'gray',
                                        }),

                                    TextEntry::make('estado_sunat')
                                        ->label('SUNAT')
                                        ->badge()
                                        ->getStateUsing(fn (Venta $record): string =>
                                            $record->sunat?->estado_sunat ?? 'NO ENVIADO')
                                        ->color(fn (string $state): string => match ($state) {
                                            'ACEPTADO'   => 'success',
                                            'NO ENVIADO' => 'danger',
                                            default      => 'warning',
                                        }),

                                    TextEntry::make('observacion')
                                        ->label('Observación')
                                        ->placeholder('—')
                                        ->columnSpanFull(),
                                ]),

                            Section::make('Cliente')
                                ->compact()
                                ->schema([
                                    TextEntry::make('cliente.datos')
                                        ->hiddenLabel()
                                        ->weight(FontWeight::SemiBold)
                                        ->placeholder('— Sin cliente —'),

                                    TextEntry::make('cliente.documento')
                                        ->label('RUC/DNI')
                                        ->placeholder('—')
                                        ->inlineLabel(),

                                    TextEntry::make('vendedor.nombre_completo')
                                        ->label('Vendedor')
                                        ->placeholder('—')
                                        ->inlineLabel(),
                                ]),

                            Section::make('Totales')
                                ->compact()
                                ->schema([
                                    TextEntry::make('subtotal')
                                        ->label('Op. Gravadas')
                                        ->money('PEN')
                                        ->inlineLabel(),

                                    TextEntry::make('igv')
                                        ->label('IGV (18%)')
                                        ->money('PEN')
                                        ->inlineLabel(),

                                    TextEntry::make('total')
                                        ->label('IMPORTE TOTAL')
                                        ->money('PEN')
                                        ->size(TextSize::Large)
                                        ->weight(FontWeight::Bold)
                                        ->color('primary')
                                        ->inlineLabel(),
                                ]),
                        ])->columnSpan(1),
                    ]),
            ]);
    }
}
