<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DespachoResource\Pages;
use App\Models\TmsDespacho;
use App\Services\CajaService;
use App\Services\TmsDespachoService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\CheckboxList;
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
                TextColumn::make('costos_sum_monto')->label('Costos')->sum('costos', 'monto')->toggleable()
                    ->formatStateUsing(fn ($state): string => 'S/ ' . number_format((float) $state, 2)),
                TextColumn::make('estado')->label('Estado')->badge()
                    ->color(fn (string $state): string => self::ESTADO_COLOR[$state] ?? 'gray')
                    ->formatStateUsing(fn (string $state): string => ucfirst(strtolower(str_replace('_', ' ', $state)))),
            ])
            ->actions([
                Action::make('reporte')
                    ->label('Reporte')
                    ->iconButton()
                    ->tooltip('Reporte')
                    ->icon('heroicon-o-document-text')
                    ->color('gray')
                    ->modalHeading(fn (TmsDespacho $record): string => 'Reporte de despacho ' . $record->codigo)
                    ->modalContent(fn (TmsDespacho $record) => view('filament.tms.despacho-reporte', [
                        'despacho' => $record,
                        'data'     => app(TmsDespachoService::class)->reporte($record->id),
                    ]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Cerrar'),

                Action::make('pdf')
                    ->label('Hoja de carga')
                    ->iconButton()
                    ->tooltip('Hoja de carga')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('gray')
                    ->modalHeading(fn (TmsDespacho $record): string => 'Hoja de carga — ' . $record->codigo)
                    ->modalDescription('Deja los filtros vacíos para sacar TODA la carga del despacho, o elige mercados/medidas para sacarla por partes.')
                    ->modalSubmitActionLabel('Generar PDF')
                    ->form([
                        CheckboxList::make('mercados')
                            ->label('Solo estos mercados (vacío = todos)')
                            ->options(fn (TmsDespacho $record) => self::mercadosDelDespacho($record))
                            ->columns(2)
                            ->bulkToggleable(),
                        CheckboxList::make('medidas')
                            ->label('Solo estas unidades de medida (vacío = todas)')
                            ->options(fn (TmsDespacho $record) => self::medidasDelDespacho($record))
                            ->columns(3)
                            ->bulkToggleable(),
                    ])
                    ->action(function (array $data, TmsDespacho $record, $livewire): void {
                        if (self::notificarPedidosSinFacturar($record, 'la hoja de carga')) return;

                        $qs = http_build_query(array_filter([
                            'mercados' => implode(',', $data['mercados'] ?? []),
                            'medidas'  => implode(',', $data['medidas'] ?? []),
                        ]));
                        $url = route('tms.despacho.pdf', $record->id) . ($qs ? "?{$qs}" : '');
                        $livewire->js("window.open(" . json_encode($url) . ", '_blank')");
                    }),

                Action::make('guias')
                    ->label('Guías de reparto')
                    ->iconButton()
                    ->tooltip('Guías de reparto')
                    ->icon('heroicon-o-ticket')
                    ->color('info')
                    ->modalHeading(fn (TmsDespacho $record): string => 'Guías de reparto — ' . $record->codigo)
                    ->modalDescription('Deja el filtro vacío para imprimir las guías de TODOS los pedidos, o elige mercados para imprimir solo esos.')
                    ->modalSubmitActionLabel('Generar PDF')
                    ->form([
                        CheckboxList::make('mercados')
                            ->label('Solo estos mercados (vacío = todos)')
                            ->options(fn (TmsDespacho $record) => self::mercadosDelDespacho($record))
                            ->columns(2)
                            ->bulkToggleable(),
                    ])
                    ->action(function (array $data, TmsDespacho $record, $livewire): void {
                        if (self::notificarPedidosSinFacturar($record, 'las guías de reparto')) return;

                        $qs = http_build_query(array_filter([
                            'mercados' => implode(',', $data['mercados'] ?? []),
                        ]));
                        $url = route('tms.despacho.guias', $record->id) . ($qs ? "?{$qs}" : '');
                        $livewire->js("window.open(" . json_encode($url) . ", '_blank')");
                    }),

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

                    Action::make('agregar_costo')->label('Agregar costo')->icon('heroicon-o-banknotes')->color('gray')
                        ->visible(fn (TmsDespacho $r) => $r->estado !== 'ANULADO')
                        ->form([
                            TextInput::make('concepto')->label('Concepto')->required()->maxLength(120)
                                ->placeholder('Combustible, peaje, viáticos...'),
                            TextInput::make('monto')->label('Monto (S/)')->required()->numeric()->minValue(0.01),
                            Select::make('id_caja')->label('Cargar a caja (opcional)')
                                ->options(fn () => DB::table('cajas')->where('id_empresa', (int) session('id_empresa'))
                                    ->where('estado', 'ACTIVA')->orderBy('nombre')->pluck('nombre', 'id'))
                                ->helperText('Si eliges una caja, se registra como EGRESO real en ella.'),
                        ])
                        ->action(function (array $data, TmsDespacho $record): void {
                            $idMov = null;
                            if (!empty($data['id_caja'])) {
                                try {
                                    $idMov = app(CajaService::class)->registrarMovimiento([
                                        'id_caja'     => $data['id_caja'],
                                        'fecha'       => now()->toDateString(),
                                        'tipo'        => 'EGRESO',
                                        'categoria'   => 'TMS',
                                        'descripcion' => 'Costo despacho ' . ($record->codigo ?? $record->id) . ': ' . $data['concepto'],
                                        'monto'       => $data['monto'],
                                        'id_usuario'  => (int) (auth()->user()->usuario_id ?? 0),
                                    ]);
                                } catch (\RuntimeException $e) {
                                    Notification::make()->danger()->title($e->getMessage())->send();
                                    return;
                                }
                            }
                            DB::table('tms_despacho_costos')->insert([
                                'id_despacho'        => $record->id,
                                'concepto'           => $data['concepto'],
                                'monto'              => $data['monto'],
                                'id_caja'            => $data['id_caja'] ?? null,
                                'id_movimiento_caja' => $idMov,
                                'id_usuario'         => (int) (auth()->user()->usuario_id ?? 0),
                                'created_at'         => now(),
                                'updated_at'         => now(),
                            ]);
                            Notification::make()->success()->title('Costo agregado.')->send();
                        }),

                    Action::make('ver_costos')->label('Ver costos')->icon('heroicon-o-list-bullet')->color('gray')
                        ->modalHeading(fn (TmsDespacho $r): string => 'Costos de ' . $r->codigo)
                        ->modalContent(fn (TmsDespacho $r) => view('filament.tms.despacho-costos', [
                            'costos' => DB::table('tms_despacho_costos as c')
                                ->leftJoin('cajas as ca', 'ca.id', '=', 'c.id_caja')
                                ->where('c.id_despacho', $r->id)
                                ->orderBy('c.id')
                                ->select('c.id', 'c.concepto', 'c.monto', DB::raw("COALESCE(ca.nombre, '—') as caja"))
                                ->get(),
                        ]))
                        ->modalSubmitAction(false)->modalCancelActionLabel('Cerrar'),

                    Action::make('anular')->label('Anular')->icon('heroicon-o-x-circle')->color('danger')
                        ->visible(fn (TmsDespacho $r) => in_array($r->estado, ['PLANIFICADO', 'CARGADO'], true))
                        ->requiresConfirmation()
                        ->modalDescription('Los pedidos quedarán libres para otro despacho.')
                        ->action(fn (TmsDespacho $r) => $r->update(['estado' => 'ANULADO'])),
                ])->label('Acciones')->icon('heroicon-m-ellipsis-vertical')->button(),
            ])
            ->defaultSort('id', 'desc');
    }

    /** Notifica si el despacho tiene pedidos sin facturar. Devuelve true si debe bloquearse la acción. */
    private static function notificarPedidosSinFacturar(TmsDespacho $despacho, string $documento): bool
    {
        $sinFacturar = app(TmsDespachoService::class)->pedidosSinFacturarDeDespacho($despacho->id);
        if (!$sinFacturar) return false;

        Notification::make()->danger()
            ->title('Pedidos sin facturar')
            ->body('No se puede generar ' . $documento . ': los pedidos ' . implode(', ', $sinFacturar) .
                ' aún no fueron convertidos a boleta o factura.')
            ->persistent()
            ->send();

        return true;
    }

    /** Mercados presentes en los pedidos del despacho, para el filtro del PDF. */
    public static function mercadosDelDespacho(TmsDespacho $despacho): array
    {
        return DB::table('tms_despacho_pedidos as dp')
            ->join('tms_mercados as m', 'm.id', '=', 'dp.id_mercado')
            ->where('dp.id_despacho', $despacho->id)
            ->distinct()
            ->orderBy('m.nombre')
            ->pluck('m.nombre', 'm.id')
            ->toArray();
    }

    /** Unidades de medida presentes en las líneas de los pedidos del despacho. */
    public static function medidasDelDespacho(TmsDespacho $despacho): array
    {
        return DB::table('productos_cotis as pc')
            ->join('tms_despacho_pedidos as dp', 'dp.id_cotizacion', '=', 'pc.id_coti')
            ->where('dp.id_despacho', $despacho->id)
            ->whereNotNull('pc.medida')->where('pc.medida', '<>', '')
            ->distinct()
            ->orderBy('pc.medida')
            ->pluck('pc.medida', 'pc.medida')
            ->toArray();
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
