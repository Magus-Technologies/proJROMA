<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlanCuentaResource\Pages;
use App\Models\PlanCuenta;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class PlanCuentaResource extends Resource
{
    use \App\Filament\Concerns\VerificaPermisoDeAcceso;

    public const PERMISO_ACCESO = 'contabilidad.ver';

    protected static ?string $model = PlanCuenta::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-book-open';
    protected static string|\UnitEnum|null $navigationGroup = 'Contabilidad';
    protected static ?string $pluralModelLabel = 'Plan de Cuentas';
    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            TextInput::make('codigo')
                ->label('Código')
                ->required()
                ->maxLength(20)
                ->unique(ignoreRecord: true),
            TextInput::make('nombre')
                ->label('Nombre')
                ->required()
                ->maxLength(200),
            Select::make('tipo')
                ->label('Tipo')
                ->options(PlanCuenta::tipos())
                ->required(),
            Select::make('padre_id')
                ->label('Cuenta Padre')
                ->relationship('padre', 'nombre')
                ->searchable()
                ->preload()
                ->nullable(),
            TextInput::make('nivel')
                ->label('Nivel')
                ->numeric()
                ->default(1)
                ->required(),
            Toggle::make('estado')
                ->label('Activo')
                ->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('codigo')
                    ->label('Código')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('nombre')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('tipo')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'activo' => 'success',
                        'pasivo' => 'warning',
                        'patrimonio' => 'info',
                        'ingreso' => 'primary',
                        'costo' => 'danger',
                        'gasto' => 'danger',
                    }),
                TextColumn::make('nivel')
                    ->label('Nivel')
                    ->sortable(),
                TextColumn::make('padre.codigo')
                    ->label('Padre')
                    ->formatStateUsing(fn ($record) => $record->padre?->codigo . ' - ' . $record->padre?->nombre)
                    ->badge()
                    ->color('gray'),
                TextColumn::make('saldo_actual')
                    ->label('Saldo actual')
                    ->getStateUsing(fn (PlanCuenta $record): float => $record->saldoActual())
                    ->money('PEN')
                    ->alignRight()
                    ->color(fn ($state): ?string => $state < 0 ? 'danger' : null)
                    ->tooltip('Saldo según la naturaleza de la cuenta (asientos no anulados)'),
                ToggleColumn::make('estado')
                    ->label('Activo'),
            ])
            ->defaultSort('codigo')
            ->actions([
                EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPlanCuentas::route('/'),
            'create' => Pages\CreatePlanCuenta::route('/create'),
            'edit' => Pages\EditPlanCuenta::route('/{record}/edit'),
        ];
    }
}
