<?php

namespace App\Filament\Resources\CotizacionResource\Pages;

use App\Filament\Resources\CotizacionResource;
use App\Models\Cliente;
use App\Models\Cotizacion;
use App\Models\CuotaCotizacion;
use App\Models\DocumentoEmpresa;
use App\Models\Producto;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
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

class CreateCotizacion extends CreateRecord
{
    protected static string $resource = CotizacionResource::class;

    protected static ?string $title = 'Nueva Cotización';

    protected static function proximoNumero(): string
    {
        $numero = (int) DocumentoEmpresa::where('id_empresa', (int) session('id_empresa'))
            ->where('sucursal', (int) session('sucursal'))
            ->where('id_tido', 6)
            ->value('numero');

        return 'COT-' . str_pad((string) ($numero + 1), 8, '0', STR_PAD_LEFT);
    }

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
                                TextInput::make('buscador_producto')
                                    ->hiddenLabel()
                                    ->placeholder('🔍 Buscar producto por descripción o código…')
                                    ->autocomplete(false)
                                    ->dehydrated(false)
                                    ->live(debounce: 300),

                                Placeholder::make('resultados_busqueda')
                                    ->hiddenLabel()
                                    ->visible(fn (callable $get): bool => filled($get('buscador_producto')))
                                    ->content(function (callable $get): HtmlString {
                                        $busqueda = trim((string) $get('buscador_producto'));
                                        if ($busqueda === '') {
                                            return new HtmlString('');
                                        }

                                        $productos = Producto::where('id_empresa', (int) session('id_empresa'))
                                            ->where(fn ($q) => $q
                                                ->where('descripcion', 'like', "%{$busqueda}%")
                                                ->orWhere('codigo', 'like', "%{$busqueda}%"))
                                            ->limit(8)
                                            ->get();

                                        if ($productos->isEmpty()) {
                                            return new HtmlString(
                                                '<div style="padding:10px 12px;opacity:.5;font-size:.875rem">Sin coincidencias para "'
                                                . e($busqueda) . '"</div>'
                                            );
                                        }

                                        $filas = $productos->map(fn (Producto $p): string =>
                                            '<button type="button" wire:click="agregarProducto(' . $p->id_producto . ')"'
                                            . ' style="display:flex;justify-content:space-between;gap:12px;width:100%;text-align:left;'
                                            . 'padding:9px 12px;border-bottom:1px solid rgba(128,128,128,.15);cursor:pointer;font-size:.875rem">'
                                            . '<span style="font-weight:600">' . e($p->descripcion) . '</span>'
                                            . '<span style="white-space:nowrap;opacity:.65">S/ ' . number_format((float) $p->precio, 2)
                                            . ' · stock ' . (int) $p->cantidad . '</span>'
                                            . '</button>'
                                        )->implode('');

                                        return new HtmlString(
                                            '<div style="border:1px solid rgba(128,128,128,.25);border-radius:10px;overflow:hidden">'
                                            . $filas . '</div>'
                                        );
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
                                        . '<tbody><tr><td colspan="4" style="padding:18px 12px;text-align:center;opacity:.45">'
                                        . 'Sin productos agregados — use el buscador de arriba'
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
                            ->description('Las cuotas deben sumar el total de la cotización')
                            ->visible(fn (callable $get): bool => (int) $get('id_tipo_pago') === 2)
                            ->schema([
                                Repeater::make('cuotas')
                                    ->hiddenLabel()
                                    ->columns(3)
                                    ->minItems(1)
                                    ->defaultItems(1)
                                    ->live()
                                    ->addActionLabel('Agregar cuota')
                                    ->schema([
                                        DatePicker::make('fecha')
                                            ->label('Fecha de cuota')
                                            ->required(),
                                        TextInput::make('monto')
                                            ->label('Monto (S/)')
                                            ->numeric()
                                            ->minValue(0.01)
                                            ->live(onBlur: true)
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
                                    ]),

                                Placeholder::make('resumen_cuotas')
                                    ->hiddenLabel()
                                    ->content(function (callable $get): HtmlString {
                                        $total = collect($get('productos') ?? [])
                                            ->sum(fn (array $l): float => (float) ($l['cantidad'] ?? 0) * (float) ($l['precio'] ?? 0));
                                        $enCuotas = collect($get('cuotas') ?? [])
                                            ->sum(fn (array $c): float => (float) ($c['monto'] ?? 0));
                                        $falta = round($total - $enCuotas, 2);

                                        $cuadra = abs($falta) < 0.01;
                                        $rgb    = $cuadra ? '22,163,74' : '220,38,38';
                                        $estado = $cuadra
                                            ? '✓ Las cuotas cuadran con el total'
                                            : ($falta > 0
                                                ? 'Faltan S/ ' . number_format($falta, 2)
                                                : 'Exceden en S/ ' . number_format(abs($falta), 2));

                                        return new HtmlString(
                                            '<div style="display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;'
                                            . 'padding:11px 14px;border-radius:12px;border:1px solid rgba(' . $rgb . ',.35);background:rgba(' . $rgb . ',.08)">'
                                            . '<span style="opacity:.85;font-size:.85rem">Total: <strong>S/ ' . number_format($total, 2) . '</strong>'
                                            . ' &nbsp;·&nbsp; En cuotas: <strong>S/ ' . number_format($enCuotas, 2) . '</strong></span>'
                                            . '<span style="font-weight:700;color:rgb(' . $rgb . ')">' . $estado . '</span>'
                                            . '</div>'
                                        );
                                    }),
                            ]),
                    ])->columnSpan(['default' => 1, 'xl' => 2]),

                    // ── COLUMNA DERECHA (angosta): datos, cliente, resumen ──
                    Group::make([
                        Section::make('Cotización')
                            ->compact()
                            ->columns(2)
                            ->schema([
                                Placeholder::make('numero_cotizacion')
                                    ->label('Número')
                                    ->content(fn (): HtmlString => new HtmlString(
                                        '<span style="font-weight:700;font-size:1.05rem;color:rgb(59,130,246)">'
                                        . static::proximoNumero() . '</span>'
                                    ))
                                    ->columnSpanFull(),

                                Select::make('id_tido')
                                    ->label('Comprobante a emitir')
                                    ->options(fn (): array => DB::table('documentos_empresas as de')
                                        ->join('documentos_sunat as ds', 'ds.id_tido', '=', 'de.id_tido')
                                        ->where('de.id_empresa', (int) session('id_empresa'))
                                        ->where('de.sucursal', (int) session('sucursal'))
                                        ->whereIn('de.id_tido', [1, 2, 6])
                                        ->pluck('ds.nombre', 'de.id_tido')
                                        ->toArray())
                                    ->default(6)
                                    ->helperText('Se usará al convertir la cotización en venta.')
                                    ->required()
                                    ->columnSpanFull(),

                                Hidden::make('id_cliente'),

                                TextInput::make('buscador_cliente')
                                    ->label('Cliente')
                                    ->placeholder('🔍 Buscar cliente por nombre o documento…')
                                    ->autocomplete(false)
                                    ->dehydrated(false)
                                    ->live(debounce: 300)
                                    ->visible(fn (callable $get): bool => blank($get('id_cliente')))
                                    ->suffixAction(
                                        Action::make('nuevo_cliente')
                                            ->icon('heroicon-m-user-plus')
                                            ->tooltip('Crear cliente nuevo')
                                            ->form([
                                                TextInput::make('documento')->label('RUC / DNI')->maxLength(15),
                                                TextInput::make('datos')->label('Nombre / Razón Social')->required()->maxLength(200),
                                                TextInput::make('telefono')->label('Teléfono')->tel()->maxLength(20),
                                            ])
                                            ->action(function (array $data, callable $set): void {
                                                $cliente = Cliente::create(array_merge($data, [
                                                    'id_empresa' => (int) session('id_empresa'),
                                                ]));
                                                $set('id_cliente', $cliente->id_cliente);
                                                $set('buscador_cliente', null);
                                            })
                                    )
                                    ->columnSpanFull(),

                                Placeholder::make('cliente_resultados')
                                    ->hiddenLabel()
                                    ->columnSpanFull()
                                    ->visible(fn (callable $get): bool => filled($get('buscador_cliente')) && blank($get('id_cliente')))
                                    ->content(function (callable $get): HtmlString {
                                        $busqueda = trim((string) $get('buscador_cliente'));
                                        if ($busqueda === '') {
                                            return new HtmlString('');
                                        }

                                        $clientes = Cliente::where('id_empresa', (int) session('id_empresa'))
                                            ->where(fn ($q) => $q
                                                ->where('datos', 'like', "%{$busqueda}%")
                                                ->orWhere('documento', 'like', "%{$busqueda}%"))
                                            ->limit(8)
                                            ->get();

                                        if ($clientes->isEmpty()) {
                                            return new HtmlString(
                                                '<div style="padding:12px 14px;border:1px dashed rgba(148,163,184,.5);border-radius:12px;'
                                                . 'color:#94a3b8;font-size:.85rem;text-align:center">'
                                                . 'Sin coincidencias · tocá el botón <strong>+</strong> para crear uno nuevo</div>'
                                            );
                                        }

                                        $filas = $clientes->map(fn (Cliente $c): string =>
                                            '<button type="button" wire:click="seleccionarCliente(' . $c->id_cliente . ')" '
                                            . 'onmouseover="this.style.background=\'rgba(59,130,246,.08)\'" '
                                            . 'onmouseout="this.style.background=\'transparent\'" '
                                            . 'style="display:flex;justify-content:space-between;align-items:center;gap:12px;width:100%;text-align:left;'
                                            . 'padding:10px 14px;border:0;border-bottom:1px solid rgba(148,163,184,.18);background:transparent;'
                                            . 'cursor:pointer;font-size:.875rem;transition:background .12s">'
                                            . '<span style="font-weight:600">' . e($c->datos) . '</span>'
                                            . '<span style="white-space:nowrap;opacity:.6;font-family:monospace;font-size:.8rem">' . e($c->documento ?: '—') . '</span>'
                                            . '</button>'
                                        )->implode('');

                                        return new HtmlString(
                                            '<div style="border:1px solid rgba(148,163,184,.35);border-radius:12px;overflow:hidden;'
                                            . 'box-shadow:0 4px 12px rgba(0,0,0,.06)">' . $filas . '</div>'
                                        );
                                    }),

                                Placeholder::make('cliente_elegido')
                                    ->hiddenLabel()
                                    ->visible(fn (callable $get): bool => filled($get('id_cliente')))
                                    ->content(function (callable $get): HtmlString {
                                        $cliente = Cliente::find($get('id_cliente'));
                                        $inicial = mb_strtoupper(mb_substr($cliente?->datos ?? 'C', 0, 1));

                                        return new HtmlString(
                                            '<div style="display:flex;align-items:center;gap:12px;'
                                            . 'padding:12px 14px;border-radius:12px;border:1px solid rgba(59,130,246,.35);background:rgba(59,130,246,.07)">'
                                            . '<div style="flex-shrink:0;width:38px;height:38px;border-radius:50%;background:rgb(59,130,246);'
                                            . 'color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:1rem">'
                                            . e($inicial) . '</div>'
                                            . '<div style="flex:1;min-width:0">'
                                            . '<div style="font-weight:700;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">' . e($cliente?->datos ?? 'Cliente') . '</div>'
                                            . ($cliente?->documento
                                                ? '<div style="opacity:.6;font-size:.8rem;font-family:monospace">' . e($cliente->documento) . '</div>'
                                                : '')
                                            . '</div>'
                                            . '<button type="button" wire:click="limpiarCliente" '
                                            . 'style="flex-shrink:0;font-size:.78rem;font-weight:600;color:rgb(220,38,38);white-space:nowrap;cursor:pointer;'
                                            . 'padding:5px 10px;border-radius:8px;border:1px solid rgba(220,38,38,.3);background:transparent">✕ Cambiar</button>'
                                            . '</div>'
                                        );
                                    })
                                    ->columnSpanFull(),

                                DatePicker::make('fecha')
                                    ->label('Fecha')
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

                                TextInput::make('observacion')
                                    ->label('Observación')
                                    ->placeholder('Opcional')
                                    ->maxLength(220),

                                TextInput::make('direccion')
                                    ->label('Dirección')
                                    ->placeholder('Opcional')
                                    ->maxLength(220),
                            ]),

                        Section::make('Resumen')
                            ->compact()
                            ->schema([
                                Placeholder::make('resumen')
                                    ->hiddenLabel()
                                    ->content(function (callable $get): HtmlString {
                                        $total = collect($get('productos') ?? [])
                                            ->sum(fn (array $l): float => (float) ($l['cantidad'] ?? 0) * (float) ($l['precio'] ?? 0));

                                        return new HtmlString(
                                            '<div style="display:flex;justify-content:space-between;align-items:center">'
                                            . '<span style="font-weight:700">IMPORTE TOTAL:</span>'
                                            . '<span style="font-weight:800;font-size:1.35rem;color:rgb(59,130,246)">S/ ' . number_format($total, 2) . '</span>'
                                            . '</div>'
                                        );
                                    }),
                            ]),
                    ])->columnSpan(1),
                ]),
        ]);
    }

    public function seleccionarCliente(int $idCliente): void
    {
        $existe = Cliente::where('id_empresa', (int) session('id_empresa'))
            ->where('id_cliente', $idCliente)
            ->exists();

        if ($existe) {
            $this->data['id_cliente']       = $idCliente;
            $this->data['buscador_cliente'] = null;
        }
    }

    public function limpiarCliente(): void
    {
        $this->data['id_cliente']       = null;
        $this->data['buscador_cliente'] = null;
    }

    public function agregarProducto(int $idProducto): void
    {
        $p = Producto::where('id_empresa', (int) session('id_empresa'))->find($idProducto);
        if (! $p) {
            return;
        }

        $items = $this->data['productos'] ?? [];

        foreach ($items as $key => $item) {
            if ((int) ($item['id_producto'] ?? 0) === (int) $p->id_producto) {
                $items[$key]['cantidad']    = (float) $item['cantidad'] + 1;
                $items[$key]['linea_total'] = number_format($items[$key]['cantidad'] * (float) $item['precio'], 2, '.', '');
                $this->data['productos'] = $items;
                $this->data['buscador_producto'] = null;

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

        $this->data['productos'] = $items;
        $this->data['buscador_producto'] = null;
    }

    protected function handleRecordCreation(array $data): Model
    {
        return DB::transaction(function () use ($data): Cotizacion {
            $empresa  = (int) session('id_empresa');
            $sucursal = (int) session('sucursal');
            $usuario  = (int) auth()->user()->usuario_id;

            if (blank($data['id_cliente'] ?? null)) {
                throw ValidationException::withMessages([
                    'buscador_cliente' => 'Seleccioná un cliente para la cotización.',
                ]);
            }

            $total  = 0.0;
            $lineas = [];
            foreach ($data['productos'] as $linea) {
                $producto = Producto::where('id_empresa', $empresa)
                    ->where('id_producto', $linea['id_producto'])
                    ->firstOrFail();

                $cantidad = (float) $linea['cantidad'];
                $precio   = (float) $linea['precio'];
                $total   += round($cantidad * $precio, 2);
                $lineas[] = [$producto, $cantidad, $precio];
            }

            if ($total <= 0) {
                throw ValidationException::withMessages(['productos' => 'El total debe ser mayor a 0.']);
            }

            // Crédito: las cuotas deben existir y sumar exactamente el total
            if ((int) $data['id_tipo_pago'] === 2) {
                $cuotas = $data['cuotas'] ?? [];

                if (count($cuotas) === 0) {
                    throw ValidationException::withMessages([
                        'cuotas' => 'Una venta a crédito debe tener al menos una cuota.',
                    ]);
                }

                $sumaCuotas = round(collect($cuotas)->sum(fn (array $c): float => (float) ($c['monto'] ?? 0)), 2);

                if (abs($sumaCuotas - $total) > 0.01) {
                    $diferencia = round($total - $sumaCuotas, 2);
                    $detalle = $diferencia > 0
                        ? 'Faltan S/ ' . number_format($diferencia, 2) . ' por cubrir.'
                        : 'Las cuotas exceden el total en S/ ' . number_format(abs($diferencia), 2) . '.';

                    throw ValidationException::withMessages([
                        'cuotas' => "Las cuotas (S/ " . number_format($sumaCuotas, 2)
                            . ") deben sumar el total de la cotización (S/ " . number_format($total, 2) . "). {$detalle}",
                    ]);
                }
            }

            // Correlativo propio de cotización (id_tido 6, igual que la API legacy)
            $tido = DocumentoEmpresa::where('id_empresa', $empresa)
                ->where('sucursal', $sucursal)
                ->where('id_tido', 6)
                ->lockForUpdate()
                ->first();

            if (! $tido) {
                throw ValidationException::withMessages(['productos' => 'No hay serie de cotización configurada.']);
            }

            $numero = $tido->numero + 1;

            $coti = Cotizacion::create([
                'numero'         => $numero,
                'id_tido'        => $data['id_tido'] ?? 6,
                'id_tipo_pago'   => $data['id_tipo_pago'],
                'fecha'          => $data['fecha'],
                'direccion'      => $data['direccion'] ?? null,
                'id_cliente'     => $data['id_cliente'],
                'total'          => $total,
                'estado'         => '1',
                'id_empresa'     => $empresa,
                'sucursal'       => $sucursal,
                'usar_precio'    => 1,
                'moneda'         => 1,
                'id_usuario'     => $usuario,
                'observacion'    => $data['observacion'] ?? null,
                'fecha_registro' => now(),
            ]);

            $tido->increment('numero');

            foreach ($lineas as [$producto, $cantidad, $precio]) {
                DB::table('productos_cotis')->insert([
                    'id_coti'      => $coti->cotizacion_id,
                    'id_producto'  => $producto->id_producto,
                    'cantidad'     => $cantidad,
                    'precio'       => $precio,
                    'costo'        => $producto->costo ?? 0,
                    'medida'       => $producto->medida ?? 'Unidad',
                    'presenta'     => 1,
                    'presenta_cnt' => 1,
                    'fecha_registro' => now(),
                    'id_usuario'     => $usuario,
                ]);
            }

            if ((int) $data['id_tipo_pago'] === 2) {
                foreach ($data['cuotas'] ?? [] as $cuota) {
                    CuotaCotizacion::create([
                        'id_coti'    => $coti->cotizacion_id,
                        'id_usuario' => $usuario,
                        'monto'      => $cuota['monto'],
                        'fecha'      => $cuota['fecha'],
                        'estado'     => '0',
                        'tipo_pago'  => $cuota['tipo_pago'] ?? 'EFECTIVO',
                    ]);
                }
            }

            Notification::make()->success()
                ->title('Cotización COT-' . str_pad((string) $numero, 8, '0', STR_PAD_LEFT) . ' registrada')
                ->body('Total: S/ ' . number_format($total, 2))
                ->send();

            return $coti;
        });
    }

    protected function getRedirectUrl(): string
    {
        // The index page auto-opens the PDF preview modal for this record
        return CotizacionResource::getUrl('index', ['previsualizar' => $this->getRecord()->cotizacion_id]);
    }
}
