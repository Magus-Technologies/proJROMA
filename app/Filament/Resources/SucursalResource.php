<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SucursalResource\Pages;
use App\Models\Sucursal;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class SucursalResource extends Resource
{
    protected static ?string $model = Sucursal::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-building-office-2';
    protected static ?string $navigationLabel = 'Sucursales';
    protected static string|\UnitEnum|null $navigationGroup = 'Administración';
    protected static ?int $navigationSort = 3;
    protected static ?string $label = 'Sucursal';
    protected static ?string $pluralLabel = 'Sucursales';
    protected static ?string $slug = 'sucursales';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('nombre')
                ->label('Nombre')
                ->required()
                ->maxLength(150),

            TextInput::make('cod_sucursal')
                ->label('Código')
                ->numeric()
                ->integer()
                ->minValue(1)
                ->required()
                ->default(fn (): int =>
                    (int) Sucursal::where('empresa_id', (int) session('id_empresa'))->max('cod_sucursal') + 1),

            TextInput::make('direccion')
                ->label('Dirección')
                ->maxLength(150),

            Select::make('estado')
                ->label('Estado')
                ->options([
                    '1' => 'Activa',
                    '0' => 'Inactiva',
                ])
                ->default('1')
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('cod_sucursal')
                    ->label('Código')
                    ->sortable(),

                TextColumn::make('nombre')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('direccion')
                    ->label('Dirección')
                    ->placeholder('—')
                    ->wrap()
                    ->limit(50),

                TextColumn::make('usuarios_count')
                    ->label('Usuarios')
                    ->badge()
                    ->getStateUsing(fn (Sucursal $record): int =>
                        DB::table('usuarios')
                            ->where('id_empresa', (int) session('id_empresa'))
                            ->where('sucursal', $record->cod_sucursal)
                            ->count()),

                TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => $state === '1' ? 'Activa' : 'Inactiva')
                    ->color(fn (string $state): string => $state === '1' ? 'success' : 'danger'),
            ])
            ->actions([
                EditAction::make(),

                Action::make('toggle')
                    ->label(fn (Sucursal $record): string => $record->estado === '1' ? 'Desactivar' : 'Activar')
                    ->icon(fn (Sucursal $record): string =>
                        $record->estado === '1' ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                    ->color(fn (Sucursal $record): string => $record->estado === '1' ? 'danger' : 'success')
                    ->requiresConfirmation()
                    ->action(function (Sucursal $record): void {
                        $record->update(['estado' => $record->estado === '1' ? '0' : '1']);
                    }),

                Action::make('eliminar')
                    ->label('Eliminar')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (Sucursal $record): void {
                        $enUso = DB::table('usuarios')
                            ->where('id_empresa', (int) session('id_empresa'))
                            ->where('sucursal', $record->cod_sucursal)
                            ->exists();

                        if ($enUso) {
                            Notification::make()->danger()
                                ->title('No se puede eliminar')
                                ->body('Hay usuarios asignados a esta sucursal.')
                                ->send();

                            return;
                        }

                        $record->delete();
                        Notification::make()->success()->title('Sucursal eliminada')->send();
                    }),
            ])
            ->defaultSort('cod_sucursal', 'asc');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('empresa_id', (int) session('id_empresa'));
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSucursales::route('/'),
        ];
    }
}
