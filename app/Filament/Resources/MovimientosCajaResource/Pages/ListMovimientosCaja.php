<?php

namespace App\Filament\Resources\MovimientosCajaResource\Pages;

use App\Filament\Resources\MovimientosCajaResource;
use App\Models\Caja;
use App\Services\CajaService;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ListRecords;

class ListMovimientosCaja extends ListRecords
{
    protected static string $resource = MovimientosCajaResource::class;

    protected function getHeaderActions(): array
    {
        $cajaOptions = Caja::where('id_empresa', (int) session('id_empresa'))
            ->pluck('nombre', 'id')
            ->toArray();

        return [
            Action::make('ingreso')
                ->label('Registrar Ingreso')
                ->color('success')
                ->icon('heroicon-o-arrow-down-circle')
                ->form([
                    Select::make('id_caja')
                        ->label('Caja')
                        ->options($cajaOptions)
                        ->required()
                        ->searchable(),
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
                            'EFECTIVO'          => 'Efectivo',
                            'TRANSFERENCIA'     => 'Transferencia',
                            'BILLETERA_DIGITAL' => 'Billetera Digital',
                        ])
                        ->required(),
                ])
                ->action(function (array $data): void {
                    app()->make(CajaService::class)->registrarMovimiento(array_merge($data, [
                        'tipo'      => 'INGRESO',
                        'categoria' => 'MANUAL',
                        'id_usuario' => auth()->id(),
                    ]));
                }),

            Action::make('egreso')
                ->label('Registrar Egreso')
                ->color('danger')
                ->icon('heroicon-o-arrow-up-circle')
                ->form([
                    Select::make('id_caja')
                        ->label('Caja')
                        ->options($cajaOptions)
                        ->required()
                        ->searchable(),
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
                            'EFECTIVO'          => 'Efectivo',
                            'TRANSFERENCIA'     => 'Transferencia',
                            'BILLETERA_DIGITAL' => 'Billetera Digital',
                        ])
                        ->required(),
                ])
                ->action(function (array $data): void {
                    app()->make(CajaService::class)->registrarMovimiento(array_merge($data, [
                        'tipo'      => 'EGRESO',
                        'categoria' => 'MANUAL',
                        'id_usuario' => auth()->id(),
                    ]));
                }),
        ];
    }
}
