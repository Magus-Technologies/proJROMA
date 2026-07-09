<?php

namespace App\Filament\Resources\GuiaRemisionResource\Pages;

use App\Filament\Resources\GuiaRemisionResource;
use App\Models\Empresa;
use App\Models\GuiaDetalle;
use App\Models\GuiaRemision;
use App\Models\ProductoVenta;
use App\Models\Venta;
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

class CreateGuiaRemision extends CreateRecord
{
    protected static string $resource = GuiaRemisionResource::class;

    protected static ?string $title = 'Nueva Guía de Remisión';

    public function mount(): void
    {
        parent::mount();

        // Pre-cargar desde una venta (link "Crear guía" de la lista de ventas)
        $idVenta = (int) request()->query('venta');
        if (! $idVenta) {
            return;
        }

        $venta = Venta::with('cliente')
            ->where('id_empresa', (int) session('id_empresa'))
            ->where('sucursal', (int) session('sucursal'))
            ->where('estado', '!=', '0')
            ->find($idVenta);

        if (! $venta) {
            return;
        }

        $this->form->fill(array_merge($this->data ?? [], [
            'id_venta'      => $venta->id_venta,
            'fecha_emision' => now()->toDateString(),
            'dir_llegada'   => $venta->cliente?->direccion,
            'productos'     => ProductoVenta::where('id_venta', $venta->id_venta)
                ->get()
                ->map(fn (ProductoVenta $p): array => [
                    'id_producto' => $p->id_producto,
                    'detalles'    => $p->descripcion,
                    'unidad'      => 'NIU',
                    'cantidad'    => (float) $p->cantidad,
                    'precio'      => (float) $p->precio,
                ])
                ->toArray(),
        ]));
    }

    /** Empresa de la sesión, cacheada por request (la usan los defaults del form). */
    protected static function empresaActual(): ?Empresa
    {
        static $empresa = null;

        return $empresa ??= Empresa::find((int) session('id_empresa'));
    }

    protected static function proximoNumero(): string
    {
        $numero = (int) GuiaRemision::where('id_empresa', (int) session('id_empresa'))
            ->where('sucursal', (int) session('sucursal'))
            ->max('numero');

        return 'T001-' . str_pad((string) ($numero + 1), 8, '0', STR_PAD_LEFT);
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Grid::make(['default' => 1, 'xl' => 3])
                ->columnSpanFull()
                ->schema([
                    // ── IZQUIERDA (ancha): venta origen + productos a trasladar ──
                    Group::make([
                        Section::make('Venta de origen')
                            ->compact()
                            ->schema([
                                Select::make('id_venta')
                                    ->hiddenLabel()
                                    ->placeholder('Buscar venta por documento o cliente…')
                                    ->searchable()
                                    ->getSearchResultsUsing(fn (string $search): array => Venta::with('cliente')
                                        ->where('id_empresa', (int) session('id_empresa'))
                                        ->where('sucursal', (int) session('sucursal'))
                                        ->where('estado', '!=', '0')
                                        ->where(fn ($q) => $q
                                            ->where('serie', 'like', "%{$search}%")
                                            ->orWhere('numero', 'like', "%{$search}%")
                                            ->orWhereHas('cliente', fn ($c) => $c->where('datos', 'like', "%{$search}%")))
                                        ->orderByDesc('id_venta')
                                        ->limit(20)
                                        ->get()
                                        ->mapWithKeys(fn (Venta $v) => [
                                            $v->id_venta => trim("{$v->serie}-" . str_pad((string) $v->numero, 8, '0', STR_PAD_LEFT))
                                                . ' — ' . ($v->cliente?->datos ?? 'Sin cliente')
                                                . ' — S/ ' . number_format((float) $v->total, 2),
                                        ])
                                        ->toArray())
                                    ->getOptionLabelUsing(function ($value): ?string {
                                        $v = Venta::find($value);

                                        return $v ? trim("{$v->serie}-" . str_pad((string) $v->numero, 8, '0', STR_PAD_LEFT)) : null;
                                    })
                                    ->live()
                                    ->afterStateUpdated(function ($state, callable $set): void {
                                        if (! $state) {
                                            $set('productos', []);

                                            return;
                                        }

                                        $venta = Venta::with('cliente')->find($state);

                                        $set('productos', ProductoVenta::where('id_venta', $state)
                                            ->get()
                                            ->map(fn (ProductoVenta $p): array => [
                                                'id_producto' => $p->id_producto,
                                                'detalles'    => $p->descripcion,
                                                'unidad'      => 'NIU',
                                                'cantidad'    => (float) $p->cantidad,
                                                'precio'      => (float) $p->precio,
                                            ])
                                            ->toArray());

                                        if ($venta?->cliente?->direccion) {
                                            $set('dir_llegada', $venta->cliente->direccion);
                                        }
                                    })
                                    ->required(),
                            ]),

                        Section::make('Productos a trasladar')
                            ->compact()
                            ->description('Se cargan desde la venta; ajuste cantidades o elimine líneas si el traslado es parcial')
                            ->schema([
                                Placeholder::make('sin_venta')
                                    ->hiddenLabel()
                                    ->visible(fn (callable $get): bool => blank($get('productos')))
                                    ->content(new HtmlString(
                                        '<div style="padding:18px 12px;text-align:center;opacity:.45">'
                                        . 'Seleccione una venta para cargar sus productos'
                                        . '</div>'
                                    )),

                                Repeater::make('productos')
                                    ->hiddenLabel()
                                    ->minItems(1)
                                    ->defaultItems(0)
                                    ->addable(false)
                                    ->reorderable(false)
                                    ->table([
                                        TableColumn::make('Producto'),
                                        TableColumn::make('Unidad')->width('100px'),
                                        TableColumn::make('Cantidad')->width('120px'),
                                    ])
                                    ->schema([
                                        Hidden::make('id_producto'),
                                        Hidden::make('precio'),

                                        TextInput::make('detalles')
                                            ->hiddenLabel()
                                            ->readOnly(),

                                        TextInput::make('unidad')
                                            ->hiddenLabel()
                                            ->default('NIU')
                                            ->required(),

                                        TextInput::make('cantidad')
                                            ->hiddenLabel()
                                            ->numeric()
                                            ->minValue(0.001)
                                            ->required(),
                                    ]),
                            ]),
                        Section::make('Punto de partida')
                            ->compact()
                            ->description('Viene de la dirección de la empresa. Cambialo si la mercadería sale de otro local.')
                            ->columns(3)
                            ->schema([
                                TextInput::make('dir_partida')
                                    ->label('Dirección de partida')
                                    ->default(fn (): ?string => static::empresaActual()?->direccion)
                                    ->required()
                                    ->maxLength(220)
                                    ->columnSpan(2),

                                TextInput::make('ubigeo_partida')
                                    ->label('Ubigeo')
                                    ->default(fn (): ?string => static::empresaActual()?->ubigeo)
                                    ->helperText('6 dígitos')
                                    ->required()
                                    ->maxLength(6),
                            ]),

                        // ── Destino: a quién y a dónde llega ─────────────────
                        Section::make('Punto de llegada')
                            ->compact()
                            ->description('A quién se entrega la mercadería y en qué dirección.')
                            ->columns(3)
                            ->schema([
                                Placeholder::make('destinatario')
                                    ->label('Destinatario')
                                    ->columnSpanFull()
                                    ->content(function (callable $get): HtmlString {
                                        $venta = Venta::with('cliente')->find($get('id_venta'));
                                        $cliente = $venta?->cliente;

                                        if (! $cliente) {
                                            return new HtmlString(
                                                '<div style="padding:10px 14px;border-radius:10px;border:1px dashed rgba(148,163,184,.5);'
                                                . 'color:#94a3b8;font-size:.85rem">Elegí una venta para ver el destinatario</div>'
                                            );
                                        }

                                        $doc = $cliente->documento ?: '—';
                                        $tipo = strlen((string) $cliente->documento) === 11 ? 'RUC' : 'DNI';

                                        return new HtmlString(
                                            '<div style="display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;'
                                            . 'padding:11px 14px;border-radius:12px;border:1px solid rgba(59,130,246,.35);background:rgba(59,130,246,.07)">'
                                            . '<div><strong>' . e($cliente->datos) . '</strong>'
                                            . '<div style="opacity:.65;font-size:.8rem;font-family:monospace">' . $tipo . ' ' . e($doc) . '</div></div>'
                                            . '<span style="opacity:.7;font-size:.8rem;text-align:right">' . e($cliente->direccion ?: 'Sin dirección registrada') . '</span>'
                                            . '</div>'
                                        );
                                    }),

                                TextInput::make('dir_llegada')
                                    ->label('Dirección de llegada')
                                    ->required()
                                    ->maxLength(220)
                                    ->columnSpan(2),

                                TextInput::make('ubigeo')
                                    ->label('Ubigeo')
                                    ->placeholder('150128')
                                    ->helperText('6 dígitos')
                                    ->required()
                                    ->maxLength(6),
                            ]),

                    ])->columnSpan(['default' => 1, 'xl' => 2]),

                    // ── DERECHA (angosta): datos de la guía y del traslado ──
                    Group::make([
                        Section::make('Datos de la guía')
                            ->compact()
                            ->columns(2)
                            ->schema([
                                Placeholder::make('numero_guia')
                                    ->label('Número')
                                    ->content(fn (): HtmlString => new HtmlString(
                                        '<span style="font-weight:700;font-size:1.05rem;color:rgb(59,130,246)">'
                                        . static::proximoNumero() . '</span>'
                                    )),

                                Select::make('motivo_traslado')
                                    ->label('Motivo de traslado')
                                    ->options([
                                        '01' => 'Venta',
                                        '02' => 'Compra',
                                        '04' => 'Traslado entre establecimientos',
                                        '08' => 'Importación',
                                        '09' => 'Exportación',
                                        '13' => 'Otros',
                                        '14' => 'Venta sujeta a confirmación',
                                        '18' => 'Traslado emisor itinerante',
                                    ])
                                    ->default('01')
                                    ->required(),

                                DatePicker::make('fecha_emision')
                                    ->label('Fecha de emisión')
                                    ->default(now())
                                    ->required(),

                                DatePicker::make('fecha_traslado')
                                    ->label('Fecha de inicio de traslado')
                                    ->default(now())
                                    ->required(),

                                TextInput::make('nro_bultos')
                                    ->label('N° de bultos')
                                    ->numeric()
                                    ->integer()
                                    ->minValue(1)
                                    ->default(1),

                                TextInput::make('peso')
                                    ->label('Peso total (kg)')
                                    ->numeric()
                                    ->minValue(0.001)
                                    ->default(1)
                                    ->required(),
                            ]),

                        // ── Origen: de dónde sale la mercadería ──────────────
                        Section::make('Transporte')
                            ->compact()
                            ->columns(2)
                            ->schema([
                                Select::make('tipo_transporte')
                                    ->label('Tipo de transporte')
                                    ->options([
                                        '1' => 'Transporte Privado',
                                        '2' => 'Transporte Público',
                                    ])
                                    ->default('1')
                                    ->live()
                                    ->required()
                                    ->columnSpanFull(),

                                // ── Transporte Público: datos del transportista ──
                                TextInput::make('ruc_transporte')
                                    ->label('RUC transportista')
                                    ->required(fn (callable $get): bool => $get('tipo_transporte') === '2')
                                    ->visible(fn (callable $get): bool => $get('tipo_transporte') === '2')
                                    ->maxLength(11),

                                TextInput::make('razon_transporte')
                                    ->label('Razón social transportista')
                                    ->required(fn (callable $get): bool => $get('tipo_transporte') === '2')
                                    ->visible(fn (callable $get): bool => $get('tipo_transporte') === '2')
                                    ->maxLength(200),

                                TextInput::make('transportista_nro_mtc')
                                    ->label('N° de registro MTC')
                                    ->visible(fn (callable $get): bool => $get('tipo_transporte') === '2')
                                    ->maxLength(30)
                                    ->columnSpanFull(),

                                // ── Transporte Privado: vehículo + conductor ──
                                TextInput::make('vehiculo')
                                    ->label('Placa del vehículo')
                                    ->required(fn (callable $get): bool => $get('tipo_transporte') === '1')
                                    ->visible(fn (callable $get): bool => $get('tipo_transporte') === '1')
                                    ->maxLength(20)
                                    ->columnSpanFull(),

                                TextInput::make('conductor_documento')
                                    ->label('DNI del conductor')
                                    ->visible(fn (callable $get): bool => $get('tipo_transporte') === '1')
                                    ->maxLength(15),

                                TextInput::make('conductor_licencia')
                                    ->label('Licencia de conducir')
                                    ->visible(fn (callable $get): bool => $get('tipo_transporte') === '1')
                                    ->maxLength(30),

                                TextInput::make('conductor_nombres')
                                    ->label('Nombres del conductor')
                                    ->visible(fn (callable $get): bool => $get('tipo_transporte') === '1')
                                    ->maxLength(150),

                                TextInput::make('conductor_apellidos')
                                    ->label('Apellidos del conductor')
                                    ->visible(fn (callable $get): bool => $get('tipo_transporte') === '1')
                                    ->maxLength(150),
                            ]),
                    ])->columnSpan(1),
                ]),
        ]);
    }

    protected function handleRecordCreation(array $data): Model
    {
        return DB::transaction(function () use ($data): GuiaRemision {
            $empresa  = (int) session('id_empresa');
            $sucursal = (int) session('sucursal');

            if (blank($data['productos'] ?? [])) {
                throw ValidationException::withMessages(['productos' => 'La guía debe tener al menos un producto.']);
            }

            $numero = (int) (GuiaRemision::where('id_empresa', $empresa)
                ->where('sucursal', $sucursal)
                ->lockForUpdate()
                ->max('numero')) + 1;

            $guia = GuiaRemision::create([
                'id_venta'              => $data['id_venta'],
                'motivo_traslado'       => $data['motivo_traslado'] ?? '01',
                'fecha_emision'         => $data['fecha_emision'],
                'fecha_traslado'        => $data['fecha_traslado'] ?? $data['fecha_emision'],
                'dir_llegada'           => $data['dir_llegada'] ?? null,
                'ubigeo'                => $data['ubigeo'] ?? null,
                'dir_partida'           => $data['dir_partida'] ?? null,
                'ubigeo_partida'        => $data['ubigeo_partida'] ?? null,
                'tipo_transporte'       => $data['tipo_transporte'] ?? '1',
                'ruc_transporte'        => $data['ruc_transporte'] ?? null,
                'razon_transporte'      => $data['razon_transporte'] ?? null,
                'transportista_nro_mtc' => $data['transportista_nro_mtc'] ?? null,
                'vehiculo'              => $data['vehiculo'] ?? null,
                'conductor_documento'   => $data['conductor_documento'] ?? null,
                'conductor_nombres'     => $data['conductor_nombres'] ?? null,
                'conductor_apellidos'   => $data['conductor_apellidos'] ?? null,
                'conductor_licencia'    => $data['conductor_licencia'] ?? null,
                'peso'                  => $data['peso'] ?? 1,
                'und_peso_total'        => 'KGM',
                'nro_bultos'            => $data['nro_bultos'] ?? 1,
                'serie'                 => 'T001',
                'numero'                => $numero,
                'estado'                => '1',
                'enviado_sunat'         => '0',
                'estado_gre'            => 'pendiente',
                'id_empresa'            => $empresa,
                'sucursal'              => $sucursal,
            ]);

            foreach ($data['productos'] as $linea) {
                GuiaDetalle::create([
                    'id_guia'     => $guia->id_guia_remision,
                    'id_producto' => $linea['id_producto'],
                    'detalles'    => $linea['detalles'],
                    'unidad'      => $linea['unidad'] ?? 'NIU',
                    'cantidad'    => $linea['cantidad'],
                    'precio'      => $linea['precio'] ?? 0,
                ]);
            }

            Notification::make()->success()
                ->title('Guía T001-' . str_pad((string) $numero, 8, '0', STR_PAD_LEFT) . ' registrada')
                ->send();

            return $guia;
        });
    }

    protected function afterCreate(): void
    {
        // Generar el XML al crear (sin enviar), para poder revisarlo antes.
        // Best-effort: si el servicio falla, la guía queda creada y se puede
        // regenerar desde la lista con "Regenerar XML".
        try {
            $res = app(\App\Services\GuiaSunatService::class)->generarXml($this->getRecord());
            if (! $res['ok']) {
                Notification::make()->warning()
                    ->title('Guía creada, pero el XML no se generó')
                    ->body($res['msg'] . ' Podés regenerarlo desde la lista.')
                    ->send();
            }
        } catch (\Throwable $e) {
            Notification::make()->warning()
                ->title('Guía creada, pero el XML no se generó')
                ->body('Podés regenerarlo desde la lista con "Regenerar XML".')
                ->send();
        }
    }

    protected function getRedirectUrl(): string
    {
        return GuiaRemisionResource::getUrl('index');
    }
}
