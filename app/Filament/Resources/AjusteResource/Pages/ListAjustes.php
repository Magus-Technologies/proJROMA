<?php

namespace App\Filament\Resources\AjusteResource\Pages;

use App\Filament\Resources\AjusteResource;
use App\Filament\Resources\AjusteResource\Widgets\AjusteStats;
use App\Models\Almacen;
use App\Models\InventarioMovimiento;
use App\Models\MotivoMovimiento;
use App\Models\Producto;
use Filament\Actions\Action;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;

class ListAjustes extends ListRecords
{
    protected static string $resource = AjusteResource::class;

    /** Stock actual de un producto en un almacén (0 si aún no existe ahí). */
    protected static function stockEnAlmacen(?int $idProducto, ?string $almacen): int
    {
        if (! $idProducto || ! $almacen) {
            return 0;
        }

        return (int) (Producto::where('id_empresa', (int) session('id_empresa'))
            ->where('id_producto', $idProducto)
            ->where('almacen', $almacen)
            ->value('cantidad') ?? 0);
    }

    protected function getHeaderWidgets(): array
    {
        return [
            AjusteStats::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('nuevo_ajuste')
                ->label('Nuevo Ajuste / Cuadre')
                ->icon('heroicon-o-adjustments-horizontal')
                ->color('primary')
                ->modalWidth('5xl')
                ->modalHeading('Nuevo Ajuste / Cuadre de stock')
                ->form([
                    Select::make('almacen')
                        ->label('Almacén')
                        ->options(fn () => Almacen::where('id_empresa', (int) session('id_empresa'))
                            ->where('estado', 1)
                            ->pluck('nombre', 'codigo')
                            ->toArray())
                        ->live()
                        ->required(),

                    Select::make('modo')
                        ->label('Tipo de ajuste')
                        ->options([
                            'INGRESO' => '▲ Ingreso — agregar stock (encontrado, devolución, etc.)',
                            'SALIDA'  => '▼ Salida / Pérdida — retirar stock (merma, rotura, robo, vencido)',
                            'CUADRE'  => '⚖ Cuadre — indicar el stock físico contado y el sistema calcula la diferencia',
                        ])
                        ->live()
                        // Al cambiar el tipo se limpia el motivo: el elegido
                        // podría no corresponder al nuevo tipo.
                        ->afterStateUpdated(fn (callable $set) => $set('id_motivo', null))
                        ->required(),

                    Select::make('id_motivo')
                        ->label('Motivo')
                        ->visible(fn (callable $get): bool => filled($get('modo')))
                        // Solo motivos compatibles con el tipo de ajuste elegido
                        // (I = ingreso, S = salida, A = ambos). En cuadre se
                        // muestran todos porque puede resultar en cualquiera.
                        ->options(function (callable $get) {
                            $tipos = match ($get('modo')) {
                                'INGRESO' => ['I', 'A'],
                                'SALIDA'  => ['S', 'A'],
                                default   => null,
                            };

                            return MotivoMovimiento::where('id_empresa', (int) session('id_empresa'))
                                ->whereNotIn('nombre', AjusteResource::AUTOMATIZADOS)
                                ->where('estado', 1)
                                ->when($tipos, fn ($q) => $q->whereIn('tipo', $tipos))
                                ->pluck('nombre', 'id_motivo')
                                ->toArray();
                        })
                        ->createOptionForm(fn (callable $get): array => [
                            TextInput::make('nombre')
                                ->label('Nombre del motivo')
                                ->required()
                                ->maxLength(100),
                            Select::make('tipo')
                                ->label('Tipo')
                                ->options(['I' => 'Ingreso', 'S' => 'Salida', 'A' => 'Ambos'])
                                ->default(match ($get('modo')) {
                                    'INGRESO' => 'I',
                                    'SALIDA'  => 'S',
                                    default   => 'A',
                                })
                                ->required(),
                        ])
                        ->createOptionUsing(fn (array $data): int =>
                            MotivoMovimiento::create(array_merge($data, [
                                'id_empresa' => (int) session('id_empresa'),
                                'estado'     => 1,
                            ]))->id_motivo),

                    Repeater::make('productos')
                        ->label('Productos')
                        ->addActionLabel('+ Agregar producto')
                        ->visible(fn (callable $get): bool => filled($get('almacen')) && filled($get('modo')))
                        ->schema([
                            Select::make('id_producto')
                                ->label('Producto')
                                // El stock mostrado es el del ALMACÉN elegido (no el global):
                                // si el producto aún no existe ahí, parte de 0.
                                ->options(function (callable $get) {
                                    $emp = (int) session('id_empresa');
                                    $alm = $get('../../almacen');

                                    return Producto::where('id_empresa', $emp)
                                        ->orderBy('descripcion')
                                        ->limit(1000)
                                        ->get()
                                        ->groupBy('id_producto')
                                        ->mapWithKeys(function ($filas, $id) use ($alm) {
                                            $enAlmacen = $filas->firstWhere('almacen', $alm);

                                            return [$id => $filas->first()->descripcion
                                                . ' — stock aquí: ' . (int) ($enAlmacen->cantidad ?? 0)];
                                        })
                                        ->toArray();
                                })
                                ->searchable()
                                ->required()
                                ->live()
                                ->columnSpan(3),

                            Placeholder::make('stock_actual')
                                ->label('Stock actual')
                                ->content(fn (callable $get): string => (string) static::stockEnAlmacen(
                                    (int) $get('id_producto'),
                                    $get('../../almacen'),
                                )),

                            TextInput::make('cantidad')
                                ->label(fn (callable $get): string => match ($get('../../modo')) {
                                    'INGRESO' => 'Cantidad a ingresar',
                                    'SALIDA'  => 'Cantidad a retirar',
                                    default   => 'Stock contado',
                                })
                                ->numeric()
                                ->integer()
                                ->minValue(fn (callable $get): int => $get('../../modo') === 'CUADRE' ? 0 : 1)
                                ->required()
                                ->live(debounce: 400),

                            Placeholder::make('efecto')
                                ->label('Stock quedará en')
                                ->content(function (callable $get): HtmlString {
                                    if ($get('cantidad') === null || $get('cantidad') === '' || ! $get('id_producto')) {
                                        return new HtmlString('<span style="color:#9ca3af">—</span>');
                                    }

                                    $actual = static::stockEnAlmacen((int) $get('id_producto'), $get('../../almacen'));
                                    $cantidad = (int) $get('cantidad');

                                    [$nuevo, $dif] = match ($get('../../modo')) {
                                        'INGRESO' => [$actual + $cantidad, $cantidad],
                                        'SALIDA'  => [$actual - $cantidad, -$cantidad],
                                        default   => [$cantidad, $cantidad - $actual],
                                    };

                                    if ($nuevo < 0) {
                                        return new HtmlString('<span style="color:#dc2626;font-weight:600">⚠ Excede el stock (' . $actual . ')</span>');
                                    }

                                    return new HtmlString(match (true) {
                                        $dif > 0 => '<span style="color:#059669;font-weight:600">' . $nuevo . ' (▲ +' . $dif . ')</span>',
                                        $dif < 0 => '<span style="color:#dc2626;font-weight:600">' . $nuevo . ' (▼ −' . abs($dif) . ')</span>',
                                        default  => '<span style="color:#9ca3af">' . $nuevo . ' (sin cambio)</span>',
                                    });
                                }),
                        ])
                        ->columns(6)
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
        $modo = $data['modo'] ?? 'CUADRE';
        $ajustados = 0;

        DB::transaction(function () use ($data, $emp, $uid, $obs, $modo, &$ajustados): void {
            foreach ($data['productos'] as $item) {
                $idProducto = (int) $item['id_producto'];
                $cantidad   = (int) $item['cantidad'];

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

                $anterior = (int) $producto->cantidad;

                // El stock final depende del modo elegido:
                // INGRESO/SALIDA usan la cantidad directa; CUADRE la trata
                // como el stock físico contado.
                $nuevo = match ($modo) {
                    'INGRESO' => $anterior + $cantidad,
                    'SALIDA'  => $anterior - $cantidad,
                    default   => $cantidad,
                };

                $diferencia = $nuevo - $anterior;

                if ($diferencia === 0) {
                    continue;
                }

                $tipo = $diferencia > 0 ? 'I' : 'S';
                $cant = abs($diferencia);

                if ($tipo === 'S' && $cant > $anterior) {
                    throw new \RuntimeException("Stock insuficiente para {$producto->descripcion}. Disponible: {$anterior}, intentas retirar {$cant}.");
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
