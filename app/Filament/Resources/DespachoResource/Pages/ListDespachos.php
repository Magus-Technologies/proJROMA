<?php

namespace App\Filament\Resources\DespachoResource\Pages;

use App\Filament\Resources\DespachoResource;
use App\Models\TmsConductor;
use App\Models\TmsRuta;
use App\Models\TmsVehiculo;
use App\Services\TmsDespachoService;
use Filament\Actions\Action;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Placeholder;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\HtmlString;

class ListDespachos extends ListRecords
{
    protected static string $resource = DespachoResource::class;

    protected function getHeaderActions(): array
    {
        $svc = app(TmsDespachoService::class);
        $empresa = (int) session('id_empresa');
        $sucursal = (int) session('sucursal');

        return [
            Action::make('armar')
                ->visible(fn (): bool => auth()->user()?->can('tms_despachos.crear') ?? false)
                ->label('Armar Despacho')
                ->icon('heroicon-o-plus')
                ->color('primary')
                ->modalWidth('5xl')
                ->form([
                    Select::make('id_ruta')
                        ->label('Ruta')
                        ->options(fn () => TmsRuta::where('id_empresa', $empresa)->where('sucursal', $sucursal)
                            ->where('estado', 1)->orderBy('nombre')->pluck('nombre', 'id')->toArray())
                        ->live()->required()->columnSpan(2),

                    DatePicker::make('fecha_desde')->label('Fecha desde')->default(now())->live()->required(),
                    DatePicker::make('fecha_hasta')->label('Fecha hasta')->default(now())->live()->required(),

                    Placeholder::make('resumen_carga')
                        ->hiddenLabel()
                        ->columnSpanFull()
                        ->content(function (callable $get) use ($svc, $empresa): HtmlString {
                            $sel = $get('pedidos') ?: [];

                            if (! $sel) {
                                return new HtmlString('<span style="opacity:.7">Elegí ruta y fechas; después marcá las ventas que van en el camión.</span>');
                            }

                            $peso = array_sum($svc->pesosPorVenta(array_map('intval', $sel)));
                            $cap = (float) TmsVehiculo::where('id_empresa', $empresa)
                                ->where('id', $get('id_vehiculo'))->value('capacidad_kg');

                            $texto = '<strong>' . count($sel) . ' ventas · ' . number_format($peso, 2) . ' kg</strong>';

                            if ($cap > 0) {
                                $dif = $cap - $peso;
                                $texto .= $dif >= 0
                                    ? '<span style="opacity:.7"> — de ' . number_format($cap, 0) . ' kg, quedan '
                                        . number_format($dif, 2) . ' kg libres.</span>'
                                    : '<span style="color:#dc2626"> — sobrepeso de ' . number_format(abs($dif), 2) . ' kg.</span>';
                            }

                            return new HtmlString($texto);
                        }),

                    // Con 40 ventas la lista se hacía interminable y empujaba el
                    // resto del formulario fuera de la pantalla. Ahora va en su
                    // propia caja con altura fija, buscador y dos columnas.
                    CheckboxList::make('pedidos')
                        ->hiddenLabel()
                        ->options(function (callable $get) use ($svc, $empresa) {
                            $ruta = $get('id_ruta');
                            $desde = $get('fecha_desde');
                            $hasta = $get('fecha_hasta');
                            if (!$ruta || !$desde || !$hasta) return [];

                            return $svc->pedidosPendientes((int) $ruta, (string) $desde, (string) $hasta, $empresa)
                                ->mapWithKeys(fn ($p) => [$p->id_venta => "{$p->documento} · {$p->cliente}"])
                                ->toArray();
                        })
                        ->descriptions(function (callable $get) use ($svc, $empresa) {
                            $ruta = $get('id_ruta');
                            $desde = $get('fecha_desde');
                            $hasta = $get('fecha_hasta');
                            if (!$ruta || !$desde || !$hasta) return [];

                            return $svc->pedidosPendientes((int) $ruta, (string) $desde, (string) $hasta, $empresa)
                                ->mapWithKeys(fn ($p) => [
                                    $p->id_venta => $p->mercado . ' · ' . number_format((float) $p->peso, 1)
                                        . ' kg · S/ ' . number_format((float) $p->total, 2),
                                ])->toArray();
                        })
                        ->searchable()
                        ->noSearchResultsMessage('Ninguna venta coincide.')
                        ->searchPrompt('Buscar por documento, cliente o mercado')
                        ->columns(2)
                        ->live()
                        ->bulkToggleable()
                        // La clase va sobre el componente en sí: ahí cuelga
                        // la grilla de opciones que necesita el scroll.
                        ->extraAlpineAttributes(['class' => 'despacho-ventas'])
                        ->columnSpanFull(),

                    Select::make('id_vehiculo')->label('Vehículo')
                        ->options(function (callable $get) use ($svc, $empresa, $sucursal) {
                            $sel  = $get('pedidos') ?: [];
                            $peso = $sel ? array_sum($svc->pesosPorVenta(array_map('intval', $sel))) : 0;

                            return TmsVehiculo::with('tipo')->where('id_empresa', $empresa)->where('sucursal', $sucursal)
                                ->where('estado', 1)->orderBy('placa')->get()
                                ->mapWithKeys(function ($v) use ($peso) {
                                    $cap   = (float) $v->capacidad_kg;
                                    $extra = $peso > 0
                                        ? ($cap >= $peso ? ' ✓' : ' ⚠ NO ALCANZA')
                                        : '';

                                    return [$v->id => "{$v->placa} · {$v->tipo?->nombre} (" . number_format($cap, 0) . " kg){$extra}"];
                                })
                                ->toArray();
                        })
                        ->live()
                        // Cada vehículo tiene un conductor a cargo: al elegirlo
                        // se completa solo, y al revés también.
                        ->afterStateUpdated(function ($state, callable $set) use ($empresa): void {
                            $idConductor = TmsVehiculo::where('id_empresa', $empresa)
                                ->where('id', $state)
                                ->value('id_conductor');

                            if ($idConductor) {
                                $set('id_conductor', $idConductor);
                            }
                        })
                        ->searchable()->required(),

                    Select::make('id_conductor')->label('Conductor')
                        ->options(fn () => TmsConductor::where('id_empresa', $empresa)->where('sucursal', $sucursal)
                            ->where('estado', 1)->orderBy('nombres')->get()
                            ->mapWithKeys(function (TmsConductor $c) use ($empresa): array {
                                $placa = TmsVehiculo::where('id_empresa', $empresa)
                                    ->where('id_conductor', $c->id)->value('placa');

                                return [$c->id => $c->nombres . ($placa ? ' · ' . $placa : '')];
                            })
                            ->toArray())
                        ->live()
                        ->afterStateUpdated(function ($state, callable $set) use ($empresa): void {
                            $idVehiculo = TmsVehiculo::where('id_empresa', $empresa)
                                ->where('id_conductor', $state)
                                ->value('id');

                            if ($idVehiculo) {
                                $set('id_vehiculo', $idVehiculo);
                            }
                        })
                        ->helperText('Si el conductor tiene un vehículo a cargo, se completa solo.')
                        ->searchable()->required(),

                    DatePicker::make('fecha_reparto')->label('Fecha de reparto')->default(now())->required(),
                    TextInput::make('observaciones')->label('Observaciones')->maxLength(255)->columnSpanFull(),
                ])
                ->action(function (array $data) use ($svc, $empresa, $sucursal): void {
                    try {
                        $res = $svc->crear($data, $empresa, $sucursal, (int) (auth()->user()->usuario_id ?? 0));

                        Notification::make()->success()
                            ->title('Despacho creado')
                            ->body('Peso total: ' . number_format($res['peso_total'], 2) . ' kg')
                            ->send();

                        foreach ($res['advertencias'] as $adv) {
                            Notification::make()->warning()->title('Advertencia')->body($adv)->send();
                        }
                    } catch (\RuntimeException $e) {
                        Notification::make()->danger()->title('No se pudo crear')->body($e->getMessage())->send();
                    }
                }),
        ];
    }
}
