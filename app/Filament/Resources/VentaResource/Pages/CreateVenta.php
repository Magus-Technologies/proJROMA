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
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
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
            Grid::make(['default' => 1, 'xl' => 3])
                ->columnSpanFull()
                ->schema([
                    // ── COLUMNA IZQUIERDA (ancha): buscador + tabla de productos ──
                    Group::make([
                        Section::make('Productos')
                            ->compact()
                            ->schema([
                                Select::make('buscador_producto')
                                    ->hiddenLabel()
                                    ->placeholder('🔍 Buscar producto por descripción o código y agregarlo…')
                                    ->searchable()
                                    ->dehydrated(false)
                                    ->live()
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
                                    ->afterStateUpdated(function ($state, callable $set, callable $get): void {
                                        if (! $state) {
                                            return;
                                        }

                                        $p = Producto::find($state);
                                        if (! $p) {
                                            return;
                                        }

                                        $items = $get('productos') ?? [];

                                        // If already in the list, bump its quantity instead of duplicating
                                        foreach ($items as $key => $item) {
                                            if ((int) ($item['id_producto'] ?? 0) === (int) $p->id_producto) {
                                                $items[$key]['cantidad']    = (float) $item['cantidad'] + 1;
                                                $items[$key]['linea_total'] = number_format($items[$key]['cantidad'] * (float) $item['precio'], 2, '.', '');
                                                $set('productos', $items);
                                                $set('buscador_producto', null);

                                                return;
                                            }
                                        }

                                        $items[] = [
                                            'id_producto' => $p->id_producto,
                                            'descripcion' => $p->descripcion,
                                            'cantidad'    => 1,
                                            'precio'      => number_format((float) $p->precio, 2, '.', ''),
                                            'linea_total' => number_format((float) $p->precio, 2, '.', ''),
                                        ];
                                        $set('productos', $items);
                                        $set('buscador_producto', null);
                                    }),

                                Placeholder::make('tabla_vacia')
                                    ->hiddenLabel()
                                    ->visible(fn (callable $get): bool => blank($get('productos')))
                                    ->content(new HtmlString(
                                        '<table style="width:100%;border-collapse:collapse;font-size:.875rem">'
                                        . '<thead><tr style="border-bottom:1px solid rgba(128,128,128,.25);text-align:left;opacity:.6">'
                                        . '<th style="padding:8px 12px;font-weight:600">Producto</th>'
                                        . '<th style="padding:8px 12px;font-weight:600;width:110px">Cant.</th>'
                                        . '<th style="padding:8px 12px;font-weight:600;width:140px">Precio</th>'
                                        . '<th style="padding:8px 12px;font-weight:600;width:140px">Total</th>'
                                        . '</tr></thead>'
                                        . '<tbody><tr><td colspan="4" style="padding:28px 12px;text-align:center;opacity:.45">'
                                        . 'Sin productos agregados — usá el buscador de arriba'
                                        . '</td></tr></tbody></table>'
                                    )),

                                Repeater::make('productos')
                                    ->hiddenLabel()
                                    ->minItems(1)
                                    ->defaultItems(0)
                                    ->addable(false)
                                    ->reorderable(false)
                                    ->live()
                                    ->table([
                                        TableColumn::make('Producto'),
                                        TableColumn::make('Cant.')->width('110px'),
                                        TableColumn::make('Precio')->width('140px'),
                                        TableColumn::make('Total')->width('140px'),
                                    ])
                                    ->schema([
                                        Hidden::make('id_producto'),

                                        TextInput::make('descripcion')
                                            ->hiddenLabel()
                                            ->readOnly()
                                            ->dehydrated(false),

                                        TextInput::make('cantidad')
                                            ->hiddenLabel()
                                            ->numeric()
                                            ->minValue(0.001)
                                            ->default(1)
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(fn ($state, callable $set, callable $get) =>
                                                $set('linea_total', number_format((float) $state * (float) $get('precio'), 2, '.', '')))
                                            ->required(),

                                        TextInput::make('precio')
                                            ->hiddenLabel()
                                            ->numeric()
                                            ->minValue(0)
                                            ->prefix('S/')
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(fn ($state, callable $set, callable $get) =>
                                                $set('linea_total', number_format((float) $get('cantidad') * (float) $state, 2, '.', '')))
                                            ->required(),

                                        TextInput::make('linea_total')
                                            ->hiddenLabel()
                                            ->prefix('S/')
                                            ->readOnly()
                                            ->dehydrated(false),
                                    ]),
                            ]),

                        Section::make('Cuotas de pago')
                            ->compact()
                            ->description('Programá las cuotas del crédito')
                            ->visible(fn (callable $get): bool => (int) $get('id_tipo_pago') === 2)
                            ->schema([
                                Repeater::make('lista_pagos')
                                    ->hiddenLabel()
                                    ->columns(4)
                                    ->defaultItems(1)
                                    ->addActionLabel('Agregar cuota')
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
                    ])->columnSpan(['default' => 1, 'xl' => 2]),

                    // ── COLUMNA DERECHA (angosta): comprobante, cliente, resumen ──
                    Group::make([
                        Section::make('Comprobante')
                            ->compact()
                            ->columns(2)
                            ->schema([
                                Select::make('id_tido')
                                    ->label('Tipo de documento')
                                    ->options(fn (): array => DB::table('documentos_empresas as de')
                                        ->join('documentos_sunat as ds', 'ds.id_tido', '=', 'de.id_tido')
                                        ->where('de.id_empresa', (int) session('id_empresa'))
                                        ->where('de.sucursal', (int) session('sucursal'))
                                        ->whereIn('de.id_tido', [1, 2, 6])
                                        ->selectRaw("de.id_tido, CONCAT(ds.nombre, ' — ', de.serie, '-', LPAD(de.numero + 1, 8, '0')) as etiqueta")
                                        ->pluck('etiqueta', 'id_tido')
                                        ->toArray())
                                    ->required()
                                    ->columnSpanFull(),

                                DatePicker::make('fecha')
                                    ->label('Fecha emisión')
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
                                    ->label('Fecha vencimiento')
                                    ->visible(fn (callable $get): bool => (int) $get('id_tipo_pago') === 2)
                                    ->requiredIf('id_tipo_pago', 2)
                                    ->after('fecha')
                                    ->columnSpanFull(),

                                TextInput::make('observacion')
                                    ->label('Observación')
                                    ->placeholder('Opcional')
                                    ->maxLength(220)
                                    ->columnSpanFull(),

                                TextInput::make('direccion')
                                    ->label('Dirección de entrega')
                                    ->placeholder('Opcional')
                                    ->maxLength(220)
                                    ->columnSpanFull(),
                            ]),

                        Section::make('Cliente')
                            ->compact()
                            ->schema([
                                Select::make('id_cliente')
                                    ->hiddenLabel()
                                    ->placeholder('Buscar por nombre o documento…')
                                    ->searchable()
                                    ->getSearchResultsUsing(fn (string $search): array => Cliente::where('id_empresa', (int) session('id_empresa'))
                                        ->where(fn ($q) => $q
                                            ->where('datos', 'like', "%{$search}%")
                                            ->orWhere('documento', 'like', "%{$search}%"))
                                        ->limit(30)
                                        ->get()
                                        ->mapWithKeys(fn (Cliente $c) => [
                                            $c->id_cliente => $c->datos . ($c->documento ? " — {$c->documento}" : ''),
                                        ])
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
                            ]),

                        Section::make('Resumen')
                            ->compact()
                            ->schema([
                                Placeholder::make('resumen')
                                    ->hiddenLabel()
                                    ->content(function (callable $get): HtmlString {
                                        $total = collect($get('productos') ?? [])
                                            ->sum(fn (array $l): float => (float) ($l['cantidad'] ?? 0) * (float) ($l['precio'] ?? 0));
                                        $subtotal = $total / 1.18;
                                        $igv      = $total - $subtotal;

                                        return new HtmlString(
                                            '<div style="line-height:2">'
                                            . '<div style="display:flex;justify-content:space-between;opacity:.7">'
                                            . '<span>Op. Gravadas:</span><span style="font-weight:600">S/ ' . number_format($subtotal, 2) . '</span></div>'
                                            . '<div style="display:flex;justify-content:space-between;opacity:.7">'
                                            . '<span>IGV (18%):</span><span style="font-weight:600">S/ ' . number_format($igv, 2) . '</span></div>'
                                            . '<div style="display:flex;justify-content:space-between;align-items:center;'
                                            . 'border-top:1px solid rgba(128,128,128,.25);margin-top:8px;padding-top:10px">'
                                            . '<span style="font-weight:700">IMPORTE TOTAL:</span>'
                                            . '<span style="font-weight:800;font-size:1.35rem;color:rgb(59,130,246)">S/ ' . number_format($total, 2) . '</span></div>'
                                            . '</div>'
                                        );
                                    }),
                            ]),
                    ])->columnSpan(1),
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
