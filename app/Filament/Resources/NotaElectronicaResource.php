<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NotaElectronicaResource\Pages;
use App\Http\Controllers\Api\NotaElectronicaApiController;
use App\Models\NotaElectronica;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
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
    use \App\Filament\Concerns\VerificaPermisoDeAcceso;

    public const PERMISO_ACCESO = 'notas.ver';

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

                TextColumn::make('motivo_desc')
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

                TextColumn::make('sunat_estado')
                    ->label('SUNAT')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'aceptado'  => 'Aceptada',
                        'rechazado' => 'Rechazada',
                        default     => 'Pendiente',
                    })
                    ->color(fn (?string $state): string => match ($state) {
                        'aceptado'  => 'success',
                        'rechazado' => 'danger',
                        default     => 'warning',
                    })
                    ->tooltip(fn (NotaElectronica $record): ?string => $record->sunat_mensaje),

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
                ActionGroup::make([
                Action::make('pdf')
                    ->label('PDF')
                    ->icon('heroicon-o-document-text')
                    ->color('danger')
                    ->url(fn (NotaElectronica $record): string =>
                        url("/nota/electronica/pdf/{$record->nota_id}"))
                    ->openUrlInNewTab(),

                Action::make('ver_xml')
                    ->label('Ver XML')
                    ->icon('heroicon-m-code-bracket')
                    ->color('gray')
                    ->visible(fn (NotaElectronica $record): bool => filled($record->xml_ruta))
                    ->url(fn (NotaElectronica $record): string => route('facturacion.xml', [
                        'ruc'     => explode('/', $record->xml_ruta)[2] ?? '',
                        'archivo' => basename($record->xml_ruta),
                    ]))
                    ->openUrlInNewTab(),

                Action::make('regenerar_xml')
                    ->label('Regenerar XML')
                    ->icon('heroicon-m-arrow-path')
                    ->color('warning')
                    ->visible(fn (NotaElectronica $record): bool =>
                        $record->estado === '1' && $record->sunat_estado !== 'aceptado')
                    ->requiresConfirmation()
                    ->modalHeading('¿Regenerar el XML de la nota?')
                    ->modalDescription('Vuelve a generar el XML con los datos actuales.')
                    ->action(function (NotaElectronica $record): void {
                        $res = app(\App\Services\NotaSunatService::class)->generarXml($record);
                        $n = Notification::make()->title($res['msg']);
                        $res['ok'] ? $n->success()->send() : $n->danger()->persistent()->send();
                    }),

                Action::make('enviar_sunat')
                    ->label('Enviar a SUNAT')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('success')
                    ->visible(fn (NotaElectronica $record): bool =>
                        $record->estado === '1' && $record->sunat_estado !== 'aceptado')
                    ->requiresConfirmation()
                    ->modalHeading('¿Enviar esta nota a SUNAT?')
                    ->modalDescription('Se enviará el XML generado. Verás el resultado (CDR) al instante.')
                    ->action(function (NotaElectronica $record): void {
                        $res = app(\App\Services\NotaSunatService::class)->enviar($record);
                        $n = Notification::make()->title($res['msg']);
                        $res['ok'] ? $n->success()->send() : $n->danger()->persistent()->send();
                    }),

                Action::make('descargar_cdr')
                    ->label('Descargar CDR')
                    ->icon('heroicon-m-document-check')
                    ->color('gray')
                    ->visible(fn (NotaElectronica $record): bool => filled($record->cdr_ruta))
                    ->action(fn (NotaElectronica $record) =>
                        response()->download(storage_path('app/private/' . $record->cdr_ruta))),

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
                ])->tooltip('Acciones'),
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
            'index'  => Pages\ListNotasElectronicas::route('/'),
            'create' => Pages\CreateNotaElectronica::route('/create'),
        ];
    }
}
