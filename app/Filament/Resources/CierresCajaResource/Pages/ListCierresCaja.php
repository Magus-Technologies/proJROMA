<?php

namespace App\Filament\Resources\CierresCajaResource\Pages;

use App\Filament\Resources\CierresCajaResource;
use App\Services\CajaService;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListCierresCaja extends ListRecords
{
    protected static string $resource = CierresCajaResource::class;

    protected function getHeaderActions(): array
    {
        // El cierre lo registra cada trabajador contando su caja en Mi Caja;
        // esta vista solo lista, revisa y aprueba esos cierres.
        return [
            Action::make('cuadre_consolidado')
                ->visible(fn (): bool => auth()->user()?->can('caja.cierres_consolidado') ?? false)
                ->label('Cuadre Consolidado')
                ->icon('heroicon-o-scale')
                ->color('primary')
                ->modalDescription('Suma los cierres de todas las cajas de la empresa en la fecha elegida.')
                ->form([
                    DatePicker::make('fecha')
                        ->label('Fecha')
                        ->default(now())
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $consolidado = app(CajaService::class)->consolidadoCajas(
                        (int) session('id_empresa'),
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
