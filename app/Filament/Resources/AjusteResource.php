<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AjusteResource\Pages;
use App\Models\InventarioMovimiento;
use App\Models\MotivoMovimiento;
use App\Models\Producto;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class AjusteResource extends Resource
{
    protected static ?string $model = InventarioMovimiento::class;

    public const AUTOMATIZADOS = ['Compra', 'Venta', 'Traslado entrada', 'Traslado salida', 'Préstamo entregado', 'Préstamo recibido'];

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-adjustments-horizontal';
    protected static ?string $navigationLabel = 'Ajustes / Cuadres';
    protected static string|\UnitEnum|null $navigationGroup = 'Almacén';
    protected static ?int $navigationSort = 6;
    protected static ?string $label = 'Ajuste';
    protected static ?string $pluralLabel = 'Ajustes / Cuadres';
    protected static ?string $slug = 'ajustes';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('fecha')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                TextColumn::make('almacen')
                    ->label('Almacén')
                    ->formatStateUsing(fn (?string $state): string =>
                        KardexResource::almacenes()[$state] ?? ($state ?: '—')),

                TextColumn::make('producto.descripcion')
                    ->label('Producto')
                    ->searchable()
                    ->placeholder('—')
                    ->wrap()
                    ->limit(45),

                TextColumn::make('tipo')
                    ->label('Tipo')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => $state === 'I' ? 'Ingreso' : 'Salida')
                    ->color(fn (string $state): string => $state === 'I' ? 'success' : 'danger'),

                TextColumn::make('motivo.nombre')
                    ->label('Motivo')
                    ->badge()
                    ->color('gray')
                    ->placeholder('—'),

                TextColumn::make('cantidad')
                    ->label('Cant.'),

                TextColumn::make('stock_anterior')
                    ->label('Stock ant.')
                    ->toggleable(),

                TextColumn::make('stock_nuevo')
                    ->label('Stock nuevo')
                    ->toggleable(),

                TextColumn::make('observacion')
                    ->label('Observación')
                    ->wrap()
                    ->limit(45)
                    ->placeholder('—')
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('tipo')
                    ->label('Tipo')
                    ->options([
                        'I' => 'Ingreso',
                        'S' => 'Salida',
                    ]),

                SelectFilter::make('almacen')
                    ->label('Almacén')
                    ->options(fn () => DB::table('almacenes')
                        ->where('id_empresa', (int) session('id_empresa'))
                        ->pluck('nombre', 'codigo')
                        ->toArray()),
            ])
            ->actions([
                Action::make('anular')
                    ->label('Deshacer')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('¿Deshacer este ajuste?')
                    ->modalDescription('Se revertirá el cambio de stock y se eliminará el movimiento.')
                    ->action(function (InventarioMovimiento $record): void {
                        try {
                            static::anularAjuste($record);
                            Notification::make()->success()->title('Ajuste deshecho')->send();
                        } catch (\Throwable $e) {
                            Notification::make()->danger()->title('Error')->body($e->getMessage())->send();
                        }
                    }),
            ])
            ->defaultSort('id_movimiento', 'desc');
    }

    protected static function anularAjuste(InventarioMovimiento $record): void
    {
        $emp = (int) session('id_empresa');

        DB::transaction(function () use ($record, $emp): void {
            $motNombre = $record->id_motivo
                ? MotivoMovimiento::where('id_motivo', $record->id_motivo)->value('nombre')
                : null;

            if ($motNombre && in_array($motNombre, static::AUTOMATIZADOS)) {
                throw new \RuntimeException('Solo se pueden deshacer ajustes manuales (no compras, ventas ni traslados).');
            }

            $p = Producto::where('id_empresa', $emp)
                ->where('id_producto', $record->id_producto)
                ->lockForUpdate()
                ->firstOrFail();

            $ant  = (int) $p->cantidad;
            $cant = (int) $record->cantidad;

            if ($record->tipo === 'I') {
                if ($ant < $cant) {
                    throw new \RuntimeException("No se puede deshacer: el stock ya fue utilizado (actual: {$ant}).");
                }
                $p->update(['cantidad' => $ant - $cant]);
            } else {
                $p->update(['cantidad' => $ant + $cant]);
            }

            $record->delete();
        });
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('id_empresa', (int) session('id_empresa'))
            ->where(fn (Builder $q) => $q
                ->whereDoesntHave('motivo')
                ->orWhereHas('motivo', fn (Builder $m) => $m->whereNotIn('nombre', static::AUTOMATIZADOS)))
            ->with(['producto', 'motivo', 'usuario']);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAjustes::route('/'),
        ];
    }
}
