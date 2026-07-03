<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\Productos;
use App\Filament\Resources\MarcaResource\Pages;
use App\Models\Marca;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MarcaResource extends Resource
{
    protected static ?string $model = Marca::class;
    protected static ?string $cluster = Productos::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-bookmark';
    protected static ?string $navigationLabel = 'Marcas';
    protected static ?int $navigationSort = 3;
    protected static ?string $label = 'Marca';
    protected static ?string $pluralLabel = 'Marcas';
    protected static ?string $slug = 'marcas';

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
                IconColumn::make('estado')->label('Estado')->boolean()
                    ->state(fn (Marca $r): bool => (string) $r->estado === '1'),
            ])
            ->actions([
                EditAction::make(),
                Action::make('toggle')
                    ->label(fn (Marca $r): string => (string) $r->estado === '1' ? 'Desactivar' : 'Activar')
                    ->icon(fn (Marca $r): string => (string) $r->estado === '1' ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                    ->color(fn (Marca $r): string => (string) $r->estado === '1' ? 'danger' : 'success')
                    ->requiresConfirmation()
                    ->action(fn (Marca $r) => $r->update(['estado' => (string) $r->estado === '1' ? '0' : '1'])),
            ])
            ->defaultSort('nombre', 'asc');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('id_empresa', (int) session('id_empresa'));
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListMarcas::route('/')];
    }
}
