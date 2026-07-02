<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PrestamoResource\Pages;
use App\Models\InventarioMovimiento;
use App\Models\MotivoMovimiento;
use App\Models\Prestamo;
use App\Models\Producto;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class PrestamoResource extends Resource
{
    protected static ?string $model = Prestamo::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-hand-raised';
    protected static ?string $navigationLabel = 'Préstamos';
    protected static string|\UnitEnum|null $navigationGroup = 'Almacén';
    protected static ?int $navigationSort = 7;
    protected static ?string $label = 'Préstamo';
    protected static ?string $pluralLabel = 'Préstamos';
    protected static ?string $slug = 'prestamos';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id_prestamo')
                    ->label('N°')
                    ->sortable(),

                TextColumn::make('fecha')
                    ->label('Fecha')
                    ->dateTime('d/m/Y')
                    ->sortable(),

                TextColumn::make('tipo')
                    ->label('Tipo')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => $state === 'P' ? 'Presté' : 'Me prestaron')
                    ->color(fn (string $state): string => $state === 'P' ? 'danger' : 'success'),

                TextColumn::make('tercero')
                    ->label('Tercero')
                    ->searchable()
                    ->wrap()
                    ->limit(35),

                TextColumn::make('almacen')
                    ->label('Almacén')
                    ->formatStateUsing(fn (?string $state): string =>
                        KardexResource::almacenes()[$state] ?? ($state ?: '—')),

                TextColumn::make('items')
                    ->label('Ítems')
                    ->badge()
                    ->getStateUsing(fn (Prestamo $record): int =>
                        DB::table('prestamo_detalle')->where('id_prestamo', $record->id_prestamo)->count()),

                TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'P' => 'Pendiente',
                        'X' => 'Parcial',
                        'D' => 'Devuelto',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'P' => 'danger',
                        'X' => 'warning',
                        'D' => 'success',
                        default => 'gray',
                    }),
            ])
            ->filters([
                SelectFilter::make('estado')
                    ->label('Estado')
                    ->options([
                        'P' => 'Pendiente',
                        'X' => 'Parcial',
                        'D' => 'Devuelto',
                    ]),

                SelectFilter::make('tipo')
                    ->label('Tipo')
                    ->options([
                        'P' => 'Presté',
                        'R' => 'Me prestaron',
                    ]),
            ])
            ->actions([
                Action::make('detalle')
                    ->label('Detalle')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->modalHeading(fn (Prestamo $record): string => "Préstamo #{$record->id_prestamo} — {$record->tercero}")
                    ->modalContent(fn (Prestamo $record) => view('filament.modals.recepcion-detalle', [
                        'lineas' => DB::table('prestamo_detalle as d')
                            ->join('productos as p', 'p.id_producto', '=', 'd.id_producto')
                            ->where('d.id_prestamo', $record->id_prestamo)
                            ->select('p.codigo', 'p.descripcion as producto', 'p.medida as unidad', 'd.cantidad')
                            ->get(),
                    ]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Cerrar'),

                Action::make('devolver')
                    ->label('Devolver')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('warning')
                    ->visible(fn (Prestamo $record): bool => $record->estado !== 'D')
                    ->modalHeading(fn (Prestamo $record): string => "Devolución — Préstamo #{$record->id_prestamo}")
                    ->fillForm(fn (Prestamo $record): array => [
                        'detalles' => static::lineasPendientes($record)
                            ->map(fn ($l) => [
                                'id_producto' => $l->id_producto,
                                'producto'    => "{$l->producto} (pendiente: {$l->pendiente})",
                                'pendiente'   => $l->pendiente,
                                'cantidad'    => $l->pendiente,
                            ])
                            ->values()
                            ->toArray(),
                    ])
                    ->form([
                        Repeater::make('detalles')
                            ->label('Líneas a devolver')
                            ->schema([
                                Hidden::make('id_producto'),
                                Hidden::make('pendiente'),
                                TextInput::make('producto')
                                    ->label('Producto')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->columnSpan(2),
                                TextInput::make('cantidad')
                                    ->label('Cantidad a devolver')
                                    ->numeric()
                                    ->integer()
                                    ->minValue(0)
                                    ->required(),
                            ])
                            ->columns(3)
                            ->addable(false)
                            ->deletable(false)
                            ->reorderable(false),
                    ])
                    ->action(function (Prestamo $record, array $data): void {
                        try {
                            $estado = static::devolver($record, $data['detalles']);
                            Notification::make()->success()
                                ->title('Devolución registrada')
                                ->body($estado === 'D' ? 'Préstamo totalmente devuelto.' : 'Devolución parcial registrada.')
                                ->send();
                        } catch (\Throwable $e) {
                            Notification::make()->danger()->title('Error en devolución')->body($e->getMessage())->send();
                        }
                    }),
            ])
            ->defaultSort('id_prestamo', 'desc');
    }

    public static function lineasPendientes(Prestamo $record)
    {
        $rows = DB::table('prestamo_detalle as d')
            ->join('productos as p', 'p.id_producto', '=', 'd.id_producto')
            ->where('d.id_prestamo', $record->id_prestamo)
            ->select('d.id_producto', 'p.descripcion as producto', 'd.cantidad as prestado')
            ->get();

        return $rows->map(function ($r) use ($record) {
            $devuelto = (int) DB::table('prestamo_devoluciones')
                ->where('id_prestamo', $record->id_prestamo)
                ->where('id_producto', $r->id_producto)
                ->sum('cantidad');
            $r->pendiente = (int) $r->prestado - $devuelto;

            return $r;
        })->filter(fn ($r) => $r->pendiente > 0);
    }

    protected static function mover(int $emp, string $tipoMov, int $idProducto, int $cant, string $motivo, string $obs, int $uid, string $almacen): void
    {
        $p = Producto::where('id_empresa', $emp)->where('id_producto', $idProducto)->lockForUpdate()->firstOrFail();
        $ant = (int) $p->cantidad;

        if ($tipoMov === 'S' && $cant > $ant) {
            throw new \RuntimeException("Stock insuficiente de \"{$p->descripcion}\" (disponible: {$ant}).");
        }

        $nuevo = $tipoMov === 'I' ? $ant + $cant : $ant - $cant;
        $p->update(['cantidad' => $nuevo]);

        $idMotivo = MotivoMovimiento::where('id_empresa', $emp)->where('tipo', $tipoMov)->where('nombre', $motivo)->value('id_motivo');
        InventarioMovimiento::create([
            'id_empresa' => $emp, 'almacen' => $almacen, 'id_producto' => $idProducto, 'tipo' => $tipoMov,
            'id_motivo' => $idMotivo, 'cantidad' => $cant, 'stock_anterior' => $ant, 'stock_nuevo' => $nuevo,
            'costo' => $p->costo, 'observacion' => $obs, 'id_usuario' => $uid, 'fecha' => now(),
        ]);
    }

    public static function crearPrestamo(array $data): Prestamo
    {
        $emp = (int) session('id_empresa');
        $uid = (int) auth()->user()->usuario_id;

        return DB::transaction(function () use ($data, $emp, $uid): Prestamo {
            // id_producto/cantidad are legacy NOT NULL header columns: they mirror
            // the first line and the total quantity (details live in prestamo_detalle)
            $prestamo = Prestamo::create([
                'id_empresa'  => $emp,
                'tipo'        => $data['tipo'],
                'tercero'     => $data['tercero'],
                'almacen'     => $data['almacen'],
                'estado'      => 'P',
                'observacion' => $data['observacion'] ?? null,
                'id_usuario'  => $uid,
                'fecha'       => now(),
                'id_producto' => (int) $data['detalles'][0]['id_producto'],
                'cantidad'    => array_sum(array_map(fn ($l) => (int) $l['cantidad'], $data['detalles'])),
            ]);

            foreach ($data['detalles'] as $linea) {
                $cant = (int) $linea['cantidad'];

                if ($data['tipo'] === 'P') {
                    static::mover($emp, 'S', (int) $linea['id_producto'], $cant, 'Préstamo entregado', "Préstamo a {$data['tercero']}", $uid, $data['almacen']);
                } else {
                    static::mover($emp, 'I', (int) $linea['id_producto'], $cant, 'Préstamo recibido', "Préstamo de {$data['tercero']}", $uid, $data['almacen']);
                }

                DB::table('prestamo_detalle')->insert([
                    'id_prestamo' => $prestamo->id_prestamo,
                    'id_producto' => $linea['id_producto'],
                    'cantidad'    => $cant,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
            }

            return $prestamo;
        });
    }

    public static function devolver(Prestamo $record, array $detalles): string
    {
        $emp = (int) session('id_empresa');
        $uid = (int) auth()->user()->usuario_id;

        return DB::transaction(function () use ($record, $detalles, $emp, $uid): string {
            $pr = Prestamo::where('id_empresa', $emp)
                ->where('id_prestamo', $record->id_prestamo)
                ->lockForUpdate()
                ->firstOrFail();

            if ($pr->estado === 'D') {
                throw new \RuntimeException('Este préstamo ya está totalmente devuelto.');
            }

            foreach ($detalles as $linea) {
                $cant = (int) $linea['cantidad'];
                if ($cant < 1) {
                    continue;
                }

                $prestado = (int) DB::table('prestamo_detalle')->where('id_prestamo', $pr->id_prestamo)->where('id_producto', $linea['id_producto'])->sum('cantidad');
                $yaDev    = (int) DB::table('prestamo_devoluciones')->where('id_prestamo', $pr->id_prestamo)->where('id_producto', $linea['id_producto'])->sum('cantidad');
                $pendiente = $prestado - $yaDev;

                if ($cant > $pendiente) {
                    throw new \RuntimeException("No puedes devolver más de lo pendiente ({$pendiente}).");
                }

                if ($pr->tipo === 'P') {
                    static::mover($emp, 'I', (int) $linea['id_producto'], $cant, 'Préstamo recibido', "Devolución de {$pr->tercero}", $uid, $pr->almacen);
                } else {
                    static::mover($emp, 'S', (int) $linea['id_producto'], $cant, 'Préstamo entregado', "Devolución a {$pr->tercero}", $uid, $pr->almacen);
                }

                DB::table('prestamo_devoluciones')->insert([
                    'id_prestamo' => $pr->id_prestamo,
                    'id_producto' => $linea['id_producto'],
                    'cantidad'    => $cant,
                    'fecha'       => now(),
                    'id_usuario'  => $uid,
                ]);
            }

            $totalPrestado = (int) DB::table('prestamo_detalle')->where('id_prestamo', $pr->id_prestamo)->sum('cantidad');
            $totalDevuelto = (int) DB::table('prestamo_devoluciones')->where('id_prestamo', $pr->id_prestamo)->sum('cantidad');
            $estado = $totalDevuelto >= $totalPrestado ? 'D' : ($totalDevuelto > 0 ? 'X' : 'P');
            $pr->update(['estado' => $estado, 'fecha_devolucion' => $estado === 'D' ? now() : null]);

            return $estado;
        });
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('id_empresa', (int) session('id_empresa'));
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPrestamos::route('/'),
        ];
    }
}
