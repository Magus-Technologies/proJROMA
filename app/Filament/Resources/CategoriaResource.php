<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\Productos;
use App\Filament\Resources\CategoriaResource\Pages;
use App\Models\Categoria;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CategoriaResource extends Resource
{
    protected static ?string $model = Categoria::class;
    protected static ?string $cluster = Productos::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationLabel = 'Categorías';
    protected static ?int $navigationSort = 1;
    protected static ?string $label = 'Categoría';
    protected static ?string $pluralLabel = 'Categorías';
    protected static ?string $slug = 'categorias';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('nombre')->label('Nombre')->required()->maxLength(100),
            TextInput::make('descripcion')->label('Descripción')->maxLength(200),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre')->label('Nombre')->searchable()->sortable(),
                TextColumn::make('descripcion')->label('Descripción')->placeholder('—')->wrap()->limit(60),
                TextColumn::make('estado')->label('Estado')->badge()
                    ->formatStateUsing(fn ($state): string => (string) $state === '1' ? 'Activo' : 'Inactivo')
                    ->color(fn ($state): string => (string) $state === '1' ? 'success' : 'danger'),
            ])
            ->actions([
                EditAction::make(),
                Action::make('toggle')
                    ->label(fn (Categoria $r): string => (string) $r->estado === '1' ? 'Desactivar' : 'Activar')
                    ->icon(fn (Categoria $r): string => (string) $r->estado === '1' ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                    ->color(fn (Categoria $r): string => (string) $r->estado === '1' ? 'danger' : 'success')
                    ->requiresConfirmation()
                    ->action(fn (Categoria $r) => $r->update(['estado' => (string) $r->estado === '1' ? '0' : '1'])),
            ])
            ->defaultSort('nombre', 'asc');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('id_empresa', (int) session('id_empresa'));
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListCategorias::route('/')];
    }
}
