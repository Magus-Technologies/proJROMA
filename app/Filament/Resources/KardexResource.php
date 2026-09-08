<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KardexResource\Pages;
use App\Models\InventarioMovimiento;
use App\Models\MotivoMovimiento;
use BackedEnum;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class KardexResource extends Resource
{
    use \App\Filament\Concerns\VerificaPermisoDeAcceso;

    public const PERMISO_ACCESO = 'productos.kardex';

    protected static ?string $model = InventarioMovimiento::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'Kardex';
    protected static string|\UnitEnum|null $navigationGroup = 'Inventario';
    protected static ?int $navigationSort = 3;
    protected static ?string $label = 'Movimiento';
    protected static ?string $pluralLabel = 'Kardex';
    protected static ?string $slug = 'kardex';

    protected static ?array $almacenesCache = null;

    /** Linea de tiempo de costos por producto, memorizada por request. */
    protected static array $costosCache = [];

    public static function almacenes(): array
    {
        return static::$almacenesCache ??= DB::table('almacenes')
            ->where('id_empresa', (int) session('id_empresa'))
            ->pluck('nombre', 'codigo')
            ->toArray();
    }

    /**
     * Costo unitario antes y despues de cada movimiento de un producto.
     *
     * inventario_movimientos solo guarda el costo DEL movimiento, y no todos
     * los movimientos lo traen (una salida puede venir sin costo). Por eso se
     * arrastra el ultimo costo conocido hacia adelante: mientras un movimiento
     * no declare costo nuevo, el producto sigue valiendo lo mismo.
     *
     * Se calcula una vez por producto y por request: como maximo una consulta
     * por producto distinto en la pagina visible.
     *
     * @return array<int, array{anterior: float|null, actual: float|null}>
     *         Indexado por id_movimiento.
     */
    public static function costosDeProducto(int $idProducto): array
    {
        if (isset(static::$costosCache[$idProducto])) {
            return static::$costosCache[$idProducto];
        }

        $linea  = [];
        $ultimo = null;

        foreach (
            DB::table('inventario_movimientos')
                ->where('id_producto', $idProducto)
                ->orderBy('id_movimiento')
                ->get(['id_movimiento', 'costo']) as $mov
        ) {
            $anterior = $ultimo;

            if ($mov->costo !== null) {
                $ultimo = (float) $mov->costo;
            }

            $linea[(int) $mov->id_movimiento] = [
                'anterior' => $anterior,
                'actual'   => $ultimo,
            ];
        }

        return static::$costosCache[$idProducto] = $linea;
    }

    /** @return float|null Costo 'anterior' o 'actual' del movimiento dado. */
    public static function costo(InventarioMovimiento $mov, string $cual): ?float
    {
        return static::costosDeProducto((int) $mov->id_producto)[$mov->id_movimiento][$cual] ?? null;
    }

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
                        static::almacenes()[$state] ?? ($state ?: '—')),

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
                    ->label('Cant.')
                    ->sortable(),

                TextColumn::make('stock_anterior')
                    ->label('Stock ant.')
                    ->toggleable(),

                TextColumn::make('stock_nuevo')
                    ->label('Stock nuevo')
                    ->toggleable(),

                TextColumn::make('costo_anterior')
                    ->label('Costo ant.')
                    ->getStateUsing(fn (InventarioMovimiento $record): ?float =>
                        static::costo($record, 'anterior'))
                    ->money('PEN')
                    ->placeholder('—')
                    ->color('gray')
                    ->alignEnd()
                    ->toggleable()
                    ->tooltip('Costo unitario que tenía el producto antes de este movimiento.'),

                TextColumn::make('costo_actual')
                    ->label('Costo actual')
                    ->getStateUsing(fn (InventarioMovimiento $record): ?float =>
                        static::costo($record, 'actual'))
                    ->money('PEN')
                    ->placeholder('—')
                    ->weight('bold')
                    ->color(fn (InventarioMovimiento $record): string => match (true) {
                        static::costo($record, 'anterior') === null => 'gray',
                        static::costo($record, 'actual') > static::costo($record, 'anterior') => 'danger',
                        static::costo($record, 'actual') < static::costo($record, 'anterior') => 'success',
                        default => 'gray',
                    })
                    ->alignEnd()
                    ->toggleable()
                    ->tooltip('Costo unitario después del movimiento. Rojo = el producto se encareció, verde = se abarató.'),

                TextColumn::make('observacion')
                    ->label('Observación')
                    ->wrap()
                    ->limit(50)
                    ->placeholder('—')
                    ->toggleable(),

                TextColumn::make('usuario.nombres')
                    ->label('Usuario')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('tipo')
                    ->label('Tipo')
                    ->options([
                        'I' => 'Ingreso',
                        'S' => 'Salida',
                    ]),

                SelectFilter::make('id_motivo')
                    ->label('Motivo')
                    ->options(fn () => MotivoMovimiento::where('id_empresa', (int) session('id_empresa'))
                        ->pluck('nombre', 'id_motivo')
                        ->toArray()),

                SelectFilter::make('almacen')
                    ->label('Almacén')
                    ->options(fn () => DB::table('almacenes')
                        ->where('id_empresa', (int) session('id_empresa'))
                        ->pluck('nombre', 'codigo')
                        ->toArray()),

                Filter::make('fecha')
                    ->form([
                        DatePicker::make('desde')->label('Desde'),
                        DatePicker::make('hasta')->label('Hasta'),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query
                        ->when($data['desde'], fn (Builder $q) => $q->whereDate('fecha', '>=', $data['desde']))
                        ->when($data['hasta'], fn (Builder $q) => $q->whereDate('fecha', '<=', $data['hasta']))),
            ])
            ->defaultSort('id_movimiento', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('id_empresa', (int) session('id_empresa'))
            ->with(['producto', 'motivo', 'usuario']);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKardex::route('/'),
        ];
    }
}
