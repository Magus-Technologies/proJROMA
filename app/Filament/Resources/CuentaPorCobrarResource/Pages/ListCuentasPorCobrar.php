<?php

namespace App\Filament\Resources\CuentaPorCobrarResource\Pages;

use App\Filament\Resources\CuentaPorCobrarResource;
use Filament\Actions\Action;
use Filament\Forms\Components\Radio;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListCuentasPorCobrar extends ListRecords
{
    protected static string $resource = CuentaPorCobrarResource::class;

    public function getTabs(): array
    {
        return [
            'pendientes' => Tab::make('Pendientes')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('dias_ventas.estado', '0')),

            'vencidas' => Tab::make('Vencidas')
                ->modifyQueryUsing(fn (Builder $query) => $query
                    ->where('dias_ventas.estado', '0')
                    ->whereDate('dias_ventas.fecha', '<', now()->toDateString())),

            'pagadas' => Tab::make('Pagadas')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('dias_ventas.estado', '1')),

            'todas' => Tab::make('Todas'),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('reporte')
                ->label('Reporte')
                ->icon('heroicon-o-chart-bar')
                ->color('success')
                ->modalHeading('Reporte de cuentas por cobrar')
                ->modalDescription('Elegí qué cuotas incluir y en qué formato descargarlo.')
                ->modalWidth('md')
                ->modalSubmitActionLabel('Descargar')
                ->form([
                    Radio::make('alcance')
                        ->label('¿Qué cuotas incluir?')
                        ->options([
                            'pendientes' => 'Pendientes — todo lo que se debe',
                            'vencidas'   => 'Vencidas — solo lo atrasado',
                            'pagadas'    => 'Pagadas — ya cobradas',
                            'todas'      => 'Todas',
                        ])
                        ->default('pendientes')
                        ->required(),

                    Radio::make('formato')
                        ->label('Formato')
                        ->options([
                            'xlsx' => 'Excel (.xlsx)',
                            'pdf'  => 'PDF',
                        ])
                        ->default('xlsx')
                        ->inline()
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $url = route('reporte.cuentas.cobrar', [
                        'alcance' => $data['alcance'],
                        'formato' => $data['formato'],
                    ]);

                    $this->js('window.open(' . json_encode($url) . ", '_blank')");
                }),
        ];
    }
}
