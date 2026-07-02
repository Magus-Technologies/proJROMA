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
    protected static ?string $model = InventarioMovimiento::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'Kardex';
    protected static string|\UnitEnum|null $navigationGroup = 'Almacén';
    protected static ?int $navigationSort = 3;
    protected static ?string $label = 'Movimiento';
    protected static ?string $pluralLabel = 'Kardex';
    protected static ?string $slug = 'kardex';

    protected static ?array $almacenesCache = null;

    public static function almacenes(): array
    {
        return static::$almacenesCache ??= DB::table('almacenes')
            ->where('id_empresa', (int) session('id_empresa'))
            ->pluck('nombre', 'codigo')
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
