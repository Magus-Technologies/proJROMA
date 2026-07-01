<?php

namespace App\Filament\Resources\RegistroCajaResource\Pages;

use App\Filament\Resources\RegistroCajaResource;
use App\Services\CajaService;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\DB;

class ListRegistroCaja extends ListRecords
{
    protected static string $resource = RegistroCajaResource::class;

    protected function cajaPrincipalId(): ?int
    {
        return DB::table('cajas')
            ->where('id_empresa', (int) session('id_empresa'))
            ->where('sucursal', session('sucursal'))
            ->whereNull('id_caja_padre')
            ->where('estado', 'ACTIVA')
            ->value('id');
    }

    protected function getHeaderActions(): array
    {
        $form = [
            TextInput::make('descripcion')
                ->label('Descripción')
                ->required()
                ->maxLength(245),
            TextInput::make('monto')
                ->label('Monto (S/)')
                ->numeric()
                ->minValue(0.01)
                ->prefix('S/')
                ->required(),
            Select::make('instrumento_tipo')
                ->label('Método de Pago')
                ->options([
                    'EFECTIVO'          => 'Efectivo',
                    'TRANSFERENCIA'     => 'Transferencia',
                    'BILLETERA_DIGITAL' => 'Billetera Digital',
                ])
                ->required(),
            DatePicker::make('fecha')
                ->label('Fecha')
                ->default(now())
                ->required(),
        ];

        $registrar = function (array $data, string $tipo): void {
            $idCaja = $this->cajaPrincipalId();

            if (! $idCaja) {
                Notification::make()->danger()
                    ->title('Sin caja principal')
                    ->body('No hay una caja principal activa para esta sucursal.')
                    ->send();
                return;
            }

            app(CajaService::class)->registrarMovimiento(array_merge($data, [
                'id_caja'    => $idCaja,
                'tipo'       => $tipo,
                'categoria'  => 'MANUAL',
                'id_usuario' => (int) auth()->user()->usuario_id,
            ]));

            Notification::make()->success()->title(ucfirst(strtolower($tipo)) . ' registrado')->send();
        };

        return [
            Action::make('ingreso')
                ->label('Registrar Ingreso')
                ->color('success')
                ->icon('heroicon-o-arrow-down-circle')
                ->form($form)
                ->action(fn (array $data) => $registrar($data, 'INGRESO')),

            Action::make('egreso')
                ->label('Registrar Egreso')
                ->color('danger')
                ->icon('heroicon-o-arrow-up-circle')
                ->form($form)
                ->action(fn (array $data) => $registrar($data, 'EGRESO')),
        ];
    }
}
