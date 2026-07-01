<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MovimientosCajaResource\Pages;
use App\Models\Caja;
use App\Models\CajaMovimiento;
use App\Services\CajaService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class MovimientosCajaResource extends Resource
{
    protected static ?string $model = CajaMovimiento::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-arrows-right-left';
    protected static ?string $navigationLabel = 'Movimientos';
    protected static string|\UnitEnum|null $navigationGroup = 'Caja';
    protected static ?int $navigationSort = 2;
    protected static ?string $label = 'Movimiento';
    protected static ?string $pluralLabel = 'Movimientos de Caja';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('id_caja')
                ->label('Caja')
                ->options(fn () => Caja::where('id_empresa', (int) session('id_empresa'))->pluck('nombre', 'id')->toArray())
                ->required()
                ->searchable(),

            Select::make('tipo')
                ->label('Tipo')
                ->options([
                    'INGRESO' => 'Ingreso',
                    'EGRESO'  => 'Egreso',
                ])
                ->required(),

            Select::make('categoria')
                ->label('Categoría')
                ->options([
                    'VENTA'      => 'Venta',
                    'COMPRA'     => 'Compra',
                    'GASTO_OP'   => 'Gasto Op.',
                    'REPOSICION' => 'Reposición',
                    'RENDICION'  => 'Rendición',
                    'AJUSTE'     => 'Ajuste',
                    'APERTURA'   => 'Apertura',
                    'CIERRE'     => 'Cierre',
                    'CUADRE'     => 'Cuadre',
                    'MANUAL'     => 'Manual',
                ])
                ->default('MANUAL'),

            TextInput::make('descripcion')
                ->label('Descripción')
                ->required(),

            TextInput::make('monto')
                ->label('Monto')
                ->numeric()
                ->prefix('S/')
                ->required(),

            DatePicker::make('fecha')
                ->label('Fecha')
                ->default(now())
                ->required(),

            Select::make('instrumento_tipo')
                ->label('Instrumento')
                ->options([
                    'EFECTIVO'           => 'Efectivo',
                    'TRANSFERENCIA'      => 'Transferencia',
                    'BILLETERA_DIGITAL'  => 'Billetera Digital',
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('caja_nombre')
                    ->label('Caja'),

                TextColumn::make('fecha')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('tipo')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'INGRESO' => 'success',
                        'EGRESO'  => 'danger',
                        default   => 'gray',
                    }),

                TextColumn::make('categoria')
                    ->label('Categoría')
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'VENTA'      => 'Venta',
                        'COMPRA'     => 'Compra',
                        'GASTO_OP'   => 'Gasto Op.',
                        'REPOSICION' => 'Reposición',
                        'RENDICION'  => 'Rendición',
                        'AJUSTE'     => 'Ajuste',
                        'APERTURA'   => 'Apertura',
                        'CIERRE'     => 'Cierre',
                        'CUADRE'     => 'Cuadre',
                        'MANUAL'     => 'Manual',
                        default      => $state,
                    }),

                TextColumn::make('descripcion')
                    ->label('Descripción')
                    ->wrap()
                    ->limit(60),

                TextColumn::make('instrumento_tipo')
                    ->label('Instrumento')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'EFECTIVO'          => 'Efectivo',
                        'TRANSFERENCIA'     => 'Transferencia',
                        'BILLETERA_DIGITAL' => 'Billetera digital',
                        default             => $state ?? '—',
                    })
                    ->toggleable(),

                TextColumn::make('monto')
                    ->label('Monto')
                    ->money('PEN')
                    ->sortable(),

                TextColumn::make('saldo_posterior')
                    ->label('Saldo')
                    ->money('PEN')
                    ->toggleable(),

                TextColumn::make('usuario.nombres')
                    ->label('Usuario')
                    ->toggleable(),

                TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'CONFIRMADO' => 'success',
                        'ANULADO'    => 'danger',
                        default      => 'gray',
                    }),
            ])
            ->filters([
                SelectFilter::make('id_caja')
                    ->label('Caja')
                    ->options(fn () => Caja::where('id_empresa', (int) session('id_empresa'))
                        ->pluck('nombre', 'id')
                        ->toArray()),

                SelectFilter::make('tipo')
                    ->label('Tipo')
                    ->options([
                        'INGRESO' => 'Ingreso',
                        'EGRESO'  => 'Egreso',
                    ]),

                SelectFilter::make('estado')
                    ->label('Estado')
                    ->options([
                        'CONFIRMADO' => 'Confirmado',
                        'ANULADO'    => 'Anulado',
                    ]),
            ])
            ->actions([
                ActionGroup::make([
                    Action::make('anular')
                        ->label('Anular')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->visible(fn (CajaMovimiento $record): bool => $record->estado === 'CONFIRMADO')
                        ->requiresConfirmation()
                        ->action(fn (CajaMovimiento $record) => app()->make(CajaService::class)->anularMovimiento($record->id)),

                    Action::make('ver_detalle')
                        ->label('Ver detalle')
                        ->icon('heroicon-o-eye')
                        ->color('info')
                        ->modalHeading('Detalle del Movimiento')
                        ->modalContent(fn (CajaMovimiento $record) => view('filament.modals.movimiento-detalle', compact('record')))
                        ->modalSubmitAction(false),
                ]),
            ]);
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->join('cajas', 'cajas.id', '=', 'caja_movimientos.id_caja')
            ->where('cajas.id_empresa', (int) session('id_empresa'))
            ->select('caja_movimientos.*', 'cajas.nombre as caja_nombre')
            ->with(['usuario']);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMovimientosCaja::route('/'),
        ];
    }
}
