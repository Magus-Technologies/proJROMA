<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TrasladoResource\Pages;
use App\Models\InventarioMovimiento;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class TrasladoResource extends Resource
{
    protected static ?string $model = InventarioMovimiento::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-arrows-right-left';
    protected static ?string $navigationLabel = 'Traslado de Stock';
    protected static string|\UnitEnum|null $navigationGroup = 'Almacén';
    protected static ?int $navigationSort = 5;
    protected static ?string $label = 'Traslado';
    protected static ?string $pluralLabel = 'Traslados de Stock';
    protected static ?string $slug = 'traslados';

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

                TextColumn::make('producto.descripcion')
                    ->label('Producto')
                    ->searchable()
                    ->placeholder('—')
                    ->wrap()
                    ->limit(45),

                TextColumn::make('tipo')
                    ->label('Movimiento')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => $state === 'S' ? 'Salida' : 'Entrada')
                    ->color(fn (string $state): string => $state === 'S' ? 'danger' : 'success'),

                TextColumn::make('almacen')
                    ->label('Almacén')
                    ->formatStateUsing(fn (?string $state): string =>
                        KardexResource::almacenes()[$state] ?? ($state ?: '—')),

                TextColumn::make('cantidad')
                    ->label('Cantidad'),

                TextColumn::make('observacion')
                    ->label('Detalle')
                    ->wrap()
                    ->limit(60)
                    ->placeholder('—'),

                TextColumn::make('usuario.nombres')
                    ->label('Usuario')
                    ->placeholder('—')
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('almacen')
                    ->label('Almacén')
                    ->options(fn () => DB::table('almacenes')
                        ->where('id_empresa', (int) session('id_empresa'))
                        ->pluck('nombre', 'codigo')
                        ->toArray()),
            ])
            ->defaultSort('id_movimiento', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('id_empresa', (int) session('id_empresa'))
            ->whereHas('motivo', fn (Builder $q) =>
                $q->whereIn('nombre', ['Traslado salida', 'Traslado entrada']))
            ->with(['producto', 'motivo', 'usuario']);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTraslados::route('/'),
        ];
    }
}
