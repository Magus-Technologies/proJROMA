<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MotivoMovimientoResource\Pages;
use App\Models\MotivoMovimiento;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class MotivoMovimientoResource extends Resource
{
    use \App\Filament\Concerns\VerificaPermisoDeAcceso;

    public const PERMISO_ACCESO = 'inventario.gestionar';

    protected static ?string $model = MotivoMovimiento::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-arrows-right-left';
    protected static ?string $navigationLabel = 'Motivos de Movimiento';
    protected static string|\UnitEnum|null $navigationGroup = 'Inventario';
    protected static ?int $navigationSort = 10;
    protected static ?string $label = 'Motivo';
    protected static ?string $pluralLabel = 'Motivos de Movimiento';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('nombre')
                ->label('Nombre')
                ->required()
                ->maxLength(120),

            Select::make('tipo')
                ->label('Tipo')
                ->options([
                    'I' => 'Ingreso',
                    'S' => 'Salida',
                ])
                ->required(),

            Toggle::make('es_sistema')
                ->label('Motivo del sistema')
                ->default(false)
                ->helperText('Los motivos del sistema no se pueden eliminar.'),

            Select::make('estado')
                ->label('Estado')
                ->options([
                    '1' => 'Activo',
                    '0' => 'Inactivo',
                ])
                ->default('1')
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id_motivo')
                    ->label('#')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('nombre')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('tipo')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'I' ? 'success' : 'danger')
                    ->formatStateUsing(fn (string $state): string => $state === 'I' ? 'Ingreso' : 'Salida'),

                IconColumn::make('es_sistema')
                    ->label('Sistema')
                    ->boolean()
                    ->trueIcon('heroicon-o-lock-closed')
                    ->falseIcon('heroicon-o-lock-open')
                    ->color(fn (bool $state): string => $state ? 'gray' : 'success'),

                IconColumn::make('estado')
                    ->label('Activo')
                    ->boolean()
                    ->getStateUsing(fn ($record): bool => $record->estado === '1'),
            ])
            ->filters([
                SelectFilter::make('tipo')
                    ->label('Tipo')
                    ->options([
                        'I' => 'Ingreso',
                        'S' => 'Salida',
                    ]),

                SelectFilter::make('estado')
                    ->label('Estado')
                    ->options([
                        '1' => 'Activo',
                        '0' => 'Inactivo',
                    ]),
            ])
            ->defaultSort('id_motivo');
    }

    public static function getRelations(): array { return []; }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMotivoMovimientos::route('/'),
            'create' => Pages\CreateMotivoMovimiento::route('/create'),
            'edit' => Pages\EditMotivoMovimiento::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->where('id_empresa', (int) session('id_empresa'));
    }
}
