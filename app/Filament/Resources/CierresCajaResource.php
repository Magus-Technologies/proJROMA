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
                    ->label('Saldo Declarado')
                    ->money('PEN')
                    ->sortable(),

                TextColumn::make('saldo_sistema')
                    ->label('Saldo Sistema')
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

                Action::make('marcar_descontada')
                    ->label('Deuda descontada')
                    ->iconButton()
                    ->tooltip('Marcar deuda como descontada al trabajador')
                    ->icon('heroicon-o-banknotes')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalDescription(fn (CierreCaja $record): string => 'Confirma que ya se descontó S/ ' . number_format($record->deuda?->monto ?? 0, 2) . ' a ' . ($record->usuarioCierra?->nombres ?? 'el trabajador') . '.')
                    ->visible(fn (CierreCaja $record): bool => $record->deuda?->estado === 'PENDIENTE')
                    ->action(function (CierreCaja $record): void {
                        $record->deuda->update([
                            'estado'              => 'DESCONTADO',
                            'id_usuario_registra' => (int) auth()->user()->usuario_id,
                        ]);
                        Notification::make()->success()->title('Deuda marcada como descontada.')->send();
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
