<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransferenciaFondoResource\Pages;
use App\Models\Caja;
use App\Models\TransferenciaFondo;
use App\Services\CajaService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TransferenciaFondoResource extends Resource
{
    use \App\Filament\Concerns\VerificaPermisoDeAcceso;

    public const PERMISO_ACCESO = 'caja.gestionar';

    protected static ?string $model = TransferenciaFondo::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-arrow-right-circle';
    protected static ?string $navigationLabel = 'Asignaciones de Fondo';
    protected static string|\UnitEnum|null $navigationGroup = 'Caja';
    protected static ?int $navigationSort = 4;
    protected static ?string $label = 'Asignación de Fondo';
    protected static ?string $pluralLabel = 'Asignaciones de Fondo';
    protected static ?string $slug = 'asignaciones-fondo';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                TextColumn::make('origen.nombre')
                    ->label('Desde (bóveda)'),

                TextColumn::make('destino.nombre')
                    ->label('Caja destino')
                    ->searchable(),

                TextColumn::make('cajero.nombres')
                    ->label('Cajero responsable'),

                TextColumn::make('monto')
                    ->label('Asignado')
                    ->money('PEN')
                    ->sortable(),

                TextColumn::make('monto_contado')
                    ->label('Contado')
                    ->money('PEN')
                    ->placeholder('—'),

                TextColumn::make('diferencia')
                    ->label('Diferencia')
                    ->getStateUsing(fn (TransferenciaFondo $record): ?float => $record->diferencia)
                    ->money('PEN')
                    ->placeholder('—')
                    ->color(fn ($state): ?string => $state === null || abs($state) < 0.01 ? null : 'danger'),

                TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => TransferenciaFondo::estados()[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        'ASIGNADA' => 'warning',
                        'APLICADA' => 'success',
                        'RECHAZADA', 'ANULADA' => 'gray',
                        default => 'gray',
                    }),

                TextColumn::make('discrepancia_estado')
                    ->label('Discrepancia')
                    ->badge()
                    ->placeholder('—')
                    ->formatStateUsing(fn (?string $state, TransferenciaFondo $record): ?string => match ($state) {
                        'PENDIENTE' => 'Pendiente',
                        'RESUELTA' => $record->discrepancia_resolucion === 'PERDIDA' ? 'Resuelta (pérdida)' : 'Resuelta (ajuste)',
                        default => null,
                    })
                    ->color(fn (?string $state): string => $state === 'PENDIENTE' ? 'danger' : 'success'),

                TextColumn::make('asignadoPor.nombres')
                    ->label('Asignó')
                    ->toggleable(),

                TextColumn::make('observaciones')
                    ->label('Observaciones')
                    ->wrap()
                    ->limit(40)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('estado')
                    ->label('Estado')
                    ->options(TransferenciaFondo::estados()),
            ])
            ->headerActions([
                Action::make('asignar')
                    ->label('Asignar Fondo')
                    ->icon('heroicon-o-plus-circle')
                    ->color('primary')
                    ->modalHeading('Asignar fondo a una caja')
                    ->modalDescription('El monto saldrá de la bóveda en este momento (el sobre ya se preparó). El cajero deberá contar el efectivo y aperturar su caja para aplicarlo.')
                    ->form([
                        Select::make('id_caja_origen')
                            ->label('Desde (bóveda / caja principal)')
                            ->options(fn () => Caja::where('id_empresa', (int) session('id_empresa'))
                                ->whereNull('id_caja_padre')
                                ->pluck('nombre', 'id'))
                            ->required(),
                        Select::make('id_caja_destino')
                            ->label('Caja destino (hija)')
                            ->options(fn () => Caja::with('responsable')
                                ->where('id_empresa', (int) session('id_empresa'))
                                ->whereNotNull('id_caja_padre')
                                ->get()
                                ->mapWithKeys(fn (Caja $c) => [
                                    $c->id => $c->nombre . ' — responsable: ' . ($c->responsable?->nombres ?? '⚠ sin asignar'),
                                ]))
                            ->helperText('El fondo quedará asignado al responsable de la caja (definido en Gestión de Cajas).')
                            ->searchable()
                            ->required(),
                        TextInput::make('monto')
                            ->label('Monto a asignar')
                            ->numeric()
                            ->minValue(0.01)
                            ->prefix('S/')
                            ->required(),
                        Textarea::make('observaciones')
                            ->label('Observaciones')
                            ->maxLength(255),
                    ])
                    ->action(function (array $data, Action $action): void {
                        try {
                            $id = app(CajaService::class)->asignarFondo(
                                (int) $data['id_caja_origen'],
                                (int) $data['id_caja_destino'],
                                (float) $data['monto'],
                                (int) auth()->user()->usuario_id,
                                $data['observaciones'] ?? null,
                            );
                        } catch (\RuntimeException $e) {
                            Notification::make()->danger()->title($e->getMessage())->send();

                            $action->halt();

                            return;
                        }

                        Notification::make()->success()
                            ->title('Fondo asignado (#' . $id . ')')
                            ->body('S/ ' . number_format((float) $data['monto'], 2) . ' salieron de la bóveda. El cajero debe contar y aperturar su caja para aplicarlo.')
                            ->send();
                    }),
            ])
            ->actions([
                Action::make('anular')
                    ->label('Anular')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (TransferenciaFondo $record): bool => $record->estado === 'ASIGNADA')
                    ->requiresConfirmation()
                    ->modalHeading('Anular asignación')
                    ->modalDescription(fn (TransferenciaFondo $record): string => 'S/ ' . number_format($record->monto, 2) . ' regresarán a la bóveda "' . ($record->origen?->nombre ?? '') . '".')
                    ->form([
                        Textarea::make('motivo')
                            ->label('Motivo')
                            ->maxLength(255),
                    ])
                    ->action(function (TransferenciaFondo $record, array $data, Action $action): void {
                        try {
                            app(CajaService::class)->revertirAsignacion(
                                $record->id,
                                (int) auth()->user()->usuario_id,
                                'ANULADA',
                                $data['motivo'] ?? null,
                            );
                        } catch (\RuntimeException $e) {
                            Notification::make()->danger()->title($e->getMessage())->send();

                            $action->halt();

                            return;
                        }

                        Notification::make()->success()
                            ->title('Asignación anulada')
                            ->body('El efectivo regresó a la bóveda.')
                            ->send();
                    }),

                Action::make('reasignar')
                    ->label('Reasignar')
                    ->icon('heroicon-o-arrow-path')
                    ->color('info')
                    ->visible(fn (TransferenciaFondo $record): bool => $record->estado === 'ASIGNADA')
                    ->modalHeading('Reasignar fondo')
                    ->modalDescription(fn (TransferenciaFondo $record): string => 'La asignación actual (S/ ' . number_format($record->monto, 2) . ' → ' . ($record->destino?->nombre ?? '') . ') se anulará y su efectivo regresará a la bóveda; en el mismo paso se creará la nueva asignación.')
                    ->fillForm(fn (TransferenciaFondo $record): array => [
                        'id_caja_origen' => $record->id_caja_origen,
                        'id_caja_destino' => $record->id_caja_destino,
                        'monto' => $record->monto,
                        'observaciones' => $record->observaciones,
                    ])
                    ->form([
                        Select::make('id_caja_origen')
                            ->label('Desde (bóveda / caja principal)')
                            ->options(fn () => Caja::where('id_empresa', (int) session('id_empresa'))
                                ->whereNull('id_caja_padre')
                                ->pluck('nombre', 'id'))
                            ->required(),
                        Select::make('id_caja_destino')
                            ->label('Caja destino (hija)')
                            ->options(fn () => Caja::with('responsable')
                                ->where('id_empresa', (int) session('id_empresa'))
                                ->whereNotNull('id_caja_padre')
                                ->get()
                                ->mapWithKeys(fn (Caja $c) => [
                                    $c->id => $c->nombre . ' — responsable: ' . ($c->responsable?->nombres ?? '⚠ sin asignar'),
                                ]))
                            ->searchable()
                            ->required(),
                        TextInput::make('monto')
                            ->label('Nuevo monto')
                            ->numeric()
                            ->minValue(0.01)
                            ->prefix('S/')
                            ->required(),
                        Textarea::make('observaciones')
                            ->label('Observaciones')
                            ->maxLength(255),
                    ])
                    ->action(function (TransferenciaFondo $record, array $data, Action $action): void {
                        try {
                            $nuevoId = \Illuminate\Support\Facades\DB::transaction(function () use ($record, $data) {
                                $svc = app(CajaService::class);
                                $svc->revertirAsignacion(
                                    $record->id,
                                    (int) auth()->user()->usuario_id,
                                    'ANULADA',
                                    'Reasignada',
                                );

                                return $svc->asignarFondo(
                                    (int) $data['id_caja_origen'],
                                    (int) $data['id_caja_destino'],
                                    (float) $data['monto'],
                                    (int) auth()->user()->usuario_id,
                                    $data['observaciones'] ?? null,
                                );
                            });
                        } catch (\RuntimeException $e) {
                            Notification::make()->danger()->title($e->getMessage())->send();

                            $action->halt();

                            return;
                        }

                        Notification::make()->success()
                            ->title('Fondo reasignado (#' . $nuevoId . ')')
                            ->body('La asignación anterior se anuló y el nuevo fondo quedó en tránsito.')
                            ->send();
                    }),

                Action::make('rechazar')
                    ->label('Rechazar')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('gray')
                    ->visible(fn (TransferenciaFondo $record): bool => $record->estado === 'ASIGNADA')
                    ->requiresConfirmation()
                    ->modalHeading('Rechazar asignación')
                    ->modalDescription(fn (TransferenciaFondo $record): string => 'El cajero no acepta el fondo: S/ ' . number_format($record->monto, 2) . ' regresarán a la bóveda "' . ($record->origen?->nombre ?? '') . '".')
                    ->form([
                        Textarea::make('motivo')
                            ->label('Motivo del rechazo')
                            ->required()
                            ->maxLength(255),
                    ])
                    ->action(function (TransferenciaFondo $record, array $data, Action $action): void {
                        try {
                            app(CajaService::class)->revertirAsignacion(
                                $record->id,
                                (int) auth()->user()->usuario_id,
                                'RECHAZADA',
                                $data['motivo'] ?? null,
                            );
                        } catch (\RuntimeException $e) {
                            Notification::make()->danger()->title($e->getMessage())->send();

                            $action->halt();

                            return;
                        }

                        Notification::make()->success()
                            ->title('Asignación rechazada')
                            ->body('El efectivo regresó a la bóveda.')
                            ->send();
                    }),

                Action::make('resolver_discrepancia')
                    ->label('Resolver discrepancia')
                    ->icon('heroicon-o-scale')
                    ->color('warning')
                    ->visible(fn (TransferenciaFondo $record): bool => $record->discrepancia_estado === 'PENDIENTE')
                    ->modalHeading(fn (TransferenciaFondo $record): string => 'Resolver discrepancia — ' .
                        ($record->diferencia < 0
                            ? 'faltante de S/ ' . number_format(abs($record->diferencia), 2)
                            : 'sobrante de S/ ' . number_format($record->diferencia, 2)))
                    ->modalDescription(fn (TransferenciaFondo $record): string => 'Se asignaron S/ ' . number_format($record->monto, 2) . ' pero el cajero contó S/ ' . number_format((float) $record->monto_contado, 2) . '.')
                    ->form([
                        Radio::make('resolucion')
                            ->label('¿Qué pasó con la diferencia?')
                            ->options([
                                'AJUSTE_BOVEDA' => 'El sobre se preparó mal: ajustar la bóveda (el dinero sigue/salió de ahí)',
                                'PERDIDA' => 'Se perdió en el traslado: registrar como pérdida (sin ajuste de cajas)',
                            ])
                            ->default('AJUSTE_BOVEDA')
                            ->required(),
                        Textarea::make('observaciones')
                            ->label('Observaciones')
                            ->maxLength(255),
                    ])
                    ->action(function (TransferenciaFondo $record, array $data, Action $action): void {
                        try {
                            app(CajaService::class)->resolverDiscrepancia(
                                $record->id,
                                $data['resolucion'],
                                (int) auth()->user()->usuario_id,
                                $data['observaciones'] ?? null,
                            );
                        } catch (\RuntimeException $e) {
                            Notification::make()->danger()->title($e->getMessage())->send();

                            $action->halt();

                            return;
                        }

                        Notification::make()->success()->title('Discrepancia resuelta')->send();
                    }),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('destino', fn ($q) => $q->where('id_empresa', (int) session('id_empresa')))
            ->with(['origen', 'destino', 'cajero', 'asignadoPor']);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransferenciasFondo::route('/'),
        ];
    }
}
