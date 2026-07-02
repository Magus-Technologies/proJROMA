<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UnidadMedidaResource\Pages;
use App\Models\UnidadMedida;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UnidadMedidaResource extends Resource
{
    protected static ?string $model = UnidadMedida::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-scale';
    protected static ?string $navigationLabel = 'Unidades de medida';
    protected static string|\UnitEnum|null $navigationGroup = 'Almacén';
    protected static ?int $navigationSort = 8;
    protected static ?string $label = 'Unidad de medida';
    protected static ?string $pluralLabel = 'Unidades de medida';
    protected static ?string $slug = 'unidades-medida';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('nombre')->label('Nombre')->required()->maxLength(60)
                ->placeholder('Ej: Unidad, Kilogramo, Caja'),
            TextInput::make('abreviatura')->label('Abreviatura')->maxLength(15)
                ->placeholder('Ej: UND, KG, CJ'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre')->label('Nombre')->searchable()->sortable(),
                TextColumn::make('abreviatura')->label('Abreviatura')->placeholder('—'),
                TextColumn::make('estado')->label('Estado')->badge()
                    ->formatStateUsing(fn (int $state): string => $state ? 'Activo' : 'Inactivo')
                    ->color(fn (int $state): string => $state ? 'success' : 'danger'),
            ])
            ->actions([
                EditAction::make(),
                Action::make('toggle')
                    ->label(fn (UnidadMedida $record): string => $record->estado ? 'Desactivar' : 'Activar')
                    ->icon(fn (UnidadMedida $record): string => $record->estado ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                    ->color(fn (UnidadMedida $record): string => $record->estado ? 'danger' : 'success')
                    ->requiresConfirmation()
                    ->action(fn (UnidadMedida $record) => $record->update(['estado' => $record->estado ? 0 : 1])),
            ])
            ->defaultSort('nombre', 'asc');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('id_empresa', (int) session('id_empresa'));
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListUnidadesMedida::route('/')];
    }
}
