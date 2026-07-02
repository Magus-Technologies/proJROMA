<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CuentaPorPagarResource\Pages;
use App\Models\Compra;
use App\Models\DiasCompra;
use App\Services\CajaService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class CuentaPorPagarResource extends Resource
{
    protected static ?string $model = Compra::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-building-library';
    protected static ?string $navigationLabel = 'Cuentas por Pagar';
    protected static string|\UnitEnum|null $navigationGroup = 'Pagos';
    protected static ?int $navigationSort = 1;
    protected static ?string $label = 'Cuenta por Pagar';
    protected static ?string $pluralLabel = 'Cuentas por Pagar';
    protected static ?string $slug = 'cuentas-por-pagar';

    protected static function saldoPendiente(Compra $record): float
    {
        $pagado = (float) $record->pagos()->where('estado', '1')->sum('monto');

        return max(0, (float) $record->total - $pagado);
    }

    protected static function cajaDelUsuario(): ?object
    {
        return DB::table('cajas')
            ->where('id_empresa', (int) session('id_empresa'))
            ->where('id_usuario_responsable', (int) auth()->user()->usuario_id)
            ->where('estado', 'ACTIVA')
            ->first();
    }

    public static function getNavigationBadge(): ?string
    {
        $pendientes = static::getEloquentQuery()
            ->whereRaw('compras.total > (SELECT COALESCE(SUM(monto),0) FROM dias_compras WHERE dias_compras.id_compra = compras.id_compra AND dias_compras.estado = \'1\')')
            ->count();

        return $pendientes > 0 ? (string) $pendientes : null;
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
                TextColumn::make('tipoDocSunat.nombre')
                    ->label('Tipo Doc.')
                    ->placeholder('—')
                    ->toggleable(),

                TextColumn::make('documento')
                    ->label('Documento')
                    ->getStateUsing(fn (Compra $record): string =>
                        trim("{$record->serie}-{$record->numero}", '-') ?: '—')
                    ->searchable(query: fn (Builder $query, string $search): Builder =>
                        $query->where(fn (Builder $q) => $q
                            ->where('compras.serie', 'like', "%{$search}%")
                            ->orWhere('compras.numero', 'like', "%{$search}%"))),

                TextColumn::make('proveedor.razon_social')
                    ->label('Proveedor')
                    ->getStateUsing(fn (Compra $record): string =>
                        $record->proveedor?->razon_social
                        ?? $record->proveedor?->nombre_comercial
                        ?? '—')
                    ->searchable(query: fn (Builder $query, string $search): Builder =>
                        $query->whereHas('proveedor', fn (Builder $q) => $q
                            ->where('razon_social', 'like', "%{$search}%")
                            ->orWhere('nombre_comercial', 'like', "%{$search}%")))
                    ->wrap()
                    ->limit(40),

                TextColumn::make('fecha_emision')
                    ->label('F. Emisión')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('fecha_vencimiento')
                    ->label('F. Venc.')
                    ->date('d/m/Y')
                    ->sortable()
                    ->placeholder('—')
                    ->color(fn (Compra $record): ?string =>
                        $record->fecha_vencimiento
                        && $record->fecha_vencimiento < now()->toDateString()
                        && static::saldoPendiente($record) > 0 ? 'danger' : null),

                TextColumn::make('total')
                    ->label('Total')
                    ->money('PEN')
                    ->sortable(),

                TextColumn::make('total_pagado')
                    ->label('Pagado')
                    ->money('PEN')
                    ->getStateUsing(fn (Compra $record): float =>
                        (float) $record->pagos()->where('estado', '1')->sum('monto')),

                TextColumn::make('saldo_pendiente')
                    ->label('Saldo')
                    ->money('PEN')
                    ->weight('bold')
                    ->getStateUsing(fn (Compra $record): float => static::saldoPendiente($record)),

                TextColumn::make('estado_pago')
                    ->label('Estado')
                    ->badge()
                    ->getStateUsing(function (Compra $record): string {
                        $saldo = static::saldoPendiente($record);
                        if ($saldo <= 0) {
                            return 'Pagado';
                        }
                        $pagado = (float) $record->total - $saldo;

                        return $pagado > 0 ? 'Parcial' : 'Pendiente';
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'Pagado'    => 'success',
                        'Parcial'   => 'warning',
                        'Pendiente' => 'danger',
                        default     => 'gray',
                    }),
            ])
            ->actions([
                Action::make('registrar_pago')
                    ->label('Pagar')
                    ->icon('heroicon-o-banknotes')
                    ->color('success')
                    ->visible(fn (Compra $record): bool => static::saldoPendiente($record) > 0)
                    ->modalHeading('Registrar Pago')
                    ->modalDescription(fn (Compra $record): string =>
                        'Saldo pendiente: S/ ' . number_format(static::saldoPendiente($record), 2))
                    ->form(fn (Compra $record): array => [
                        TextInput::make('monto')
                            ->label('Monto (S/)')
                            ->numeric()
                            ->minValue(0.01)
                            ->maxValue(static::saldoPendiente($record))
                            ->default(static::saldoPendiente($record))
                            ->prefix('S/')
                            ->required(),
                        DatePicker::make('fecha')
                            ->label('Fecha')
                            ->default(now())
                            ->required(),
                        Select::make('instrumento_tipo')
                            ->label('Método de Pago')
                            ->options([
                                'EFECTIVO'          => 'Efectivo',
                                'TRANSFERENCIA'     => 'Transferencia',
                                'BILLETERA_DIGITAL' => 'Billetera Digital',
                            ]),
                    ])
                    ->action(function (Compra $record, array $data): void {
                        $saldo = static::saldoPendiente($record);

                        if ((float) $data['monto'] > $saldo) {
                            Notification::make()->danger()
                                ->title('Monto inválido')
                                ->body('El monto excede el saldo pendiente (S/ ' . number_format($saldo, 2) . ').')
                                ->send();

                            return;
                        }

                        DB::transaction(function () use ($record, $data): void {
                            $idCaja = null;
                            $caja = static::cajaDelUsuario();

                            if ($caja) {
                                $idCaja = $caja->id;
                                $doc = trim("{$record->serie}-{$record->numero}", '-');

                                app(CajaService::class)->registrarMovimiento([
                                    'id_caja'          => $caja->id,
                                    'tipo'             => 'EGRESO',
                                    'categoria'        => 'COMPRA',
                                    'descripcion'      => 'Pago compra ' . ($doc ?: "#{$record->id_compra}"),
                                    'monto'            => (float) $data['monto'],
                                    'fecha'            => $data['fecha'],
                                    'instrumento_tipo' => $data['instrumento_tipo'] ?? null,
                                    'id_usuario'       => (int) auth()->user()->usuario_id,
                                ]);
                            }

                            DiasCompra::create([
                                'id_compra'        => $record->id_compra,
                                'monto'            => $data['monto'],
                                'fecha'            => $data['fecha'],
                                'estado'           => '1',
                                'id_caja'          => $idCaja,
                                'instrumento_tipo' => $data['instrumento_tipo'] ?? null,
                            ]);
                        });

                        Notification::make()->success()->title('Pago registrado')->send();
                    }),

                Action::make('historial')
                    ->label('Historial')
                    ->icon('heroicon-o-clock')
                    ->color('info')
                    ->modalHeading(fn (Compra $record): string =>
                        'Historial de pagos — ' . trim("{$record->serie}-{$record->numero}", '-'))
                    ->modalContent(fn (Compra $record) => view('filament.modals.pagos-historial', [
                        'pagos'   => $record->pagos()->orderByDesc('fecha')->orderByDesc('dias_compra_id')->get(),
                        'total'   => (float) $record->total,
                        'pagado'  => (float) $record->pagos()->where('estado', '1')->sum('monto'),
                    ]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Cerrar'),

                Action::make('anular_pago')
                    ->label('Anular pago')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Compra $record): bool =>
                        $record->pagos()->where('estado', '1')->exists())
                    ->modalHeading('Anular un pago')
                    ->form(fn (Compra $record): array => [
                        Select::make('dias_compra_id')
                            ->label('Pago a anular')
                            ->options($record->pagos()->where('estado', '1')->get()
                                ->mapWithKeys(fn (DiasCompra $p) => [
                                    $p->dias_compra_id => $p->fecha?->format('d/m/Y') . ' — S/ ' . number_format($p->monto, 2),
                                ])
                                ->toArray())
                            ->required(),
                    ])
                    ->action(function (Compra $record, array $data): void {
                        DB::transaction(function () use ($record, $data): void {
                            $pago = DiasCompra::findOrFail($data['dias_compra_id']);
                            $pago->update(['estado' => '0']);

                            if ($pago->id_caja) {
                                $doc = trim("{$record->serie}-{$record->numero}", '-');

                                app(CajaService::class)->registrarMovimiento([
                                    'id_caja'          => $pago->id_caja,
                                    'tipo'             => 'INGRESO',
                                    'categoria'        => 'COMPRA',
                                    'descripcion'      => 'Reversión pago anulado compra ' . ($doc ?: "#{$record->id_compra}"),
                                    'monto'            => (float) $pago->monto,
                                    'fecha'            => now()->toDateString(),
                                    'instrumento_tipo' => $pago->instrumento_tipo,
                                    'id_usuario'       => (int) auth()->user()->usuario_id,
                                ]);
                            }
                        });

                        Notification::make()->success()->title('Pago anulado')->send();
                    }),
            ])
            ->defaultSort('fecha_vencimiento', 'asc');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('compras.id_empresa', (int) session('id_empresa'))
            ->where('compras.id_tipo_pago', 2)
            ->with(['proveedor', 'tipoDocSunat']);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCuentasPorPagar::route('/'),
        ];
    }
}
