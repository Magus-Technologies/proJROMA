<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CierresCajaResource\Pages;
use App\Models\Caja;
use App\Models\CierreCaja;
use App\Services\CajaService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CierresCajaResource extends Resource
{
    use \App\Filament\Concerns\VerificaPermisoDeAcceso;

    public const PERMISO_ACCESO = 'caja.gestionar';

    protected static ?string $model = CierreCaja::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationLabel = 'Cierres y Cuadre';
    protected static string|\UnitEnum|null $navigationGroup = 'Caja';
    protected static ?int $navigationSort = 3;
    protected static ?string $label = 'Cierre';
    protected static ?string $pluralLabel = 'Cierres y Cuadre';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('fecha')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('caja.nombre')
                    ->label('Caja')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('saldo_declarado')
                    ->label('Declarado')
                    ->money('PEN')
                    ->sortable(),

                TextColumn::make('saldo_sistema')
                    ->label('Esperado (turno)')
                    ->money('PEN')
                    ->sortable(),

                TextColumn::make('diferencia')
                    ->label('Diferencia')
                    ->money('PEN')
                    ->getStateUsing(fn (CierreCaja $record): float =>
                        $record->saldo_declarado - $record->saldo_sistema
                    )
                    ->color(fn (float $state): string => $state == 0 ? 'success' : 'danger'),

                TextColumn::make('usuarioCierra.nombres')
                    ->label('Cerró')
                    ->toggleable(),

                TextColumn::make('usuarioAprueba.nombres')
                    ->label('Aprobó')
                    ->placeholder('—')
                    ->toggleable(),

                TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'APROBADO'  => 'success',
                        'PENDIENTE' => 'warning',
                        'RECHAZADO' => 'danger',
                        default     => 'gray',
                    }),

                TextColumn::make('deuda_info')
                    ->label('Deuda trabajador')
                    ->badge()
                    ->getStateUsing(fn (CierreCaja $record): ?string => $record->deuda
                        ? 'S/ ' . number_format($record->deuda->monto, 2) . ' · ' . ucfirst(strtolower($record->deuda->estado))
                        : null)
                    ->placeholder('—')
                    ->color(fn (CierreCaja $record): string => $record->deuda?->estado === 'PENDIENTE' ? 'danger' : 'success'),

                TextColumn::make('observaciones')
                    ->label('Observaciones')
                    ->wrap()
                    ->limit(40)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('id_caja')
                    ->label('Caja')
                    ->options(fn () => Caja::where('id_empresa', (int) session('id_empresa'))
                        ->pluck('nombre', 'id')
                        ->toArray()),

                SelectFilter::make('estado')
                    ->label('Estado')
                    ->options([
                        'PENDIENTE' => 'Pendiente',
                        'APROBADO'  => 'Aprobado',
                        'RECHAZADO' => 'Rechazado',
                    ]),

                Filter::make('fecha')
                    ->form([
                        DatePicker::make('fecha')->label('Fecha'),
                    ])
                    ->query(fn (Builder $query, array $data): Builder =>
                        $query->when($data['fecha'], fn (Builder $q) =>
                            $q->whereDate('cierre_caja.fecha', $data['fecha'])
                        )
                    ),
            ])
            ->actions([
                Action::make('ver_conteo')
                    ->label('Ver conteo')
                    ->iconButton()
                    ->tooltip('Ver conteo de billetes y monedas')
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->modalHeading(fn (CierreCaja $record): string => 'Conteo del cierre — ' . ($record->caja?->nombre ?? '') . ' (' . $record->fecha?->format('d/m/Y') . ')')
                    ->modalWidth('lg')
                    ->modalContent(fn (CierreCaja $record) => view('filament.caja.cierre-detalle', [
                        'cierre'   => $record,
                        'detalles' => collect($record->desglose_instrumentos ?? []),
                    ]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Cerrar'),

                Action::make('ver_movimientos')
                    ->label('Movimientos del turno')
                    ->iconButton()
                    ->tooltip('Ver todos los movimientos desde la apertura hasta el cierre')
                    ->icon('heroicon-o-list-bullet')
                    ->color('info')
                    ->modalHeading(fn (CierreCaja $record): string => 'Movimientos del turno — ' . ($record->caja?->nombre ?? '') . ' (' . $record->fecha?->format('d/m/Y') . ')')
                    ->modalWidth('4xl')
                    ->modalContent(function (CierreCaja $record) {
                        $apertura = $record->id_apertura
                            ? \Illuminate\Support\Facades\DB::table('caja_aperturas')->where('id', $record->id_apertura)->first()
                            : \Illuminate\Support\Facades\DB::table('caja_aperturas')
                                ->where('id_caja', $record->id_caja)
                                ->where('created_at', '<=', $record->created_at ?? now())
                                ->orderByDesc('id')
                                ->first();

                        $movimientos = \Illuminate\Support\Facades\DB::table('caja_movimientos')
                            ->where('id_caja', $record->id_caja)
                            ->when($apertura, fn ($q) => $q->where('created_at', '>=', $apertura->created_at))
                            ->when($record->created_at, fn ($q) => $q->where('created_at', '<=', $record->created_at))
                            ->orderBy('created_at')
                            ->get();

                        $confirmados = $movimientos->where('estado', 'CONFIRMADO');
                        $fondo    = (float) ($apertura->monto_total ?? 0);
                        $ingresos = (float) $confirmados->where('tipo', 'INGRESO')->where('categoria', '!=', 'APERTURA')->sum('monto');
                        $egresos  = (float) $confirmados->where('tipo', 'EGRESO')->sum('monto');

                        return view('filament.caja.cierre-movimientos', [
                            'cierre'      => $record,
                            'apertura'    => $apertura,
                            'movimientos' => $movimientos,
                            'fondo'       => $fondo,
                            'ingresos'    => $ingresos,
                            'egresos'     => $egresos,
                            'esperado'    => $fondo + $ingresos - $egresos,
                        ]);
                    })
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Cerrar'),

                Action::make('aprobar')
                    ->label('Aprobar')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalDescription(function (CierreCaja $record): string {
                        $dif = round($record->saldo_declarado - $record->saldo_sistema, 2);
                        if ($dif < -0.001) {
                            return 'Hay un FALTANTE de S/ ' . number_format(abs($dif), 2) . ': al aprobar quedará registrado como deuda del trabajador para descontar.';
                        }
                        if ($dif > 0.001) {
                            return 'Hay un sobrante de S/ ' . number_format($dif, 2) . ' que se ajustará al saldo.';
                        }

                        return 'El cierre cuadra exacto.';
                    })
                    ->visible(fn (CierreCaja $record): bool => $record->estado === 'PENDIENTE')
                    ->action(function (CierreCaja $record): void {
                        app(CajaService::class)->aprobarCierre(
                            $record->id,
                            (int) auth()->user()->usuario_id,
                            'APROBADO'
                        );

                        $dif = round($record->saldo_declarado - $record->saldo_sistema, 2);
                        Notification::make()->success()
                            ->title('Cierre aprobado')
                            ->body($dif < -0.001
                                ? 'Faltante de S/ ' . number_format(abs($dif), 2) . ' registrado como deuda de ' . ($record->usuarioCierra?->nombres ?? 'el trabajador') . '.'
                                : 'Saldo ajustado y consolidado.')
                            ->send();
                    }),

                Action::make('rechazar')
                    ->label('Rechazar')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (CierreCaja $record): bool => $record->estado === 'PENDIENTE')
                    ->form([
                        Textarea::make('observaciones')
                            ->label('Motivo del rechazo')
                            ->maxLength(500),
                    ])
                    ->action(function (CierreCaja $record, array $data): void {
                        app(CajaService::class)->aprobarCierre(
                            $record->id,
                            (int) auth()->user()->usuario_id,
                            'RECHAZADO',
                            $data['observaciones'] ?? null
                        );
                        Notification::make()->success()
                            ->title('Cierre rechazado')
                            ->body('La caja quedó reabierta para que el trabajador corrija y vuelva a cerrar.')
                            ->send();
                    }),

                Action::make('cancelar_deuda')
                    ->label('Cancelar deuda')
                    ->icon('heroicon-o-banknotes')
                    ->color('warning')
                    ->visible(fn (CierreCaja $record): bool => $record->deuda?->estado === 'PENDIENTE')
                    ->modalHeading(fn (CierreCaja $record): string => 'Cancelar deuda de ' . ($record->usuarioCierra?->nombres ?? 'el trabajador') . ' — S/ ' . number_format($record->deuda?->monto ?? 0, 2))
                    ->form([
                        \Filament\Forms\Components\Radio::make('modo')
                            ->label('¿Cómo se cancela?')
                            ->options([
                                'EFECTIVO'  => 'El trabajador devuelve el dinero (ingresa a la caja principal)',
                                'DESCUENTO' => 'Se descuenta de su sueldo / planilla (no ingresa dinero a caja)',
                            ])
                            ->default('EFECTIVO')
                            ->required(),
                        Textarea::make('observaciones')
                            ->label('Observaciones')
                            ->placeholder('Ej. devolvió en efectivo el 27/07, descuento en planilla de agosto...')
                            ->maxLength(255),
                    ])
                    ->action(function (CierreCaja $record, array $data): void {
                        $deuda = $record->deuda;

                        \Illuminate\Support\Facades\DB::transaction(function () use ($record, $deuda, $data): void {
                            if ($data['modo'] === 'EFECTIVO') {
                                // El dinero devuelto entra a la caja principal
                                // (o a la propia caja si no tiene padre).
                                $idCajaDestino = $record->caja?->id_caja_padre ?: $record->id_caja;

                                app(CajaService::class)->registrarMovimiento([
                                    'id_caja'          => $idCajaDestino,
                                    'tipo'             => 'INGRESO',
                                    'categoria'        => 'REPOSICION',
                                    'descripcion'      => 'Pago de deuda por faltante en cierre #' . $record->id . ' — ' . ($record->usuarioCierra?->nombres ?? 'trabajador'),
                                    'monto'            => (float) $deuda->monto,
                                    'instrumento_tipo' => 'EFECTIVO',
                                    'origen_tipo'      => 'DEUDA_CIERRE',
                                    'origen_id'        => $deuda->id,
                                    'id_usuario'       => (int) auth()->user()->usuario_id,
                                ]);
                            }

                            $deuda->update([
                                'estado'              => $data['modo'] === 'EFECTIVO' ? 'PAGADO' : 'DESCONTADO',
                                'observaciones'       => trim(($deuda->observaciones ? $deuda->observaciones . ' | ' : '') . ($data['observaciones'] ?? '')) ?: $deuda->observaciones,
                                'id_usuario_registra' => (int) auth()->user()->usuario_id,
                            ]);
                        });

                        Notification::make()->success()
                            ->title('Deuda cancelada')
                            ->body($data['modo'] === 'EFECTIVO'
                                ? 'S/ ' . number_format($deuda->monto, 2) . ' ingresó a la caja principal como reposición.'
                                : 'Marcada para descuento en planilla — no ingresó dinero a caja.')
                            ->send();
                    }),
            ])
            ->defaultSort('fecha', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['caja', 'usuarioCierra', 'usuarioAprueba', 'deuda'])
            ->whereHas('caja', fn (Builder $q) =>
                $q->where('id_empresa', (int) session('id_empresa'))
            );
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCierresCaja::route('/'),
        ];
    }
}
