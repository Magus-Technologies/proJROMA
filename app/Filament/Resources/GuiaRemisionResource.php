<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GuiaRemisionResource\Pages;
use App\Models\GuiaRemision;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class GuiaRemisionResource extends Resource
{
    protected static ?string $model = GuiaRemision::class;

    protected static string|BackedEnum|null $navigationIcon  = 'heroicon-o-truck';
    protected static ?string $navigationLabel = 'Guías de Remisión';
    protected static string|\UnitEnum|null $navigationGroup = 'Facturación';
    protected static ?int    $navigationSort  = 2;
    protected static ?string $label           = 'Guía de Remisión';
    protected static ?string $pluralLabel     = 'Guías de Remisión';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id_guia_remision')
                    ->label('#')
                    ->sortable(),

                TextColumn::make('documento')
                    ->label('Documento')
                    ->getStateUsing(fn (GuiaRemision $record): string =>
                        trim("{$record->serie}-" . str_pad((string) $record->numero, 8, '0', STR_PAD_LEFT), '-') ?: '—')
                    ->searchable(query: fn (Builder $query, string $search): Builder =>
                        $query->where(fn (Builder $q) => $q
                            ->where('serie', 'like', "%{$search}%")
                            ->orWhere('numero', 'like', "%{$search}%"))),

                TextColumn::make('fecha_emision')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('venta.cliente.datos')
                    ->label('Cliente')
                    ->searchable()
                    ->placeholder('—')
                    ->wrap()
                    ->limit(40),

                TextColumn::make('dir_llegada')
                    ->label('Destino')
                    ->wrap()
                    ->limit(45)
                    ->placeholder('—')
                    ->toggleable(),

                TextColumn::make('peso')
                    ->label('Peso (kg)')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('estado_gre')
                    ->label('SUNAT')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'aceptado'  => 'Aceptado',
                        'enviado'   => 'Enviado (esperando)',
                        'rechazado' => 'Rechazado',
                        default     => 'Pendiente',
                    })
                    ->color(fn (?string $state): string => match ($state) {
                        'aceptado'  => 'success',
                        'enviado'   => 'info',
                        'rechazado' => 'danger',
                        default     => 'warning',
                    })
                    ->tooltip(fn (GuiaRemision $record): ?string => $record->mensaje_sunat),

                TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn ($state): string => $state == '1' ? 'Activa' : 'Anulada')
                    ->color(fn ($state): string => $state == '1' ? 'success' : 'gray'),
            ])
            ->filters([
                SelectFilter::make('estado')
                    ->label('Estado')
                    ->options([
                        '1' => 'Activa',
                        '0' => 'Anulada',
                    ]),
            ])
            ->actions([
                Action::make('detalle')
                    ->label('Detalle')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->modalHeading(fn (GuiaRemision $record): string =>
                        'Guía ' . trim("{$record->serie}-{$record->numero}", '-'))
                    ->modalContent(fn (GuiaRemision $record) => view('filament.modals.recepcion-detalle', [
                        'lineas' => DB::table('guia_detalles as d')
                            ->leftJoin('productos as p', 'p.id_producto', '=', 'd.id_producto')
                            ->where('d.id_guia', $record->id_guia_remision)
                            ->select('p.codigo', 'd.detalles as producto', 'd.unidad', 'd.cantidad')
                            ->get(),
                    ]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Cerrar'),

                Action::make('pdf')
                    ->label('PDF')
                    ->icon('heroicon-o-document-text')
                    ->color('danger')
                    ->url(fn (GuiaRemision $record): string =>
                        route('guia.pdf', $record->id_guia_remision))
                    ->openUrlInNewTab(),

                Action::make('enviar_sunat')
                    ->label('Enviar a SUNAT')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('success')
                    ->visible(fn (GuiaRemision $record): bool =>
                        $record->estado === '1' && ! in_array($record->estado_gre, ['aceptado', 'enviado'], true))
                    ->requiresConfirmation()
                    ->modalHeading('¿Enviar esta guía a SUNAT?')
                    ->modalDescription('Se generará el XML y se enviará. Luego consultá el ticket para ver el resultado.')
                    ->action(function (GuiaRemision $record): void {
                        $res = app(\App\Services\GuiaSunatService::class)->enviar($record);
                        $n = Notification::make()->title($res['msg']);
                        $res['ok'] ? $n->success()->send() : $n->danger()->persistent()->send();
                    }),

                Action::make('consultar_ticket')
                    ->label('Consultar ticket')
                    ->icon('heroicon-o-arrow-path')
                    ->color('info')
                    ->visible(fn (GuiaRemision $record): bool =>
                        $record->estado_gre === 'enviado' && filled($record->ticket_sunat))
                    ->action(function (GuiaRemision $record): void {
                        $res = app(\App\Services\GuiaSunatService::class)->consultarTicket($record);
                        $n = Notification::make()->title($res['msg']);
                        $res['ok'] ? $n->success()->send() : $n->danger()->persistent()->send();
                    }),

                Action::make('anular')
                    ->label('Anular')
                    ->icon('heroicon-o-no-symbol')
                    ->color('danger')
                    ->visible(fn (GuiaRemision $record): bool => $record->estado === '1')
                    ->requiresConfirmation()
                    ->modalHeading('¿Anular esta guía de remisión?')
                    ->action(function (GuiaRemision $record): void {
                        $record->update(['estado' => '0']);
                        Notification::make()->success()->title('Guía anulada')->send();
                    }),
            ])
            ->defaultSort('id_guia_remision', 'desc');
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
            'index'  => Pages\ListGuiasRemision::route('/'),
            'create' => Pages\CreateGuiaRemision::route('/create'),
        ];
    }
}
