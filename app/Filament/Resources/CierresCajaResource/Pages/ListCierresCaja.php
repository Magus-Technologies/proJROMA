<?php

namespace App\Filament\Resources\CierresCajaResource\Pages;

use App\Filament\Resources\CierresCajaResource;
use App\Services\CajaService;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\DB;

class ListCierresCaja extends ListRecords
{
    protected static string $resource = CierresCajaResource::class;

    protected function getHeaderActions(): array
    {
        // El cierre lo registra cada trabajador contando su caja en Mi Caja;
        // esta vista solo lista, revisa y aprueba esos cierres.
        return [
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
