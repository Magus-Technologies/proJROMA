<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TrasladoResource\Pages;
use App\Models\InventarioMovimiento;
use App\Models\MotivoMovimiento;
use App\Models\Producto;
use App\Models\Traslado;
use App\Models\TrasladoDetalle;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\DatePicker;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class TrasladoResource extends Resource
{
    use \App\Filament\Concerns\VerificaPermisoDeAcceso;

    public const PERMISO_ACCESO = 'almacen_traslados.ver';

    protected static ?string $model = Traslado::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-arrows-right-left';
    protected static ?string $navigationLabel = 'Traslado de Stock';
    protected static string|\UnitEnum|null $navigationGroup = 'Inventario';
    protected static ?int $navigationSort = 5;
    protected static ?string $label = 'Traslado';
    protected static ?string $pluralLabel = 'Traslados de Stock';
    protected static ?string $slug = 'traslados';

    public static function numeroDocumento(int $id): string
    {
        return 'TS-' . str_pad((string) $id, 8, '0', STR_PAD_LEFT);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        $almacenes = fn () => KardexResource::almacenes();

        return $table
            ->columns([
                TextColumn::make('id_traslado')
                    ->label('N°')
                    ->formatStateUsing(fn ($state) => static::numeroDocumento((int) $state))
                    ->sortable(),

                TextColumn::make('fecha')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                TextColumn::make('almacen_origen')
                    ->label('Origen')
                    ->badge()
                    ->color('danger')
                    ->formatStateUsing(fn (?string $state): string =>
                        KardexResource::almacenes()[$state] ?? ($state ?: '—')),

                TextColumn::make('almacen_destino')
                    ->label('Destino')
                    ->badge()
                    ->color('success')
                    ->formatStateUsing(fn (?string $state): string =>
                        KardexResource::almacenes()[$state] ?? ($state ?: '—')),

                TextColumn::make('detalles_count')
                    ->label('Ítems')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('detalles_sum_cantidad')
                    ->label('Cantidad')
                    ->placeholder('0'),

                TextColumn::make('usuario.nombres')
                    ->label('Usuario')
                    ->placeholder('—')
                    ->toggleable(),

                TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn ($state): string => (string) $state === '1' ? 'Activo' : 'Anulado')
                    ->color(fn ($state): string => (string) $state === '1' ? 'success' : 'danger'),

                TextColumn::make('observacion')
                    ->label('Observaciones')
                    ->wrap()
                    ->limit(40)
                    ->placeholder('—')
                    ->searchable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('almacen_origen')
                    ->label('Origen')
                    ->options($almacenes),

                SelectFilter::make('almacen_destino')
                    ->label('Destino')
                    ->options($almacenes),

                Filter::make('fecha_rango')
                    ->label('Rango de fechas')
                    ->form([
                        DatePicker::make('fecha_desde')->label('Desde'),
                        DatePicker::make('fecha_hasta')->label('Hasta'),
                    ])
                    ->query(fn (Builder $q, array $data) => $q
                        ->when($data['fecha_desde'], fn ($q, $v) => $q->whereDate('fecha', '>=', $v))
                        ->when($data['fecha_hasta'], fn ($q, $v) => $q->whereDate('fecha', '<=', $v))
                    ),
            ])
            ->actions([
                ActionGroup::make([
                    Action::make('ver_detalle')
                        ->label('Ver detalle')
                        ->icon('heroicon-m-eye')
                        ->color('info')
                        ->modalHeading(fn (Traslado $record): string =>
                            'Traslado ' . static::numeroDocumento((int) $record->id_traslado))
                        ->modalWidth('7xl')
                        ->modalContent(fn (Traslado $record) => view('filament.modals.traslado-detalle', [
                            'traslado'  => $record,
                            'almacenes' => KardexResource::almacenes(),
                            'lineas'    => DB::table('traslado_detalle as td')
                                ->leftJoin('productos as p', 'p.id_producto', '=', 'td.id_producto')
                                ->where('td.id_traslado', $record->id_traslado)
                                ->orderBy('td.id_detalle')
                                ->get(['td.*', 'p.codigo', 'p.descripcion', 'p.medida', 'p.costo as costo_actual']),
                        ]))
                        ->modalSubmitAction(false)
                        ->modalCancelActionLabel('Cerrar'),

                    Action::make('pdf')
                        ->label('PDF')
                        ->icon('heroicon-m-document-arrow-down')
                        ->color('gray')
                        ->url(fn (Traslado $record): string => route('traslado.pdf', $record->id_traslado))
                        ->openUrlInNewTab(),

                    Action::make('anular')
                        ->label('Anular')
                        ->icon('heroicon-m-no-symbol')
                        ->color('danger')
                        ->visible(fn (Traslado $record): bool => (string) $record->estado === '1')
                        ->requiresConfirmation()
                        ->modalHeading('Anular traslado')
                        ->modalDescription('Se revertirá el stock: los productos vuelven del almacén destino al origen. ¿Confirmás?')
                        ->modalSubmitActionLabel('Sí, anular')
                        ->action(function (Traslado $record): void {
                            try {
                                static::anularTraslado($record);
                                Notification::make()->success()
                                    ->title('Traslado ' . static::numeroDocumento((int) $record->id_traslado) . ' anulado')
                                    ->body('El stock fue devuelto al almacén origen.')
                                    ->send();
                            } catch (\Throwable $e) {
                                Notification::make()->danger()->title('Error al anular')->body($e->getMessage())->send();
                            }
                        }),
                ])->tooltip('Acciones'),
            ])
            ->recordUrl(null)
            ->defaultSort('id_traslado', 'desc');
    }

    /**
     * Motivo de sistema para reversiones de traslado. Se autocrea si no existe
     * (es_sistema = 1 → no se puede eliminar desde la UI de motivos).
     */
    public static function motivoAnulacion(int $emp, string $tipo): int
    {
        return (int) MotivoMovimiento::firstOrCreate(
            ['id_empresa' => $emp, 'nombre' => 'Anulación traslado', 'tipo' => $tipo],
            ['es_sistema' => 1, 'estado' => '1']
        )->id_motivo;
    }

    /**
     * Revierte $cant unidades de una línea: salen del destino y regresan al origen,
     * registrando ambos movimientos con el motivo de sistema "Anulación traslado".
     */
    protected static function revertirStockLinea(Traslado $t, TrasladoDetalle $d, int $cant, int $uid, string $etiqueta = 'Anulación'): void
    {
        $emp     = (int) $t->id_empresa;
        $alm     = KardexResource::almacenes();
        $nomOrig = $alm[$t->almacen_origen] ?? $t->almacen_origen;
        $nomDest = $alm[$t->almacen_destino] ?? $t->almacen_destino;
        $numero  = static::numeroDocumento((int) $t->id_traslado);

        $origen = Producto::where('id_empresa', $emp)
            ->where('id_producto', $d->id_producto)
            ->where('almacen', $t->almacen_origen)
            ->lockForUpdate()
            ->first();

        if (! $origen) {
            throw new \RuntimeException("No se encontró el producto #{$d->id_producto} en el almacén origen.");
        }

        $dest = null;
        if (! empty($origen->codigo)) {
            $dest = Producto::where('id_empresa', $emp)
                ->where('almacen', $t->almacen_destino)
                ->where('codigo', $origen->codigo)
                ->lockForUpdate()
                ->first();
        }

        if (! $dest) {
            throw new \RuntimeException("No se encontró \"{$origen->descripcion}\" en el almacén destino.");
        }

        if ($cant > (int) $dest->cantidad) {
            throw new \RuntimeException("Stock insuficiente en destino para anular \"{$dest->descripcion}\" (disponible: {$dest->cantidad}).");
        }

        // Salida del destino (revierte la entrada)
        $antD   = (int) $dest->cantidad;
        $nuevoD = $antD - $cant;
        $dest->update(['cantidad' => $nuevoD]);
        InventarioMovimiento::create([
            'id_empresa' => $emp, 'almacen' => $t->almacen_destino, 'id_producto' => $dest->id_producto,
            'tipo' => 'S', 'id_motivo' => static::motivoAnulacion($emp, 'S'), 'cantidad' => $cant,
            'stock_anterior' => $antD, 'stock_nuevo' => $nuevoD, 'costo' => $d->costo ?: $dest->costo,
            'observacion' => "{$etiqueta} {$numero}: \"{$origen->descripcion}\" devuelve a {$nomOrig}", 'id_usuario' => $uid, 'fecha' => now(),
        ]);

        // Reingreso al origen (revierte la salida)
        $antO   = (int) $origen->cantidad;
        $nuevoO = $antO + $cant;
        $origen->update(['cantidad' => $nuevoO]);
        InventarioMovimiento::create([
            'id_empresa' => $emp, 'almacen' => $t->almacen_origen, 'id_producto' => $origen->id_producto,
            'tipo' => 'I', 'id_motivo' => static::motivoAnulacion($emp, 'I'), 'cantidad' => $cant,
            'stock_anterior' => $antO, 'stock_nuevo' => $nuevoO, 'costo' => $d->costo ?: $origen->costo,
            'observacion' => "{$etiqueta} {$numero}: \"{$origen->descripcion}\" regresa desde {$nomDest}", 'id_usuario' => $uid, 'fecha' => now(),
        ]);
    }

    /** Anula UNA línea del traslado, devolviendo su stock al origen. */
    public static function anularLinea(int $idDetalle): void
    {
        $emp = (int) session('id_empresa');
        $uid = (int) auth()->user()->usuario_id;

        DB::transaction(function () use ($idDetalle, $emp, $uid): void {
            $d = TrasladoDetalle::where('id_detalle', $idDetalle)->lockForUpdate()->firstOrFail();
            $t = Traslado::where('id_empresa', $emp)->where('id_traslado', $d->id_traslado)->lockForUpdate()->firstOrFail();

            if ((string) $t->estado !== '1') {
                throw new \RuntimeException('El traslado ya está anulado.');
            }
            if ((string) $d->estado !== '1') {
                throw new \RuntimeException('Este producto ya fue anulado.');
            }

            static::revertirStockLinea($t, $d, (int) $d->cantidad, $uid);
            $d->update(['estado' => '0']);

            // Si no queda ninguna línea activa, el documento completo pasa a anulado
            if (! TrasladoDetalle::where('id_traslado', $t->id_traslado)->where('estado', '1')->exists()) {
                $t->update(['estado' => '0']);
            }
        });
    }

    /** Cambia la cantidad de una línea ajustando el stock por la diferencia. */
    public static function editarLinea(int $idDetalle, int $nuevaCantidad): void
    {
        $emp = (int) session('id_empresa');
        $uid = (int) auth()->user()->usuario_id;

        DB::transaction(function () use ($idDetalle, $nuevaCantidad, $emp, $uid): void {
            $d = TrasladoDetalle::where('id_detalle', $idDetalle)->lockForUpdate()->firstOrFail();
            $t = Traslado::where('id_empresa', $emp)->where('id_traslado', $d->id_traslado)->lockForUpdate()->firstOrFail();

            if ((string) $t->estado !== '1') {
                throw new \RuntimeException('El traslado está anulado.');
            }
            if ((string) $d->estado !== '1') {
                throw new \RuntimeException('Este producto está anulado.');
            }
            if ($nuevaCantidad < 1) {
                throw new \RuntimeException('La cantidad debe ser mayor a 0.');
            }

            $diff = $nuevaCantidad - (int) $d->cantidad;
            if ($diff === 0) {
                return;
            }

            if ($diff < 0) {
                // Reduce: revierte parcialmente (motivo de sistema "Anulación traslado")
                static::revertirStockLinea($t, $d, abs($diff), $uid, 'Ajuste');
            } else {
                // Aumenta: transfiere la diferencia adicional del origen al destino
                $alm     = KardexResource::almacenes();
                $nomOrig = $alm[$t->almacen_origen] ?? $t->almacen_origen;
                $nomDest = $alm[$t->almacen_destino] ?? $t->almacen_destino;
                $numero  = static::numeroDocumento((int) $t->id_traslado);

                $origen = Producto::where('id_empresa', $emp)
                    ->where('id_producto', $d->id_producto)
                    ->where('almacen', $t->almacen_origen)
                    ->lockForUpdate()
                    ->first();

                if (! $origen) {
                    throw new \RuntimeException("No se encontró el producto #{$d->id_producto} en el almacén origen.");
                }
                if ($diff > (int) $origen->cantidad) {
                    throw new \RuntimeException("Stock insuficiente de \"{$origen->descripcion}\" en el origen. Disponible: {$origen->cantidad}.");
                }

                $dest = null;
                if (! empty($origen->codigo)) {
                    $dest = Producto::where('id_empresa', $emp)
                        ->where('almacen', $t->almacen_destino)
                        ->where('codigo', $origen->codigo)
                        ->lockForUpdate()
                        ->first();
                }
                if (! $dest) {
                    throw new \RuntimeException("No se encontró \"{$origen->descripcion}\" en el almacén destino.");
                }

                $motSal = MotivoMovimiento::where('id_empresa', $emp)->where('tipo', 'S')->where('nombre', 'Traslado salida')->value('id_motivo');
                $motIng = MotivoMovimiento::where('id_empresa', $emp)->where('tipo', 'I')->where('nombre', 'Traslado entrada')->value('id_motivo');
                $obs    = "Ajuste {$numero}: \"{$origen->descripcion}\" {$d->cantidad} → {$nuevaCantidad}";

                $antO   = (int) $origen->cantidad;
                $nuevoO = $antO - $diff;
                $origen->update(['cantidad' => $nuevoO]);
                InventarioMovimiento::create([
                    'id_empresa' => $emp, 'almacen' => $t->almacen_origen, 'id_producto' => $origen->id_producto,
                    'tipo' => 'S', 'id_motivo' => $motSal, 'cantidad' => $diff,
                    'stock_anterior' => $antO, 'stock_nuevo' => $nuevoO, 'costo' => $d->costo ?: $origen->costo,
                    'observacion' => $obs, 'id_usuario' => $uid, 'fecha' => now(),
                ]);

                $antD   = (int) $dest->cantidad;
                $nuevoD = $antD + $diff;
                $dest->update(['cantidad' => $nuevoD]);
                InventarioMovimiento::create([
                    'id_empresa' => $emp, 'almacen' => $t->almacen_destino, 'id_producto' => $dest->id_producto,
                    'tipo' => 'I', 'id_motivo' => $motIng, 'cantidad' => $diff,
                    'stock_anterior' => $antD, 'stock_nuevo' => $nuevoD, 'costo' => $d->costo ?: $dest->costo,
                    'observacion' => $obs, 'id_usuario' => $uid, 'fecha' => now(),
                ]);
            }

            $d->update([
                'cantidad'            => $nuevaCantidad,
                'stock_nuevo_origen'  => (int) $d->stock_nuevo_origen - $diff,
                'stock_nuevo_destino' => (int) $d->stock_nuevo_destino + $diff,
            ]);
        });
    }

    /** Anula el documento completo: revierte todas las líneas que sigan activas. */
    public static function anularTraslado(Traslado $traslado): void
    {
        $uid = (int) auth()->user()->usuario_id;

        DB::transaction(function () use ($traslado, $uid): void {
            $t = Traslado::where('id_traslado', $traslado->id_traslado)->lockForUpdate()->firstOrFail();

            if ((string) $t->estado !== '1') {
                throw new \RuntimeException('Este traslado ya está anulado.');
            }

            foreach ($t->detalles()->where('estado', '1')->lockForUpdate()->get() as $d) {
                $cant = (int) $d->cantidad;
                if ($cant >= 1) {
                    static::revertirStockLinea($t, $d, $cant, $uid);
                }
                $d->update(['estado' => '0']);
            }

            $t->update(['estado' => '0']);
        });
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('id_empresa', (int) session('id_empresa'))
            ->with(['usuario'])
            // Ítems y cantidades solo de líneas activas (las anuladas no cuentan)
            ->withCount(['detalles as detalles_count' => fn ($q) => $q->where('estado', '1')])
            ->withSum(['detalles as detalles_sum_cantidad' => fn ($q) => $q->where('estado', '1')], 'cantidad');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTraslados::route('/'),
        ];
    }
}
