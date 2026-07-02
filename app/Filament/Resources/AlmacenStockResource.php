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

class AlmacenStockResource extends Resource
{
    protected static ?string $model = Producto::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-building-storefront';
    protected static ?string $navigationLabel = 'Almacén';
    protected static string|\UnitEnum|null $navigationGroup = 'Almacén';
    protected static ?int $navigationSort = 2;
    protected static ?string $label = 'Producto';
    protected static ?string $pluralLabel = 'Existencias por Almacén';
    protected static ?string $slug = 'almacen-stock';

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

                TextColumn::make('cantidad')
                    ->label('Stock')
                    ->sortable()
                    ->badge()
                    ->color(fn (int|float|string $state): string =>
                        (float) $state <= 0 ? 'danger' : ((float) $state <= 10 ? 'warning' : 'success')),

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
