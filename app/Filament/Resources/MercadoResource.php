<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MercadoResource\Pages;
use App\Models\TmsMercado;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MercadoResource extends Resource
{
    protected static ?string $model = TmsMercado::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-building-storefront';
    protected static ?string $navigationLabel = 'Mercados';
    protected static string|\UnitEnum|null $navigationGroup = 'Transporte (TMS)';
    protected static ?int $navigationSort = 20;
    protected static ?string $label = 'Mercado';
    protected static ?string $pluralLabel = 'Mercados';
    protected static ?string $slug = 'tms-mercados';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('nombre')->label('Nombre')->required()->maxLength(120),
            TextInput::make('direccion')->label('Dirección (específica)')->required()->maxLength(245)
                ->helperText('A dónde llega el vehículo.'),
            TextInput::make('referencia')->label('Referencia')->maxLength(245),
            TextInput::make('distrito')->label('Distrito')->maxLength(120),
            TextInput::make('telefono')->label('Teléfono')->maxLength(20),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre')->label('Nombre')->searchable()->sortable(),
                TextColumn::make('direccion')->label('Dirección')->placeholder('—')->wrap()->limit(50),
                TextColumn::make('distrito')->label('Distrito')->placeholder('—'),
                TextColumn::make('telefono')->label('Teléfono')->placeholder('—'),
                TextColumn::make('estado')->label('Estado')->badge()
                    ->formatStateUsing(fn (int $state): string => $state ? 'Activo' : 'Inactivo')
                    ->color(fn (int $state): string => $state ? 'success' : 'danger'),
            ])
            ->actions([
                EditAction::make(),
                Action::make('toggle')
                    ->label(fn (TmsMercado $record): string => $record->estado ? 'Desactivar' : 'Activar')
                    ->icon(fn (TmsMercado $record): string => $record->estado ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                    ->color(fn (TmsMercado $record): string => $record->estado ? 'danger' : 'success')
                    ->requiresConfirmation()
                    ->action(fn (TmsMercado $record) => $record->update(['estado' => $record->estado ? 0 : 1])),
            ])
            ->defaultSort('nombre', 'asc');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('id_empresa', (int) session('id_empresa'))
            ->where('sucursal', (int) session('sucursal'));
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMercados::route('/'),
        ];
    }
}
