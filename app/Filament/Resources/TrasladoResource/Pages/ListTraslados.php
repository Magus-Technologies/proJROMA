<?php

namespace App\Filament\Resources\TrasladoResource\Pages;

use App\Filament\Resources\TrasladoResource;
use App\Models\Almacen;
use App\Models\InventarioMovimiento;
use App\Models\MotivoMovimiento;
use App\Models\Producto;
use App\Models\Traslado;
use App\Models\TrasladoDetalle;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ListTraslados extends ListRecords
{
    protected static string $resource = TrasladoResource::class;

    protected function getHeaderActions(): array
    {
        $almacenes = fn () => Almacen::where('id_empresa', (int) session('id_empresa'))
            ->where('estado', 1)
            ->pluck('nombre', 'codigo')
            ->toArray();

        return [
            Action::make('nuevo_traslado')
                ->label('Transferir Stock')
                ->icon('heroicon-o-arrows-right-left')
                ->color('primary')
                ->modalHeading('Transferir Stock')
                ->modalWidth('4xl')
                ->modalSubmitActionLabel('Transferir')
                ->form([
                    DatePicker::make('fecha')
                        ->label('Fecha')
                        ->default(now())
                        ->maxDate(now())
                        ->required(),

                    Select::make('almacen_origen')
                        ->label('Almacén origen')
                        ->options($almacenes)
                        ->live()
                        ->required(),

                    Select::make('almacen_destino')
                        ->label('Almacén destino')
                        ->options($almacenes)
                        ->different('almacen_origen')
                        ->required(),

                    Repeater::make('detalles')
                        ->label('Productos a transferir')
                        ->addActionLabel('Agregar producto')
                        ->minItems(1)
                        ->defaultItems(1)
                        ->columns(3)
                        ->schema([
                            Select::make('id_producto')
                                ->label('Producto')
                                ->options(function (callable $get) {
                                    $alm = $get('../../almacen_origen');
                                    if (blank($alm)) {
                                        return [];
                                    }

                                    return Producto::where('id_empresa', (int) session('id_empresa'))
                                        ->where('almacen', $alm)
                                        ->where('cantidad', '>', 0)
                                        ->orderBy('descripcion')
                                        ->limit(500)
                                        ->get()
                                        ->mapWithKeys(fn (Producto $p) => [
                                            $p->id_producto => trim(($p->codigo ? "[{$p->codigo}] " : '') . "{$p->descripcion} (stock: {$p->cantidad})"),
                                        ])
                                        ->toArray();
                                })
                                ->searchable()
                                ->distinct()
                                ->required()
                                ->placeholder('Seleccioná primero el almacén origen')
                                ->columnSpan(2),

                            TextInput::make('cantidad')
                                ->label('Cantidad')
                                ->numeric()
                                ->integer()
                                ->minValue(1)
                                ->required(),
                        ]),

                    TextInput::make('observacion')
                        ->label('Observaciones')
                        ->maxLength(255),
                ])
                ->action(function (array $data): void {
                    try {
                        $traslado = $this->ejecutarTraslado($data);
                        $numero   = TrasladoResource::numeroDocumento((int) $traslado->id_traslado);
                        $items    = $traslado->detalles()->count();

                        Notification::make()->success()
                            ->title("Traslado {$numero} registrado")
                            ->body($items === 1 ? '1 producto transferido.' : "{$items} productos transferidos.")
                            ->send();
                    } catch (\Throwable $e) {
                        Notification::make()->danger()->title('Error en traslado')->body($e->getMessage())->send();
                    }
                }),
        ];
    }

    public function anularLineaTraslado(int $idDetalle): void
    {
        try {
            TrasladoResource::anularLinea($idDetalle);
            Notification::make()->success()
                ->title('Producto anulado')
                ->body('El stock regresó al almacén origen.')
                ->send();
        } catch (\Throwable $e) {
            Notification::make()->danger()->title('Error al anular')->body($e->getMessage())->send();
        }
    }

    public function editarLineaTraslado(int $idDetalle, int $nuevaCantidad): void
    {
        try {
            TrasladoResource::editarLinea($idDetalle, $nuevaCantidad);
            Notification::make()->success()->title('Cantidad actualizada')->send();
        } catch (\Throwable $e) {
            Notification::make()->danger()->title('Error al editar')->body($e->getMessage())->send();
        }
    }

    protected function ejecutarTraslado(array $data): Traslado
    {
        $emp = (int) session('id_empresa');
        $uid = (int) auth()->user()->usuario_id;

        return DB::transaction(function () use ($data, $emp, $uid): Traslado {
            $motSal  = MotivoMovimiento::where('id_empresa', $emp)->where('tipo', 'S')->where('nombre', 'Traslado salida')->value('id_motivo');
            $motIng  = MotivoMovimiento::where('id_empresa', $emp)->where('tipo', 'I')->where('nombre', 'Traslado entrada')->value('id_motivo');
            $nomOrig = Almacen::where('id_empresa', $emp)->where('codigo', $data['almacen_origen'])->value('nombre') ?? $data['almacen_origen'];
            $nomDest = Almacen::where('id_empresa', $emp)->where('codigo', $data['almacen_destino'])->value('nombre') ?? $data['almacen_destino'];
            $obs     = trim($data['observacion'] ?? '');

            // La fecha del form es solo día: se completa con la hora actual
            $fecha = filled($data['fecha'] ?? null)
                ? Carbon::parse($data['fecha'])->setTimeFrom(now())
                : now();

            $traslado = Traslado::create([
                'id_empresa'      => $emp,
                'almacen_origen'  => $data['almacen_origen'],
                'almacen_destino' => $data['almacen_destino'],
                'fecha'           => $fecha,
                'observacion'     => $obs ?: null,
                'id_usuario'      => $uid,
                'estado'          => '1',
            ]);

            $numero = TrasladoResource::numeroDocumento((int) $traslado->id_traslado);
            $lineas = 0;

            foreach ($data['detalles'] as $linea) {
                $cant = (int) ($linea['cantidad'] ?? 0);
                if ($cant < 1) {
                    continue;
                }

                $origen = Producto::where('id_empresa', $emp)
                    ->where('id_producto', $linea['id_producto'])
                    ->where('almacen', $data['almacen_origen'])
                    ->lockForUpdate()
                    ->first();

                if (! $origen) {
                    throw new \RuntimeException('Uno de los productos no pertenece al almacén origen seleccionado.');
                }

                if ($cant > (int) $origen->cantidad) {
                    throw new \RuntimeException("Stock insuficiente de \"{$origen->descripcion}\". Disponible: {$origen->cantidad}.");
                }

                // Salida del origen
                $antO   = (int) $origen->cantidad;
                $nuevoO = $antO - $cant;
                $origen->update(['cantidad' => $nuevoO]);
                InventarioMovimiento::create([
                    'id_empresa' => $emp, 'almacen' => $data['almacen_origen'], 'id_producto' => $origen->id_producto,
                    'tipo' => 'S', 'id_motivo' => $motSal, 'cantidad' => $cant,
                    'stock_anterior' => $antO, 'stock_nuevo' => $nuevoO, 'costo' => $origen->costo,
                    'observacion' => trim("Traslado {$numero} a {$nomDest}. {$obs}"), 'id_usuario' => $uid, 'fecha' => $fecha,
                ]);

                // Ingreso al destino (busca por código; si no existe, clona el producto)
                $dest = null;
                if (! empty($origen->codigo)) {
                    $dest = Producto::where('id_empresa', $emp)
                        ->where('almacen', $data['almacen_destino'])
                        ->where('codigo', $origen->codigo)
                        ->lockForUpdate()
                        ->first();
                }

                if ($dest) {
                    $antD   = (int) $dest->cantidad;
                    $nuevoD = $antD + $cant;
                    $dest->update(['cantidad' => $nuevoD]);
                } else {
                    $dest = $origen->replicate();
                    $dest->almacen  = $data['almacen_destino'];
                    $dest->cantidad = $cant;
                    $dest->save();
                    $antD   = 0;
                    $nuevoD = $cant;
                }

                InventarioMovimiento::create([
                    'id_empresa' => $emp, 'almacen' => $data['almacen_destino'], 'id_producto' => $dest->id_producto,
                    'tipo' => 'I', 'id_motivo' => $motIng, 'cantidad' => $cant,
                    'stock_anterior' => $antD, 'stock_nuevo' => $nuevoD, 'costo' => $dest->costo,
                    'observacion' => trim("Traslado {$numero} desde {$nomOrig}. {$obs}"), 'id_usuario' => $uid, 'fecha' => $fecha,
                ]);

                TrasladoDetalle::create([
                    'id_traslado'        => $traslado->id_traslado,
                    'id_producto'        => $origen->id_producto,
                    'cantidad'           => $cant,
                    'costo'              => $origen->costo,
                    'stock_ant_origen'   => $antO,
                    'stock_nuevo_origen' => $nuevoO,
                    'stock_ant_destino'  => $antD,
                    'stock_nuevo_destino' => $nuevoD,
                ]);

                $lineas++;
            }

            if ($lineas === 0) {
                throw new \RuntimeException('Agregá al menos un producto con cantidad mayor a 0.');
            }

            return $traslado;
        });
    }
}
