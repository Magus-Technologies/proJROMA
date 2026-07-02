<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NotaElectronicaResource\Pages;
use App\Http\Controllers\Api\NotaElectronicaApiController;
use App\Models\NotaElectronica;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class NotaElectronicaResource extends Resource
{
    protected static ?string $model = NotaElectronica::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-minus';
    protected static ?string $navigationLabel = 'Notas Electrónicas';
    protected static string|\UnitEnum|null $navigationGroup = 'Facturación';
    protected static ?int $navigationSort = 3;
    protected static ?string $label = 'Nota';
    protected static ?string $pluralLabel = 'Notas Electrónicas';
    protected static ?string $slug = 'notas-electronicas';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('documento')
                    ->label('Nota')
                    ->getStateUsing(fn (NotaElectronica $record): string =>
                        $record->serie . '-' . str_pad($record->numero, 8, '0', STR_PAD_LEFT))
                    ->searchable(query: fn (Builder $query, string $search): Builder =>
                        $query->where(fn (Builder $q) => $q
                            ->where('serie', 'like', "%{$search}%")
                            ->orWhere('numero', 'like', "%{$search}%"))),

                TextColumn::make('tipo')
                    ->label('Tipo')
                    ->badge()
                    ->placeholder('—')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'credito' => 'Nota de Crédito',
                        'debito'  => 'Nota de Débito',
                        default   => $state ?: '—',
                    })
                    ->color(fn (?string $state): string =>
                        $state === 'credito' ? 'warning' : 'info'),

                TextColumn::make('comprobante_afectado')
                    ->label('Comprobante Afectado')
                    ->getStateUsing(fn (NotaElectronica $record): string =>
                        $record->venta?->documento_completo ?? '—'),

                TextColumn::make('motivo')
                    ->label('Motivo')
                    ->wrap()
                    ->limit(35)
                    ->placeholder('—'),

                TextColumn::make('cliente')
                    ->label('Cliente')
                    ->getStateUsing(fn (NotaElectronica $record): string =>
                        $record->venta?->cliente?->datos ?? '—')
                    ->wrap()
                    ->limit(35),

                TextColumn::make('total')
                    ->label('Total')
                    ->money('PEN')
                    ->sortable(),

                TextColumn::make('fecha_emision')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('estado_sunat')
                    ->label('SUNAT')
                    ->badge()
                    ->getStateUsing(fn (NotaElectronica $record): string =>
                        in_array($record->estado_sunat, [null, '', '0'], true) ? 'NO ENVIADO' : $record->estado_sunat)
                    ->color(fn (string $state): string => match ($state) {
                        'ACEPTADO'   => 'success',
                        'NO ENVIADO' => 'danger',
                        default      => 'warning',
                    }),

                TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => $state === '1' ? 'Activa' : 'Anulada')
                    ->color(fn (string $state): string => $state === '1' ? 'success' : 'gray'),
            ])
            ->filters([
                SelectFilter::make('tipo')
                    ->label('Tipo')
                    ->options([
                        'credito' => 'Nota de Crédito',
                        'debito'  => 'Nota de Débito',
                    ]),

                SelectFilter::make('estado')
                    ->label('Estado')
                    ->options([
                        '1' => 'Activa',
                        '0' => 'Anulada',
                    ]),
            ])
            ->actions([
                Action::make('pdf')
                    ->label('PDF')
                    ->icon('heroicon-o-document-text')
                    ->color('danger')
                    ->url(fn (NotaElectronica $record): string =>
                        url("/nota/electronica/pdf/{$record->nota_id}"))
                    ->openUrlInNewTab(),

                Action::make('enviar_sunat')
                    ->label('SUNAT')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('success')
                    ->visible(fn (NotaElectronica $record): bool =>
                        $record->enviado_sunat !== '1' && $record->estado === '1')
                    ->requiresConfirmation()
                    ->modalHeading('¿Enviar nota a SUNAT?')
                    ->action(function (NotaElectronica $record): void {
                        try {
                            $response = app(NotaElectronicaApiController::class)
                                ->enviarSunat(new Request(['id_nota' => $record->nota_id]));
                            $json = $response->getData(true);

                            if ($json['res'] ?? false) {
                                Notification::make()->success()
                                    ->title('Enviado a SUNAT')
                                    ->body($json['msg'] ?? 'Nota aceptada.')
                                    ->send();
                            } else {
                                Notification::make()->danger()
                                    ->title('SUNAT rechazó el envío')
                                    ->body($json['msg'] ?? 'Error desconocido.')
                                    ->send();
                            }
                        } catch (\Throwable $e) {
                            Notification::make()->danger()->title('Error al enviar')->body($e->getMessage())->send();
                        }
                    }),

                Action::make('anular')
                    ->label('Anular')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->visible(fn (NotaElectronica $record): bool =>
                        $record->enviado_sunat !== '1' && $record->estado === '1')
                    ->requiresConfirmation()
                    ->action(function (NotaElectronica $record): void {
                        $record->update(['estado' => '0']);
                        Notification::make()->success()->title('Nota anulada')->send();
                    }),
            ])
            ->defaultSort('nota_id', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('id_empresa', (int) session('id_empresa'))
            ->where('sucursal', (int) session('sucursal'))
            ->with(['venta.cliente']);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNotasElectronicas::route('/'),
        ];
    }
}
