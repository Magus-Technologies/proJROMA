<?php

namespace App\Filament\Resources\VentaResource\Pages;

use App\Filament\Resources\VentaResource;
use App\Models\Cliente;
use App\Models\DiasVenta;
use App\Models\DocumentoEmpresa;
use App\Models\InventarioMovimiento;
use App\Models\MotivoMovimiento;
use App\Models\Producto;
use App\Models\ProductoVenta;
use App\Models\Venta;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;
use Illuminate\Validation\ValidationException;

class CreateVenta extends CreateRecord
{
    protected static string $resource = VentaResource::class;

    protected static ?string $title = 'Nueva Venta';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Comprobante')
                ->columns(4)
                ->schema([
                    Select::make('id_tido')
                        ->label('Tipo de comprobante')
                        ->options(fn (): array => DB::table('documentos_empresas as de')
                            ->join('documentos_sunat as ds', 'ds.id_tido', '=', 'de.id_tido')
                            ->where('de.id_empresa', (int) session('id_empresa'))
                            ->where('de.sucursal', (int) session('sucursal'))
                            ->whereIn('de.id_tido', [1, 2, 6])
                            ->selectRaw("de.id_tido, CONCAT(ds.nombre, ' (', de.serie, '-', LPAD(de.numero + 1, 8, '0'), ')') as etiqueta")
                            ->pluck('etiqueta', 'id_tido')
                            ->toArray())
                        ->required(),

                    Select::make('id_cliente')
                        ->label('Cliente')
                        ->searchable()
                        ->getSearchResultsUsing(fn (string $search): array => Cliente::where('id_empresa', (int) session('id_empresa'))
                            ->where(fn ($q) => $q
                                ->where('datos', 'like', "%{$search}%")
                                ->orWhere('documento', 'like', "%{$search}%"))
                            ->limit(30)
                            ->pluck('datos', 'id_cliente')
                            ->toArray())
                        ->getOptionLabelUsing(fn ($value): ?string => Cliente::find($value)?->datos)
                        ->createOptionForm([
                            TextInput::make('documento')->label('RUC / DNI')->maxLength(15),
                            TextInput::make('datos')->label('Nombre / Razón Social')->required()->maxLength(200),
                            TextInput::make('telefono')->label('Teléfono')->tel()->maxLength(20),
                        ])
                        ->createOptionUsing(fn (array $data): int => Cliente::create(array_merge($data, [
                            'id_empresa' => (int) session('id_empresa'),
                        ]))->id_cliente)
                        ->required(),

                    DatePicker::make('fecha')
                        ->label('Fecha de emisión')
                        ->default(now())
                        ->required(),

                    Select::make('id_tipo_pago')
                        ->label('Forma de pago')
                        ->options([
                            1 => 'Contado',
                            2 => 'Crédito',
                        ])
                        ->default(1)
                        ->live()
                        ->required(),

                    DatePicker::make('fecha_vencimiento')
                        ->label('Fecha de vencimiento')
                        ->visible(fn (callable $get): bool => (int) $get('id_tipo_pago') === 2)
                        ->requiredIf('id_tipo_pago', 2)
                        ->after('fecha'),
                ]),

            Section::make('Productos')
                ->schema([
                    Repeater::make('productos')
                        ->hiddenLabel()
                        ->columns(12)
                        ->minItems(1)
                        ->defaultItems(1)
                        ->live()
                        ->schema([
                            Select::make('id_producto')
                                ->label('Producto')
                                ->searchable()
                                ->getSearchResultsUsing(fn (string $search): array => Producto::where('id_empresa', (int) session('id_empresa'))
                                    ->where('cantidad', '>', 0)
                                    ->where(fn ($q) => $q
                                        ->where('descripcion', 'like', "%{$search}%")
                                        ->orWhere('codigo', 'like', "%{$search}%"))
                                    ->limit(30)
                                    ->get()
                                    ->mapWithKeys(fn (Producto $p) => [
                                        $p->id_producto => "{$p->descripcion} — S/ " . number_format((float) $p->precio, 2) . " (stock: {$p->cantidad})",
                                    ])
                                    ->toArray())
                                ->getOptionLabelUsing(fn ($value): ?string => Producto::find($value)?->descripcion)
                                ->live()
                                ->afterStateUpdated(function ($state, callable $set): void {
                                    $p = Producto::find($state);
                                    $set('precio', $p ? (string) $p->precio : null);
                                    $set('cantidad', 1);
                                })
                                ->required()
                                ->columnSpan(6),

                            TextInput::make('cantidad')
                                ->label('Cantidad')
                                ->numeric()
                                ->minValue(0.001)
                                ->default(1)
                                ->live(onBlur: true)
                                ->required()
                                ->columnSpan(2),

                            TextInput::make('precio')
                                ->label('Precio (S/)')
                                ->numeric()
                                ->minValue(0)
                                ->live(onBlur: true)
                                ->required()
                                ->columnSpan(2),

                            Placeholder::make('linea_total')
                                ->label('Total')
                                ->content(fn (callable $get): string =>
                                    'S/ ' . number_format((float) $get('cantidad') * (float) $get('precio'), 2))
                                ->columnSpan(2),
                        ]),

                    Placeholder::make('resumen')
                        ->hiddenLabel()
                        ->content(function (callable $get): HtmlString {
                            $total = collect($get('productos') ?? [])
                                ->sum(fn (array $l): float => (float) ($l['cantidad'] ?? 0) * (float) ($l['precio'] ?? 0));
                            $subtotal = $total / 1.18;
                            $igv      = $total - $subtotal;

                            return new HtmlString(
                                '<div style="text-align:right;font-size:1rem;line-height:1.9">'
                                . 'Subtotal: <strong>S/ ' . number_format($subtotal, 2) . '</strong><br>'
                                . 'IGV (18%): <strong>S/ ' . number_format($igv, 2) . '</strong><br>'
                                . '<span style="font-size:1.25rem">Total: <strong>S/ ' . number_format($total, 2) . '</strong></span>'
                                . '</div>'
                            );
                        }),
                ]),

            Section::make('Detalles adicionales')
                ->columns(2)
                ->collapsed()
                ->schema([
                    TextInput::make('direccion')
                        ->label('Dirección de entrega')
                        ->maxLength(220),

                    Textarea::make('observacion')
                        ->label('Observación')
                        ->maxLength(220),
                ]),

            Section::make('Cuotas de pago')
                ->visible(fn (callable $get): bool => (int) $get('id_tipo_pago') === 2)
                ->schema([
                    Repeater::make('lista_pagos')
                        ->hiddenLabel()
                        ->columns(4)
                        ->defaultItems(1)
                        ->schema([
                            DatePicker::make('fecha')
                                ->label('Fecha de cuota')
                                ->required(),
                            TextInput::make('monto')
                                ->label('Monto (S/)')
                                ->numeric()
                                ->minValue(0.01)
                                ->required(),
                            Select::make('tipo_pago')
                                ->label('Tipo de pago')
                                ->options([
                                    'EFECTIVO'      => 'Efectivo',
                                    'YAPE'          => 'Yape',
                                    'PLIN'          => 'Plin',
                                    'TRANSFERENCIA' => 'Transferencia',
                                    'DEPOSITO'      => 'Depósito',
                                ])
                                ->default('EFECTIVO'),
                            Toggle::make('pagado')
                                ->label('Ya pagado')
                                ->inline(false),
                        ]),
                ]),
        ]);
    }

    protected function handleRecordCreation(array $data): Model
    {
        return DB::transaction(function () use ($data): Venta {
            $empresa  = (int) session('id_empresa');
            $sucursal = (int) session('sucursal');
            $usuario  = (int) auth()->user()->usuario_id;

            // Validate stock before touching anything
            $lineas = [];
            $total  = 0.0;
            foreach ($data['productos'] as $linea) {
                $producto = Producto::where('id_empresa', $empresa)
                    ->where('id_producto', $linea['id_producto'])
                    ->lockForUpdate()
                    ->firstOrFail();

                $cantidad = (float) $linea['cantidad'];
                if ($cantidad > (float) $producto->cantidad) {
                    throw ValidationException::withMessages([
                        'productos' => "Stock insuficiente de \"{$producto->descripcion}\" (disponible: {$producto->cantidad}).",
                    ]);
                }

                $lineaTotal = round($cantidad * (float) $linea['precio'], 2);
                $total     += $lineaTotal;
                $lineas[]   = [$producto, $cantidad, (float) $linea['precio'], $lineaTotal];
            }

            if ($total <= 0) {
                throw ValidationException::withMessages(['productos' => 'El total debe ser mayor a 0.']);
            }

            // Correlative number with lock (same as legacy API)
            $tido = DocumentoEmpresa::where('id_empresa', $empresa)
                ->where('sucursal', $sucursal)
                ->where('id_tido', $data['id_tido'])
                ->lockForUpdate()
                ->firstOrFail();
            $numero = $tido->numero + 1;

            $venta = Venta::create([
                'id_tido'           => $data['id_tido'],
                'id_tipo_pago'      => $data['id_tipo_pago'],
                'fecha_emision'     => $data['fecha'],
                'fecha_vencimiento' => $data['fecha_vencimiento'] ?? $data['fecha'],
                'direccion'         => $data['direccion'] ?? '-',
                'serie'             => $tido->serie,
                'numero'            => $numero,
                'id_cliente'        => $data['id_cliente'],
                'total'             => $total,
                'subtotal'          => round($total / 1.18, 2),
                'igv'               => round($total - $total / 1.18, 2),
                'apli_igv'          => '1',
                'estado'            => '1',
                'enviado_sunat'     => '0',
                'id_empresa'        => $empresa,
                'sucursal'          => $sucursal,
                'id_vendedor'       => $usuario,
                'observacion'       => $data['observacion'] ?? null,
            ]);

            $tido->increment('numero');

            $motivoVenta = MotivoMovimiento::where('id_empresa', $empresa)
                ->where('nombre', 'Venta')
                ->value('id_motivo');
            $doc = "{$tido->serie}-" . str_pad((string) $numero, 8, '0', STR_PAD_LEFT);

            foreach ($lineas as [$producto, $cantidad, $precio, $lineaTotal]) {
                ProductoVenta::create([
                    'id_venta'    => $venta->id_venta,
                    'id_producto' => $producto->id_producto,
                    'descripcion' => $producto->descripcion,
                    'cantidad'    => $cantidad,
                    'precio'      => $precio,
                    'total'       => $lineaTotal,
                    'igv_prod'    => 0,
                    'descuento'   => 0,
                ]);

                $anterior = (int) $producto->cantidad;
                $producto->decrement('cantidad', $cantidad);

                InventarioMovimiento::create([
                    'id_empresa'     => $empresa,
                    'almacen'        => $producto->almacen ?? '',
                    'id_producto'    => $producto->id_producto,
                    'tipo'           => 'S',
                    'id_motivo'      => $motivoVenta,
                    'cantidad'       => (int) $cantidad,
                    'stock_anterior' => $anterior,
                    'stock_nuevo'    => $anterior - (int) $cantidad,
                    'costo'          => $producto->costo,
                    'observacion'    => "Venta {$doc}",
                    'id_usuario'     => $usuario,
                    'fecha'          => now(),
                ]);
            }

            foreach ($data['lista_pagos'] ?? [] as $pago) {
                DiasVenta::create([
                    'id_venta'   => $venta->id_venta,
                    'fecha'      => $pago['fecha'],
                    'monto'      => $pago['monto'],
                    'estado'     => ($pago['pagado'] ?? false) ? '1' : '0',
                    'tipo_pago'  => $pago['tipo_pago'] ?? 'EFECTIVO',
                    'id_usuario' => $usuario,
                ]);
            }

            Cliente::where('id_cliente', $data['id_cliente'])->update([
                'ultima_venta' => now()->toDateString(),
                'total_venta'  => DB::raw('IFNULL(total_venta, 0) + ' . $total),
            ]);

            Notification::make()->success()
                ->title("Venta {$doc} registrada")
                ->body('Total: S/ ' . number_format($total, 2))
                ->send();

            return $venta;
        });
    }

    protected function getRedirectUrl(): string
    {
        return VentaResource::getUrl('view', ['record' => $this->getRecord()]);
    }
}
