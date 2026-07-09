<?php

namespace App\Filament\Resources\VentaResource\Pages;

use App\Filament\Resources\CotizacionResource;
use App\Filament\Resources\VentaResource;
use App\Models\Cliente;
use App\Models\Cotizacion;
use App\Models\CuotaCotizacion;
use App\Models\DiasVenta;
use App\Models\DocumentoEmpresa;
use App\Services\CajaService;
use App\Models\InventarioMovimiento;
use App\Models\MotivoMovimiento;
use App\Models\Producto;
use App\Models\ProductoVenta;
use App\Models\Venta;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Exceptions\Halt;
use Filament\Schemas\Components\Actions;
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
    use \App\Filament\Concerns\HasClienteBuscador;
    use \App\Filament\Concerns\ReparteCuotas;

    protected static string $resource = VentaResource::class;

    protected static ?string $title = 'Nueva Venta';

    public ?int $desdeCotizacion = null;

    public function mount(): void
    {
        parent::mount();

        $cotiId = (int) request()->query('cotizacion');
        if (! $cotiId) {
            return;
        }

        $coti = Cotizacion::with('productos')
            ->where('id_empresa', (int) session('id_empresa'))
            ->find($cotiId);

        if (! $coti) {
            return;
        }

        if ($coti->estado !== '1' || $coti->id_venta) {
            Notification::make()->warning()
                ->title('Esta cotización ya no puede convertirse')
                ->body('Solo cotizaciones activas y sin venta asociada.')
                ->send();
            $this->redirect(CotizacionResource::getUrl('index'));

            return;
        }

        $this->desdeCotizacion = (int) $coti->cotizacion_id;

        $productos = $coti->productos->map(function ($linea): array {
            $p = Producto::find($linea->id_producto);

            return [
                'id_producto' => $linea->id_producto,
                'descripcion' => $p?->descripcion ?? "Producto #{$linea->id_producto}",
                'cantidad'    => (float) $linea->cantidad,
                'precio'      => number_format((float) $linea->precio, 2, '.', ''),
                'linea_total' => number_format((float) $linea->cantidad * (float) $linea->precio, 2, '.', ''),
            ];
        })->values()->toArray();

        $cuotas = CuotaCotizacion::where('id_coti', $coti->cotizacion_id)
            ->get()
            ->map(fn (CuotaCotizacion $c): array => [
                'fecha'     => $c->fecha?->toDateString(),
                'monto'     => $c->monto,
                'tipo_pago' => $c->tipo_pago ?: 'EFECTIVO',
                'pagado'    => false,
            ])
            ->toArray();

        $this->form->fill(array_merge($this->data ?? [], array_filter([
            'id_tido'      => $coti->id_tido,
            'id_cliente'   => $coti->id_cliente,
            'id_tipo_pago' => (int) $coti->id_tipo_pago,
            'fecha'        => now()->toDateString(),
            'direccion'    => $coti->direccion,
            'observacion'  => 'Convertido de cotización N° ' . $coti->numero,
            'productos'    => $productos,
            'lista_pagos'  => $cuotas ?: null,
        ], fn ($v) => $v !== null)));
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
                                            ->where('cantidad', '>', 0)
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
                            ->description('Programe las cuotas del crédito')
                            ->visible(fn (callable $get): bool => (int) $get('id_tipo_pago') === 2)
                            ->schema([
                                // El estado vive acá; el repeater se edita en un modal.
                                Hidden::make('lista_pagos'),

                                Placeholder::make('resumen_pagos')
                                    ->hiddenLabel()
                                    ->content(fn (callable $get): HtmlString => static::resumenCuotas($get)),

                                Actions::make([
                                    Action::make('configurar_cuotas')
                                        ->label(fn (callable $get): string =>
                                            filled($get('lista_pagos')) ? 'Editar cuotas' : 'Programar cuotas')
                                        ->icon('heroicon-m-calendar-days')
                                        ->color('primary')
                                        ->modalHeading('Cuotas de pago')
                                        ->modalDescription('Las cuotas deben sumar el total de la venta.')
                                        ->modalWidth('3xl')
                                        ->modalSubmitActionLabel('Guardar cuotas')
                                        ->fillForm(function (callable $get): array {
                                            $total = collect($get('productos') ?? [])->sum(
                                                fn (array $l): float => (float) ($l['cantidad'] ?? 0) * (float) ($l['precio'] ?? 0)
                                            );

                                            $cuotas = $get('lista_pagos') ?: [];

                                            if (blank($cuotas)) {
                                                $cuotas = [[
                                                    'fecha'     => now()->addDays(30)->toDateString(),
                                                    'monto'     => number_format(round($total, 2), 2, '.', ''),
                                                    'tipo_pago' => 'EFECTIVO',
                                                    'pagado'    => false,
                                                ]];
                                            }

                                            return [
                                                'lista_pagos' => $cuotas,
                                                'total_ref'   => $total,
                                                'n_cuotas'    => count($cuotas),
                                            ];
                                        })
                                        ->form([
                                            // Contexto para repartir montos dentro del modal.
                                            Hidden::make('total_ref'),
                                            Hidden::make('n_cuotas'),

                                            Repeater::make('lista_pagos')
                                                ->hiddenLabel()
                                                ->columns(4)
                                                ->minItems(1)
                                                ->defaultItems(1)
                                                ->live()
                                                ->addActionLabel('Agregar cuota')
                                                ->afterStateUpdated(function (?array $state, callable $get, callable $set): void {
                                                    $cantidad = count($state ?? []);

                                                    if ($cantidad === 0 || (int) $get('n_cuotas') === $cantidad) {
                                                        return;
                                                    }

                                                    $set('n_cuotas', $cantidad);
                                                    $set('lista_pagos', static::repartirCuotas($state, (float) $get('total_ref')));
                                                })
                                                ->schema([
                                                    DatePicker::make('fecha')
                                                        ->label('Fecha de cuota')
                                                        ->required(),
                                                    TextInput::make('monto')
                                                        ->label('Monto (S/)')
                                                        ->numeric()
                                                        ->minValue(0.01)
                                                        ->prefix('S/')
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
                                                        ->default('EFECTIVO')
                                                        ->live(),
                                                    Toggle::make('pagado')
                                                        ->label('Ya pagado')
                                                        ->inline(false),

                                                    // Referencia y captura: solo si el pago no fue en efectivo.
                                                    TextInput::make('referencia')
                                                        ->label('N° de operación')
                                                        ->placeholder('Código del comprobante de pago')
                                                        ->maxLength(60)
                                                        ->visible(fn (callable $get): bool =>
                                                            filled($get('tipo_pago')) && $get('tipo_pago') !== 'EFECTIVO')
                                                        ->columnSpan(2),

                                                    FileUpload::make('voucher')
                                                        ->label('Captura del pago')
                                                        ->image()
                                                        ->disk('public')
                                                        ->directory('vouchers')
                                                        ->imagePreviewHeight('90')
                                                        ->maxSize(4096)
                                                        ->visible(fn (callable $get): bool =>
                                                            filled($get('tipo_pago')) && $get('tipo_pago') !== 'EFECTIVO')
                                                        ->columnSpan(2),
                                                ]),
                                        ])
                                        ->action(function (array $data, callable $set): void {
                                            $set('lista_pagos', $data['lista_pagos'] ?? []);
                                        }),
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

                                ...static::clienteBuscadorSchema(),

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

                                Select::make('tipo_igv')
                                    ->label('Afectación IGV')
                                    ->helperText('Aplica a todo el comprobante.')
                                    ->options([
                                        'gravado'   => 'Gravado (18%)',
                                        'exonerado' => 'Exonerado',
                                        'inafecto'  => 'Inafecto',
                                    ])
                                    ->default('gravado')
                                    ->live()
                                    ->required(),

                                DatePicker::make('fecha_vencimiento')
                                    ->label('Fecha vencimiento')
                                    ->visible(fn (callable $get): bool => (int) $get('id_tipo_pago') === 2)
                                    ->requiredIf('id_tipo_pago', 2)
                                    ->after('fecha'),

                                // ── Pago al contado ──────────────────────────
                                // En crédito el pago se registra por cuota (modal),
                                // así que estos campos solo aplican al contado.
                                Select::make('metodo_pago')
                                    ->label('Método de pago')
                                    ->options([
                                        'EFECTIVO'      => 'Efectivo',
                                        'YAPE'          => 'Yape',
                                        'PLIN'          => 'Plin',
                                        'TRANSFERENCIA' => 'Transferencia',
                                        'DEPOSITO'      => 'Depósito',
                                    ])
                                    ->default('EFECTIVO')
                                    ->live()
                                    ->visible(fn (callable $get): bool => (int) $get('id_tipo_pago') === 1)
                                    ->columnSpanFull(),

                                TextInput::make('pago_referencia')
                                    ->label('N° de operación')
                                    ->placeholder('Código que figura en el comprobante del pago')
                                    ->maxLength(60)
                                    ->visible(fn (callable $get): bool =>
                                        (int) $get('id_tipo_pago') === 1
                                        && filled($get('metodo_pago'))
                                        && $get('metodo_pago') !== 'EFECTIVO')
                                    ->columnSpanFull(),

                                FileUpload::make('pago_voucher')
                                    ->label('Captura del pago')
                                    ->helperText('Opcional. Imagen del Yape, Plin o la transferencia.')
                                    ->image()
                                    ->disk('public')
                                    ->directory('vouchers')
                                    ->imagePreviewHeight('120')
                                    ->maxSize(4096)
                                    ->visible(fn (callable $get): bool =>
                                        (int) $get('id_tipo_pago') === 1
                                        && filled($get('metodo_pago'))
                                        && $get('metodo_pago') !== 'EFECTIVO')
                                    ->columnSpanFull(),

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

                                        $afectacion = $get('tipo_igv') ?? 'gravado';
                                        $esGravado  = $afectacion === 'gravado';
                                        $subtotal   = $esGravado ? $total / 1.18 : $total;
                                        $igv        = $esGravado ? $total - $subtotal : 0.0;
                                        $etiquetaOp = match ($afectacion) {
                                            'exonerado' => 'Op. Exoneradas:',
                                            'inafecto'  => 'Op. Inafectas:',
                                            default     => 'Op. Gravadas:',
                                        };

                                        return new HtmlString(
                                            '<div style="line-height:1.7">'
                                            . '<div style="display:flex;justify-content:space-between;opacity:.7">'
                                            . '<span>' . $etiquetaOp . '</span><span style="font-weight:600">S/ ' . number_format($subtotal, 2) . '</span></div>'
                                            . '<div style="display:flex;justify-content:space-between;opacity:.7">'
                                            . '<span>IGV' . ($esGravado ? ' (18%)' : '') . ':</span><span style="font-weight:600">S/ ' . number_format($igv, 2) . '</span></div>'
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

    public function agregarProducto(int $idProducto): void
    {
        $p = Producto::where('id_empresa', (int) session('id_empresa'))->find($idProducto);
        if (! $p) {
            return;
        }

        $items = $this->data['productos'] ?? [];

        // If already in the list, bump its quantity instead of duplicating
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

    /**
     * Muestra un error de negocio como notificación (siempre visible) y detiene
     * la creación limpiamente. Evita el problema de ValidationException con
     * claves que no matchean el statePath del form (errores invisibles).
     */
    /** Barra de cuadre + listado compacto de las cuotas programadas. */
    protected static function resumenCuotas(callable $get): HtmlString
    {
        $total = collect($get('productos') ?? [])
            ->sum(fn (array $l): float => (float) ($l['cantidad'] ?? 0) * (float) ($l['precio'] ?? 0));

        $cuotas   = collect($get('lista_pagos') ?? [])->filter(fn ($c) => filled($c['monto'] ?? null));
        $enCuotas = $cuotas->sum(fn (array $c): float => (float) ($c['monto'] ?? 0));
        $falta    = round($total - $enCuotas, 2);

        $cuadra = abs($falta) < 0.01 && $cuotas->isNotEmpty();
        $rgb    = $cuadra ? '22,163,74' : '220,38,38';
        $estado = $cuotas->isEmpty()
            ? 'Sin cuotas programadas'
            : ($cuadra
                ? '✓ Las cuotas cuadran con el total'
                : ($falta > 0
                    ? 'Faltan S/ ' . number_format($falta, 2)
                    : 'Exceden en S/ ' . number_format(abs($falta), 2)));

        $barra = '<div style="display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;'
            . 'padding:11px 14px;border-radius:12px;border:1px solid rgba(' . $rgb . ',.35);background:rgba(' . $rgb . ',.08)">'
            . '<span style="opacity:.85;font-size:.85rem">Total: <strong>S/ ' . number_format($total, 2) . '</strong>'
            . ' &nbsp;·&nbsp; En cuotas: <strong>S/ ' . number_format($enCuotas, 2) . '</strong></span>'
            . '<span style="font-weight:700;color:rgb(' . $rgb . ')">' . e($estado) . '</span>'
            . '</div>';

        if ($cuotas->isEmpty()) {
            return new HtmlString($barra);
        }

        $filas = $cuotas->values()->map(fn (array $c, int $i): string =>
            '<div style="display:flex;justify-content:space-between;align-items:center;gap:12px;'
            . 'padding:8px 14px;border-bottom:1px solid rgba(148,163,184,.18);font-size:.85rem">'
            . '<span style="opacity:.6">Cuota ' . ($i + 1) . '</span>'
            . '<span>' . e($c['fecha'] ?? '—') . '</span>'
            . '<span style="opacity:.65;font-size:.78rem">' . e($c['tipo_pago'] ?? 'EFECTIVO') . '</span>'
            . (($c['pagado'] ?? false)
                ? '<span style="font-size:.72rem;color:rgb(22,163,74);font-weight:600">PAGADO</span>'
                : '<span style="font-size:.72rem;opacity:.4">pendiente</span>')
            . '<span style="font-weight:600">S/ ' . number_format((float) $c['monto'], 2) . '</span>'
            . '</div>'
        )->implode('');

        return new HtmlString(
            $barra
            . '<div style="margin-top:10px;border:1px solid rgba(148,163,184,.3);border-radius:12px;overflow:hidden">'
            . $filas . '</div>'
        );
    }

    protected function fallo(string $mensaje): never
    {
        Notification::make()->danger()->title($mensaje)->persistent()->send();

        throw new Halt();
    }

    protected function handleRecordCreation(array $data): Model
    {
        return DB::transaction(function () use ($data): Venta {
            $empresa  = (int) session('id_empresa');
            $sucursal = (int) session('sucursal');
            $usuario  = (int) auth()->user()->usuario_id;

            if (blank($data['id_cliente'] ?? null)) {
                $this->fallo('Seleccioná un cliente para la venta.');
            }

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
                    $this->fallo("Stock insuficiente de «{$producto->descripcion}» (disponible: {$producto->cantidad}).");
                }

                $lineaTotal = round($cantidad * (float) $linea['precio'], 2);
                $total     += $lineaTotal;
                $lineas[]   = [$producto, $cantidad, (float) $linea['precio'], $lineaTotal];
            }

            if ($total <= 0) {
                $this->fallo('El total debe ser mayor a 0.');
            }

            // Correlative number with lock (same as legacy API)
            $tido = DocumentoEmpresa::where('id_empresa', $empresa)
                ->where('sucursal', $sucursal)
                ->where('id_tido', $data['id_tido'])
                ->lockForUpdate()
                ->firstOrFail();
            $numero = $tido->numero + 1;

            $esContado = (int) $data['id_tipo_pago'] === 1;

            // Afectación IGV del comprobante: gravado descompone el 18%;
            // exonerado/inafecto no llevan IGV (subtotal = total).
            $afectacion = $data['tipo_igv'] ?? 'gravado';
            $esGravado  = $afectacion === 'gravado';
            $subtotal   = $esGravado ? round($total / 1.18, 2) : $total;
            $igv        = $esGravado ? round($total - $subtotal, 2) : 0.0;

            $venta = Venta::create([
                'id_tido'           => $data['id_tido'],
                'id_tipo_pago'      => $data['id_tipo_pago'],
                // El pago del contado se registra en la venta; el del crédito,
                // por cuota (dias_ventas).
                'metodo_pago'       => $esContado ? ($data['metodo_pago'] ?? 'EFECTIVO') : null,
                'pago_referencia'   => $esContado ? ($data['pago_referencia'] ?? null) : null,
                'pago_voucher'      => $esContado ? ($data['pago_voucher'] ?? null) : null,
                'fecha_emision'     => $data['fecha'],
                'fecha_vencimiento' => $data['fecha_vencimiento'] ?? $data['fecha'],
                'direccion'         => $data['direccion'] ?? '-',
                'serie'             => $tido->serie,
                'numero'            => $numero,
                'id_cliente'        => $data['id_cliente'],
                'total'             => $total,
                'subtotal'          => $subtotal,
                'igv'               => $igv,
                'apli_igv'          => $esGravado ? '1' : '0',
                'tipo_igv'          => $afectacion,
                'estado'            => '1',
                'enviado_sunat'     => '0',
                'id_empresa'        => $empresa,
                'sucursal'          => $sucursal,
                'id_vendedor'       => $usuario,
                'observacion'       => $data['observacion'] ?? null,
                'id_coti'           => $this->desdeCotizacion,
            ]);

            $tido->increment('numero');

            if ($this->desdeCotizacion) {
                Cotizacion::where('cotizacion_id', $this->desdeCotizacion)
                    ->update(['estado' => '3', 'id_venta' => $venta->id_venta]);
            }

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
                    'costo'       => (float) ($producto->costo ?? 0),
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

            // Las cuotas solo aplican a crédito; ignorar ítems vacíos del repeater.
            $cuotas = ((int) $data['id_tipo_pago'] === 2) ? ($data['lista_pagos'] ?? []) : [];
            foreach ($cuotas as $pago) {
                if (blank($pago['monto'] ?? null) || blank($pago['fecha'] ?? null)) {
                    continue;
                }

                $dv = DiasVenta::create([
                    'id_venta'   => $venta->id_venta,
                    'fecha'      => $pago['fecha'],
                    'monto'      => $pago['monto'],
                    'estado'     => ($pago['pagado'] ?? false) ? '1' : '0',
                    'tipo_pago'  => $pago['tipo_pago'] ?? 'EFECTIVO',
                    'referencia' => $pago['referencia'] ?? null,
                    'voucher'    => $pago['voucher'] ?? null,
                    'id_usuario' => $usuario,
                ]);

                if (($pago['pagado'] ?? false)) {
                    app(CajaService::class)->registrarCobro(
                        idUsuario:   $usuario,
                        monto:       (float) $pago['monto'],
                        tipoPago:    $pago['tipo_pago'] ?? 'EFECTIVO',
                        idVenta:     $venta->id_venta,
                        documento:   $doc,
                        idDiasVenta: $dv->dias_venta_id,
                        categoria:   'VENTA',
                    );
                }
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

    protected function afterCreate(): void
    {
        // Generar el XML del comprobante al crear la venta (sin enviar). Es
        // best-effort: si el servicio SUNAT falla, la venta ya quedó creada y
        // se puede regenerar después con el botón "Regenerar XML".
        $venta = $this->getRecord();

        if (! in_array((int) $venta->id_tido, [1, 2], true)) {
            return; // solo factura/boleta
        }

        try {
            $res = app(\App\Services\VentaSunatService::class)->generarXml($venta);
            if (! $res['ok']) {
                Notification::make()->warning()
                    ->title('Venta creada, pero el XML no se generó')
                    ->body($res['msg'] . ' Podés regenerarlo desde la lista.')
                    ->send();
            }
        } catch (\Throwable $e) {
            Notification::make()->warning()
                ->title('Venta creada, pero el XML no se generó')
                ->body('Podés regenerarlo desde la lista con "Regenerar XML".')
                ->send();
        }
    }

    protected function getRedirectUrl(): string
    {
        // La lista abre automáticamente el modal de vista previa del comprobante
        // recién creado; al cerrarlo, el usuario queda en el listado de ventas.
        return VentaResource::getUrl('index', ['previsualizar' => $this->getRecord()->id_venta]);
    }
}
