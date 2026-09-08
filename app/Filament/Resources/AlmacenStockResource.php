<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AlmacenStockResource\Pages;
use App\Models\Producto;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class AlmacenStockResource extends Resource
{
    use \App\Filament\Concerns\VerificaPermisoDeAcceso;

    public const PERMISO_ACCESO = 'almacen_existencias.ver';

    protected static ?string $model = Producto::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-building-storefront';
    protected static ?string $navigationLabel = 'Almacén';
    protected static string|\UnitEnum|null $navigationGroup = 'Inventario';
    protected static ?int $navigationSort = 2;
    protected static ?string $label = 'Producto';
    protected static ?string $pluralLabel = 'Existencias por Almacén';
    protected static ?string $slug = 'almacen-stock';

    /** Ultimo movimiento de kardex por producto, memorizado por request. */
    protected static ?array $ultimoMovimientoCache = null;

    /**
     * El stock anterior de un producto no se guarda en productos: vive en el
     * kardex. Es el stock_anterior del ULTIMO movimiento registrado, o sea
     * cuanto habia justo antes del ultimo ingreso/salida.
     *
     * Se resuelve en una sola consulta para toda la tabla (sin N+1).
     *
     * @return array<int, array{anterior: int, nuevo: int, fecha: string}>
     */
    public static function ultimoMovimiento(): array
    {
        if (static::$ultimoMovimientoCache !== null) {
            return static::$ultimoMovimientoCache;
        }

        $empresa = (int) session('id_empresa');

        $ultimos = DB::table('inventario_movimientos')
            ->where('id_empresa', $empresa)
            ->groupBy('id_producto')
            ->selectRaw('id_producto, MAX(id_movimiento) as id_movimiento');

        return static::$ultimoMovimientoCache = DB::table('inventario_movimientos as m')
            ->joinSub($ultimos, 'u', 'u.id_movimiento', '=', 'm.id_movimiento')
            ->get(['m.id_producto', 'm.stock_anterior', 'm.stock_nuevo', 'm.fecha'])
            ->mapWithKeys(fn ($r): array => [(int) $r->id_producto => [
                'anterior' => (int) $r->stock_anterior,
                'nuevo'    => (int) $r->stock_nuevo,
                'fecha'    => $r->fecha,
            ]])
            ->toArray();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('codigo')
                    ->label('Código')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('descripcion')
                    ->label('Descripción')
                    ->searchable()
                    ->wrap()
                    ->limit(55),

                TextColumn::make('categoria.nombre')
                    ->label('Categoría')
                    ->placeholder('—')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('stock_anterior')
                    ->label('Stock anterior')
                    ->getStateUsing(fn (Producto $record): ?int =>
                        static::ultimoMovimiento()[$record->id_producto]['anterior'] ?? null)
                    ->placeholder('—')
                    ->badge()
                    ->color('gray')
                    ->alignEnd()
                    ->tooltip('Cuánto había justo antes del último movimiento de kardex.'),

                TextColumn::make('cantidad')
                    ->label('Stock actual')
                    ->sortable()
                    ->badge()
                    ->alignEnd()
                    ->color(fn (int|float|string $state): string =>
                        (float) $state <= 0 ? 'danger' : ((float) $state <= 10 ? 'warning' : 'success')),

                TextColumn::make('ultimo_movimiento')
                    ->label('Últ. movimiento')
                    ->getStateUsing(fn (Producto $record): ?string =>
                        static::ultimoMovimiento()[$record->id_producto]['fecha'] ?? null)
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('Sin movimientos')
                    ->toggleable()
                    ->tooltip('Fecha del movimiento que produjo el stock actual.'),

                TextColumn::make('precio')
                    ->label('Precio')
                    ->money('PEN')
                    ->sortable(),

                TextColumn::make('costo')
                    ->label('Costo')
                    ->money('PEN')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('descripcion', 'asc');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('id_empresa', (int) session('id_empresa'))
            ->with(['categoria']);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAlmacenStock::route('/'),
        ];
    }
}
