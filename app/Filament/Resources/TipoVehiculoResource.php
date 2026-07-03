<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\Tms;
use App\Filament\Resources\TipoVehiculoResource\Pages;
use App\Models\TmsTipoVehiculo;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TipoVehiculoResource extends Resource
{
    protected static ?string $model = TmsTipoVehiculo::class;

    protected static ?string $cluster = Tms::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationLabel = 'Tipos de Vehículo';
    protected static ?int $navigationSort = 1;
    protected static ?string $label = 'Tipo de Vehículo';
    protected static ?string $pluralLabel = 'Tipos de Vehículo';
    protected static ?string $slug = 'tipos-vehiculo';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('nombre')->label('Nombre')->required()->maxLength(60),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre')->label('Tipo')->searchable()->sortable(),
                IconColumn::make('estado')->label('Estado')->boolean(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->defaultSort('nombre', 'asc');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('id_empresa', (int) session('id_empresa'));
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTipoVehiculos::route('/'),
        ];
    }
}
