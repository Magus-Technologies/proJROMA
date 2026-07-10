<?php

namespace App\Filament\Resources\GuiaRemisionResource\Pages;

use App\Filament\Resources\GuiaRemisionResource;
use App\Models\Empresa;
use App\Models\GuiaDetalle;
use App\Models\GuiaRemision;
use App\Models\ProductoVenta;
use App\Models\Venta;
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
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\HtmlString;
use Illuminate\Validation\ValidationException;

class CreateGuiaRemision extends CreateRecord
{
    use \App\Filament\Concerns\HasUbigeoSelector;

    protected static string $resource = GuiaRemisionResource::class;

    protected static ?string $title = 'Nueva Guía de Remisión';

    public function mount(): void
    {
        parent::mount();

        $this->precargarPartida();

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
        ], static::transporteDesdeDespacho($venta)));
    }

    /**
     * Si la venta pertenece a un despacho TMS activo, pre-carga el
     * transporte: modalidad privada, placa del vehículo y datos del
     * conductor asignados en el despacho.
     */
    protected static function transporteDesdeDespacho(Venta $venta): array
    {
        $despacho = DB::table('tms_despachos as d')
            ->join('tms_despacho_pedidos as dp', 'dp.id_despacho', '=', 'd.id')
            ->join('cotizaciones as c', 'c.cotizacion_id', '=', 'dp.id_cotizacion')
            ->leftJoin('tms_vehiculos as v', 'v.id', '=', 'd.id_vehiculo')
            ->leftJoin('tms_conductores as co', 'co.id', '=', 'd.id_conductor')
            ->where('d.estado', '<>', 'ANULADO')
            ->where(fn ($q) => $q
                ->where('c.id_venta', $venta->id_venta)
                ->orWhere('c.cotizacion_id', $venta->id_coti ?? 0))
            ->orderByDesc('d.id')
            ->first(['v.placa', 'co.documento', 'co.licencia', 'co.nombres']);

        if (! $despacho || blank($despacho->placa)) {
            return [];
        }

        // tms_conductores guarda el nombre completo en un solo campo:
        // las 2 últimas palabras se toman como apellidos (editable luego).
        $palabras  = preg_split('/\s+/', trim((string) $despacho->nombres)) ?: [];
        $apellidos = count($palabras) >= 3 ? implode(' ', array_splice($palabras, -2)) : (count($palabras) === 2 ? array_pop($palabras) : null);
        $nombres   = $palabras ? implode(' ', $palabras) : null;

        return array_filter([
            'tipo_transporte'     => '1', // Privado: vehículo propio del despacho
            'vehiculo'            => $despacho->placa,
            'conductor_documento' => preg_match('/^\d{8}$/', (string) $despacho->documento) ? $despacho->documento : null,
            'conductor_licencia'  => $despacho->licencia,
            'conductor_nombres'   => $nombres,
            'conductor_apellidos' => $apellidos,
        ], fn ($v) => filled($v));
    }

    /**
     * PHP convierte las claves '1' y '2' de ->options() en enteros, así que
     * `$get('tipo_transporte') === '1'` (estricto) siempre daba false y ningún
     * campo del transporte llegaba a mostrarse. Comparamos por valor.
     *
     * projRoma: 1 = Privado, 2 = Público.
     */
    protected static function esPublico(callable $get): bool
    {
        return (int) $get('tipo_transporte') === 2;
    }

    protected static function esPrivado(callable $get): bool
    {
        return (int) $get('tipo_transporte') === 1;
    }

    /**
     * El punto de partida arranca en el local de la empresa. Los selects de
     * departamento y provincia no se guardan, pero sin ellos la cascada del
     * ubigeo se ve vacía aunque el código ya esté cargado.
     */
    protected function precargarPartida(): void
    {
        $empresa = static::empresaActual();
        $ubigeo  = $empresa?->ubigeo;

        if (blank($ubigeo)) {
            return;
        }

        $partes = static::partesDeUbigeo($ubigeo);

        $this->form->fill(array_merge($this->data ?? [], array_filter([
            'dir_partida'                 => $empresa?->direccion,
            'ubigeo_partida_departamento' => $partes['departamento'],
            'ubigeo_partida_provincia'    => $partes['provincia'],
            'ubigeo_partida'              => $ubigeo,
        ], fn ($valor): bool => filled($valor))));
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

                                        // Transporte según el despacho de la venta
                                        if ($venta) {
                                            foreach (static::transporteDesdeDespacho($venta) as $campo => $valor) {
                                                $set($campo, $valor);
                                            }
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
                                    ->columnSpanFull(),

                                ...static::ubigeoSelector('ubigeo_partida', 'Distrito de partida'),
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
                                    ->columnSpanFull(),

                                ...static::ubigeoSelector('ubigeo', 'Distrito de llegada'),
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

                        // ── Transporte: SUNAT pide datos distintos según la modalidad ──
                        Section::make('Transporte')
                            ->compact()
                            ->columns(2)
                            ->schema([
                                Select::make('tipo_transporte')
                                    ->label('Modalidad de traslado')
                                    ->options([
                                        '1' => 'Privado — vehículo propio',
                                        '2' => 'Público — empresa de transporte',
                                    ])
                                    ->default('1')
                                    ->live()
                                    ->required()
                                    ->columnSpanFull(),

                                // ═══ PÚBLICO: los datos son del transportista ═══
                                // SUNAT rechaza (3347) si en privado se manda transportista.
                                Placeholder::make('nota_publico')
                                    ->hiddenLabel()
                                    ->columnSpanFull()
                                    ->visible(fn (callable $get): bool => static::esPublico($get))
                                    ->content(new HtmlString(
                                        '<div style="padding:9px 12px;border-radius:10px;font-size:.8rem;'
                                        . 'border:1px solid rgba(59,130,246,.35);background:rgba(59,130,246,.07)">'
                                        . 'En transporte público, la guía identifica a la <strong>empresa transportista</strong>. '
                                        . 'El vehículo y el conductor los declara ella en su propia guía.</div>'
                                    )),

                                TextInput::make('ruc_transporte')
                                    ->label('RUC del transportista')
                                    ->placeholder('20123456789')
                                    ->helperText('11 dígitos. Tocá la lupa para traerlo de SUNAT.')
                                    ->rule('digits:11')
                                    ->required(fn (callable $get): bool => static::esPublico($get))
                                    ->visible(fn (callable $get): bool => static::esPublico($get))
                                    ->maxLength(11)
                                    ->suffixAction(
                                        Action::make('consultar_transportista')
                                            ->icon('heroicon-m-magnifying-glass')
                                            ->tooltip('Consultar SUNAT')
                                            ->action(fn ($state, callable $set) => static::consultarTransportista($state, $set))
                                    ),

                                TextInput::make('razon_transporte')
                                    ->label('Razón social del transportista')
                                    ->required(fn (callable $get): bool => static::esPublico($get))
                                    ->visible(fn (callable $get): bool => static::esPublico($get))
                                    ->maxLength(200),

                                TextInput::make('transportista_nro_mtc')
                                    ->label('N° de registro MTC')
                                    ->helperText('Opcional. Registro del Ministerio de Transportes.')
                                    ->visible(fn (callable $get): bool => static::esPublico($get))
                                    ->maxLength(30)
                                    ->columnSpanFull(),

                                // ═══ PRIVADO: vehículo propio + conductor ═══
                                Placeholder::make('nota_privado')
                                    ->hiddenLabel()
                                    ->columnSpanFull()
                                    ->visible(fn (callable $get): bool => static::esPrivado($get))
                                    ->content(new HtmlString(
                                        '<div style="padding:9px 12px;border-radius:10px;font-size:.8rem;'
                                        . 'border:1px solid rgba(148,163,184,.4);background:rgba(148,163,184,.08)">'
                                        . 'La <strong>placa es obligatoria</strong>. Los datos del conductor son opcionales, '
                                        . 'pero si cargás uno tenés que cargar <strong>los cuatro</strong> — SUNAT rechaza '
                                        . 'los conductores incompletos.</div>'
                                    )),

                                TextInput::make('vehiculo')
                                    ->label('Placa del vehículo')
                                    ->placeholder('ABC-123')
                                    ->required(fn (callable $get): bool => static::esPrivado($get))
                                    ->visible(fn (callable $get): bool => static::esPrivado($get))
                                    ->maxLength(20)
                                    ->columnSpanFull(),

                                TextInput::make('conductor_documento')
                                    ->label('DNI del conductor')
                                    ->helperText('8 dígitos. Tocá la lupa para traerlo de RENIEC.')
                                    ->rule('digits:8')
                                    ->visible(fn (callable $get): bool => static::esPrivado($get))
                                    ->maxLength(8)
                                    ->suffixAction(
                                        Action::make('consultar_conductor')
                                            ->icon('heroicon-m-magnifying-glass')
                                            ->tooltip('Consultar RENIEC')
                                            ->action(fn ($state, callable $set) => static::consultarConductor($state, $set))
                                    ),

                                TextInput::make('conductor_licencia')
                                    ->label('Licencia de conducir')
                                    ->visible(fn (callable $get): bool => static::esPrivado($get))
                                    ->maxLength(30),

                                TextInput::make('conductor_nombres')
                                    ->label('Nombres del conductor')
                                    ->visible(fn (callable $get): bool => static::esPrivado($get))
                                    ->maxLength(150),

                                TextInput::make('conductor_apellidos')
                                    ->label('Apellidos del conductor')
                                    ->visible(fn (callable $get): bool => static::esPrivado($get))
                                    ->maxLength(150),
                            ]),
                    ])->columnSpan(1),
                ]),
        ]);
    }

    /** Trae nombres y apellidos del conductor desde RENIEC por su DNI. */
    protected static function consultarConductor(?string $dni, callable $set): void
    {
        $dni = preg_replace('/\D/', '', (string) $dni);

        if (strlen($dni) !== 8) {
            Notification::make()->warning()->title('Ingresá los 8 dígitos del DNI.')->send();

            return;
        }

        try {
            $data = Http::timeout(8)
                ->get(config('apisperu.url') . "/dni/{$dni}", ['token' => config('apisperu.token')])
                ->json();
        } catch (\Throwable) {
            Notification::make()->warning()->title('No se pudo consultar RENIEC. Intentá de nuevo.')->send();

            return;
        }

        $nombres   = trim((string) ($data['nombres'] ?? ''));
        $apellidos = trim(($data['apellidoPaterno'] ?? '') . ' ' . ($data['apellidoMaterno'] ?? ''));

        if ($nombres === '' && $apellidos === '') {
            Notification::make()->warning()->title($data['message'] ?? 'DNI no encontrado.')->send();

            return;
        }

        $set('conductor_nombres', $nombres);
        $set('conductor_apellidos', $apellidos);

        Notification::make()->success()->title('Conductor cargado desde RENIEC')->send();
    }

    /** Trae la razón social del transportista desde SUNAT por su RUC. */
    protected static function consultarTransportista(?string $ruc, callable $set): void
    {
        $ruc = preg_replace('/\D/', '', (string) $ruc);

        if (strlen($ruc) !== 11) {
            Notification::make()->warning()->title('Ingresá los 11 dígitos del RUC.')->send();

            return;
        }

        try {
            $data = Http::timeout(8)
                ->get(config('apisperu.url') . "/ruc/{$ruc}", ['token' => config('apisperu.token')])
                ->json();
        } catch (\Throwable) {
            Notification::make()->warning()->title('No se pudo consultar SUNAT. Intentá de nuevo.')->send();

            return;
        }

        if (empty($data['razonSocial'])) {
            Notification::make()->warning()->title('RUC no encontrado.')->send();

            return;
        }

        $set('razon_transporte', $data['razonSocial']);

        Notification::make()->success()->title('Transportista cargado desde SUNAT')->send();
    }

    /** Error de negocio siempre visible (una notificación, no un campo oculto). */
    protected function fallo(string $mensaje): never
    {
        Notification::make()->danger()->title($mensaje)->persistent()->send();

        throw new Halt();
    }

    /**
     * Reglas de SUNAT según la modalidad de traslado, con los códigos de error
     * que devuelve si no se cumplen:
     *
     *  PÚBLICO (mod_traslado 01)
     *    - RUC del transportista: 11 dígitos, obligatorio.
     *    - Razón social: obligatoria.
     *    - No se declara vehículo ni conductor (los declara el transportista).
     *
     *  PRIVADO (mod_traslado 02)
     *    - Placa del vehículo: obligatoria      → error 2566 si falta.
     *    - Conductor: los 4 datos o ninguno     → error 3357 si está incompleto.
     *    - No se declara transportista           → error 3347 si se envía.
     */
    protected function validarTransporte(array $data): void
    {
        $esPublico = (int) ($data['tipo_transporte'] ?? 1) === 2;

        if ($esPublico) {
            $ruc = preg_replace('/\D/', '', (string) ($data['ruc_transporte'] ?? ''));

            if (strlen($ruc) !== 11) {
                $this->fallo('En transporte público, el RUC del transportista debe tener 11 dígitos.');
            }

            if (blank($data['razon_transporte'] ?? null)) {
                $this->fallo('En transporte público, la razón social del transportista es obligatoria.');
            }

            return;
        }

        // ── Privado ──
        if (blank($data['vehiculo'] ?? null)) {
            $this->fallo('En transporte privado, la placa del vehículo es obligatoria (SUNAT la exige).');
        }

        $conductor = [
            'DNI'       => $data['conductor_documento'] ?? null,
            'nombres'   => $data['conductor_nombres'] ?? null,
            'apellidos' => $data['conductor_apellidos'] ?? null,
            'licencia'  => $data['conductor_licencia'] ?? null,
        ];

        $cargados = array_filter($conductor, fn ($v) => filled($v));

        // Todo o nada: SUNAT rechaza un conductor con datos incompletos.
        if ($cargados !== [] && count($cargados) !== 4) {
            $faltan = array_keys(array_diff_key($conductor, $cargados));

            $this->fallo('Los datos del conductor están incompletos. Falta: ' . implode(', ', $faltan)
                . '. Cargá los cuatro (DNI, nombres, apellidos y licencia) o dejalos todos vacíos.');
        }
    }

    protected function handleRecordCreation(array $data): Model
    {
        return DB::transaction(function () use ($data): GuiaRemision {
            $empresa  = (int) session('id_empresa');
            $sucursal = (int) session('sucursal');

            if (blank($data['productos'] ?? [])) {
                $this->fallo('La guía debe tener al menos un producto.');
            }

            $this->validarTransporte($data);

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

            // El XML se genera dentro de la transacción: si SUNAT rechaza los
            // datos, la guía no llega a existir. Una guía guardada sin XML deja
            // el correlativo quemado y obliga a corregirla a mano.
            $this->generarXmlOAbortar($guia);

            Notification::make()->success()
                ->title('Guía T001-' . str_pad((string) $numero, 8, '0', STR_PAD_LEFT) . ' registrada')
                ->send();

            return $guia;
        });
    }

    /**
     * Genera el XML y decide qué hacer si falla:
     *   - datos inválidos → revierte la transacción, la guía no se guarda.
     *   - servicio caído  → deja la guía guardada y avisa; se regenera después.
     */
    protected function generarXmlOAbortar(GuiaRemision $guia): void
    {
        try {
            $res = app(\App\Services\GuiaSunatService::class)->generarXml($guia);
        } catch (\Throwable $e) {
            Notification::make()->warning()
                ->title('Guía guardada, pero el XML no se generó')
                ->body('Podés regenerarlo desde la lista con "Regenerar XML".')
                ->send();

            return;
        }

        if ($res['ok']) {
            return;
        }

        if ($res['datos_invalidos'] ?? true) {
            $this->fallo('No se guardó la guía: ' . $res['msg']);
        }

        Notification::make()->warning()
            ->title('Guía guardada, pero el XML no se generó')
            ->body($res['msg'] . ' Podés regenerarlo desde la lista.')
            ->send();
    }

    protected function getRedirectUrl(): string
    {
        return GuiaRemisionResource::getUrl('index');
    }
}
