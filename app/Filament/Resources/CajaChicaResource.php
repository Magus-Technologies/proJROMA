<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CajaChicaResource\Pages;
use App\Models\CajaMovimiento;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CajaChicaResource extends Resource
{
    protected static ?string $model = CajaMovimiento::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-wallet';
    protected static ?string $navigationLabel = 'Caja Chica';
    protected static string|\UnitEnum|null $navigationGroup = 'Caja';
    protected static ?int $navigationSort = 7;
    protected static ?string $label = 'Movimiento';
    protected static ?string $pluralLabel = 'Caja Chica';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
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

                TextColumn::make('descripcion')
                    ->label('Descripción')
                    ->wrap()
                    ->limit(50),

                TextColumn::make('instrumento_tipo')
                    ->label('Método de pago')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'EFECTIVO'          => 'Efectivo',
                        'TRANSFERENCIA'     => 'Transferencia',
                        'BILLETERA_DIGITAL' => 'Billetera digital',
                        default             => $state ?? '—',
                    }),

                TextColumn::make('monto')
                    ->label('Monto')
                    ->money('PEN')
                    ->sortable(),

                TextColumn::make('saldo_posterior')
                    ->label('Saldo')
                    ->money('PEN')
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
                SelectFilter::make('tipo')
                    ->label('Tipo')
                    ->options([
                        'INGRESO' => 'Ingreso',
                        'EGRESO'  => 'Egreso',
                    ]),

                SelectFilter::make('instrumento_tipo')
                    ->label('Método de pago')
                    ->options([
                        'EFECTIVO'          => 'Efectivo',
                        'TRANSFERENCIA'     => 'Transferencia',
                        'BILLETERA_DIGITAL' => 'Billetera Digital',
                    ]),
            ])
            ->defaultSort('caja_movimientos.id', 'desc');
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->join('cajas', 'cajas.id', '=', 'caja_movimientos.id_caja')
            ->where('cajas.id_empresa', (int) session('id_empresa'))
            ->where('cajas.sucursal', session('sucursal'))
            ->where('cajas.nombre', 'Caja Chica')
            ->select('caja_movimientos.*');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCajaChica::route('/'),
        ];
    }
}
