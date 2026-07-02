<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ConductorResource\Pages;
use App\Models\TmsConductor;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ConductorResource extends Resource
{
    protected static ?string $model = TmsConductor::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-identification';
    protected static ?string $navigationLabel = 'Conductores';
    protected static string|\UnitEnum|null $navigationGroup = 'Transporte (TMS)';
    protected static ?int $navigationSort = 40;
    protected static ?string $label = 'Conductor';
    protected static ?string $pluralLabel = 'Conductores';
    protected static ?string $slug = 'tms-conductores';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('nombres')->label('Nombres')->required()->maxLength(120),
            TextInput::make('documento')->label('Documento (DNI)')->maxLength(15),
            TextInput::make('telefono')->label('Teléfono')->maxLength(20),
            TextInput::make('licencia')->label('Licencia')->maxLength(30),
            TextInput::make('licencia_categoria')->label('Categoría')->maxLength(10),
            DatePicker::make('licencia_vence')->label('Licencia vence'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombres')->label('Nombres')->searchable()->sortable(),
                TextColumn::make('documento')->label('Documento')->placeholder('—'),
                TextColumn::make('licencia')->label('Licencia')->placeholder('—'),
                TextColumn::make('licencia_categoria')->label('Cat.')->placeholder('—'),
                TextColumn::make('licencia_vence')->label('Vence')->date('d/m/Y')->placeholder('—')
                    ->color(fn ($state): string => $state && $state->isPast() ? 'danger' : 'gray'),
                TextColumn::make('telefono')->label('Teléfono')->placeholder('—'),
                TextColumn::make('estado')->label('Estado')->badge()
                    ->formatStateUsing(fn (int $state): string => $state ? 'Activo' : 'Inactivo')
                    ->color(fn (int $state): string => $state ? 'success' : 'danger'),
            ])
            ->actions([
                EditAction::make(),
                Action::make('toggle')
                    ->label(fn (TmsConductor $record): string => $record->estado ? 'Desactivar' : 'Activar')
                    ->icon(fn (TmsConductor $record): string => $record->estado ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                    ->color(fn (TmsConductor $record): string => $record->estado ? 'danger' : 'success')
                    ->requiresConfirmation()
                    ->action(fn (TmsConductor $record) => $record->update(['estado' => $record->estado ? 0 : 1])),
            ])
            ->defaultSort('nombres', 'asc');
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
            'index' => Pages\ListConductores::route('/'),
        ];
    }
}
