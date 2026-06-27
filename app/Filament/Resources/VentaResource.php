<?php

namespace App\Filament\Resources;

use App\Models\Venta;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use App\Filament\Resources\VentaResource\Pages;

class VentaResource extends Resource
{
    protected static ?string $model = Venta::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $navigationLabel = 'Ventas';

    protected static ?string $pluralLabel = 'Ventas';

    protected static ?string $label = 'Venta';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('documento_completo')
                    ->label('Documento')
                    ->searchable(query: function ($query, $search) {
                        return $query->where('serie', 'like', "%{$search}%")
                            ->orWhere('numero', 'like', "%{$search}%");
                    })
                    ->sortable(query: function ($query, $direction) {
                        return $query->orderBy('serie', $direction)
                            ->orderBy('numero', $direction);
                    }),

                TextColumn::make('cliente.datos')
                    ->label('Cliente')
                    ->searchable(),

                TextColumn::make('fecha_emision')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('total')
                    ->label('Total')
                    ->money('PEN')
                    ->sortable(),

                TextColumn::make('estado')
                    ->label('Estado')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        '1' => 'Activa',
                        '0' => 'Anulada',
                        default => $state,
                    })
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        '1' => 'success',
                        '0' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('vendedor.nombre_completo')
                    ->label('Vendedor')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('estado')
                    ->options([
                        '1' => 'Activa',
                        '0' => 'Anulada',
                    ]),
            ])
            ->defaultSort('fecha_emision', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListVentas::route('/'),
            'view'   => Pages\ViewVenta::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->with(['cliente', 'vendedor', 'productosVenta']);
    }
}
