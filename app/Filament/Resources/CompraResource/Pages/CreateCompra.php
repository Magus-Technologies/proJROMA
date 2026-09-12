<?php

namespace App\Filament\Resources\CompraResource\Pages;

use App\Filament\Resources\CompraResource;
use App\Models\Compra;
use App\Models\Producto;
use App\Services\CajaService;
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
use Filament\Support\Exceptions\Halt;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;

/**
 * Registro de compras. Una compra NO mueve stock: nace con recepcionado = 0
 * y el inventario recién entra cuando se procesa en el módulo de Recepción.
 */
class CreateCompra extends CreateRecord
{
    // Mismo repetidor de métodos de pago que usa Ventas: un método por línea,
    // con su monto, su N° de operación y sus comprobantes.
    use \App\Filament\Concerns\ArmaPagosVenta;

    protected static string $resource = CompraResource::class;

    protected static ?string $title = 'Nueva Compra';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            // 4 columnas: la tabla de productos necesita el ancho para las
            // columnas de la factura, así que la ficha de la compra se queda
            // con un cuarto en lugar de un tercio.
            Grid::make(['default' => 1, 'xl' => 4])
                ->columnSpanFull()
                ->schema([
                    // ── COLUMNA IZQUIERDA: buscador + tabla de productos ──
                    Group::make([
                        Section::make('Productos')
                            ->compact()
                            // El desplegable de resultados se abre al enfocar el
                            // buscador y se cierra al hacer clic fuera. El estado
                            // va acá y no en el Placeholder porque ese sí lo
                            // reemplaza Livewire en cada búsqueda.
                            ->extraAttributes([
                                'x-data' => '{ buscadorAbierto: false }',
                                'x-on:click.outside' => 'buscadorAbierto = false',
                            ])
                            ->schema([
                                // Envuelve al buscador y a sus resultados: el
                                // desplegable se posiciona respecto de este grupo,
                                // así queda justo debajo del input y flotando.
                                Group::make([
                                TextInput::make('buscador_producto')
                                    ->hiddenLabel()
                                    ->placeholder('🔍 Buscar producto por descripción o código…')
                                    ->autocomplete(false)
                                    ->dehydrated(false)
                                    ->extraInputAttributes([
                                        'x-on:focus' => 'buscadorAbierto = true',
                                    ])
                                    ->live(debounce: 300),

                                Placeholder::make('resultados_busqueda')
                                    ->hiddenLabel()
                                    ->content(function (callable $get): HtmlString {
                                        $busqueda = trim((string) $get('buscador_producto'));

                                        // Con el buscador vacío se muestran igual los primeros
                                        // productos: antes no salía nada hasta escribir y no había
                                        // forma de ver qué hay disponible.
                                        // En compras NO se filtra por stock: se compra lo que falta.
                                        $productos = Producto::where('id_empresa', (int) session('id_empresa'))
                                            ->when($busqueda !== '', fn ($q) => $q
                                                ->where(fn ($w) => $w
                                                    ->where('descripcion', 'like', "%{$busqueda}%")
                                                    ->orWhere('codigo', 'like', "%{$busqueda}%")))
                                            ->orderBy('descripcion')
                                            ->limit(8)
                                            ->get();

                                        if ($productos->isEmpty()) {
                                            return new HtmlString(
                                                '<div x-show="buscadorAbierto" x-cloak class="buscador-resultados"'
                                                . ' style="padding:10px 12px;opacity:.5;font-size:.875rem">'
                                                . ($busqueda === ''
                                                    ? 'No hay productos cargados todavia.'
                                                    : 'Sin coincidencias para "' . e($busqueda) . '"')
                                                . '</div>'
                                            );
                                        }

                                        $filas = $productos->map(fn (Producto $p): string =>
                                            '<button type="button" wire:click="agregarProducto(' . $p->id_producto . ')"'
                                            . ' onmouseover="this.style.background=\'rgba(59,130,246,.10)\'"'
                                            . ' onmouseout="this.style.background=\'transparent\'"'
                                            . ' style="display:flex;justify-content:space-between;gap:12px;width:100%;text-align:left;'
                                            . 'padding:9px 12px;border:0;border-bottom:1px solid rgba(148,163,184,.18);background:transparent;'
                                            . 'cursor:pointer;font-size:.875rem;transition:background .12s">'
                                            . '<span style="font-weight:600">' . e($p->descripcion) . '</span>'
                                            . '<span style="white-space:nowrap;opacity:.65">costo S/ ' . number_format((float) $p->costo, 2)
                                            . ' · stock ' . (int) $p->cantidad . '</span>'
                                            . '</button>'
                                        )->implode('');

                                        return new HtmlString(
                                            '<div x-show="buscadorAbierto" x-cloak class="buscador-resultados">'
                                            . $filas . '</div>'
                                        );
                                    }),
                                ])->extraAttributes(['class' => 'buscador-ancla']),

                                Placeholder::make('tabla_vacia')
                                    ->hiddenLabel()
                                    ->visible(fn (callable $get): bool => blank($get('productos')))
                                    ->content(new HtmlString(
                                        '<div style="padding:18px 12px;text-align:center;opacity:.45;font-size:.875rem">'
                                        . 'Sin productos agregados — use el buscador de arriba</div>'
                                    )),

                                Repeater::make('productos')
                                    ->hiddenLabel()
                                    ->minItems(1)
                                    ->defaultItems(0)
                                    ->addable(false)
                                    ->reorderable(false)
                                    ->live()
                                    // Las mismas columnas que trae la factura del
                                    // proveedor. El bruto, el valor de venta y el
                                    // IGV se calculan y salen en el resumen y en
                                    // el PDF; acá se escribe lo que se lee del
                                    // documento: cantidad, importe unitario y
                                    // descuento.
                                    ->table([
                                        TableColumn::make('Código')->width('90px'),
                                        TableColumn::make('Descripción del producto'),
                                        TableColumn::make('Und.')->width('80px'),
                                        TableColumn::make('Cant.')->width('90px'),
                                        TableColumn::make('Imp. unit.')->width('115px'),
                                        TableColumn::make('Desct.')->width('105px'),
                                        TableColumn::make('Total')->width('120px'),
                                    ])
                                    ->schema([
                                        Hidden::make('id_producto'),

                                        TextInput::make('codigo')
                                            ->hiddenLabel()
                                            ->readOnly()
                                            ->dehydrated(false),

                                        TextInput::make('descripcion')
                                            ->hiddenLabel()
                                            ->readOnly()
                                            ->dehydrated(false),

                                        TextInput::make('unidad')
                                            ->hiddenLabel()
                                            ->maxLength(20),

                                        TextInput::make('cantidad')
                                            ->hiddenLabel()
                                            ->numeric()
                                            ->minValue(0.01)
                                            ->default(1)
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(fn (callable $set, callable $get) => static::recalcularLinea($set, $get))
                                            ->required(),

                                        TextInput::make('costo')
                                            ->hiddenLabel()
                                            ->numeric()
                                            ->minValue(0)
                                            ->prefix('S/')
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(fn (callable $set, callable $get) => static::recalcularLinea($set, $get))
                                            ->required(),

                                        TextInput::make('descuento')
                                            ->hiddenLabel()
                                            ->numeric()
                                            ->minValue(0)
                                            ->default(0)
                                            ->prefix('S/')
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(fn (callable $set, callable $get) => static::recalcularLinea($set, $get)),

                                        TextInput::make('linea_total')
                                            ->hiddenLabel()
                                            ->prefix('S/')
                                            ->readOnly()
                                            ->dehydrated(false),
                                    ]),
                            ]),
                    ])->columnSpan(['default' => 1, 'xl' => 3]),

                    // ── COLUMNA DERECHA: proveedor, documento, pago, resumen ──
                    Group::make([
                        Section::make('Compra')
                            ->compact()
                            ->columns(2)
                            ->schema([
                                Select::make('id_proveedor')
                                    ->label('Proveedor')
                                    ->placeholder('Elegí un proveedor o buscá por razón social / RUC')
                                    ->searchable()
                                    ->required()
                                    ->columnSpanFull()
                                    // Con solo getSearchResultsUsing el desplegable salía vacío
                                    // hasta escribir algo. Estas opciones se muestran al abrirlo;
                                    // al escribir toma el mando la búsqueda de abajo, que además
                                    // busca por RUC y no se limita a estos primeros.
                                    ->options(fn (): array => DB::table('proveedores')
                                        ->where('id_empresa', (int) session('id_empresa'))
                                        ->orderBy('razon_social')
                                        ->limit(50)
                                        ->get(['proveedor_id', 'razon_social', 'ruc'])
                                        ->mapWithKeys(fn ($p) => [
                                            $p->proveedor_id => $p->razon_social . ($p->ruc ? " — {$p->ruc}" : ''),
                                        ])
                                        ->toArray())
                                    ->getSearchResultsUsing(fn (string $search): array => DB::table('proveedores')
                                        ->where('id_empresa', (int) session('id_empresa'))
                                        ->where(fn ($q) => $q
                                            ->where('razon_social', 'like', "%{$search}%")
                                            ->orWhere('ruc', 'like', "%{$search}%"))
                                        ->limit(20)
                                        ->get(['proveedor_id', 'razon_social', 'ruc'])
                                        ->mapWithKeys(fn ($p) => [
                                            $p->proveedor_id => $p->razon_social . ($p->ruc ? " — {$p->ruc}" : ''),
                                        ])
                                        ->toArray())
                                    ->getOptionLabelUsing(fn ($value): ?string => DB::table('proveedores')
                                        ->where('proveedor_id', $value)
                                        ->value('razon_social')),

                                Select::make('id_tido')
                                    ->label('Tipo de documento')
                                    ->options(fn (): array => DB::table('documentos_sunat')
                                        ->whereIn('id_tido', [2, 1, 6])
                                        ->orderByRaw('FIELD(id_tido, 2, 1, 6)')
                                        ->pluck('nombre', 'id_tido')
                                        ->toArray())
                                    ->default(2)
                                    ->required()
                                    ->columnSpanFull(),

                                TextInput::make('serie')
                                    ->label('Serie')
                                    ->placeholder('F001')
                                    ->maxLength(50),

                                TextInput::make('numero')
                                    ->label('Número')
                                    ->placeholder('00001234')
                                    ->maxLength(50),

                                DatePicker::make('fecha')
                                    ->label('Fecha de emisión')
                                    ->default(now())
                                    ->required()
                                    ->columnSpanFull(),

                                Select::make('id_tipo_pago')
                                    ->label('Forma de pago')
                                    ->options(fn (): array => DB::table('tipo_pago')
                                        ->pluck('nombre', 'tipo_pago_id')
                                        ->toArray())
                                    ->default(1)
                                    // live() para que el bloque de métodos de pago
                                    // aparezca o se oculte al cambiar contado/crédito.
                                    ->live()
                                    ->required(),

                                // Solo el contado se paga ahora; el crédito se paga
                                // después, cuando venza cada cuota.
                                static::repetidorPagos('pagos', 'Cómo se paga')
                                    ->visible(fn (callable $get): bool => (int) $get('id_tipo_pago') === 1)
                                    ->columnSpanFull(),

                                TextInput::make('observacion')
                                    ->label('Observación')
                                    ->placeholder('Opcional')
                                    ->maxLength(200)
                                    ->columnSpanFull(),
                            ]),

                        Section::make('Retenciones y percepciones')
                            ->compact()
                            ->columns(2)
                            ->schema([
                                Toggle::make('sujeto_retencion')
                                    ->label('Sujeto a retención')
                                    ->helperText('Si se apaga, el documento sale con la leyenda de la RS 037-2002/SUNAT.')
                                    ->live()
                                    ->columnSpanFull(),

                                TextInput::make('retencion_porcentaje')
                                    ->label('Retención %')
                                    ->numeric()
                                    ->default(3)
                                    ->suffix('%')
                                    ->live(onBlur: true)
                                    ->visible(fn (callable $get): bool => (bool) $get('sujeto_retencion')),

                                Toggle::make('sujeto_percepcion')
                                    ->label('Operación sujeta a percepción del IGV')
                                    ->live()
                                    ->columnSpanFull(),

                                TextInput::make('percepcion_porcentaje')
                                    ->label('Percepción %')
                                    ->numeric()
                                    ->default(2)
                                    ->suffix('%')
                                    ->live(onBlur: true)
                                    ->visible(fn (callable $get): bool => (bool) $get('sujeto_percepcion')),
                            ]),

                        Section::make('Resumen')
                            ->compact()
                            ->schema([
                                Placeholder::make('resumen')
                                    ->hiddenLabel()
                                    ->content(function (callable $get): HtmlString {
                                        $d = static::desglose($get('productos') ?? []);

                                        $fila = fn (string $etiqueta, float $monto, string $extra = ''): string =>
                                            '<div style="display:flex;justify-content:space-between;padding:2px 0' . $extra . '">'
                                            . '<span>' . $etiqueta . '</span><span>S/ ' . number_format($monto, 2) . '</span></div>';

                                        $html = $fila('Imp. bruto', $d['bruto']);

                                        if ($d['descuento'] > 0) {
                                            $html .= $fila('Descuento', -$d['descuento']);
                                        }

                                        $html .= $fila('Valor de venta', $d['valor'])
                                            . $fila('IGV 18%', $d['igv'])
                                            . '<div style="display:flex;justify-content:space-between;align-items:center;'
                                            . 'border-top:1px solid rgba(128,128,128,.25);padding-top:8px;margin-top:6px">'
                                            . '<span style="font-weight:700">TOTAL:</span>'
                                            . '<span style="font-weight:800;font-size:1.25rem;color:rgb(59,130,246)">S/ '
                                            . number_format($d['total'], 2) . '</span></div>';

                                        // La percepción no es parte del total del
                                        // documento: se cobra aparte y por eso el
                                        // importe a pagar es "referencial".
                                        if ($get('sujeto_percepcion')) {
                                            $pct = (float) ($get('percepcion_porcentaje') ?: 2);
                                            $percepcion = round($d['total'] * $pct / 100, 2);

                                            $html .= $fila('Percepción ' . number_format($pct, 2) . '%', $percepcion, ';opacity:.85')
                                                . '<div style="display:flex;justify-content:space-between;align-items:center;'
                                                . 'border-top:1px dashed rgba(128,128,128,.35);padding-top:8px;margin-top:6px">'
                                                . '<span style="font-weight:700">TOTAL A PAGAR REFERENCIAL:</span>'
                                                . '<span style="font-weight:800">S/ ' . number_format($d['total'] + $percepcion, 2)
                                                . '</span></div>';
                                        }

                                        if ($get('sujeto_retencion')) {
                                            $pct = (float) ($get('retencion_porcentaje') ?: 3);
                                            $html .= $fila('Retención ' . number_format($pct, 2) . '%',
                                                round($d['total'] * $pct / 100, 2), ';opacity:.85');
                                        }

                                        return new HtmlString($html);
                                    }),
                            ]),
                    ])->columnSpan(1),
                ]),
        ]);
    }

    /**
     * El desglose de la factura del proveedor.
     *
     * El importe unitario es SIN IGV, como en el documento: bruto es cantidad
     * por unitario, el descuento se resta para llegar al valor de venta, y el
     * IGV se calcula sobre ese valor.
     *
     * @param  array<int, array<string, mixed>>  $lineas
     * @return array{bruto: float, descuento: float, valor: float, igv: float, total: float}
     */
    public static function desglose(array $lineas, float $igvPorcentaje = 18.0): array
    {
        $bruto = 0.0;
        $descuento = 0.0;

        foreach ($lineas as $l) {
            $bruto += round((float) ($l['cantidad'] ?? 0) * (float) ($l['costo'] ?? 0), 2);
            $descuento += (float) ($l['descuento'] ?? 0);
        }

        $valor = round($bruto - $descuento, 2);
        $igv   = round($valor * $igvPorcentaje / 100, 2);

        return [
            'bruto'     => round($bruto, 2),
            'descuento' => round($descuento, 2),
            'valor'     => $valor,
            'igv'       => $igv,
            'total'     => round($valor + $igv, 2),
        ];
    }

    /** Total de una línea, con su IGV, tal como sale impreso. */
    protected static function totalDeLinea(float $cantidad, float $costo, float $descuento): string
    {
        $valor = max(round($cantidad * $costo, 2) - $descuento, 0);

        return number_format(round($valor * 1.18, 2), 2, '.', '');
    }

    /** Recalcula el total de la línea cuando cambia cantidad, costo o descuento. */
    protected static function recalcularLinea(callable $set, callable $get): void
    {
        $set('linea_total', static::totalDeLinea(
            (float) $get('cantidad'),
            (float) $get('costo'),
            (float) $get('descuento'),
        ));
    }

    /** @return array<int, string> */
    protected static function opcionesInstrumento(?string $tipo): array
    {
        $empresa = (int) session('id_empresa');

        return match ($tipo) {
            'TRANSFERENCIA' => DB::table('cuentas_bancarias as cb')
                ->leftJoin('bancos as b', 'b.id_banco', '=', 'cb.id_banco')
                ->where('cb.id_empresa', $empresa)
                ->get(['cb.id_cuenta', 'cb.numero_cuenta', 'b.nombre as banco'])
                ->mapWithKeys(fn ($c) => [
                    $c->id_cuenta => ($c->banco ?? '') . ' ****' . substr((string) $c->numero_cuenta, -4),
                ])->toArray(),

            'BILLETERA_DIGITAL' => DB::table('billeteras_digitales as bd')
                ->leftJoin('billetera_tipos as bt', 'bt.id', '=', 'bd.id_billetera_tipo')
                ->where('bd.id_empresa', $empresa)
                ->get(['bd.id_billetera', 'bd.titular', 'bt.nombre as tipo'])
                ->mapWithKeys(fn ($b) => [
                    $b->id_billetera => ($b->tipo ?? '') . ' - ' . $b->titular,
                ])->toArray(),

            default => [],
        };
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
                $items[$key]['linea_total'] = static::totalDeLinea(
                    $items[$key]['cantidad'],
                    (float) $item['costo'],
                    (float) ($item['descuento'] ?? 0),
                );
                $this->data['productos'] = $items;
                $this->data['buscador_producto'] = null;

                return;
            }
        }

        $items[] = [
            'id_producto' => $p->id_producto,
            'codigo'      => $p->codigo ?: $p->cod_barra,
            'descripcion' => $p->descripcion,
            'unidad'      => $p->medida,
            'cantidad'    => 1,
            'costo'       => number_format((float) ($p->costo ?? 0), 2, '.', ''),
            'descuento'   => 0,
            'linea_total' => static::totalDeLinea(1, (float) ($p->costo ?? 0), 0),
        ];

        $this->data['productos'] = $items;
        $this->data['buscador_producto'] = null;
    }

    /** Error de negocio siempre visible (una notificación, no un campo oculto). */
    protected function fallo(string $mensaje): never
    {
        Notification::make()->danger()->title($mensaje)->persistent()->send();

        throw new Halt();
    }

    /** Valida las líneas y devuelve [lineas, total]. */
    protected function resolverLineas(array $data): array
    {
        $lineas = [];

        foreach ($data['productos'] as $linea) {
            $cantidad  = (float) $linea['cantidad'];
            $costo     = (float) $linea['costo'];
            $descuento = (float) ($linea['descuento'] ?? 0);

            if ($cantidad <= 0) {
                $this->fallo('Las cantidades deben ser mayores a 0.');
            }

            if ($descuento > round($cantidad * $costo, 2)) {
                $this->fallo('El descuento de una línea no puede superar su importe bruto.');
            }

            $lineas[] = [
                'id_producto' => (int) $linea['id_producto'],
                'unidad'      => $linea['unidad'] ?? null,
                'cantidad'    => $cantidad,
                'costo'       => $costo,
                'descuento'   => $descuento,
            ];
        }

        $desglose = static::desglose($lineas);

        if ($desglose['total'] <= 0) {
            $this->fallo('El total de la compra debe ser mayor a 0.');
        }

        return [$lineas, $desglose['total'], $desglose];
    }

    /** Reemplaza las líneas de la compra. */
    protected function guardarLineas(int $idCompra, array $lineas): void
    {
        DB::table('productos_compras')->where('id_compra', $idCompra)->delete();

        foreach ($lineas as $l) {
            DB::table('productos_compras')->insert([
                'id_compra'   => $idCompra,
                'id_producto' => $l['id_producto'],
                'unidad'      => $l['unidad'] ?? null,
                'cantidad'    => $l['cantidad'],
                'costo'       => $l['costo'],
                'precio'      => $l['costo'],
                'descuento'   => $l['descuento'] ?? 0,
            ]);
        }
    }

    protected function handleRecordCreation(array $data): Model
    {
        try {
            return $this->crearCompraConPago($data);
        } catch (\RuntimeException $e) {
            // Validaciones de caja (ej. saldo insuficiente): la transacción ya
            // revirtió todo; se notifica y el formulario queda para corregir.
            Notification::make()->danger()
                ->title('No se pudo registrar la compra')
                ->body($e->getMessage())
                ->persistent()
                ->send();

            throw new \Filament\Support\Exceptions\Halt();
        }
    }

    protected function crearCompraConPago(array $data): Compra
    {
        return DB::transaction(function () use ($data): Compra {
            [$lineas, $total, $desglose] = $this->resolverLineas($data);

            $retencionPct = (bool) ($data['sujeto_retencion'] ?? false)
                ? (float) ($data['retencion_porcentaje'] ?: 3)
                : 0.0;
            $percepcionPct = (bool) ($data['sujeto_percepcion'] ?? false)
                ? (float) ($data['percepcion_porcentaje'] ?: 2)
                : 0.0;

            $percepcion = round($total * $percepcionPct / 100, 2);

            $esContado = (int) ($data['id_tipo_pago'] ?? 1) === 1;
            $pagos     = $esContado ? array_values($data['pagos'] ?? []) : [];

            if ($esContado) {
                $this->validarPagos($pagos, $total);
            }

            // El primer método queda en la compra por compatibilidad con lo que
            // ya leía compras.instrumento_tipo; el detalle vive en compra_pagos.
            [$instrumentoTipo, $instrumentoId] = $pagos
                ? CajaService::mapInstrumento($pagos[0]['metodo_pago'])
                : [null, null];

            $compra = Compra::create([
                'id_proveedor'      => $data['id_proveedor'],
                'id_tido'           => $data['id_tido'],
                'id_tipo_pago'      => $data['id_tipo_pago'] ?? 1,
                'instrumento_tipo'  => $instrumentoTipo,
                'instrumento_id'    => $instrumentoId,
                'fecha_emision'     => $data['fecha'],
                'fecha_vencimiento' => $data['fecha'],
                'direccion'         => $data['observacion'] ?? '',
                'serie'             => $data['serie'] ?? '',
                'numero'            => $data['numero'] ?? '',
                'total'             => $total,
                'subtotal'          => $desglose['valor'],
                'descuento_total'   => $desglose['descuento'],
                'igv'               => $desglose['igv'],
                'igv_porcentaje'    => 18,
                'sujeto_retencion'      => $retencionPct > 0,
                'retencion_porcentaje'  => $retencionPct ?: 3,
                'retencion_monto'       => round($total * $retencionPct / 100, 2),
                'sujeto_percepcion'     => $percepcionPct > 0,
                'percepcion_porcentaje' => $percepcionPct ?: 2,
                'percepcion_monto'      => $percepcion,
                // La percepción se paga aparte del documento: por eso el
                // importe a pagar es "referencial".
                'total_referencial'     => round($total + $percepcion, 2),
                'id_empresa'        => (int) session('id_empresa'),
                'id_usuario'        => (int) auth()->id(),
                'sucursal'          => (int) session('sucursal'),
                'moneda'            => 'S',
                'recepcionado'      => 0,
            ]);

            $this->guardarLineas($compra->id_compra, $lineas);

            $descontado = $esContado
                ? $this->registrarPagos($compra, $pagos, $data['fecha'])
                : 0.0;

            Notification::make()->success()
                ->title('Compra registrada')
                ->body('Total: S/ ' . number_format($total, 2)
                    . '. El stock ingresa al procesarla en Recepción.'
                    . ($descontado > 0 ? ' Se descontaron S/ ' . number_format($descontado, 2) . ' de tu caja.' : ''))
                ->send();

            return $compra;
        });
    }

    /** La suma de los métodos tiene que dar exactamente el total de la compra. */
    protected function validarPagos(array $pagos, float $total): void
    {
        if (! $pagos) {
            $this->fallo('Indicá al menos un método de pago.');
        }

        $suma = static::sumaPagos($pagos);

        if (abs($suma - $total) >= 0.01) {
            $this->fallo(
                'Los métodos de pago suman S/ ' . number_format($suma, 2)
                . ' y la compra es de S/ ' . number_format($total, 2) . '. Tienen que coincidir.'
            );
        }
    }

    /**
     * Una fila en compra_pagos por método, y su egreso en la caja del usuario.
     *
     * @return float lo efectivamente descontado de caja
     */
    protected function registrarPagos(Compra $compra, array $pagos, string $fecha): float
    {
        $caja = DB::table('cajas')
            ->where('id_empresa', (int) session('id_empresa'))
            ->where('id_usuario_responsable', auth()->id())
            ->where('estado', 'ACTIVA')
            ->first();

        $documento = trim(($compra->serie ?? '') . '-' . ($compra->numero ?? ''), '-');
        $detalle   = $documento ? "Pago compra {$documento}" : "Pago compra #{$compra->id_compra}";
        $descontado = 0.0;

        foreach ($pagos as $pago) {
            $monto  = round((float) $pago['monto'], 2);
            $metodo = $pago['metodo_pago'];

            $idMovimiento = null;

            if ($caja) {
                [$instrumentoTipo, $instrumentoId] = CajaService::mapInstrumento($metodo);

                $idMovimiento = app(CajaService::class)->registrarMovimiento([
                    'id_caja'          => $caja->id,
                    'fecha'            => $fecha,
                    'tipo'             => 'EGRESO',
                    'categoria'        => 'COMPRA',
                    'descripcion'      => $detalle,
                    'monto'            => $monto,
                    'instrumento_tipo' => $instrumentoTipo,
                    'instrumento_id'   => $instrumentoId,
                    'referencia'       => $pago['referencia'] ?? "Compra #{$compra->id_compra}",
                    'origen_tipo'      => 'Compra',
                    'origen_id'        => $compra->id_compra,
                    'id_usuario'       => auth()->id(),
                ]);

                $descontado += $monto;
            }

            \App\Models\CompraPago::create([
                'id_compra'          => $compra->id_compra,
                'metodo_pago'        => $metodo,
                'monto'              => $monto,
                'referencia'         => $pago['referencia'] ?? null,
                'comprobantes'       => $pago['comprobantes'] ?? null,
                'id_movimiento_caja' => $idMovimiento,
                'id_usuario'         => auth()->id(),
            ]);
        }

        return $descontado;
    }

    protected function getRedirectUrl(): string
    {
        return CompraResource::getUrl('index');
    }
}
