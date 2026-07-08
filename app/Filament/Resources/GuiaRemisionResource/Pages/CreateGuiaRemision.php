<?php

namespace App\Filament\Resources\GuiaRemisionResource\Pages;

use App\Filament\Resources\GuiaRemisionResource;
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
                    ])->columnSpan(['default' => 1, 'xl' => 2]),

                    // ── DERECHA (angosta): datos de la guía y del traslado ──
                    Group::make([
                        Section::make('Guía')
                            ->compact()
                            ->columns(2)
                            ->schema([
                                Placeholder::make('numero_guia')
                                    ->label('Número')
                                    ->content(fn (): HtmlString => new HtmlString(
                                        '<span style="font-weight:700;font-size:1.05rem;color:rgb(59,130,246)">'
                                        . static::proximoNumero() . '</span>'
                                    )),

                                DatePicker::make('fecha_emision')
                                    ->label('Fecha de emisión')
                                    ->default(now())
                                    ->required(),

                                TextInput::make('dir_llegada')
                                    ->label('Dirección de llegada')
                                    ->maxLength(220)
                                    ->columnSpanFull(),

                                TextInput::make('ubigeo')
                                    ->label('Ubigeo')
                                    ->maxLength(10),

                                TextInput::make('nro_bultos')
                                    ->label('N° de bultos')
                                    ->numeric()
                                    ->integer()
                                    ->minValue(1)
                                    ->default(1),

                                TextInput::make('peso')
                                    ->label('Peso total (kg)')
                                    ->numeric()
                                    ->minValue(0)
                                    ->default(0)
                                    ->columnSpanFull(),
                            ]),

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
                                    ->required()
                                    ->columnSpanFull(),

                                TextInput::make('ruc_transporte')
                                    ->label('RUC transportista')
                                    ->maxLength(11),

                                TextInput::make('razon_transporte')
                                    ->label('Razón social transportista')
                                    ->maxLength(200),

                                TextInput::make('vehiculo')
                                    ->label('Placa del vehículo')
                                    ->maxLength(20),

                                TextInput::make('chofer_brevete')
                                    ->label('Chofer / Brevete')
                                    ->maxLength(100),
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
                'id_venta'         => $data['id_venta'],
                'fecha_emision'    => $data['fecha_emision'],
                'dir_llegada'      => $data['dir_llegada'] ?? null,
                'ubigeo'           => $data['ubigeo'] ?? null,
                'tipo_transporte'  => $data['tipo_transporte'] ?? '1',
                'ruc_transporte'   => $data['ruc_transporte'] ?? null,
                'razon_transporte' => $data['razon_transporte'] ?? null,
                'vehiculo'         => $data['vehiculo'] ?? null,
                'chofer_brevete'   => $data['chofer_brevete'] ?? null,
                'peso'             => $data['peso'] ?? 0,
                'nro_bultos'       => $data['nro_bultos'] ?? 1,
                'serie'            => 'T001',
                'numero'           => $numero,
                'estado'           => '1',
                'enviado_sunat'    => '0',
                'id_empresa'       => $empresa,
                'sucursal'         => $sucursal,
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

    protected function getRedirectUrl(): string
    {
        return GuiaRemisionResource::getUrl('index');
    }
}
