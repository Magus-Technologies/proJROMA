<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CuentaPorCobrarResource\Pages;
use App\Models\CxcAbono;
use App\Models\DiasVenta;
use App\Services\CajaService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class CuentaPorCobrarResource extends Resource
{
    use \App\Filament\Concerns\VerificaPermisoDeAcceso;

    public const PERMISO_ACCESO = 'cobranzas.ver';

    protected static ?string $model = DiasVenta::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationLabel = 'Cuentas por Cobrar';
    protected static string|\UnitEnum|null $navigationGroup = 'Cobranzas';
    protected static ?int $navigationSort = 1;
    protected static ?string $label = 'Cuenta por Cobrar';
    protected static ?string $pluralLabel = 'Cuentas por Cobrar';

    public const TIPOS_PAGO = [
        'EFECTIVO'      => 'Efectivo',
        'YAPE'          => 'Yape',
        'PLIN'          => 'Plin',
        'TRANSFERENCIA' => 'Transferencia',
        'DEPOSITO'      => 'Depósito',
    ];

    public static function getNavigationBadge(): ?string
    {
        $count = static::getEloquentQuery()->where('dias_ventas.estado', '0')->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('documento')
                    ->label('Documento')
                    ->getStateUsing(fn (DiasVenta $record): string =>
                        $record->venta
                            ? "{$record->venta->serie}-" . str_pad($record->venta->numero, 8, '0', STR_PAD_LEFT)
                            : '—')
                    ->searchable(query: fn (Builder $query, string $search): Builder =>
                        $query->whereHas('venta', fn (Builder $q) => $q
                            ->where('serie', 'like', "%{$search}%")
                            ->orWhere('numero', 'like', "%{$search}%"))),

                TextColumn::make('venta.cliente.datos')
                    ->label('Cliente')
                    ->searchable()
                    ->placeholder('— Sin cliente —')
                    ->wrap()
                    ->limit(40),

                TextColumn::make('fecha')
                    ->label('Fecha Venc.')
                    ->date('d/m/Y')
                    ->sortable()
                    ->color(fn (DiasVenta $record): ?string =>
                        $record->estado !== '1' && $record->fecha?->isPast() ? 'danger' : null)
                    ->weight(fn (DiasVenta $record): ?string =>
                        $record->estado !== '1' && $record->fecha?->isPast() ? 'bold' : null),

                TextColumn::make('monto')
                    ->label('Monto')
                    ->money('PEN')
                    ->sortable(),

                TextColumn::make('abonado')
                    ->label('Abonado')
                    ->money('PEN')
                    ->getStateUsing(fn (DiasVenta $record): float => static::abonadoDe($record))
                    ->color(fn ($state, DiasVenta $record): string => $state > 0
                        ? ($state + 0.001 >= (float) $record->monto ? 'success' : 'warning')
                        : 'gray'),

                TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn (string $state, DiasVenta $record): string => $state === '1'
                        ? 'Pagado'
                        : (static::abonadoDe($record) > 0 ? 'Parcial' : 'Pendiente'))
                    ->color(fn (string $state, DiasVenta $record): string => $state === '1'
                        ? 'success'
                        : (static::abonadoDe($record) > 0 ? 'warning' : 'danger')),

                TextColumn::make('tipo_pago')
                    ->label('Tipo Pago')
                    ->formatStateUsing(fn (?string $state): string => CajaService::etiquetaMetodoPago($state)),

                TextColumn::make('fecha_pago_real')
                    ->label('Fecha Pago')
                    ->date('d/m/Y')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('usuario.nombres')
                    ->label('Cobrado por')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('venta.vendedor.nombre_completo')
                    ->label('Vendedor')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('tipo_pago')
                    ->label('Tipo Pago')
                    ->options(fn (): array => CajaService::opcionesMetodoPago()),
            ])
            ->actions([
                ActionGroup::make([
                Action::make('cobrar')
                    ->label('Abonar')
                    ->icon('heroicon-o-banknotes')
                    ->color('success')
                    ->visible(fn (DiasVenta $record): bool => $record->estado !== '1'
                        && auth()->user()->can('cobranzas.registrar'))
                    ->modalHeading(fn (DiasVenta $record): string => 'Registrar abono'
                        . ($record->venta?->cliente ? ' — ' . $record->venta->cliente->datos : ''))
                    ->modalWidth('3xl')
                    ->modalContent(fn (DiasVenta $record) => view('filament.cobranzas.abonos-cuota', [
                        'cuota'  => $record,
                        'abonos' => $record->abonos()->with('usuario')->orderBy('id')->get(),
                    ]))
                    ->form(fn (DiasVenta $record): array => [
                        \Filament\Forms\Components\TextInput::make('monto')
                            ->label('Monto del abono (S/)')
                            ->numeric()
                            ->minValue(0.01)
                            ->maxValue(static::saldoCuota($record))
                            ->default(static::saldoCuota($record))
                            ->prefix('S/')
                            ->helperText('Saldo pendiente: S/ ' . number_format(static::saldoCuota($record), 2) . '. Puedes abonar una parte.')
                            ->required(),
                        Select::make('tipo_pago')
                            ->label('Método de pago')
                            ->options(fn (): array => CajaService::opcionesMetodoPago())
                            ->default('EFECTIVO')
                            ->live()
                            ->required(),
                        \Filament\Forms\Components\TextInput::make('referencia')
                            ->label('N° de operación')
                            ->placeholder('Código del comprobante del pago')
                            ->maxLength(60)
                            ->visible(fn (callable $get): bool =>
                                filled($get('tipo_pago')) && $get('tipo_pago') !== 'EFECTIVO')
                            ->required(fn (callable $get): bool =>
                                filled($get('tipo_pago')) && $get('tipo_pago') !== 'EFECTIVO'),
                    ])
                    ->action(function (DiasVenta $record, array $data): void {
                        static::registrarAbono($record, $data);
                    }),

                Action::make('ver_abonos')
                    ->label('Abonos')
                    ->icon('heroicon-o-list-bullet')
                    ->color('gray')
                    ->visible(fn (DiasVenta $record): bool => $record->abonos()->exists())
                    ->modalHeading(fn (DiasVenta $record): string => 'Abonos de la cuota')
                    ->modalWidth('3xl')
                    ->modalContent(fn (DiasVenta $record) => view('filament.cobranzas.abonos-cuota', [
                        'cuota'  => $record,
                        'abonos' => $record->abonos()->with('usuario')->orderBy('id')->get(),
                    ]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Cerrar'),

                Action::make('editar_abono')
                    ->label('Editar abono')
                    ->icon('heroicon-o-pencil-square')
                    ->color('warning')
                    ->visible(fn (DiasVenta $record): bool => $record->abonos()->where('estado', 'ACTIVO')->exists()
                        && auth()->user()->can('cobranzas.editar'))
                    ->modalHeading('Editar abono')
                    ->form(fn (DiasVenta $record): array => [
                        Select::make('id_abono')
                            ->label('Abono a editar')
                            ->options(fn () => $record->abonos()->where('estado', 'ACTIVO')->orderBy('id')->get()
                                ->mapWithKeys(fn (CxcAbono $a) => [
                                    $a->id => "#{$a->id} · {$a->fecha?->format('d/m/Y')} · S/ " . number_format($a->monto, 2)
                                        . ' · ' . CajaService::etiquetaMetodoPago($a->metodo_pago),
                                ])->toArray())
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set): void {
                                if ($abono = CxcAbono::find($state)) {
                                    $set('monto', $abono->monto);
                                    $set('tipo_pago', $abono->metodo_pago);
                                    $set('referencia', $abono->referencia);
                                }
                            })
                            ->required(),
                        \Filament\Forms\Components\TextInput::make('monto')
                            ->label('Monto (S/)')
                            ->numeric()
                            ->minValue(0.01)
                            ->prefix('S/')
                            ->required(),
                        Select::make('tipo_pago')
                            ->label('Método de pago')
                            ->options(fn (): array => CajaService::opcionesMetodoPago())
                            ->live()
                            ->required(),
                        \Filament\Forms\Components\TextInput::make('referencia')
                            ->label('N° de operación')
                            ->maxLength(60)
                            ->visible(fn (callable $get): bool =>
                                filled($get('tipo_pago')) && $get('tipo_pago') !== 'EFECTIVO')
                            ->required(fn (callable $get): bool =>
                                filled($get('tipo_pago')) && $get('tipo_pago') !== 'EFECTIVO'),
                    ])
                    ->action(function (DiasVenta $record, array $data): void {
                        static::editarAbono($record, $data);
                    }),

                Action::make('anular_abono')
                    ->label('Anular abono')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (DiasVenta $record): bool => $record->abonos()->where('estado', 'ACTIVO')->exists()
                        && auth()->user()->can('cobranzas.anular'))
                    ->modalHeading('Anular abono')
                    ->modalDescription('El monto vuelve a quedar pendiente y el ingreso en caja se revierte.')
                    ->form(fn (DiasVenta $record): array => [
                        Select::make('id_abono')
                            ->label('Abono a anular')
                            ->options(fn () => $record->abonos()->where('estado', 'ACTIVO')->orderBy('id')->get()
                                ->mapWithKeys(fn (CxcAbono $a) => [
                                    $a->id => "#{$a->id} · {$a->fecha?->format('d/m/Y')} · S/ " . number_format($a->monto, 2)
                                        . ' · ' . CajaService::etiquetaMetodoPago($a->metodo_pago),
                                ])->toArray())
                            ->required(),
                        \Filament\Forms\Components\Textarea::make('motivo')
                            ->label('Motivo de anulación')
                            ->required()
                            ->maxLength(200),
                    ])
                    ->action(function (DiasVenta $record, array $data): void {
                        static::anularAbono($record, (int) $data['id_abono'], $data['motivo']);
                    }),

                Action::make('ver_venta')
                    ->label('Ver venta')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->url(fn (DiasVenta $record): string =>
                        VentaResource::getUrl('view', ['record' => $record->id_venta])),
                ])->tooltip('Acciones'),
            ])
            ->defaultSort('fecha', 'asc');
    }

    // ── Abonos parciales ─────────────────────────────────────────────────

    public static function abonadoDe(DiasVenta $cuota): float
    {
        return round((float) $cuota->abonos()->where('estado', 'ACTIVO')->sum('monto'), 2);
    }

    public static function saldoCuota(DiasVenta $cuota): float
    {
        return max(0, round((float) $cuota->monto - static::abonadoDe($cuota), 2));
    }

    protected static function documentoDe(DiasVenta $cuota): string
    {
        return $cuota->venta
            ? "{$cuota->venta->serie}-" . str_pad($cuota->venta->numero, 8, '0', STR_PAD_LEFT)
            : "#{$cuota->id_venta}";
    }

    /** La cuota queda Pagada cuando los abonos activos cubren el monto. */
    protected static function sincronizarCuota(DiasVenta $cuota): void
    {
        $ultimo = $cuota->abonos()->where('estado', 'ACTIVO')->orderByDesc('id')->first();
        $pagada = static::saldoCuota($cuota) <= 0.001 && $ultimo !== null;

        $cuota->update([
            'estado'          => $pagada ? '1' : '0',
            'tipo_pago'       => $ultimo?->metodo_pago,
            'referencia'      => $ultimo?->referencia,
            'fecha_pago_real' => $pagada ? now()->toDateString() : null,
            'id_usuario'      => $ultimo?->id_usuario,
        ]);
    }

    protected static function registrarAbono(DiasVenta $record, array $data): void
    {
        $idUsuario = (int) auth()->user()->usuario_id;
        $monto     = round((float) $data['monto'], 2);
        $saldo     = static::saldoCuota($record);

        if ($monto <= 0 || $monto > $saldo + 0.001) {
            Notification::make()->danger()
                ->title('Monto inválido')
                ->body('El abono no puede superar el saldo pendiente (S/ ' . number_format($saldo, 2) . ').')
                ->send();

            return;
        }

        DB::transaction(function () use ($record, $data, $idUsuario, $monto): void {
            $idMovimiento = app(CajaService::class)->registrarCobro(
                idUsuario:   $idUsuario,
                monto:       $monto,
                tipoPago:    $data['tipo_pago'],
                idVenta:     $record->id_venta,
                documento:   static::documentoDe($record),
                idDiasVenta: $record->dias_venta_id,
                categoria:   'COBRO',
                referencia:  $data['referencia'] ?? null,
            );

            CxcAbono::create([
                'id_dias_venta'      => $record->dias_venta_id,
                'id_venta'           => $record->id_venta,
                'fecha'              => now()->toDateString(),
                'monto'              => $monto,
                'metodo_pago'        => $data['tipo_pago'],
                'referencia'         => $data['referencia'] ?? null,
                'id_movimiento_caja' => $idMovimiento,
                'id_usuario'         => $idUsuario,
                'estado'             => 'ACTIVO',
            ]);

            static::sincronizarCuota($record->refresh());
        });

        $saldoNuevo = static::saldoCuota($record->refresh());
        Notification::make()->success()
            ->title('Abono de S/ ' . number_format($monto, 2) . ' registrado')
            ->body($saldoNuevo <= 0 ? 'Cuota cancelada por completo.' : 'Saldo pendiente: S/ ' . number_format($saldoNuevo, 2))
            ->send();
    }

    protected static function editarAbono(DiasVenta $record, array $data): void
    {
        $abono = CxcAbono::where('id', (int) $data['id_abono'])
            ->where('id_dias_venta', $record->dias_venta_id)
            ->where('estado', 'ACTIVO')
            ->first();

        if (! $abono) {
            Notification::make()->danger()->title('Abono no encontrado.')->send();

            return;
        }

        $monto = round((float) $data['monto'], 2);
        $maximo = static::saldoCuota($record) + (float) $abono->monto; // su propio monto se libera

        if ($monto <= 0 || $monto > $maximo + 0.001) {
            Notification::make()->danger()
                ->title('Monto inválido')
                ->body('Con este cambio el abono superaría el monto de la cuota (máximo S/ ' . number_format($maximo, 2) . ').')
                ->send();

            return;
        }

        DB::transaction(function () use ($record, $abono, $data, $monto): void {
            $svc = app(CajaService::class);

            // Reemplazar el movimiento de caja: anular el anterior y crear el corregido
            if ($abono->id_movimiento_caja) {
                $svc->anularMovimiento($abono->id_movimiento_caja);
            }

            $idMovimiento = $svc->registrarCobro(
                idUsuario:   (int) auth()->user()->usuario_id,
                monto:       $monto,
                tipoPago:    $data['tipo_pago'],
                idVenta:     $record->id_venta,
                documento:   static::documentoDe($record) . ' (abono corregido)',
                idDiasVenta: $record->dias_venta_id,
                categoria:   'COBRO',
                referencia:  $data['referencia'] ?? null,
            );

            $abono->update([
                'monto'              => $monto,
                'metodo_pago'        => $data['tipo_pago'],
                'referencia'         => $data['referencia'] ?? null,
                'id_movimiento_caja' => $idMovimiento,
            ]);

            static::sincronizarCuota($record->refresh());
        });

        Notification::make()->success()->title('Abono corregido.')->send();
    }

    protected static function anularAbono(DiasVenta $record, int $idAbono, string $motivo): void
    {
        $abono = CxcAbono::where('id', $idAbono)
            ->where('id_dias_venta', $record->dias_venta_id)
            ->where('estado', 'ACTIVO')
            ->first();

        if (! $abono) {
            Notification::make()->danger()->title('Abono no encontrado.')->send();

            return;
        }

        DB::transaction(function () use ($record, $abono, $motivo): void {
            if ($abono->id_movimiento_caja) {
                app(CajaService::class)->anularMovimiento($abono->id_movimiento_caja);
            }

            $abono->update([
                'estado'           => 'ANULADO',
                'motivo_anulacion' => $motivo,
            ]);

            static::sincronizarCuota($record->refresh());
        });

        Notification::make()->success()
            ->title('Abono anulado')
            ->body('Saldo pendiente: S/ ' . number_format(static::saldoCuota($record->refresh()), 2) . '. El ingreso en caja fue revertido.')
            ->send();
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('venta', fn (Builder $q) => $q
                ->where('id_empresa', (int) session('id_empresa'))
                ->where('sucursal', (int) session('sucursal'))
                ->where('estado', '!=', '0'))
            ->with(['venta.cliente', 'venta.vendedor', 'usuario']);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCuentasPorCobrar::route('/'),
        ];
    }
}
