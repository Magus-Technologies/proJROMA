<?php

namespace App\Filament\Resources;

use App\Models\GuiaRemision;
use App\Filament\Resources\GuiaRemisionResource\Pages;
use BackedEnum;
use Filament\Actions\ViewAction;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class GuiaRemisionResource extends Resource
{
    protected static ?string $model = GuiaRemision::class;

    protected static string|BackedEnum|null $navigationIcon  = 'heroicon-o-truck';
    protected static ?string $navigationLabel = 'Guías de Remisión';
    protected static string|\UnitEnum|null $navigationGroup = 'Facturación';
    protected static ?int    $navigationSort  = 2;
    protected static ?string $label           = 'Guía de Remisión';
    protected static ?string $pluralLabel     = 'Guías de Remisión';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id_guia_remision')->label('#')->sortable(),
                TextColumn::make('documento')->label('Documento')->searchable(),
                TextColumn::make('venta.cliente.datos')->label('Cliente')->searchable()->wrap(),
                TextColumn::make('fecha_emision')->label('Fecha')->date('d/m/Y')->sortable(),
                TextColumn::make('dir_llegada')->label('Destino')->toggleable()->wrap(),
                TextColumn::make('enviado_sunat')->label('SUNAT')
                    ->badge()
                    ->color(fn ($state) => $state == '1' ? 'success' : 'warning')
                    ->formatStateUsing(fn ($state) => $state == '1' ? 'Enviado' : 'Pendiente'),
                TextColumn::make('estado')->label('Estado')
                    ->badge()
                    ->color(fn ($state) => $state == '1' ? 'success' : 'danger')
                    ->formatStateUsing(fn ($state) => $state == '1' ? 'Activa' : 'Anulada'),
            ])
            ->filters([
                SelectFilter::make('estado')->options(['1' => 'Activa', '0' => 'Anulada']),
            ])
            ->actions([ViewAction::make()])
            ->defaultSort('id_guia_remision', 'desc');
    }

    public static function getRelations(): array { return []; }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGuiasRemision::route('/'),
        ];
    }
}
