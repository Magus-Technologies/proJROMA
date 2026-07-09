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

class CreateCotizacion extends CreateRecord
{
    use \App\Filament\Concerns\HasClienteBuscador;
    use \App\Filament\Concerns\ReparteCuotas;

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
                                // El estado vive acá; el repeater se edita en un modal.
                                Hidden::make('cuotas'),

                                Placeholder::make('resumen_cuotas')
                                    ->hiddenLabel()
                                    ->content(fn (callable $get): HtmlString => static::resumenCuotas($get)),

                                Actions::make([
                                    Action::make('configurar_cuotas')
                                        ->label(fn (callable $get): string =>
                                            filled($get('cuotas')) ? 'Editar cuotas' : 'Programar cuotas')
                                        ->icon('heroicon-m-calendar-days')
                                        ->color('primary')
                                        ->modalHeading('Cuotas de pago')
                                        ->modalDescription('Las cuotas deben sumar el total de la cotización.')
                                        ->modalWidth('3xl')
                                        ->modalSubmitActionLabel('Guardar cuotas')
                                        ->fillForm(function (callable $get): array {
                                            $total = collect($get('productos') ?? [])->sum(
                                                fn (array $l): float => (float) ($l['cantidad'] ?? 0) * (float) ($l['precio'] ?? 0)
                                            );

                                            $cuotas = $get('cuotas') ?: [];

                                            // Sin cuotas aún: proponer una por el total, a 30 días.
                                            if (blank($cuotas)) {
                                                $cuotas = [[
                                                    'fecha'     => now()->addDays(30)->toDateString(),
                                                    'monto'     => number_format(round($total, 2), 2, '.', ''),
                                                    'tipo_pago' => 'EFECTIVO',
                                                ]];
                                            }

                                            return [
                                                'cuotas'     => $cuotas,
                                                'total_ref'  => $total,
                                                'n_cuotas'   => count($cuotas),
                                            ];
                                        })
                                        ->form([
                                            // Contexto para repartir montos dentro del modal.
                                            Hidden::make('total_ref'),
                                            Hidden::make('n_cuotas'),

                                            Repeater::make('cuotas')
                                                ->hiddenLabel()
                                                ->columns(3)
                                                ->minItems(1)
                                                ->defaultItems(1)
                                                ->live()
                                                ->addActionLabel('Agregar cuota')
                                                ->afterStateUpdated(function (?array $state, callable $get, callable $set): void {
                                                    $cantidad = count($state ?? []);

                                                    // Solo repartir cuando cambia la CANTIDAD de cuotas;
                                                    // si el usuario editó un monto, se respeta.
                                                    if ($cantidad === 0 || (int) $get('n_cuotas') === $cantidad) {
                                                        return;
                                                    }

                                                    $set('n_cuotas', $cantidad);
                                                    $set('cuotas', static::repartirCuotas($state, (float) $get('total_ref')));
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
                                                        ->default('EFECTIVO'),
                                                ]),
                                        ])
                                        ->action(function (array $data, callable $set): void {
                                            $set('cuotas', $data['cuotas'] ?? []);
                                        }),
                                ]),
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

                                ...static::clienteBuscadorSchema(),

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

    /**
     * Muestra un error de negocio como notificación (siempre visible) y detiene
     * el guardado. Evita ValidationException con claves que no matchean el
     * statePath del form, que quedan invisibles para el usuario.
     */
    protected function fallo(string $mensaje): never
    {
        Notification::make()->danger()->title($mensaje)->persistent()->send();

        throw new Halt();
    }

    /** Barra de cuadre + listado compacto de las cuotas programadas. */
    protected static function resumenCuotas(callable $get): HtmlString
    {
        $total = collect($get('productos') ?? [])
            ->sum(fn (array $l): float => (float) ($l['cantidad'] ?? 0) * (float) ($l['precio'] ?? 0));

        $cuotas   = collect($get('cuotas') ?? [])->filter(fn ($c) => filled($c['monto'] ?? null));
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
            . '<span style="font-weight:600">S/ ' . number_format((float) $c['monto'], 2) . '</span>'
            . '</div>'
        )->implode('');

        return new HtmlString(
            $barra
            . '<div style="margin-top:10px;border:1px solid rgba(148,163,184,.3);border-radius:12px;overflow:hidden">'
            . $filas . '</div>'
        );
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
                $this->fallo('Seleccioná un cliente para la cotización.');
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
                $this->fallo('El total debe ser mayor a 0.');
            }

            // Crédito: las cuotas deben existir y sumar exactamente el total
            if ((int) $data['id_tipo_pago'] === 2) {
                $cuotas = $data['cuotas'] ?? [];

                if (count($cuotas) === 0) {
                    $this->fallo('Una cotización a crédito debe tener al menos una cuota.');
                }

                $sumaCuotas = round(collect($cuotas)->sum(fn (array $c): float => (float) ($c['monto'] ?? 0)), 2);

                if (abs($sumaCuotas - $total) > 0.01) {
                    $diferencia = round($total - $sumaCuotas, 2);
                    $detalle = $diferencia > 0
                        ? 'Faltan S/ ' . number_format($diferencia, 2) . ' por cubrir.'
                        : 'Exceden el total en S/ ' . number_format(abs($diferencia), 2) . '.';

                    $this->fallo('Las cuotas (S/ ' . number_format($sumaCuotas, 2)
                        . ') deben sumar el total de la cotización (S/ ' . number_format($total, 2) . "). {$detalle}");
                }
            }

            // Correlativo propio de cotización (id_tido 6, igual que la API legacy)
            $tido = DocumentoEmpresa::where('id_empresa', $empresa)
                ->where('sucursal', $sucursal)
                ->where('id_tido', 6)
                ->lockForUpdate()
                ->first();

            if (! $tido) {
                $this->fallo('No hay serie de cotización configurada.');
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
