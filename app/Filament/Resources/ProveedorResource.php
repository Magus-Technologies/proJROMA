<?php

namespace App\Filament\Resources;

use App\Models\Proveedor;
use App\Filament\Resources\ProveedorResource\Pages;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProveedorResource extends Resource
{
    protected static ?string $model = Proveedor::class;

    protected static string|BackedEnum|null $navigationIcon  = 'heroicon-o-building-storefront';
    protected static ?string $navigationLabel = 'Proveedores';
    protected static string|\UnitEnum|null $navigationGroup = 'Maestros';
    protected static ?int    $navigationSort  = 2;
    protected static ?string $label           = 'Proveedor';
    protected static ?string $pluralLabel     = 'Proveedores';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('num_doc')->label('RUC / DNI')->maxLength(15),
            TextInput::make('nombre')->label('Razón Social')->required()->maxLength(200)->columnSpanFull(),
            TextInput::make('nombre_comercial')->label('Nombre Comercial')->maxLength(200)->columnSpanFull(),
            TextInput::make('direccion')->label('Dirección')->maxLength(200)->columnSpanFull(),
            TextInput::make('telefono')->label('Teléfono')->maxLength(20),
            TextInput::make('email')->label('Email')->email()->maxLength(100),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('num_doc')->label('RUC/DNI')->searchable()->sortable(),
                TextColumn::make('nombre')->label('Razón Social')->searchable()->sortable()->wrap(),
                TextColumn::make('nombre_comercial')->label('Nombre Comercial')->toggleable()->searchable(),
                TextColumn::make('telefono')->label('Teléfono')->toggleable(),
                TextColumn::make('email')->label('Email')->toggleable(),
            ])
            ->actions([EditAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])])
            ->defaultSort('nombre');
    }

    public static function getRelations(): array { return []; }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListProveedores::route('/'),
            'create' => Pages\CreateProveedor::route('/create'),
            'edit'   => Pages\EditProveedor::route('/{record}/edit'),
        ];
    }
}
