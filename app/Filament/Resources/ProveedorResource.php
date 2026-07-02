<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProveedorResource\Pages;
use App\Models\Proveedor;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rules\Unique;

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
            TextInput::make('ruc')
                ->label('RUC / DNI')
                ->required()
                ->maxLength(11)
                ->unique(
                    ignoreRecord: true,
                    modifyRuleUsing: fn (Unique $rule) => $rule->where('id_empresa', (int) session('id_empresa')),
                ),
            TextInput::make('razon_social')->label('Razón Social')->required()->maxLength(200)->columnSpanFull(),
            TextInput::make('nombre_comercial')->label('Nombre Comercial')->maxLength(255)->columnSpanFull(),
            TextInput::make('direccion')->label('Dirección')->maxLength(100)->columnSpanFull(),
            TextInput::make('telefono')->label('Teléfono')->maxLength(100),
            TextInput::make('email')->label('Email')->email()->maxLength(150),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('ruc')->label('RUC/DNI')->searchable()->sortable(),
                TextColumn::make('razon_social')->label('Razón Social')->searchable()->sortable()->wrap(),
                TextColumn::make('nombre_comercial')->label('Nombre Comercial')->toggleable()->searchable()->placeholder('—'),
                TextColumn::make('telefono')->label('Teléfono')->toggleable()->placeholder('—'),
                TextColumn::make('email')->label('Email')->toggleable()->placeholder('—'),
            ])
            ->actions([EditAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])])
            ->defaultSort('razon_social');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('id_empresa', (int) session('id_empresa'));
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
