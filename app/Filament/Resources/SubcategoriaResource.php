<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\Productos;
use App\Filament\Resources\SubcategoriaResource\Pages;
use App\Models\Categoria;
use App\Models\Subcategoria;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SubcategoriaResource extends Resource
{
    protected static ?string $model = Subcategoria::class;
    protected static ?string $cluster = Productos::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-squares-2x2';
    protected static ?string $navigationLabel = 'Subcategorías';
    protected static ?int $navigationSort = 2;
    protected static ?string $label = 'Subcategoría';
    protected static ?string $pluralLabel = 'Subcategorías';
    protected static ?string $slug = 'subcategorias';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('id_categoria')->label('Categoría')->required()
                ->options(fn () => Categoria::where('id_empresa', (int) session('id_empresa'))
                    ->orderBy('nombre')->pluck('nombre', 'id_categoria'))
                ->searchable(),
            TextInput::make('nombre')->label('Nombre')->required()->maxLength(100),
            TextInput::make('descripcion')->label('Descripción')->maxLength(200),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre')->label('Nombre')->searchable()->sortable(),
                TextColumn::make('categoria.nombre')->label('Categoría')->placeholder('—')->sortable(),
                TextColumn::make('estado')->label('Estado')->badge()
                    ->formatStateUsing(fn ($state): string => (string) $state === '1' ? 'Activo' : 'Inactivo')
                    ->color(fn ($state): string => (string) $state === '1' ? 'success' : 'danger'),
            ])
            ->actions([
                EditAction::make(),
                Action::make('toggle')
                    ->label(fn (Subcategoria $r): string => (string) $r->estado === '1' ? 'Desactivar' : 'Activar')
                    ->icon(fn (Subcategoria $r): string => (string) $r->estado === '1' ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                    ->color(fn (Subcategoria $r): string => (string) $r->estado === '1' ? 'danger' : 'success')
                    ->requiresConfirmation()
                    ->action(fn (Subcategoria $r) => $r->update(['estado' => (string) $r->estado === '1' ? '0' : '1'])),
            ])
            ->defaultSort('nombre', 'asc');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('categoria')->where('id_empresa', (int) session('id_empresa'));
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListSubcategorias::route('/')];
    }
}
