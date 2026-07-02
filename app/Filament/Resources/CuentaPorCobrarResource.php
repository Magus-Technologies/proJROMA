<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CuentaPorCobrarResource\Pages;
use App\Models\DiasVenta;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CuentaPorCobrarResource extends Resource
{
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

                TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => $state === '1' ? 'Pagado' : 'Pendiente')
                    ->color(fn (string $state): string => $state === '1' ? 'success' : 'danger'),

                TextColumn::make('tipo_pago')
                    ->label('Tipo Pago')
                    ->formatStateUsing(fn (?string $state): string =>
                        self::TIPOS_PAGO[strtoupper((string) $state)] ?? ($state ?: '—')),

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
                    ->options(self::TIPOS_PAGO),
            ])
            ->actions([
                Action::make('cobrar')
                    ->label('Cobrar')
                    ->icon('heroicon-o-banknotes')
                    ->color('success')
                    ->visible(fn (DiasVenta $record): bool => $record->estado !== '1')
                    ->modalHeading('Registrar Cobro')
                    ->modalDescription(fn (DiasVenta $record): string =>
                        'Monto: S/ ' . number_format($record->monto, 2)
                        . ($record->venta?->cliente ? ' — ' . $record->venta->cliente->datos : ''))
                    ->form([
                        Select::make('tipo_pago')
                            ->label('Tipo de pago')
                            ->options(self::TIPOS_PAGO)
                            ->default('EFECTIVO')
                            ->required(),
                    ])
                    ->action(function (DiasVenta $record, array $data): void {
                        $record->update([
                            'estado'          => '1',
                            'tipo_pago'       => $data['tipo_pago'],
                            'fecha_pago_real' => now()->toDateString(),
                            'id_usuario'      => (int) auth()->user()->usuario_id,
                        ]);

                        Notification::make()->success()->title('Cobro registrado')->send();
                    }),

                Action::make('ver_venta')
                    ->label('Ver venta')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->url(fn (DiasVenta $record): string =>
                        VentaResource::getUrl('view', ['record' => $record->id_venta])),
            ])
            ->defaultSort('fecha', 'asc');
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
