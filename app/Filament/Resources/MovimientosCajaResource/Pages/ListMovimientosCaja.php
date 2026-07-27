<?php

namespace App\Filament\Resources\MovimientosCajaResource\Pages;

use App\Filament\Resources\MovimientosCajaResource;
use App\Models\Caja;
use App\Services\CajaService;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListMovimientosCaja extends ListRecords
{
    protected static string $resource = MovimientosCajaResource::class;

    protected function getHeaderActions(): array
    {
        $cajaOptions = Caja::where('id_empresa', (int) session('id_empresa'))
            ->pluck('nombre', 'id')
            ->toArray();

        $movimientoForm = [
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
            Select::make('metodo_pago')
                ->label('Método de pago')
                ->options(fn (): array => CajaService::opcionesMetodoPago())
                ->default('EFECTIVO')
                ->live()
                ->required(),
            TextInput::make('referencia')
                ->label('N° de operación')
                ->placeholder('Código del comprobante del pago')
                ->maxLength(60)
                ->visible(fn (callable $get): bool =>
                    filled($get('metodo_pago')) && $get('metodo_pago') !== 'EFECTIVO')
                ->required(fn (callable $get): bool =>
                    filled($get('metodo_pago')) && $get('metodo_pago') !== 'EFECTIVO'),
        ];

        $registrar = function (array $data, string $tipo): void {
            [$instrumentoTipo, $instrumentoId] = CajaService::mapInstrumento($data['metodo_pago'] ?? 'EFECTIVO');

            app(CajaService::class)->registrarMovimiento([
                'id_caja'          => $data['id_caja'],
                'tipo'             => $tipo,
                'categoria'        => 'MANUAL',
                'fecha'            => $data['fecha'],
                'descripcion'      => $data['descripcion'],
                'monto'            => $data['monto'],
                'instrumento_tipo' => $instrumentoTipo,
                'instrumento_id'   => $instrumentoId,
                'referencia'       => $data['referencia'] ?? null,
                'id_usuario'       => auth()->id(),
            ]);
        };

        // Las validaciones del servicio (ej. saldo insuficiente) se muestran
        // como notificación y el modal queda abierto para corregir.
        $ejecutar = function (array $data, string $tipo, Action $action) use ($registrar): void {
            try {
                $registrar($data, $tipo);
            } catch (\RuntimeException $e) {
                Notification::make()->danger()->title($e->getMessage())->send();

                $action->halt();

                return;
            }

            Notification::make()->success()
                ->title(($tipo === 'INGRESO' ? 'Ingreso' : 'Egreso') . ' registrado')
                ->send();
        };

        return [
            Action::make('ingreso')
                ->label('Registrar Ingreso')
                ->color('success')
                ->icon('heroicon-o-arrow-down-circle')
                ->form($movimientoForm)
                ->action(fn (array $data, Action $action) => $ejecutar($data, 'INGRESO', $action)),

            Action::make('egreso')
                ->label('Registrar Egreso')
                ->color('danger')
                ->icon('heroicon-o-arrow-up-circle')
                ->form($movimientoForm)
                ->action(fn (array $data, Action $action) => $ejecutar($data, 'EGRESO', $action)),
        ];
    }
}
