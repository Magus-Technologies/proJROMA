<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VentaResource\Pages;
use App\Models\InventarioMovimiento;
use App\Models\MotivoMovimiento;
use App\Models\Producto;
use App\Models\Venta;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class VentaResource extends Resource
{
    protected static ?string $model = Venta::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationLabel = 'Ventas';
    protected static string|\UnitEnum|null $navigationGroup = 'Facturación';
    protected static ?int    $navigationSort  = 1;
    protected static ?string $pluralLabel = 'Ventas';
    protected static ?string $label = 'Venta';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id_venta')
                    ->label('#')
                    ->sortable(),

                TextColumn::make('tipoDocumento.tipo_doc')
                    ->label('Tipo')
                    ->placeholder('—')
                    ->toggleable(),

                TextColumn::make('documento_completo')
                    ->label('Documento')
                    ->searchable(query: fn (Builder $query, string $search): Builder =>
                        $query->where(fn (Builder $q) => $q
                            ->where('serie', 'like', "%{$search}%")
                            ->orWhere('numero', 'like', "%{$search}%")))
                    ->sortable(query: fn (Builder $query, string $direction): Builder =>
                        $query->orderBy('serie', $direction)->orderBy('numero', $direction)),

                TextColumn::make('fecha_emision')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('cliente.datos')
                    ->label('Cliente')
                    ->searchable()
                    ->wrap()
                    ->limit(40),

                TextColumn::make('total')
                    ->label('Total')
                    ->money('PEN')
                    ->sortable(),

                TextColumn::make('estado_sunat')
                    ->label('SUNAT')
                    ->badge()
                    ->getStateUsing(fn (Venta $record): string =>
                        $record->sunat?->estado_sunat ?? 'NO ENVIADO')
                    ->color(fn (string $state): string => match ($state) {
                        'ACEPTADO'   => 'success',
                        'NO ENVIADO' => 'danger',
                        default      => 'warning',
                    }),

                TextColumn::make('estado')
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

                TextColumn::make('vendedor.nombre_completo')
                    ->label('Vendedor')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('observacion')
                    ->label('Observación')
                    ->wrap()
                    ->limit(40)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('estado')
                    ->label('Estado')
                    ->options([
                        '1' => 'Activa',
                        '2' => 'Crédito',
                        '0' => 'Anulada',
                    ]),

                Filter::make('fecha_emision')
                    ->form([
                        DatePicker::make('desde')->label('Desde'),
                        DatePicker::make('hasta')->label('Hasta'),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query
                        ->when($data['desde'], fn (Builder $q) => $q->whereDate('fecha_emision', '>=', $data['desde']))
                        ->when($data['hasta'], fn (Builder $q) => $q->whereDate('fecha_emision', '<=', $data['hasta']))),
            ])
            ->actions([
                ActionGroup::make([
                ViewAction::make()
                    ->label('Ver detalle'),

                Action::make('pdf_a4')
                    ->label('PDF A4')
                    ->icon('heroicon-m-document-arrow-down')
                    ->color('danger')
                    ->url(fn (Venta $record): string => url("/venta/comprobante/pdf/{$record->id_venta}"))
                    ->openUrlInNewTab(),

                Action::make('voucher')
                    ->label('Voucher 8cm')
                    ->icon('heroicon-m-printer')
                    ->color('gray')
                    ->url(fn (Venta $record): string => url("/venta/pdf/voucher/8cm/{$record->id_venta}"))
                    ->openUrlInNewTab(),

                Action::make('anular')
                    ->label('Anular')
                    ->icon('heroicon-o-no-symbol')
                    ->color('danger')
                    ->visible(fn (Venta $record): bool => $record->estado !== '0')
                    ->requiresConfirmation()
                    ->modalHeading('¿Anular esta venta?')
                    ->modalDescription('Se repondrá el stock de los productos.')
                    ->action(function (Venta $record): void {
                        DB::beginTransaction();
                        try {
                            $record->loadMissing('productosVenta');

                            $motivo = MotivoMovimiento::where('id_empresa', (int) session('id_empresa'))
                                ->where('nombre', 'Venta')
                                ->value('id_motivo');
                            $doc = "{$record->serie}-" . str_pad($record->numero, 8, '0', STR_PAD_LEFT);

                            foreach ($record->productosVenta as $det) {
                                $p = Producto::find($det->id_producto);
                                if (! $p) {
                                    continue;
                                }

                                $ant  = (int) $p->cantidad;
                                $cant = (int) $det->cantidad;
                                $p->increment('cantidad', $det->cantidad);

                                InventarioMovimiento::create([
                                    'id_empresa'     => (int) session('id_empresa'),
                                    'almacen'        => $p->almacen ?? '',
                                    'id_producto'    => $p->id_producto,
                                    'tipo'           => 'I',
                                    'id_motivo'      => $motivo,
                                    'cantidad'       => $cant,
                                    'stock_anterior' => $ant,
                                    'stock_nuevo'    => $ant + $cant,
                                    'costo'          => $p->costo,
                                    'observacion'    => "Anulación de venta {$doc}",
                                    'id_usuario'     => (int) (auth()->user()->usuario_id ?? 0),
                                    'fecha'          => now(),
                                ]);
                            }

                            $record->update(['estado' => '0']);
                            DB::commit();

                            Notification::make()->success()
                                ->title('Venta anulada')
                                ->body('Stock repuesto correctamente.')
                                ->send();
                        } catch (\Throwable $e) {
                            DB::rollBack();
                            Notification::make()->danger()
                                ->title('Error al anular la venta')
                                ->body($e->getMessage())
                                ->send();
                        }
                    }),
                ]),
            ])
            ->defaultSort('id_venta', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListVentas::route('/'),
            'view'   => Pages\ViewVenta::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('id_empresa', (int) session('id_empresa'))
            ->where('sucursal', (int) session('sucursal'))
            ->with(['cliente', 'vendedor', 'tipoDocumento', 'sunat']);
    }
}
