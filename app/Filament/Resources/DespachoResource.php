<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DespachoResource\Pages;
use App\Models\TmsDespacho;
use App\Services\TmsDespachoService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class DespachoResource extends Resource
{
    protected static ?string $model = TmsDespacho::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationLabel = 'Despachos';
    protected static string|\UnitEnum|null $navigationGroup = 'Transporte (TMS)';
    protected static ?int $navigationSort = 5;
    protected static ?string $label = 'Despacho';
    protected static ?string $pluralLabel = 'Despachos';
    protected static ?string $slug = 'tms-despachos';

    private const ESTADO_COLOR = [
        'PLANIFICADO' => 'info',
        'CARGADO'     => 'warning',
        'EN_RUTA'     => 'primary',
        'CERRADO'     => 'success',
        'ANULADO'     => 'gray',
    ];

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('codigo')->label('Código')->searchable()->sortable(),
                TextColumn::make('fecha_reparto')->label('Fecha')->date('d/m/Y')->sortable(),
                TextColumn::make('ruta.nombre')->label('Ruta')->placeholder('—'),
                TextColumn::make('vehiculo.placa')->label('Vehículo')->placeholder('—'),
                TextColumn::make('conductor.nombres')->label('Conductor')->placeholder('—')->wrap(),
                TextColumn::make('pedidos_count')->label('Pedidos')->counts('pedidos')->badge(),
                TextColumn::make('peso_total')->label('Peso')->sortable()
                    ->formatStateUsing(fn ($state): string => number_format((float) $state, 2) . ' kg'),
                TextColumn::make('estado')->label('Estado')->badge()
                    ->color(fn (string $state): string => self::ESTADO_COLOR[$state] ?? 'gray')
                    ->formatStateUsing(fn (string $state): string => ucfirst(strtolower(str_replace('_', ' ', $state)))),
            ])
            ->actions([
                Action::make('reporte')
                    ->label('Reporte')
                    ->icon('heroicon-o-document-text')
                    ->color('gray')
                    ->modalHeading(fn (TmsDespacho $record): string => 'Reporte de despacho ' . $record->codigo)
                    ->modalContent(fn (TmsDespacho $record) => view('filament.tms.despacho-reporte', [
                        'despacho' => $record,
                        'data'     => app(TmsDespachoService::class)->reporte($record->id),
                    ]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Cerrar'),

                ActionGroup::make([
                    Action::make('cargar')->label('Cargar')->icon('heroicon-o-inbox-arrow-down')->color('warning')
                        ->visible(fn (TmsDespacho $r) => $r->estado === 'PLANIFICADO')
                        ->requiresConfirmation()
                        ->action(fn (TmsDespacho $r) => $r->update(['estado' => 'CARGADO'])),

                    Action::make('salir')->label('Salir a ruta')->icon('heroicon-o-truck')->color('primary')
                        ->visible(fn (TmsDespacho $r) => $r->estado === 'CARGADO')
                        ->requiresConfirmation()
                        ->action(fn (TmsDespacho $r) => $r->update(['estado' => 'EN_RUTA'])),

                    Action::make('cerrar')->label('Cerrar')->icon('heroicon-o-lock-closed')->color('success')
                        ->visible(fn (TmsDespacho $r) => $r->estado === 'EN_RUTA')
                        ->requiresConfirmation()
                        ->action(fn (TmsDespacho $r) => $r->update(['estado' => 'CERRADO'])),

                    Action::make('entregas')->label('Registrar entregas')->icon('heroicon-o-check-circle')->color('info')
                        ->visible(fn (TmsDespacho $r) => in_array($r->estado, ['CARGADO', 'EN_RUTA'], true))
                        ->fillForm(fn (TmsDespacho $r): array => [
                            'pedidos' => $r->pedidos()->get()->map(fn ($p) => [
                                'id'             => $p->id,
                                'cliente'        => DB::table('clientes')->where('id_cliente', $p->id_cliente)->value('datos') ?? '-',
                                'estado_entrega' => $p->estado_entrega,
                                'motivo_rechazo' => $p->motivo_rechazo,
                            ])->toArray(),
                        ])
                        ->form([
                            Repeater::make('pedidos')
                                ->label('Puntos de entrega')
                                ->addable(false)->deletable(false)->reorderable(false)
                                ->columns(3)
                                ->schema([
                                    TextInput::make('cliente')->label('Cliente')->disabled()->columnSpan(1),
                                    Select::make('estado_entrega')->label('Entrega')->columnSpan(1)
                                        ->options([
                                            'PENDIENTE' => 'Pendiente',
                                            'ENTREGADO' => 'Entregado',
                                            'RECHAZADO' => 'Rechazado',
                                            'PARCIAL'   => 'Parcial',
                                        ])->required(),
                                    TextInput::make('motivo_rechazo')->label('Motivo (si rechazo)')->columnSpan(1),
                                    TextInput::make('id')->hidden(),
                                ]),
                        ])
                        ->action(function (array $data): void {
                            foreach ($data['pedidos'] as $p) {
                                DB::table('tms_despacho_pedidos')->where('id', $p['id'])->update([
                                    'estado_entrega' => $p['estado_entrega'],
                                    'motivo_rechazo' => $p['estado_entrega'] === 'RECHAZADO' ? ($p['motivo_rechazo'] ?? null) : null,
                                    'hora_entrega'   => now(),
                                ]);
                            }
                            Notification::make()->success()->title('Entregas actualizadas.')->send();
                        }),

                    Action::make('anular')->label('Anular')->icon('heroicon-o-x-circle')->color('danger')
                        ->visible(fn (TmsDespacho $r) => in_array($r->estado, ['PLANIFICADO', 'CARGADO'], true))
                        ->requiresConfirmation()
                        ->modalDescription('Los pedidos quedarán libres para otro despacho.')
                        ->action(fn (TmsDespacho $r) => $r->update(['estado' => 'ANULADO'])),
                ])->label('Acciones')->icon('heroicon-m-ellipsis-vertical')->button(),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['ruta', 'vehiculo', 'conductor'])
            ->where('id_empresa', (int) session('id_empresa'))
            ->where('sucursal', (int) session('sucursal'));
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDespachos::route('/'),
        ];
    }
}
