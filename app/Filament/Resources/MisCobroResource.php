<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MisCobroResource\Pages;
use App\Models\DiasVenta;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MisCobroResource extends Resource
{
    protected static ?string $model = DiasVenta::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-wallet';
    protected static ?string $navigationLabel = 'Mis Cobros';
    protected static string|\UnitEnum|null $navigationGroup = 'Cobranzas';
    protected static ?int $navigationSort = 3;
    protected static ?string $label = 'Cobro';
    protected static ?string $pluralLabel = 'Mis Cobros';
    protected static ?string $slug = 'mis-cobros';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('fecha_cobro')
                    ->label('Fecha Cobro')
                    ->getStateUsing(fn (DiasVenta $record): string =>
                        ($record->fecha_pago_real ?? $record->fecha)?->format('d/m/Y') ?? '—')
                    ->sortable(query: fn (Builder $query, string $direction): Builder =>
                        $query->orderByRaw("IFNULL(dias_ventas.fecha_pago_real, dias_ventas.fecha) {$direction}")),

                TextColumn::make('documento')
                    ->label('Documento')
                    ->getStateUsing(fn (DiasVenta $record): string =>
                        $record->venta
                            ? "{$record->venta->serie}-" . str_pad($record->venta->numero, 8, '0', STR_PAD_LEFT)
                            : '—'),

                TextColumn::make('venta.cliente.datos')
                    ->label('Cliente')
                    ->searchable()
                    ->placeholder('— Sin cliente —')
                    ->wrap()
                    ->limit(40),

                TextColumn::make('tipo_pago')
                    ->label('Tipo Pago')
                    ->formatStateUsing(fn (?string $state): string =>
                        CuentaPorCobrarResource::TIPOS_PAGO[strtoupper((string) $state)] ?? ($state ?: '—')),

                TextColumn::make('monto')
                    ->label('Monto')
                    ->money('PEN')
                    ->sortable()
                    ->summarize(Sum::make()->label('Total cobrado')->money('PEN')),
            ])
            ->filters([
                SelectFilter::make('tipo_pago')
                    ->label('Tipo Pago')
                    ->options(CuentaPorCobrarResource::TIPOS_PAGO),
            ])
            ->actions([
                Action::make('ver_venta')
                    ->label('Ver venta')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->url(fn (DiasVenta $record): string =>
                        VentaResource::getUrl('view', ['record' => $record->id_venta])),
            ])
            ->defaultSort('dias_venta_id', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        $usuarioId = (int) auth()->user()->usuario_id;

        return parent::getEloquentQuery()
            ->where('dias_ventas.estado', '1')
            ->whereHas('venta', fn (Builder $q) => $q
                ->where('id_empresa', (int) session('id_empresa'))
                ->where('sucursal', (int) session('sucursal'))
                ->where('estado', '!=', '0'))
            // The collector is dv.id_usuario, falling back to the sale's vendedor
            // (same rule as the arqueo diario aggregation).
            ->where(fn (Builder $q) => $q
                ->where('dias_ventas.id_usuario', $usuarioId)
                ->orWhere(fn (Builder $q2) => $q2
                    ->whereNull('dias_ventas.id_usuario')
                    ->whereHas('venta', fn (Builder $v) => $v->where('id_vendedor', $usuarioId))))
            ->with(['venta.cliente']);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMisCobros::route('/'),
        ];
    }
}
