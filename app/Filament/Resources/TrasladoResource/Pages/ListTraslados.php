<?php

namespace App\Filament\Resources\TrasladoResource\Pages;

use App\Filament\Resources\TrasladoResource;
use App\Models\Almacen;
use App\Models\InventarioMovimiento;
use App\Models\MotivoMovimiento;
use App\Models\Producto;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
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
                ->label('Nuevo Traslado')
                ->icon('heroicon-o-arrows-right-left')
                ->color('primary')
                ->form([
                    Select::make('almacen_origen')
                        ->label('Almacén origen')
                        ->options($almacenes)
                        ->live()
                        ->required(),

                    Select::make('id_producto')
                        ->label('Producto')
                        ->options(fn (callable $get) => Producto::where('id_empresa', (int) session('id_empresa'))
                            ->when($get('almacen_origen'), fn ($q, $alm) => $q->where('almacen', $alm))
                            ->where('cantidad', '>', 0)
                            ->orderBy('descripcion')
                            ->limit(500)
                            ->get()
                            ->mapWithKeys(fn (Producto $p) => [
                                $p->id_producto => "{$p->descripcion} (stock: {$p->cantidad})",
                            ])
                            ->toArray())
                        ->searchable()
                        ->required(),

                    Select::make('almacen_destino')
                        ->label('Almacén destino')
                        ->options($almacenes)
                        ->different('almacen_origen')
                        ->required(),

                    TextInput::make('cantidad')
                        ->label('Cantidad')
                        ->numeric()
                        ->integer()
                        ->minValue(1)
                        ->required(),

                    TextInput::make('observacion')
                        ->label('Observación')
                        ->maxLength(200),
                ])
                ->action(function (array $data): void {
                    try {
                        $this->ejecutarTraslado($data);
                        Notification::make()->success()->title('Traslado realizado')->send();
                    } catch (\Throwable $e) {
                        Notification::make()->danger()->title('Error en traslado')->body($e->getMessage())->send();
                    }
                }),
        ];
    }

    protected function ejecutarTraslado(array $data): void
    {
        $emp  = (int) session('id_empresa');
        $cant = (int) $data['cantidad'];

        DB::transaction(function () use ($data, $emp, $cant): void {
            $origen = Producto::where('id_empresa', $emp)
                ->where('id_producto', $data['id_producto'])
                ->where('almacen', $data['almacen_origen'])
                ->lockForUpdate()
                ->firstOrFail();

            if ($cant > (int) $origen->cantidad) {
                throw new \RuntimeException("Stock insuficiente en el origen. Disponible: {$origen->cantidad}.");
            }

            $motSal = MotivoMovimiento::where('id_empresa', $emp)->where('tipo', 'S')->where('nombre', 'Traslado salida')->value('id_motivo');
            $motIng = MotivoMovimiento::where('id_empresa', $emp)->where('tipo', 'I')->where('nombre', 'Traslado entrada')->value('id_motivo');
            $nomOrig = Almacen::where('id_empresa', $emp)->where('codigo', $data['almacen_origen'])->value('nombre') ?? $data['almacen_origen'];
            $nomDest = Almacen::where('id_empresa', $emp)->where('codigo', $data['almacen_destino'])->value('nombre') ?? $data['almacen_destino'];
            $obs = trim($data['observacion'] ?? '');
            $uid = (int) auth()->user()->usuario_id;

            // Salida del origen
            $antO   = (int) $origen->cantidad;
            $nuevoO = $antO - $cant;
            $origen->update(['cantidad' => $nuevoO]);
            InventarioMovimiento::create([
                'id_empresa' => $emp, 'almacen' => $data['almacen_origen'], 'id_producto' => $origen->id_producto,
                'tipo' => 'S', 'id_motivo' => $motSal, 'cantidad' => $cant,
                'stock_anterior' => $antO, 'stock_nuevo' => $nuevoO, 'costo' => $origen->costo,
                'observacion' => trim("Traslado a {$nomDest}. {$obs}"), 'id_usuario' => $uid, 'fecha' => now(),
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
                $antD = 0;
                $nuevoD = $cant;
            }

            InventarioMovimiento::create([
                'id_empresa' => $emp, 'almacen' => $data['almacen_destino'], 'id_producto' => $dest->id_producto,
                'tipo' => 'I', 'id_motivo' => $motIng, 'cantidad' => $cant,
                'stock_anterior' => $antD, 'stock_nuevo' => $nuevoD, 'costo' => $dest->costo,
                'observacion' => trim("Traslado desde {$nomOrig}. {$obs}"), 'id_usuario' => $uid, 'fecha' => now(),
            ]);
        });
    }
}
