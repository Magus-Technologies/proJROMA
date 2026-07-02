<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PresentacionResource\Pages;
use App\Models\Presentacion;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PresentacionResource extends Resource
{
    protected static ?string $model = Presentacion::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cube-transparent';
    protected static ?string $navigationLabel = 'Presentaciones';
    protected static string|\UnitEnum|null $navigationGroup = 'Almacén';
    protected static ?int $navigationSort = 9;
    protected static ?string $label = 'Presentación';
    protected static ?string $pluralLabel = 'Presentaciones';
    protected static ?string $slug = 'presentaciones';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('nombre')->label('Nombre')->required()->maxLength(60)
                ->placeholder('Ej: Caja, Bulto, Saco, Display'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre')->label('Nombre')->searchable()->sortable(),
                TextColumn::make('estado')->label('Estado')->badge()
                    ->formatStateUsing(fn (int $state): string => $state ? 'Activo' : 'Inactivo')
                    ->color(fn (int $state): string => $state ? 'success' : 'danger'),
            ])
            ->actions([
                EditAction::make(),
                Action::make('toggle')
                    ->label(fn (Presentacion $record): string => $record->estado ? 'Desactivar' : 'Activar')
                    ->icon(fn (Presentacion $record): string => $record->estado ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                    ->color(fn (Presentacion $record): string => $record->estado ? 'danger' : 'success')
                    ->requiresConfirmation()
                    ->action(fn (Presentacion $record) => $record->update(['estado' => $record->estado ? 0 : 1])),
            ])
            ->defaultSort('nombre', 'asc');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('id_empresa', (int) session('id_empresa'));
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListPresentaciones::route('/')];
    }
}
