<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RecepcionResource\Pages;
use App\Models\InventarioMovimiento;
use App\Models\Producto;
use App\Models\Recepcion;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class RecepcionResource extends Resource
{
    protected static ?string $model = Recepcion::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-inbox-arrow-down';
    protected static ?string $navigationLabel = 'Recepción';
    protected static string|\UnitEnum|null $navigationGroup = 'Almacén';
    protected static ?int $navigationSort = 4;
    protected static ?string $label = 'Recepción';
    protected static ?string $pluralLabel = 'Registro de Recepciones';
    protected static ?string $slug = 'recepciones';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id_recepcion')
                    ->label('N°')
                    ->sortable(),

                TextColumn::make('fecha')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('compra_doc')
                    ->label('Compra')
                    ->getStateUsing(fn (Recepcion $record): string =>
                        $record->compra
                            ? (trim("{$record->compra->serie}-{$record->compra->numero}", '-') ?: "#{$record->id_compra}")
                            : "#{$record->id_compra}"),

                TextColumn::make('proveedor')
                    ->label('Proveedor')
                    ->getStateUsing(fn (Recepcion $record): string =>
                        $record->compra?->proveedor?->razon_social
                        ?? $record->compra?->proveedor?->nombre_comercial
                        ?? '—')
                    ->wrap()
                    ->limit(40),

                TextColumn::make('almacen')
                    ->label('Almacén')
                    ->formatStateUsing(fn (?string $state): string =>
                        KardexResource::almacenes()[$state] ?? ($state ?: '—')),

                TextColumn::make('items')
                    ->label('Ítems')
                    ->badge()
                    ->getStateUsing(fn (Recepcion $record): int =>
                        DB::table('recepcion_detalle')->where('id_recepcion', $record->id_recepcion)->count()),

                TextColumn::make('usuario.nombres')
                    ->label('Usuario')
                    ->placeholder('—')
                    ->toggleable(),

                TextColumn::make('observacion')
                    ->label('Observación')
                    ->wrap()
                    ->limit(40)
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                Action::make('detalle')
                    ->label('Detalle')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->modalHeading(fn (Recepcion $record): string => "Recepción #{$record->id_recepcion}")
                    ->modalContent(fn (Recepcion $record) => view('filament.modals.recepcion-detalle', [
                        'lineas' => DB::table('recepcion_detalle as d')
                            ->join('productos as p', 'p.id_producto', '=', 'd.id_producto')
                            ->where('d.id_recepcion', $record->id_recepcion)
                            ->select('p.codigo', 'p.descripcion as producto', 'p.medida as unidad', 'd.cantidad')
                            ->get(),
                    ]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Cerrar'),

                Action::make('deshacer')
                    ->label('Deshacer')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('¿Deshacer esta recepción?')
                    ->modalDescription('Se revertirá el stock ingresado y la compra volverá a estar pendiente de recepción.')
                    ->action(function (Recepcion $record): void {
                        try {
                            static::deshacerRecepcion($record);
                            Notification::make()->success()->title('Recepción deshecha')->body('Stock revertido correctamente.')->send();
                        } catch (\Throwable $e) {
                            Notification::make()->danger()->title('Error al deshacer')->body($e->getMessage())->send();
                        }
                    }),
            ])
            ->defaultSort('id_recepcion', 'desc');
    }

    protected static function deshacerRecepcion(Recepcion $record): void
    {
        $emp = (int) session('id_empresa');
        $uid = (int) auth()->user()->usuario_id;

        DB::transaction(function () use ($record, $emp, $uid): void {
            $detalles = DB::table('recepcion_detalle')->where('id_recepcion', $record->id_recepcion)->get();

            foreach ($detalles as $d) {
                static::revertirStock(
                    $emp,
                    (string) $record->almacen,
                    (int) $d->id_producto,
                    (int) $d->cantidad,
                    "Deshacer recepción #{$record->id_recepcion} (compra #{$record->id_compra})",
                    $uid
                );
            }

            DB::table('recepcion_detalle')->where('id_recepcion', $record->id_recepcion)->delete();
            DB::table('recepciones')->where('id_recepcion', $record->id_recepcion)->delete();

            // Recalcular estado de la compra: 0 pendiente · 2 parcial · 1 completo
            $totalPedido   = (int) DB::table('productos_compras')->where('id_compra', $record->id_compra)->sum('cantidad');
            $totalRecibido = (int) DB::table('recepcion_detalle as rd')
                ->join('recepciones as rc', 'rc.id_recepcion', '=', 'rd.id_recepcion')
                ->where('rc.id_compra', $record->id_compra)
                ->sum('rd.cantidad');
            $estado = ($totalPedido > 0 && $totalRecibido >= $totalPedido) ? 1 : ($totalRecibido > 0 ? 2 : 0);
            DB::table('compras')->where('id_compra', $record->id_compra)->update(['recepcionado' => $estado]);
        });
    }

    protected static function revertirStock(int $emp, string $almacen, int $sourceId, int $cant, string $obs, int $uid): void
    {
        $source = Producto::where('id_empresa', $emp)->where('id_producto', $sourceId)->first();
        if (! $source) {
            return;
        }

        $dest = null;
        if (! empty($source->codigo)) {
            $dest = Producto::where('id_empresa', $emp)
                ->where('almacen', $almacen)
                ->where('codigo', $source->codigo)
                ->lockForUpdate()
                ->first();
        }
        if (! $dest && (string) $source->almacen === $almacen) {
            $dest = $source;
        }
        if (! $dest) {
            return;
        }

        $ant   = (int) $dest->cantidad;
        $nuevo = max(0, $ant - $cant);
        $dest->update(['cantidad' => $nuevo]);

        InventarioMovimiento::create([
            'id_empresa' => $emp, 'almacen' => $almacen, 'id_producto' => $dest->id_producto,
            'tipo' => 'S', 'id_motivo' => null, 'cantidad' => $cant,
            'stock_anterior' => $ant, 'stock_nuevo' => $nuevo, 'costo' => null,
            'observacion' => $obs, 'id_usuario' => $uid, 'fecha' => now(),
        ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('id_empresa', (int) session('id_empresa'))
            ->with(['compra.proveedor', 'usuario']);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRecepciones::route('/'),
        ];
    }
}
