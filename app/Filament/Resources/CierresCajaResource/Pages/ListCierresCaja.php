<?php

namespace App\Filament\Resources\CierresCajaResource\Pages;

use App\Filament\Resources\CierresCajaResource;
use App\Services\CajaService;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\DB;

class ListCierresCaja extends ListRecords
{
    protected static string $resource = CierresCajaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('cerrar_caja')
                ->label('Cerrar Caja')
                ->icon('heroicon-o-lock-closed')
                ->color('warning')
                ->form([
                    Select::make('id_caja')
                        ->label('Caja hija')
                        ->options(fn () => DB::table('cajas')
                            ->where('id_empresa', (int) session('id_empresa'))
                            ->where('estado', 'ACTIVA')
                            ->whereNotNull('id_caja_padre')
                            ->pluck('nombre', 'id')
                            ->toArray())
                        ->required()
                        ->live()
                        ->afterStateUpdated(function ($state, callable $set): void {
                            $saldo = DB::table('cajas')->where('id', $state)->value('saldo_actual');
                            $set('saldo_sistema', number_format((float) $saldo, 2, '.', ''));
                        }),

                    TextInput::make('saldo_sistema')
                        ->label('Saldo según sistema (S/)')
                        ->disabled()
                        ->dehydrated(false),

                    TextInput::make('saldo_declarado')
                        ->label('Saldo declarado (S/)')
                        ->numeric()
                        ->minValue(0)
                        ->required(),
                ])
                ->action(function (array $data): void {
                    try {
                        app(CajaService::class)->cerrarCaja(
                            (int) $data['id_caja'],
                            (float) $data['saldo_declarado'],
                            [],
                            (int) auth()->user()->usuario_id
                        );
                        Notification::make()->success()->title('Cierre registrado')->body('Queda pendiente de aprobación.')->send();
                    } catch (\Throwable $e) {
                        Notification::make()->danger()->title('Error al cerrar caja')->body($e->getMessage())->send();
                    }
                }),

            Action::make('cuadre_consolidado')
                ->label('Cuadre Consolidado')
                ->icon('heroicon-o-scale')
                ->color('primary')
                ->form([
                    Select::make('id_caja_padre')
                        ->label('Caja principal')
                        ->options(fn () => DB::table('cajas')
                            ->where('id_empresa', (int) session('id_empresa'))
                            ->whereNull('id_caja_padre')
                            ->pluck('nombre', 'id')
                            ->toArray())
                        ->required(),

                    DatePicker::make('fecha')
                        ->label('Fecha')
                        ->default(now())
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $consolidado = app(CajaService::class)->consolidadoCajasHijas(
                        (int) $data['id_caja_padre'],
                        $data['fecha']
                    );

                    $detalle = collect($consolidado['cierres'])
                        ->map(fn ($c) => "{$c->caja_nombre}: declarado S/ " . number_format($c->saldo_declarado, 2)
                            . " / sistema S/ " . number_format($c->saldo_sistema, 2)
                            . " ({$c->estado})")
                        ->implode("\n");

                    Notification::make()
                        ->title('Cuadre del ' . \Carbon\Carbon::parse($data['fecha'])->format('d/m/Y'))
                        ->body(
                            "Total declarado: S/ " . number_format($consolidado['total_declarado'], 2)
                            . "\nTotal sistema: S/ " . number_format($consolidado['total_sistema'], 2)
                            . "\nDiferencia: S/ " . number_format($consolidado['diferencia'], 2)
                            . ($detalle ? "\n\n" . $detalle : "\n\nSin cierres registrados en esa fecha.")
                        )
                        ->color(abs($consolidado['diferencia']) < 0.01 ? 'success' : 'danger')
                        ->persistent()
                        ->send();
                }),
        ];
    }
}
