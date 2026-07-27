<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CuentaPorPagarResource\Pages;
use App\Models\BilleteraTipo;
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
    use \App\Filament\Concerns\VerificaPermisoDeAcceso;

    public const PERMISO_ACCESO = 'pagos.ver';

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
                Action::make('pagos')
                    ->label(fn (Compra $record): string => static::saldoPendiente($record) > 0 ? 'Pagar' : 'Historial')
                    ->icon(fn (Compra $record): string => static::saldoPendiente($record) > 0
                        ? 'heroicon-o-banknotes' : 'heroicon-o-clock')
                    ->color(fn (Compra $record): string => static::saldoPendiente($record) > 0 ? 'success' : 'info')
                    ->modalHeading(fn (Compra $record): string =>
                        trim("{$record->serie}-{$record->numero}", '-') ?: "Compra #{$record->id_compra}")
                    ->modalWidth('lg')
                    ->modalSubmitAction(fn (Compra $record) => static::saldoPendiente($record) > 0 ? null : false)
                    ->modalSubmitActionLabel('Pagar')
                    ->modalContent(fn (Compra $record) => view('filament.modals.pagos-historial', [
                        'pagos'   => $record->pagos()->orderByDesc('fecha')->orderByDesc('dias_compra_id')->get(),
                        'total'   => (float) $record->total,
                        'pagado'  => (float) $record->pagos()->where('estado', '1')->sum('monto'),
                    ]))
                    ->form(function (Compra $record): array {
                        if (static::saldoPendiente($record) <= 0) return [];

                        return [
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
                            Select::make('metodo_pago')
                                ->label('Método de Pago')
                                ->options(fn (): array => CajaService::opcionesMetodoPago())
                                ->default('EFECTIVO')
                                ->live()
                                ->required(),
                            TextInput::make('referencia')
                                ->label('N° de operación')
                                ->placeholder('Código del comprobante del pago')
                                ->maxLength(60)
                                ->visible(fn (callable $get): bool =>
                                    filled($get('metodo_pago')) && $get('metodo_pago') !== 'EFECTIVO')
                                ->required(fn (callable $get): bool =>
                                    filled($get('metodo_pago')) && $get('metodo_pago') !== 'EFECTIVO'),
                        ];
                    })
                    ->action(function (Compra $record, array $data): void {
                        $saldo = static::saldoPendiente($record);

                        if ((float) $data['monto'] > $saldo) {
                            Notification::make()->danger()
                                ->title('Monto inválido')
                                ->body('El monto excede el saldo pendiente (S/ ' . number_format($saldo, 2) . ').')
                                ->send();

                            return;
                        }

                        $caja = static::cajaDelUsuario();
                        if ($caja) {
                            $saldoCaja = (float) $caja->saldo_actual;
                            if ((float) $data['monto'] > $saldoCaja) {
                                Notification::make()->danger()
                                    ->title('Saldo insuficiente en caja')
                                    ->body('El saldo disponible es S/ ' . number_format($saldoCaja, 2) . '.')
                                    ->persistent()
                                    ->send();

                                return;
                            }
                        }

                        try {
                            DB::transaction(function () use ($record, $data): void {
                                [$instrumentoTipo, $instrumentoId] = CajaService::mapInstrumento($data['metodo_pago'] ?? 'EFECTIVO');

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
                                        'instrumento_tipo' => $instrumentoTipo,
                                        'instrumento_id'   => $instrumentoId,
                                        'referencia'       => $data['referencia'] ?? null,
                                        'id_usuario'       => (int) auth()->user()->usuario_id,
                                    ]);
                                }

                                DiasCompra::create([
                                    'id_compra'        => $record->id_compra,
                                    'monto'            => $data['monto'],
                                    'fecha'            => $data['fecha'],
                                    'estado'           => '1',
                                    'id_caja'          => $idCaja,
                                    'instrumento_tipo' => $instrumentoTipo,
                                ]);
                            });
                        } catch (\RuntimeException $e) {
                            Notification::make()->danger()->title($e->getMessage())->persistent()->send();

                            return;
                        }

                        Notification::make()->success()->title('Pago registrado')->send();
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
