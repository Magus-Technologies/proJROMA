<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MisCobroResource\Pages;
use App\Models\CxcAbono;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;

/**
 * Libro de cobros del usuario logueado: cada abono (parcial o total)
 * que registró, con su método de pago y N° de operación.
 */
class MisCobroResource extends Resource
{
    use \App\Filament\Concerns\VerificaPermisoDeAcceso;

    public const PERMISO_ACCESO = 'cobranzas_miscobros.ver';

    protected static ?string $model = CxcAbono::class;

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
                TextColumn::make('fecha')
                    ->label('Fecha Cobro')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('documento')
                    ->label('Documento')
                    ->getStateUsing(fn (CxcAbono $record): string =>
                        $record->venta
                            ? "{$record->venta->serie}-" . str_pad($record->venta->numero, 8, '0', STR_PAD_LEFT)
                            : '—'),

                TextColumn::make('venta.cliente.datos')
                    ->label('Cliente')
                    ->searchable()
                    ->placeholder('— Sin cliente —')
                    ->wrap()
                    ->limit(40),

                TextColumn::make('metodo_pago')
                    ->label('Método')
                    ->formatStateUsing(fn (?string $state): string =>
                        \App\Services\CajaService::etiquetaMetodoPago($state))
                    ->wrap(),

                TextColumn::make('referencia')
                    ->label('N° operación')
                    ->placeholder('—')
                    ->toggleable(),

                TextColumn::make('monto')
                    ->label('Monto')
                    ->money('PEN')
                    ->sortable()
                    ->summarize(Sum::make()->label('Total cobrado')->money('PEN')),

                TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => $state === 'ACTIVO' ? 'Activo' : 'Anulado')
                    ->color(fn (string $state): string => $state === 'ACTIVO' ? 'success' : 'danger'),
            ])
            ->filters([
                SelectFilter::make('metodo_pago')
                    ->label('Método')
                    ->options(fn (): array => \App\Services\CajaService::opcionesMetodoPago()),

                Filter::make('fecha')
                    ->form([
                        DatePicker::make('desde')->label('Desde'),
                        DatePicker::make('hasta')->label('Hasta'),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query
                        ->when($data['desde'], fn (Builder $q) => $q->whereDate('fecha', '>=', $data['desde']))
                        ->when($data['hasta'], fn (Builder $q) => $q->whereDate('fecha', '<=', $data['hasta']))),
            ])
            ->actions([
                Action::make('ver_venta')
                    ->label('Ver venta')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->url(fn (CxcAbono $record): string =>
                        VentaResource::getUrl('view', ['record' => $record->id_venta])),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        $usuarioId = (int) auth()->user()->usuario_id;

        return parent::getEloquentQuery()
            ->where('cxc_abonos.id_usuario', $usuarioId)
            ->whereHas('venta', fn (Builder $q) => $q
                ->where('id_empresa', (int) session('id_empresa'))
                ->where('sucursal', (int) session('sucursal'))
                ->where('estado', '!=', '0'))
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
