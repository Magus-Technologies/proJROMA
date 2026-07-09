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
            Select::make('metodo_pago')
                ->label('Método de Pago')
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

            [$instrumentoTipo, $instrumentoId] = CajaService::mapInstrumento($data['metodo_pago'] ?? 'EFECTIVO');

            app(CajaService::class)->registrarMovimiento([
                'id_caja'          => $idCaja,
                'tipo'             => $tipo,
                'categoria'        => 'MANUAL',
                'fecha'            => $data['fecha'],
                'descripcion'      => $data['descripcion'],
                'monto'            => $data['monto'],
                'instrumento_tipo' => $instrumentoTipo,
                'instrumento_id'   => $instrumentoId,
                'referencia'       => $data['referencia'] ?? null,
                'id_usuario'       => (int) auth()->user()->usuario_id,
            ]);

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
