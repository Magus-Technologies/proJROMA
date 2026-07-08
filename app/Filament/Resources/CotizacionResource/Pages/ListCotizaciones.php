<?php

namespace App\Filament\Resources\CotizacionResource\Pages;

use App\Filament\Resources\CotizacionResource;
use Filament\Actions;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Grid;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;

class ListCotizaciones extends ListRecords
{
    protected static string $resource = CotizacionResource::class;

    public ?int $recienCreada = null;

    public function mount(): void
    {
        parent::mount();

        // Round-trip through the browser so the table is booted when the
        // listener runs (mounting a table action directly in mount() fails).
        if ($id = (int) request()->query('previsualizar')) {
            $this->recienCreada = $id;
            $this->dispatch('abrir-vista-previa', id: $id);
        }
    }

    #[On('abrir-vista-previa')]
    public function abrirVistaPrevia(int $id): void
    {
        $this->mountTableAction('vista_previa', (string) $id);
        // Clean the URL (real Livewire cycle) so a refresh doesn't reopen the modal
        $this->js("window.history.replaceState({}, '', window.location.pathname)");
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('nueva_cotizacion')
                ->label('Nueva Cotización')
                ->icon('heroicon-o-plus')
                ->color('primary')
                ->url(CotizacionResource::getUrl('create')),

            Actions\Action::make('reporte')
                ->label('Reporte')
                ->icon('heroicon-o-chart-bar')
                ->color('success')
                ->modalHeading('Generar reporte de cotizaciones')
                ->modalDescription('Elegí qué información querés y en qué formato descargarla.')
                ->modalWidth('lg')
                ->modalSubmitActionLabel('Descargar reporte')
                ->form([
                    Radio::make('tipo')
                        ->label('¿Qué reporte necesitás?')
                        ->options([
                            'general'  => 'General — todas las cotizaciones',
                            'producto' => 'Por producto — qué se cotiza más',
                            'vendedor' => 'Por vendedor — rendimiento del equipo',
                        ])
                        ->descriptions([
                            'general'  => 'Número, fecha, cliente, vendedor, estado y total de cada cotización.',
                            'producto' => 'Cantidad cotizada, veces cotizado y monto acumulado por producto.',
                            'vendedor' => 'Cotizaciones emitidas, convertidas, anuladas y monto por vendedor.',
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
                            ->default('todo')
                            ->live()
                            ->required(),

                        Select::make('anio')
                            ->label('Año')
                            ->options(fn (): array => DB::table('cotizaciones')
                                ->where('id_empresa', (int) session('id_empresa'))
                                ->selectRaw('DISTINCT YEAR(fecha) as anio')
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
                    $url = route('reporte.cotizaciones', array_filter([
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
