<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\Productos;
use App\Filament\Resources\SubmarcaResource\Pages;
use App\Models\Marca;
use App\Models\Submarca;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SubmarcaResource extends Resource
{
    protected static ?string $model = Submarca::class;
    protected static ?string $cluster = Productos::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-bookmark-square';
    protected static ?string $navigationLabel = 'Submarcas';
    protected static ?int $navigationSort = 4;
    protected static ?string $label = 'Submarca';
    protected static ?string $pluralLabel = 'Submarcas';
    protected static ?string $slug = 'submarcas';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('id_marca')->label('Marca')->required()
                ->options(fn () => Marca::where('id_empresa', (int) session('id_empresa'))
                    ->orderBy('nombre')->pluck('nombre', 'id_marca'))
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
                TextColumn::make('marca.nombre')->label('Marca')->placeholder('—')->sortable(),
                IconColumn::make('estado')->label('Estado')->boolean()
                    ->state(fn (Submarca $r): bool => (string) $r->estado === '1'),
            ])
            ->actions([
                EditAction::make(),
                Action::make('toggle')
                    ->label(fn (Submarca $r): string => (string) $r->estado === '1' ? 'Desactivar' : 'Activar')
                    ->icon(fn (Submarca $r): string => (string) $r->estado === '1' ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                    ->color(fn (Submarca $r): string => (string) $r->estado === '1' ? 'danger' : 'success')
                    ->requiresConfirmation()
                    ->action(fn (Submarca $r) => $r->update(['estado' => (string) $r->estado === '1' ? '0' : '1'])),
            ])
            ->defaultSort('nombre', 'asc');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('marca')->where('id_empresa', (int) session('id_empresa'));
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListSubmarcas::route('/')];
    }
}
