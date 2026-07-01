<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CompraResource\Pages;
use App\Models\Almacen;
use App\Models\Compra;
use App\Models\InventarioMovimiento;
use App\Models\MotivoMovimiento;
use App\Models\Producto;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CompraResource extends Resource
{
    protected static ?string $model = Compra::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationLabel = 'Compras';
    protected static string|\UnitEnum|null $navigationGroup = 'Almacén';
    protected static ?int    $navigationSort  = 2;
    protected static ?string $label           = 'Compra';
    protected static ?string $pluralLabel     = 'Compras';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id_compra')
                    ->label('#')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('tipo_doc')
                    ->label('Tipo')
                    ->getStateUsing(fn ($record) => match ((int) $record->id_tido) {
                        1 => 'Boleta',
                        2 => 'Factura',
                        3 => 'Liquidación',
                        6 => 'N. Venta',
                        default => "Tipo {$record->id_tido}",
                    })
                    ->badge()
                    ->color('gray')
                    ->toggleable(),

                TextColumn::make('documento')
                    ->label('Serie - Número')
                    ->getStateUsing(fn ($record) => trim(
                        ($record->serie ? $record->serie . '-' : '') . ($record->numero ?? ''),
                        '-'
                    ))
                    ->searchable(query: fn (Builder $q, string $search) => $q->where(
                        fn ($sub) => $sub->where('serie', 'like', "%{$search}%")
                                         ->orWhere('numero', 'like', "%{$search}%")
                    ))
                    ->toggleable(),

                TextColumn::make('fecha_emision')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('proveedor.nombre')
                    ->label('Proveedor')
                    ->searchable()
                    ->wrap()
                    ->toggleable(),

                TextColumn::make('total')
                    ->label('Total')
                    ->money('PEN')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('recepcionado')
                    ->label('Recepción')
                    ->badge()
                    ->toggleable()
                    ->color(fn ($state) => match ((int) $state) {
                        1       => 'success',
                        2       => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => match ((int) $state) {
                        1       => 'Recepcionado',
                        2       => 'Parcial',
                        default => 'Pendiente',
                    }),
            ])
            ->filters([
                SelectFilter::make('recepcionado')
                    ->label('Recepción')
                    ->options([0 => 'Pendiente', 2 => 'Parcial', 1 => 'Recepcionado']),

                Filter::make('fecha_rango')
                    ->label('Rango de fechas')
                    ->form([
                        DatePicker::make('fecha_desde')->label('Desde'),
                        DatePicker::make('fecha_hasta')->label('Hasta'),
                    ])
                    ->query(fn (Builder $q, array $data) => $q
                        ->when($data['fecha_desde'], fn ($q, $v) => $q->whereDate('fecha_emision', '>=', $v))
                        ->when($data['fecha_hasta'], fn ($q, $v) => $q->whereDate('fecha_emision', '<=', $v))
                    ),
            ])
            ->actions([
                ActionGroup::make([
                    Action::make('recepcionar')
                        ->label('Recepcionar')
                        ->icon('heroicon-m-archive-box-arrow-down')
                        ->color('success')
                        ->visible(fn (Compra $record) => (int) $record->recepcionado !== 1)
                        ->fillForm(function (Compra $record): array {
                            $lineas = DB::table('productos_compras as pc')
                                ->join('productos as p', 'p.id_producto', '=', 'pc.id_producto')
                                ->where('pc.id_compra', $record->id_compra)
                                ->select('pc.id_producto', 'p.descripcion as producto', DB::raw('SUM(pc.cantidad) as pedido'))
                                ->groupBy('pc.id_producto', 'p.descripcion')
                                ->get()
                                ->map(function ($l) use ($record) {
                                    $recibido = (int) DB::table('recepcion_detalle as rd')
                                        ->join('recepciones as rc', 'rc.id_recepcion', '=', 'rd.id_recepcion')
                                        ->where('rc.id_compra', $record->id_compra)
                                        ->where('rd.id_producto', $l->id_producto)
                                        ->sum('rd.cantidad');

                                    $pendiente = max(0, (int) $l->pedido - $recibido);

                                    return [
                                        'id_producto' => $l->id_producto,
                                        'producto'    => $l->producto,
                                        'pedido'      => (int) $l->pedido,
                                        'recibido'    => $recibido,
                                        'pendiente'   => $pendiente,
                                        'recibir'     => $pendiente,
                                    ];
                                })
                                ->toArray();

                            return ['lineas' => $lineas];
                        })
                        ->form([
                            Select::make('id_almacen')
                                ->label('Almacén destino')
                                ->required()
                                ->options(fn () => Almacen::where('id_empresa', session('id_empresa'))
                                    ->pluck('nombre', 'codigo')),

                            Repeater::make('lineas')
                                ->label('Productos')
                                ->addable(false)
                                ->deletable(false)
                                ->reorderable(false)
                                ->columns(6)
                                ->schema([
                                    TextInput::make('producto')->label('Producto')->disabled()->columnSpan(2),
                                    TextInput::make('pedido')->label('Pedido')->disabled(),
                                    TextInput::make('recibido')->label('Recibido')->disabled(),
                                    TextInput::make('pendiente')->label('Pendiente')->disabled(),
                                    TextInput::make('recibir')->label('Recibir')->numeric()->minValue(0),
                                    Hidden::make('id_producto'),
                                ]),
                        ])
                        ->action(function (array $data, Compra $record): void {
                            $emp     = (int) session('id_empresa');
                            $uid     = (int) (auth()->user()?->usuario_id ?? 0);
                            $almacen = $data['id_almacen'];

                            $detalles = collect($data['lineas'])
                                ->filter(fn ($l) => (int) ($l['recibir'] ?? 0) > 0)
                                ->values();

                            if ($detalles->isEmpty()) {
                                Notification::make()->warning()->title('Indicá cuánto recibir en al menos un producto.')->send();
                                return;
                            }

                            DB::beginTransaction();
                            try {
                                $motivo = MotivoMovimiento::where('id_empresa', $emp)
                                    ->where('tipo', 'I')
                                    ->where('nombre', 'Compra')
                                    ->value('id_motivo');

                                $idRecepcion = DB::table('recepciones')->insertGetId([
                                    'id_empresa' => $emp,
                                    'id_compra'  => $record->id_compra,
                                    'almacen'    => $almacen,
                                    'fecha'      => now(),
                                    'id_usuario' => $uid,
                                ]);

                                foreach ($detalles as $linea) {
                                    $cant = min((int) $linea['recibir'], (int) $linea['pendiente']);
                                    if ($cant <= 0) continue;

                                    $source = Producto::where('id_empresa', $emp)
                                        ->where('id_producto', $linea['id_producto'])
                                        ->firstOrFail();

                                    $dest = null;
                                    if (!empty($source->codigo)) {
                                        $dest = Producto::where('id_empresa', $emp)
                                            ->where('almacen', $almacen)
                                            ->where('codigo', $source->codigo)
                                            ->lockForUpdate()
                                            ->first();
                                    }

                                    $costo = DB::table('productos_compras')
                                        ->where('id_compra', $record->id_compra)
                                        ->where('id_producto', $linea['id_producto'])
                                        ->value('costo');

                                    if ($dest) {
                                        $ant = (int) $dest->cantidad;
                                        $dest->update(['cantidad' => $ant + $cant, 'costo' => $costo ?: $dest->costo]);
                                    } else {
                                        $dest = $source->replicate();
                                        $dest->almacen  = $almacen;
                                        $dest->cantidad = $cant;
                                        if ($costo) $dest->costo = $costo;
                                        $dest->save();
                                        $ant = 0;
                                    }

                                    InventarioMovimiento::create([
                                        'id_empresa'     => $emp,
                                        'almacen'        => $almacen,
                                        'id_producto'    => $dest->id_producto,
                                        'tipo'           => 'I',
                                        'id_motivo'      => $motivo,
                                        'cantidad'       => $cant,
                                        'stock_anterior' => $ant,
                                        'stock_nuevo'    => $ant + $cant,
                                        'costo'          => $costo ?: null,
                                        'observacion'    => "Recepción #{$idRecepcion} (compra #{$record->id_compra})",
                                        'id_usuario'     => $uid,
                                        'fecha'          => now(),
                                    ]);

                                    DB::table('recepcion_detalle')->insert([
                                        'id_recepcion' => $idRecepcion,
                                        'id_producto'  => $linea['id_producto'],
                                        'cantidad'     => $cant,
                                    ]);
                                }

                                $totalPedido   = (int) DB::table('productos_compras')->where('id_compra', $record->id_compra)->sum('cantidad');
                                $totalRecibido = (int) DB::table('recepcion_detalle as rd')
                                    ->join('recepciones as rc', 'rc.id_recepcion', '=', 'rd.id_recepcion')
                                    ->where('rc.id_compra', $record->id_compra)
                                    ->sum('rd.cantidad');

                                $estado = $totalRecibido >= $totalPedido ? 1 : ($totalRecibido > 0 ? 2 : 0);
                                $record->update(['recepcionado' => $estado]);

                                DB::commit();

                                $msg = $estado === 1 ? 'Recepción completa.' : 'Recepción parcial registrada.';
                                Notification::make()->success()->title($msg)->send();

                            } catch (\Throwable $e) {
                                DB::rollBack();
                                Log::error('Error recepción Filament: ' . $e->getMessage());
                                Notification::make()->danger()->title('Error al recepcionar.')->send();
                            }
                        }),

                    Action::make('editar')
                        ->label('Editar')
                        ->icon('heroicon-m-pencil')
                        ->color('info')
                        ->visible(fn (Compra $record) => (int) $record->recepcionado === 0)
                        ->url(fn (Compra $record) => url("/compras/add?id={$record->id_compra}")),

                    Action::make('pdf')
                        ->label('PDF')
                        ->icon('heroicon-m-document-arrow-down')
                        ->color('gray')
                        ->url(fn (Compra $record) => route('reporte.compra.pdf', $record->id_compra))
                        ->openUrlInNewTab(),

                    Action::make('eliminar')
                        ->label('Eliminar')
                        ->icon('heroicon-m-trash')
                        ->color('danger')
                        ->visible(fn (Compra $record) => (int) $record->recepcionado === 0)
                        ->requiresConfirmation()
                        ->modalHeading('Eliminar compra')
                        ->modalDescription('¿Confirmás que querés eliminar esta compra? Se eliminarán el documento y todos sus ítems.')
                        ->modalSubmitActionLabel('Sí, eliminar')
                        ->action(function (Compra $record): void {
                            if ((int) $record->recepcionado !== 0) {
                                Notification::make()->warning()->title('No se puede eliminar: ya tiene recepciones.')->send();
                                return;
                            }
                            DB::table('productos_compras')->where('id_compra', $record->id_compra)->delete();
                            $record->delete();
                            Notification::make()->success()->title('Compra eliminada.')->send();
                        }),
                ])->tooltip('Acciones'),
            ])
            ->defaultSort('id_compra', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['proveedor'])
            ->where('id_empresa', (int) session('id_empresa'));
    }

    public static function getRelations(): array { return []; }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCompras::route('/'),
        ];
    }
}
