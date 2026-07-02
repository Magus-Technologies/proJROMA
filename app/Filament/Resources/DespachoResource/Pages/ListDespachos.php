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
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

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

                    CheckboxList::make('pedidos')
                        ->label('Pedidos a despachar')
                        ->options(function (callable $get) use ($svc, $empresa) {
                            $ruta = $get('id_ruta');
                            $desde = $get('fecha_desde');
                            $hasta = $get('fecha_hasta');
                            if (!$ruta || !$desde || !$hasta) return [];

                            return $svc->pedidosPendientes((int) $ruta, (string) $desde, (string) $hasta, $empresa)
                                ->mapWithKeys(fn ($p) => [
                                    $p->cotizacion_id => "{$p->cliente} · {$p->mercado} · " .
                                        number_format((float) $p->peso, 1) . ' kg · S/ ' . number_format((float) $p->total, 2),
                                ])->toArray();
                        })
                        ->live()
                        ->bulkToggleable()
                        ->helperText(function (callable $get) use ($svc, $empresa) {
                            $sel = $get('pedidos') ?: [];
                            if (!$sel) return 'Selecciona ruta y fechas para ver los pedidos.';
                            $pesos = $svc->pesosPorPedido(array_map('intval', $sel));
                            $peso = array_sum($pesos);
                            return count($sel) . ' pedidos seleccionados · ' . number_format($peso, 2) . ' kg';
                        })
                        ->columnSpanFull(),

                    Select::make('id_vehiculo')->label('Vehículo')
                        ->options(fn () => TmsVehiculo::where('id_empresa', $empresa)->where('sucursal', $sucursal)
                            ->where('estado', 1)->orderBy('placa')->get()
                            ->mapWithKeys(fn ($v) => [$v->id => "{$v->placa} · {$v->tipo} (" . number_format((float) $v->capacidad_kg, 0) . ' kg)'])
                            ->toArray())
                        ->searchable()->required(),

                    Select::make('id_conductor')->label('Conductor')
                        ->options(fn () => TmsConductor::where('id_empresa', $empresa)->where('sucursal', $sucursal)
                            ->where('estado', 1)->orderBy('nombres')->pluck('nombres', 'id')->toArray())
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
