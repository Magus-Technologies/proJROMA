<?php

namespace App\Filament\Resources;

use App\Models\Cotizacion;
use App\Models\DocumentoEmpresa;
use App\Filament\Resources\CotizacionResource\Pages;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CotizacionResource extends Resource
{
    protected static ?string $model = Cotizacion::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Cotizaciones';
    protected static string|\UnitEnum|null $navigationGroup = 'Cotizaciones';
    protected static ?int    $navigationSort  = 1;
    protected static ?string $label           = 'Cotización';
    protected static ?string $pluralLabel     = 'Cotizaciones';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('numero')->label('N°')->sortable()->searchable()->toggleable(),
                TextColumn::make('cliente.datos')->label('Cliente')->searchable()->wrap()->toggleable(),
                TextColumn::make('usuario.nombres')->label('Vendedor')->sortable()->searchable()->toggleable(),
                TextColumn::make('fecha')->label('Fecha')->date('d/m/Y')->sortable()->toggleable(),
                TextColumn::make('fecha_registro')->label('Registrado')->date('d/m/Y')->sortable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('total')->label('Total')->money('PEN')->sortable()->toggleable(),
                TextColumn::make('observacion')->label('Observación')->wrap()->limit(40)->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('estado')->label('Estado')
                    ->badge()
                    ->toggleable()
                    ->color(fn (string $state): string => match ($state) {
                        '1'     => 'success',
                        '3'     => 'warning',
                        default => 'danger',
                    })
                    ->formatStateUsing(fn ($state) => match ($state) {
                        '1'     => 'Activa',
                        '3'     => 'Facturado',
                        default => 'Anulada',
                    }),
            ])
            ->filters([
                SelectFilter::make('estado')
                    ->label('Estado')
                    ->options(['1' => 'Activa', '0' => 'Anulada', '3' => 'Facturado']),
                Filter::make('fecha_rango')
                    ->label('Rango de fechas')
                    ->form([
                        DatePicker::make('fecha_desde')->label('Desde'),
                        DatePicker::make('fecha_hasta')->label('Hasta'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when($data['fecha_desde'], fn ($q, $v) => $q->whereDate('fecha', '>=', $v))
                            ->when($data['fecha_hasta'], fn ($q, $v) => $q->whereDate('fecha', '<=', $v));
                    }),
            ])
            ->actions([
                \App\Filament\Actions\PdfPreviewAction::make(
                    pdfUrl: fn (Cotizacion $record): string => route('cotizacion.reporte', $record->cotizacion_id),
                    titulo: function (Cotizacion $record, $livewire): string {
                        $doc = 'Cotización COT-' . str_pad((string) $record->numero, 8, '0', STR_PAD_LEFT);
                        $esNueva = property_exists($livewire, 'recienCreada')
                            && (int) $livewire->recienCreada === (int) $record->cotizacion_id;

                        return $esNueva ? "¡Cotización creada exitosamente! — {$doc}" : $doc;
                    },
                    whatsappUrl: function (Cotizacion $record): string {
                        $doc     = 'COT-' . str_pad((string) $record->numero, 8, '0', STR_PAD_LEFT);
                        $mensaje = rawurlencode(
                            "Hola! Le comparto la cotización {$doc} por S/ " . number_format($record->total, 2)
                            . ".\n" . route('cotizacion.reporte', $record->cotizacion_id)
                        );
                        $telefono = preg_replace('/\D/', '', (string) $record->cliente?->telefono);
                        $telefono = strlen($telefono) === 9 ? "51{$telefono}" : $telefono;

                        return $telefono
                            ? "https://wa.me/{$telefono}?text={$mensaje}"
                            : "https://wa.me/?text={$mensaje}";
                    },
                    emailUrl: function (Cotizacion $record): string {
                        $doc = 'COT-' . str_pad((string) $record->numero, 8, '0', STR_PAD_LEFT);

                        return 'mailto:' . ($record->cliente?->email ?? '')
                            . '?subject=' . rawurlencode("Cotización {$doc}")
                            . '&body=' . rawurlencode(
                                "Estimado cliente,\n\nLe compartimos la cotización {$doc} por S/ "
                                . number_format($record->total, 2)
                                . ".\n\nPuede verla aquí: " . route('cotizacion.reporte', $record->cotizacion_id)
                            );
                    },
                    accionesExtra: [
                        Action::make('convertir_desde_preview')
                            ->label('Convertir a Venta')
                            ->icon('heroicon-m-arrow-path')
                            ->color('warning')
                            ->visible(fn (Cotizacion $record): bool => $record->estado === '1' && ! $record->id_venta)
                            ->url(fn (Cotizacion $record): string =>
                                \App\Filament\Resources\VentaResource::getUrl('create', ['cotizacion' => $record->cotizacion_id])),
                    ],
                )->iconButton()->tooltip('Vista previa'),

                ActionGroup::make([
                Action::make('editar')
                    ->label('Editar')
                    ->icon('heroicon-m-pencil')
                    ->color('info')
                    ->url(fn (Cotizacion $record) => route('cotizaciones.edit', $record->cotizacion_id))
                    ->visible(fn (Cotizacion $record) => $record->estado === '1'),

                Action::make('pdf')
                    ->label('PDF')
                    ->icon('heroicon-m-document-arrow-down')
                    ->color('gray')
                    ->url(fn (Cotizacion $record) => route('cotizacion.reporte', $record->cotizacion_id))
                    ->openUrlInNewTab(),

                Action::make('convertir')
                    ->label('Convertir')
                    ->icon('heroicon-m-arrow-path')
                    ->color('warning')
                    ->visible(fn (Cotizacion $record) => $record->estado === '1' && !$record->id_venta)
                    ->url(fn (Cotizacion $record): string =>
                        \App\Filament\Resources\VentaResource::getUrl('create', ['cotizacion' => $record->cotizacion_id])),

                Action::make('anular')
                    ->label('Anular')
                    ->icon('heroicon-m-x-circle')
                    ->color('danger')
                    ->visible(fn (Cotizacion $record) => $record->estado === '1')
                    ->requiresConfirmation()
                    ->modalHeading('Anular cotización')
                    ->modalDescription('¿Confirmás que querés anular esta cotización? Esta acción no se puede deshacer.')
                    ->modalSubmitActionLabel('Sí, anular')
                    ->action(function (Cotizacion $record) {
                        if ($record->estado !== '1') {
                            Notification::make()->warning()->title('La cotización ya está anulada.')->send();
                            return;
                        }
                        $record->update(['estado' => '0']);
                        Notification::make()->success()->title('Cotización anulada.')->send();
                    }),
                ])->tooltip('Acciones'),
            ])
            ->defaultSort('fecha', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['cliente', 'usuario'])
            ->where('id_empresa', (int) session('id_empresa'));
    }

    public static function getRelations(): array { return []; }

    public static function convertirAVenta(Cotizacion $record, int $idTido): void
    {
        if ($record->estado !== '1') {
            Notification::make()->warning()->title('Solo se pueden convertir cotizaciones activas.')->send();
            return;
        }
        if ($record->id_venta) {
            Notification::make()->warning()->title('Esta cotización ya fue convertida a venta.')->send();
            return;
        }

        $empresa  = (int) session('id_empresa');
        $sucursal = (int) session('sucursal');

        DB::beginTransaction();
        try {
            $record->load('productos');

            $tido = DocumentoEmpresa::where('id_empresa', $empresa)
                ->where('sucursal', $sucursal)
                ->where('id_tido', $idTido)
                ->lockForUpdate()
                ->firstOrFail();

            $numero = $tido->numero + 1;
            $serie  = $tido->serie;

            $idVenta = DB::table('ventas')->insertGetId([
                'id_tido'           => $idTido,
                'id_tipo_pago'      => $record->id_tipo_pago,
                'fecha_emision'     => now()->toDateString(),
                'fecha_vencimiento' => now()->toDateString(),
                'dias_pagos'        => $record->dias_pagos,
                'direccion'         => $record->direccion ?? '-',
                'serie'             => $serie,
                'numero'            => $numero,
                'id_cliente'        => $record->id_cliente,
                'total'             => $record->total,
                'igv'               => round($record->total - ($record->total / 1.18), 2),
                'apli_igv'          => '1',
                'estado'            => '1',
                'enviado_sunat'     => '0',
                'id_empresa'        => $empresa,
                'sucursal'          => $sucursal,
                'id_vendedor'       => auth()->id(),
                'observacion'       => 'Convertido de cotización N° ' . $record->numero,
                'pagado'            => '0',
                'id_coti'           => $record->cotizacion_id,
            ]);

            $tido->increment('numero');

            foreach ($record->productos as $prod) {
                DB::table('productos_ventas')->insert([
                    'id_venta'     => $idVenta,
                    'id_producto'  => $prod->id_producto,
                    'cantidad'     => $prod->cantidad,
                    'precio'       => $prod->precio,
                    'costo'        => $prod->costo ?? 0,
                    'medida'       => $prod->medida ?? '',
                    'presenta'     => $prod->presenta ?? '',
                    'presenta_cnt' => $prod->presenta_cnt ?? 0,
                ]);
            }

            $record->update(['estado' => '3', 'id_venta' => $idVenta]);

            DB::commit();

            $doc = "{$serie}-" . str_pad($numero, 8, '0', STR_PAD_LEFT);
            Notification::make()->success()->title("Venta {$doc} generada correctamente.")->send();

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error convertir cotización: ' . $e->getMessage());
            Notification::make()->danger()->title('Error al convertir la cotización.')->send();
        }
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCotizaciones::route('/'),
            'create' => Pages\CreateCotizacion::route('/create'),
        ];
    }
}
