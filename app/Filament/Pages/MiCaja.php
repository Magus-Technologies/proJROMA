<?php

namespace App\Filament\Pages;

use App\Models\CajaMovimiento;
use App\Services\CajaService;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class MiCaja extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-wallet';
    protected static ?string $navigationLabel = 'Mi Caja';
    protected static string|\UnitEnum|null $navigationGroup = 'Caja';
    protected static ?int $navigationSort = 5;
    protected string $view = 'filament.pages.mi-caja';

    public ?object $caja = null;

    public function mount(): void
    {
        $this->caja = $this->resolverCaja();
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
                TextColumn::make('fecha')
                    ->label('Fecha')
                    ->date('d/m/Y')
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

                TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'CONFIRMADO' => 'success',
                        'ANULADO'    => 'danger',
                        default      => 'gray',
                    }),
            ])
            ->defaultSort('id', 'desc');
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
                ->visible(fn (): bool => $esHija && ! DB::table('caja_aperturas')
                    ->where('id_caja', $cajaId)
                    ->where('estado', 'ABIERTA')
                    ->exists())
                ->form([
                    DatePicker::make('fecha')
                        ->label('Fecha')
                        ->default(now())
                        ->required(),
                    Repeater::make('detalles')
                        ->label('Desglose de efectivo')
                        ->schema([
                            Select::make('tipo')
                                ->label('Tipo')
                                ->options(['BILLETE' => 'Billete', 'MONEDA' => 'Moneda'])
                                ->default('BILLETE')
                                ->required(),
                            TextInput::make('denominacion')
                                ->label('Denominación (S/)')
                                ->numeric()
                                ->minValue(0)
                                ->required(),
                            TextInput::make('cantidad')
                                ->label('Cantidad')
                                ->numeric()
                                ->integer()
                                ->minValue(0)
                                ->required(),
                        ])
                        ->columns(3)
                        ->minItems(1)
                        ->defaultItems(1),
                    Textarea::make('observaciones')
                        ->label('Observaciones')
                        ->maxLength(500),
                ])
                ->action(function (array $data) use ($cajaId): void {
                    $montoTotal = collect($data['detalles'])
                        ->sum(fn (array $d): float => ((float) $d['denominacion']) * ((int) $d['cantidad']));

                    DB::transaction(function () use ($data, $cajaId, $montoTotal): void {
                        $idApertura = DB::table('caja_aperturas')->insertGetId([
                            'id_caja'             => $cajaId,
                            'fecha'               => $data['fecha'],
                            'monto_total'         => $montoTotal,
                            'estado'              => 'ABIERTA',
                            'id_usuario_apertura' => (int) auth()->user()->usuario_id,
                            'observaciones'       => $data['observaciones'] ?? null,
                            'created_at'          => now(),
                            'updated_at'          => now(),
                        ]);

                        $detalles = collect($data['detalles'])
                            ->filter(fn (array $d): bool => (int) $d['cantidad'] > 0)
                            ->map(fn (array $d): array => [
                                'id_apertura'  => $idApertura,
                                'denominacion' => $d['denominacion'],
                                'tipo'         => $d['tipo'],
                                'cantidad'     => $d['cantidad'],
                                'subtotal'     => ((float) $d['denominacion']) * ((int) $d['cantidad']),
                            ])
                            ->values()
                            ->toArray();

                        if ($detalles !== []) {
                            DB::table('caja_apertura_detalles')->insert($detalles);
                        }
                    });

                    Notification::make()->success()
                        ->title('Caja aperturada')
                        ->body('Monto de apertura: S/ ' . number_format($montoTotal, 2))
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
                ->form([
                    TextInput::make('saldo_declarado')
                        ->label('Saldo declarado (S/)')
                        ->numeric()
                        ->minValue(0)
                        ->required()
                        ->helperText('Saldo según sistema: S/ ' . number_format((float) ($this->caja->saldo_actual ?? 0), 2)),
                ])
                ->action(function (array $data) use ($cajaId): void {
                    try {
                        app(CajaService::class)->cerrarCaja(
                            $cajaId,
                            (float) $data['saldo_declarado'],
                            [],
                            (int) auth()->user()->usuario_id
                        );
                        DB::table('caja_aperturas')
                            ->where('id_caja', $cajaId)
                            ->where('estado', 'ABIERTA')
                            ->update(['estado' => 'CERRADA', 'updated_at' => now()]);

                        Notification::make()->success()
                            ->title('Cierre registrado')
                            ->body('Queda pendiente de aprobación.')
                            ->send();
                        $this->caja = $this->resolverCaja();
                    } catch (\Throwable $e) {
                        Notification::make()->danger()->title('Error al cerrar caja')->body($e->getMessage())->send();
                    }
                }),
        ];
    }
}
