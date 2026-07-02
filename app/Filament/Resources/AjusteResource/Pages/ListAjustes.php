<?php

namespace App\Filament\Resources\AjusteResource\Pages;

use App\Filament\Resources\AjusteResource;
use App\Models\Almacen;
use App\Models\InventarioMovimiento;
use App\Models\MotivoMovimiento;
use App\Models\Producto;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\DB;

class ListAjustes extends ListRecords
{
    protected static string $resource = AjusteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('nuevo_ajuste')
                ->label('Nuevo Ajuste / Cuadre')
                ->icon('heroicon-o-adjustments-horizontal')
                ->color('primary')
                ->form([
                    Select::make('almacen')
                        ->label('Almacén')
                        ->options(fn () => Almacen::where('id_empresa', (int) session('id_empresa'))
                            ->where('estado', 1)
                            ->pluck('nombre', 'codigo')
                            ->toArray())
                        ->live()
                        ->required(),

                    Select::make('id_motivo')
                        ->label('Motivo')
                        ->options(fn () => MotivoMovimiento::where('id_empresa', (int) session('id_empresa'))
                            ->whereNotIn('nombre', AjusteResource::AUTOMATIZADOS)
                            ->pluck('nombre', 'id_motivo')
                            ->toArray())
                        ->createOptionForm([
                            TextInput::make('nombre')
                                ->label('Nombre del motivo')
                                ->required()
                                ->maxLength(100),
                            Select::make('tipo')
                                ->label('Tipo')
                                ->options(['I' => 'Ingreso', 'S' => 'Salida', 'A' => 'Ambos'])
                                ->default('A')
                                ->required(),
                        ])
                        ->createOptionUsing(fn (array $data): int =>
                            MotivoMovimiento::create(array_merge($data, [
                                'id_empresa' => (int) session('id_empresa'),
                                'estado'     => 1,
                            ]))->id_motivo),

                    Repeater::make('productos')
                        ->label('Productos a cuadrar')
                        ->schema([
                            Select::make('id_producto')
                                ->label('Producto')
                                ->options(fn (callable $get) => Producto::where('id_empresa', (int) session('id_empresa'))
                                    ->orderBy('descripcion')
                                    ->limit(500)
                                    ->get()
                                    ->mapWithKeys(fn (Producto $p) => [
                                        $p->id_producto => "{$p->descripcion} (stock: {$p->cantidad})",
                                    ])
                                    ->toArray())
                                ->searchable()
                                ->required()
                                ->columnSpan(2),

                            TextInput::make('nuevo_stock')
                                ->label('Nuevo stock')
                                ->numeric()
                                ->integer()
                                ->minValue(0)
                                ->required(),
                        ])
                        ->columns(3)
                        ->minItems(1)
                        ->defaultItems(1),

                    TextInput::make('observacion')
                        ->label('Observación')
                        ->maxLength(255),
                ])
                ->action(function (array $data): void {
                    try {
                        $ajustados = $this->guardarBatch($data);
                        Notification::make()->success()
                            ->title('Ajuste registrado')
                            ->body("{$ajustados} producto(s) ajustado(s).")
                            ->send();
                    } catch (\Throwable $e) {
                        Notification::make()->danger()->title('Error en ajuste')->body($e->getMessage())->send();
                    }
                }),
        ];
    }

    protected function guardarBatch(array $data): int
    {
        $emp = (int) session('id_empresa');
        $uid = (int) auth()->user()->usuario_id;
        $obs = trim($data['observacion'] ?? '');
        $ajustados = 0;

        DB::transaction(function () use ($data, $emp, $uid, $obs, &$ajustados): void {
            foreach ($data['productos'] as $item) {
                $idProducto = (int) $item['id_producto'];
                $nuevo      = (int) $item['nuevo_stock'];

                $producto = Producto::where('id_empresa', $emp)
                    ->where('id_producto', $idProducto)
                    ->where('almacen', $data['almacen'])
                    ->lockForUpdate()
                    ->first();

                // Si no existe en el almacén destino, clonarlo con stock 0
                if (! $producto) {
                    $origen = Producto::where('id_empresa', $emp)
                        ->where('id_producto', $idProducto)
                        ->lockForUpdate()
                        ->firstOrFail();

                    $producto = $origen->replicate();
                    $producto->almacen  = $data['almacen'];
                    $producto->cantidad = 0;
                    $producto->save();
                }

                $anterior   = (int) $producto->cantidad;
                $diferencia = $nuevo - $anterior;

                if ($diferencia === 0) {
                    continue;
                }

                $tipo = $diferencia > 0 ? 'I' : 'S';
                $cant = abs($diferencia);

                if ($tipo === 'S' && $cant > $anterior) {
                    throw new \RuntimeException("Stock insuficiente para {$producto->descripcion}. Disponible: {$anterior}.");
                }

                $producto->update(['cantidad' => $nuevo]);

                InventarioMovimiento::create([
                    'id_empresa'     => $emp,
                    'almacen'        => $data['almacen'],
                    'id_producto'    => $producto->id_producto,
                    'tipo'           => $tipo,
                    'id_motivo'      => $data['id_motivo'] ?? null,
                    'cantidad'       => $cant,
                    'stock_anterior' => $anterior,
                    'stock_nuevo'    => $nuevo,
                    'observacion'    => $obs ?: 'Cuadre de stock',
                    'id_usuario'     => $uid,
                    'fecha'          => now(),
                ]);

                $ajustados++;
            }
        });

        return $ajustados;
    }
}
