<?php

namespace App\Filament\Resources\VentaResource\Pages;

use App\Filament\Resources\VentaResource;
use Filament\Actions\Action;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Grid;
use Illuminate\Support\Facades\DB;

class ListVentas extends ListRecords
{
    protected static string $resource = VentaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('nueva_venta')
                ->label('Nueva Venta')
                ->icon('heroicon-o-plus')
                ->color('primary')
                ->url(VentaResource::getUrl('create')),

            Action::make('reporte')
                ->label('Reporte')
                ->icon('heroicon-o-chart-bar')
                ->color('success')
                ->modalHeading('Generar reporte de ventas')
                ->modalDescription('Elegí qué información querés y en qué formato descargarla.')
                ->modalWidth('lg')
                ->modalSubmitActionLabel('Descargar reporte')
                ->form([
                    Radio::make('tipo')
                        ->label('¿Qué reporte necesitás?')
                        ->options([
                            'general'   => 'Registro de ventas — todas las ventas',
                            'producto'  => 'Por producto — qué se vende más',
                            'cliente'   => 'Por cliente — quién te compra más',
                            'vendedor'  => 'Por vendedor — rendimiento del equipo',
                            'ganancias' => 'Ganancias — utilidad por línea de venta',
                            'rvta'      => 'RVTA — Registro de Ventas e Ingresos (SUNAT)',
                        ])
                        ->descriptions([
                            'general'   => 'Documento, fecha, cliente, vendedor, tipo de pago, estado y total.',
                            'producto'  => 'Cantidad vendida, número de ventas y monto por producto.',
                            'cliente'   => 'Número de compras, ticket promedio y monto por cliente.',
                            'vendedor'  => 'Ventas emitidas, anuladas y monto por vendedor (incluye rol).',
                            'ganancias' => 'Precio de venta vs costo, con la utilidad de cada línea y total.',
                            'rvta'      => 'Formato contable: base imponible, IGV, doc. del cliente. Solo boletas y facturas; anuladas en cero.',
                        ])
                        ->default('general')
                        ->required(),

                    Grid::make(3)->schema([
                        Select::make('periodo')
                            ->label('Periodo')
                            ->options([
                                'todo' => 'Todo el historial',
                                'anio' => 'Por año',
                                'mes'  => 'Por mes y año',
                            ])
                            ->default('mes')
                            ->live()
                            ->required(),

                        Select::make('anio')
                            ->label('Año')
                            ->options(fn (): array => DB::table('ventas')
                                ->where('id_empresa', (int) session('id_empresa'))
                                ->selectRaw('DISTINCT YEAR(fecha_emision) as anio')
                                ->orderByDesc('anio')
                                ->pluck('anio', 'anio')
                                ->toArray())
                            ->default(now()->year)
                            ->visible(fn (callable $get): bool => in_array($get('periodo'), ['anio', 'mes']))
                            ->requiredIf('periodo', ['anio', 'mes']),

                        Select::make('mes')
                            ->label('Mes')
                            ->options([
                                1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
                                5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
                                9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre',
                            ])
                            ->default(now()->month)
                            ->visible(fn (callable $get): bool => $get('periodo') === 'mes')
                            ->requiredIf('periodo', 'mes'),
                    ]),

                    Radio::make('formato')
                        ->label('Formato')
                        ->options([
                            'xlsx' => 'Excel (.xlsx)',
                            'pdf'  => 'PDF',
                        ])
                        ->descriptions([
                            'xlsx' => 'Con filtros, totales y formato de moneda.',
                            'pdf'  => 'Listo para imprimir o compartir.',
                        ])
                        ->default('xlsx')
                        ->inline()
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $url = route('reporte.ventas.avanzado', array_filter([
                        'tipo'    => $data['tipo'],
                        'periodo' => $data['periodo'],
                        'anio'    => in_array($data['periodo'], ['anio', 'mes']) ? $data['anio'] : null,
                        'mes'     => $data['periodo'] === 'mes' ? $data['mes'] : null,
                        'formato' => $data['formato'],
                    ]));

                    $this->js("window.open(" . json_encode($url) . ", '_blank')");
                }),
        ];
    }
}
