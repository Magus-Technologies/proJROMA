<?php

namespace App\Filament\Pages;

use App\Models\CajaMovimiento;
use App\Services\CajaService;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Illuminate\Support\HtmlString;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class MiCaja extends Page implements HasTable
{
    use \App\Filament\Concerns\VerificaPermisoDeAcceso;

    public const PERMISO_ACCESO = 'caja.ver';

    use InteractsWithTable;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-wallet';
    protected static ?string $navigationLabel = 'Mi Caja';
    protected static string|\UnitEnum|null $navigationGroup = 'Caja';
    protected static ?int $navigationSort = 5;
    protected string $view = 'filament.pages.mi-caja';

    public ?object $caja = null;

    /** Denominaciones de billetes y monedas (PEN): clave de campo => [tipo, valor, etiqueta]. */
    private const DENOMINACIONES = [
        'den_200'  => ['BILLETE', 200.00, 'Billete S/ 200.00'],
        'den_100'  => ['BILLETE', 100.00, 'Billete S/ 100.00'],
        'den_50'   => ['BILLETE', 50.00,  'Billete S/ 50.00'],
        'den_20'   => ['BILLETE', 20.00,  'Billete S/ 20.00'],
        'den_10'   => ['BILLETE', 10.00,  'Billete S/ 10.00'],
        'den_5'    => ['MONEDA',  5.00,   'Moneda S/ 5.00'],
        'den_2'    => ['MONEDA',  2.00,   'Moneda S/ 2.00'],
        'den_1'    => ['MONEDA',  1.00,   'Moneda S/ 1.00'],
        'den_0_50' => ['MONEDA',  0.50,   'Moneda S/ 0.50'],
        'den_0_20' => ['MONEDA',  0.20,   'Moneda S/ 0.20'],
        'den_0_10' => ['MONEDA',  0.10,   'Moneda S/ 0.10'],
        'den_0_05' => ['MONEDA',  0.05,   'Moneda S/ 0.05'],
    ];

    public function mount(): void
    {
        $this->caja = $this->resolverCaja();
    }

    /** Fila de conteo de una denominación: etiqueta · cantidad · total en vivo. */
    protected function triadaDenominacion(string $clave, float $valor, string $etiqueta): array
    {
        return [
            Placeholder::make("lbl_{$clave}")->hiddenLabel()->content($etiqueta),

            TextInput::make($clave)
                ->hiddenLabel()
                ->numeric()
                ->integer()
                ->minValue(0)
                ->default(0)
                ->live(debounce: 400)
                // El monto a declarar se autocompleta con el total del conteo
                ->afterStateUpdated(fn (callable $set, callable $get) => $set('monto_fijo', self::sumaDesglose($get))),

            Placeholder::make("tot_{$clave}")->hiddenLabel()
                ->content(fn (callable $get): string => number_format($valor * (int) ($get($clave) ?: 0), 2)),
        ];
    }

    /** Conteo en dos columnas (billetes | monedas) + totales en una sola fila. */
    protected function componentesConteoEfectivo(): array
    {
        $billetes = [];
        $monedas  = [];
        foreach (self::DENOMINACIONES as $clave => [$tipo, $valor, $etiqueta]) {
            $corta = 'S/ ' . number_format($valor, 2);
            if ($tipo === 'BILLETE') {
                $billetes[] = $this->triadaDenominacion($clave, $valor, $corta);
            } else {
                $monedas[] = $this->triadaDenominacion($clave, $valor, $corta);
            }
        }

        $cabecera = fn (string $sufijo, string $texto) => Placeholder::make("cab_{$sufijo}")
            ->hiddenLabel()->content(new HtmlString("<strong>{$texto}</strong>"));

        $celdas = [
            $cabecera('b_den', 'Billete'), $cabecera('b_cant', 'Cant.'), $cabecera('b_tot', 'Total'),
            $cabecera('m_den', 'Moneda'),  $cabecera('m_cant', 'Cant.'), $cabecera('m_tot', 'Total'),
        ];

        $filasMax = max(count($billetes), count($monedas));
        for ($i = 0; $i < $filasMax; $i++) {
            foreach (($billetes[$i] ?? null) ?? [] as $c) $celdas[] = $c;
            if (! isset($billetes[$i])) {
                foreach (['den', 'cant', 'tot'] as $s) {
                    $celdas[] = Placeholder::make("vacio_b_{$i}_{$s}")->hiddenLabel()->content('');
                }
            }
            foreach (($monedas[$i] ?? null) ?? [] as $c) $celdas[] = $c;
            if (! isset($monedas[$i])) {
                foreach (['den', 'cant', 'tot'] as $s) {
                    $celdas[] = Placeholder::make("vacio_m_{$i}_{$s}")->hiddenLabel()->content('');
                }
            }
        }

        return [
            Section::make('Desglose de billetes y monedas')
                ->compact()
                ->schema([
                    Grid::make(6)->schema($celdas),
                ]),

            Grid::make(3)->schema([
                Placeholder::make('total_contado')
                    ->label('Total calculado')
                    ->content(fn (callable $get): string => 'S/ ' . number_format(self::sumaDesglose($get), 2)),

                TextInput::make('monto_fijo')
                    ->label('Monto a declarar')
                    ->numeric()
                    ->minValue(0)
                    ->prefix('S/')
                    ->live(debounce: 400)
                    ->helperText('Se autocompleta; puedes corregirlo.'),

                Placeholder::make('total_final')
                    ->label('TOTAL FINAL')
                    ->content(function (callable $get): string {
                        $fijo  = (float) ($get('monto_fijo') ?: 0);
                        $suma  = self::sumaDesglose($get);
                        $total = $fijo > 0 ? $fijo : $suma;

                        return 'S/ ' . number_format($total, 2) . ($fijo > 0 && abs($fijo - $suma) > 0.001 ? ' (fijo)' : '');
                    }),
            ]),
        ];
    }

    protected static function sumaDesglose(callable $get): float
    {
        $total = 0.0;
        foreach (self::DENOMINACIONES as $clave => [$tipo, $valor, $etiqueta]) {
            $total += $valor * (int) ($get($clave) ?: 0);
        }

        return round($total, 2);
    }

    /** Total final y filas con cantidad > 0 a partir del estado del formulario. */
    protected static function resolverConteo(array $data): array
    {
        $detalles = [];
        $suma = 0.0;
        foreach (self::DENOMINACIONES as $clave => [$tipo, $valor, $etiqueta]) {
            $cantidad = (int) ($data[$clave] ?? 0);
            if ($cantidad <= 0) {
                continue;
            }
            $subtotal = round($valor * $cantidad, 2);
            $suma += $subtotal;
            $detalles[] = [
                'tipo'         => $tipo,
                'denominacion' => $valor,
                'cantidad'     => $cantidad,
                'subtotal'     => $subtotal,
            ];
        }

        $fijo  = (float) ($data['monto_fijo'] ?? 0);
        $total = $fijo > 0 ? $fijo : round($suma, 2);

        return [$total, $detalles, $fijo > 0];
    }

    protected function resolverCaja(): ?object
    {
        return DB::table('cajas')
            ->where('id_empresa', (int) session('id_empresa'))
            ->where('id_usuario_responsable', auth()->user()->usuario_id)
            ->where('estado', 'ACTIVA')
            ->orderByRaw('CASE WHEN id_caja_padre IS NOT NULL THEN 0 ELSE 1 END')
            ->first();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => CajaMovimiento::query()
                ->where('id_caja', $this->caja->id ?? 0))
            ->columns([
                TextColumn::make('created_at')
                    ->label('Fecha y hora')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                TextColumn::make('tipo')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'INGRESO' => 'success',
                        'EGRESO'  => 'danger',
                        default   => 'gray',
                    }),

                TextColumn::make('categoria')
                    ->label('Categoría')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('descripcion')
                    ->label('Descripción')
                    ->wrap()
                    ->limit(50),

                TextColumn::make('instrumento_tipo')
                    ->label('Instrumento')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'EFECTIVO'          => 'Efectivo',
                        'TRANSFERENCIA'     => 'Transferencia',
                        'BILLETERA_DIGITAL' => 'Billetera digital',
                        default             => $state ?? '—',
                    }),

                TextColumn::make('monto')
                    ->label('Monto')
                    ->money('PEN')
                    ->sortable(),

                TextColumn::make('saldo_posterior')
                    ->label('Saldo')
                    ->money('PEN'),

                IconColumn::make('estado')
                    ->label('Estado')
                    ->boolean()
                    ->getStateUsing(fn ($record): bool => $record->estado === 'CONFIRMADO')
                    ->tooltip(fn ($record): string => ucfirst(strtolower($record->estado ?? ''))),
            ])
            ->actions([
                Action::make('ver_apertura')
                    ->label('Ver apertura')
                    ->iconButton()
                    ->tooltip('Ver detalle de la apertura')
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->visible(fn (CajaMovimiento $record): bool => $this->esMovimientoDeApertura($record)
                        && auth()->user()->can('caja.apertura_ver'))
                    ->modalHeading('Apertura — ' . ($this->caja->nombre ?? ''))
                    ->modalWidth('lg')
                    ->modalContent(function (CajaMovimiento $record) {
                        $apertura = $this->aperturaDeMovimiento($record);

                        return view('filament.caja.apertura-detalle', [
                            'apertura' => $apertura,
                            'detalles' => DB::table('caja_apertura_detalles')
                                ->where('id_apertura', $apertura->id)
                                ->orderByDesc('denominacion')
                                ->get(),
                            'usuario'  => DB::table('usuarios')
                                ->where('usuario_id', $apertura->id_usuario_apertura)
                                ->value('nombres'),
                        ]);
                    })
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Cerrar'),

                Action::make('editar_apertura')
                    ->label('Editar apertura')
                    ->iconButton()
                    ->tooltip('Editar la apertura')
                    ->icon('heroicon-o-pencil-square')
                    ->color('warning')
                    ->modalWidth('3xl')
                    ->visible(fn (CajaMovimiento $record): bool => $this->esMovimientoDeApertura($record)
                        && $this->aperturaDeMovimiento($record)?->estado === 'ABIERTA'
                        && auth()->user()->can('caja.apertura_editar'))
                    ->fillForm(function (CajaMovimiento $record): array {
                        $apertura = $this->aperturaDeMovimiento($record);

                        $data = [
                            'fecha'         => $apertura->fecha,
                            'observaciones' => trim(str_replace('[Monto fijo ingresado]', '', (string) $apertura->observaciones)) ?: null,
                            'monto_fijo'    => (float) $apertura->monto_total,
                        ];
                        foreach (array_keys(self::DENOMINACIONES) as $clave) {
                            $data[$clave] = 0;
                        }

                        $detalles = DB::table('caja_apertura_detalles')->where('id_apertura', $apertura->id)->get();
                        foreach ($detalles as $d) {
                            foreach (self::DENOMINACIONES as $clave => [$tipo, $valor, $etiqueta]) {
                                if ($tipo === $d->tipo && abs($valor - (float) $d->denominacion) < 0.001) {
                                    $data[$clave] = (int) $d->cantidad;
                                    break;
                                }
                            }
                        }

                        return $data;
                    })
                    ->form([
                        DatePicker::make('fecha')
                            ->label('Fecha')
                            ->required(),
                        ...$this->componentesConteoEfectivo(),
                        Textarea::make('observaciones')
                            ->label('Observaciones')
                            ->maxLength(500),
                    ])
                    ->action(function (array $data, CajaMovimiento $record): void {
                        $apertura = $this->aperturaDeMovimiento($record);
                        $cajaId   = (int) $this->caja->id;
                        [$montoTotal, $detalles, $esMontoFijo] = self::resolverConteo($data);

                        if ($montoTotal <= 0) {
                            Notification::make()->warning()->title('Ingresa el conteo de efectivo o un monto fijo.')->send();

                            return;
                        }

                        DB::transaction(function () use ($data, $cajaId, $apertura, $montoTotal, $detalles, $esMontoFijo): void {
                            DB::table('caja_aperturas')->where('id', $apertura->id)->update([
                                'fecha'         => $data['fecha'],
                                'monto_total'   => $montoTotal,
                                'observaciones' => trim(($data['observaciones'] ?? '') . ($esMontoFijo ? ' [Monto fijo ingresado]' : '')) ?: null,
                                'updated_at'    => now(),
                            ]);

                            DB::table('caja_apertura_detalles')->where('id_apertura', $apertura->id)->delete();
                            if ($detalles !== []) {
                                DB::table('caja_apertura_detalles')->insert(array_map(fn (array $d): array => [
                                    'id_apertura'  => $apertura->id,
                                    'denominacion' => $d['denominacion'],
                                    'tipo'         => $d['tipo'],
                                    'cantidad'     => $d['cantidad'],
                                    'subtotal'     => $d['subtotal'],
                                ], $detalles));
                            }

                            // Reemplazar el movimiento de apertura: anular el anterior
                            // (restaura el saldo) y registrar el nuevo monto.
                            $svc = app(CajaService::class);
                            $movAnterior = DB::table('caja_movimientos')
                                ->where('origen_tipo', 'APERTURA')
                                ->where('origen_id', $apertura->id)
                                ->where('estado', 'CONFIRMADO')
                                ->orderByDesc('id')
                                ->first();
                            if ($movAnterior) {
                                $svc->anularMovimiento($movAnterior->id);
                            }
                            $svc->registrarMovimiento([
                                'id_caja'          => $cajaId,
                                'fecha'            => $data['fecha'],
                                'tipo'             => 'INGRESO',
                                'categoria'        => 'APERTURA',
                                'descripcion'      => 'Apertura de caja (fondo inicial, corregida)',
                                'monto'            => $montoTotal,
                                'instrumento_tipo' => 'EFECTIVO',
                                'origen_tipo'      => 'APERTURA',
                                'origen_id'        => $apertura->id,
                                'id_usuario'       => (int) auth()->user()->usuario_id,
                            ]);
                        });

                        Notification::make()->success()
                            ->title('Apertura actualizada')
                            ->body('Nuevo monto de apertura: S/ ' . number_format($montoTotal, 2))
                            ->send();
                        $this->caja = $this->resolverCaja();
                    }),
            ])
            ->defaultSort('id', 'desc');
    }

    protected function esMovimientoDeApertura(CajaMovimiento $record): bool
    {
        return $record->categoria === 'APERTURA'
            && $record->origen_tipo === 'APERTURA'
            && $record->estado === 'CONFIRMADO'
            && $record->origen_id !== null;
    }

    protected function aperturaDeMovimiento(CajaMovimiento $record): ?object
    {
        return DB::table('caja_aperturas')->where('id', (int) $record->origen_id)->first();
    }

    protected function getHeaderActions(): array
    {
        if (! $this->caja) {
            return [];
        }

        $cajaId = (int) $this->caja->id;
        $esHija = $this->caja->id_caja_padre !== null;

        $movimientoForm = [
            TextInput::make('descripcion')
                ->label('Descripción')
                ->required(),
            TextInput::make('monto')
                ->label('Monto')
                ->numeric()
                ->minValue(0.01)
                ->prefix('S/')
                ->required(),
            DatePicker::make('fecha')
                ->label('Fecha')
                ->default(now())
                ->required(),
            Select::make('instrumento_tipo')
                ->label('Instrumento')
                ->options([
                    'EFECTIVO'          => 'Efectivo',
                    'TRANSFERENCIA'     => 'Transferencia',
                    'BILLETERA_DIGITAL' => 'Billetera Digital',
                ])
                ->required(),
        ];

        return [
            Action::make('aperturar')
                ->label('Aperturar Caja')
                ->icon('heroicon-o-lock-open')
                ->color('primary')
                ->modalWidth('3xl')
                ->visible(fn (): bool => $esHija && ! DB::table('caja_aperturas')
                    ->where('id_caja', $cajaId)
                    ->where('estado', 'ABIERTA')
                    ->exists())
                ->form([
                    DatePicker::make('fecha')
                        ->label('Fecha')
                        ->default(now())
                        ->required(),
                    ...$this->componentesConteoEfectivo(),
                    Textarea::make('observaciones')
                        ->label('Observaciones')
                        ->maxLength(500),
                ])
                ->action(function (array $data) use ($cajaId): void {
                    [$montoTotal, $detalles, $esMontoFijo] = self::resolverConteo($data);

                    if ($montoTotal <= 0) {
                        Notification::make()->warning()->title('Ingresa el conteo de efectivo o un monto fijo.')->send();

                        return;
                    }

                    DB::transaction(function () use ($data, $cajaId, $montoTotal, $detalles, $esMontoFijo): void {
                        $idApertura = DB::table('caja_aperturas')->insertGetId([
                            'id_caja'             => $cajaId,
                            'fecha'               => $data['fecha'],
                            'monto_total'         => $montoTotal,
                            'estado'              => 'ABIERTA',
                            'id_usuario_apertura' => (int) auth()->user()->usuario_id,
                            'observaciones'       => trim(($data['observaciones'] ?? '') . ($esMontoFijo ? ' [Monto fijo ingresado]' : '')) ?: null,
                            'created_at'          => now(),
                            'updated_at'          => now(),
                        ]);

                        $filas = array_map(fn (array $d): array => [
                            'id_apertura'  => $idApertura,
                            'denominacion' => $d['denominacion'],
                            'tipo'         => $d['tipo'],
                            'cantidad'     => $d['cantidad'],
                            'subtotal'     => $d['subtotal'],
                        ], $detalles);

                        if ($filas !== []) {
                            DB::table('caja_apertura_detalles')->insert($filas);
                        }

                        // El fondo de apertura entra como INGRESO: actualiza el
                        // saldo y aparece en el listado de movimientos.
                        app(CajaService::class)->registrarMovimiento([
                            'id_caja'          => $cajaId,
                            'fecha'            => $data['fecha'],
                            'tipo'             => 'INGRESO',
                            'categoria'        => 'APERTURA',
                            'descripcion'      => 'Apertura de caja (fondo inicial)',
                            'monto'            => $montoTotal,
                            'instrumento_tipo' => 'EFECTIVO',
                            'origen_tipo'      => 'APERTURA',
                            'origen_id'        => $idApertura,
                            'id_usuario'       => (int) auth()->user()->usuario_id,
                        ]);
                    });

                    Notification::make()->success()
                        ->title('Caja aperturada')
                        ->body('Monto de apertura: S/ ' . number_format($montoTotal, 2) . ($esMontoFijo ? ' (monto fijo)' : ''))
                        ->send();
                    $this->caja = $this->resolverCaja();
                }),

            Action::make('ingreso')
                ->label('Ingreso')
                ->color('success')
                ->icon('heroicon-o-arrow-down-circle')
                ->form($movimientoForm)
                ->action(function (array $data) use ($cajaId): void {
                    app(CajaService::class)->registrarMovimiento(array_merge($data, [
                        'id_caja'    => $cajaId,
                        'tipo'       => 'INGRESO',
                        'categoria'  => 'MANUAL',
                        'id_usuario' => (int) auth()->user()->usuario_id,
                    ]));
                    Notification::make()->success()->title('Ingreso registrado')->send();
                    $this->caja = $this->resolverCaja();
                }),

            Action::make('egreso')
                ->label('Egreso')
                ->color('danger')
                ->icon('heroicon-o-arrow-up-circle')
                ->form($movimientoForm)
                ->action(function (array $data) use ($cajaId): void {
                    app(CajaService::class)->registrarMovimiento(array_merge($data, [
                        'id_caja'    => $cajaId,
                        'tipo'       => 'EGRESO',
                        'categoria'  => 'MANUAL',
                        'id_usuario' => (int) auth()->user()->usuario_id,
                    ]));
                    Notification::make()->success()->title('Egreso registrado')->send();
                    $this->caja = $this->resolverCaja();
                }),

            Action::make('cerrar')
                ->label('Cerrar Caja')
                ->color('warning')
                ->icon('heroicon-o-lock-closed')
                ->visible(fn (): bool => $esHija)
                ->modalWidth('3xl')
                ->modalDescription('Cuenta el efectivo de tu caja: el saldo declarado se calcula solo con el desglose de billetes y monedas. Saldo según sistema: S/ ' . number_format((float) ($this->caja->saldo_actual ?? 0), 2))
                ->form($this->componentesConteoEfectivo())
                ->action(function (array $data) use ($cajaId): void {
                    [$saldoDeclarado, $detalles, $esMontoFijo] = self::resolverConteo($data);

                    $saldoSistema = (float) ($this->caja->saldo_actual ?? 0);
                    $diferencia   = round($saldoDeclarado - $saldoSistema, 2);

                    try {
                        app(CajaService::class)->cerrarCaja(
                            $cajaId,
                            $saldoDeclarado,
                            $detalles,
                            (int) auth()->user()->usuario_id
                        );
                        DB::table('caja_aperturas')
                            ->where('id_caja', $cajaId)
                            ->where('estado', 'ABIERTA')
                            ->update(['estado' => 'CERRADA', 'updated_at' => now()]);

                        $detalleDif = $diferencia == 0.0
                            ? 'Cuadre exacto.'
                            : ($diferencia > 0
                                ? 'Sobrante de S/ ' . number_format($diferencia, 2) . '.'
                                : 'Faltante de S/ ' . number_format(abs($diferencia), 2) . '.');

                        Notification::make()->success()
                            ->title('Cierre registrado — contado S/ ' . number_format($saldoDeclarado, 2))
                            ->body($detalleDif . ' Queda pendiente de aprobación.')
                            ->send();
                        $this->caja = $this->resolverCaja();
                    } catch (\Throwable $e) {
                        Notification::make()->danger()->title('Error al cerrar caja')->body($e->getMessage())->send();
                    }
                }),
        ];
    }
}
