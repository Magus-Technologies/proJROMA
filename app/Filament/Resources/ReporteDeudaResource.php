<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReporteDeudaResource\Pages;
use App\Models\DiasVenta;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ReporteDeudaResource extends Resource
{
    protected static ?string $model = DiasVenta::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'Reporte Deudas';
    protected static string|\UnitEnum|null $navigationGroup = 'Cobranzas';
    protected static ?int $navigationSort = 2;
    protected static ?string $label = 'Deuda';
    protected static ?string $pluralLabel = 'Reporte de Deudas';
    protected static ?string $slug = 'reporte-deudas';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('venta.cliente.datos')
                    ->label('Cliente')
                    ->searchable()
                    ->placeholder('— Sin cliente —')
                    ->wrap()
                    ->limit(40),

                TextColumn::make('documento')
                    ->label('Documento')
                    ->getStateUsing(fn (DiasVenta $record): string =>
                        $record->venta
                            ? "{$record->venta->serie}-" . str_pad($record->venta->numero, 8, '0', STR_PAD_LEFT)
                            : '—'),

                TextColumn::make('fecha')
                    ->label('Fecha Venc.')
                    ->date('d/m/Y')
                    ->sortable()
                    ->color(fn (DiasVenta $record): ?string =>
                        $record->fecha?->isPast() ? 'danger' : null),

                TextColumn::make('dias_atraso')
                    ->label('Días de atraso')
                    ->getStateUsing(fn (DiasVenta $record): string =>
                        $record->fecha?->isPast()
                            ? (string) (int) $record->fecha->diffInDays(now())
                            : '—')
                    ->badge()
                    ->color(fn (string $state): string => $state === '—' ? 'gray' : 'danger'),

                TextColumn::make('monto')
                    ->label('Monto')
                    ->money('PEN')
                    ->sortable()
                    ->summarize(Sum::make()->label('Total')->money('PEN')),

                TextColumn::make('venta.vendedor.nombre_completo')
                    ->label('Vendedor')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->groups([
                Group::make('venta.cliente.datos')
                    ->label('Cliente')
                    ->collapsible(),
            ])
            ->defaultGroup('venta.cliente.datos')
            ->filters([
                Filter::make('vencidas')
                    ->label('Solo vencidas')
                    ->query(fn (Builder $query): Builder =>
                        $query->whereDate('dias_ventas.fecha', '<', now()->toDateString())),
            ])
            ->actions([
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
            ->where('dias_ventas.estado', '0')
            ->whereHas('venta', fn (Builder $q) => $q
                ->where('id_empresa', (int) session('id_empresa'))
                ->where('sucursal', (int) session('sucursal'))
                ->where('estado', '!=', '0'))
            ->with(['venta.cliente', 'venta.vendedor']);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReporteDeudas::route('/'),
        ];
    }
}
