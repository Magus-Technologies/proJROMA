<?php

namespace App\Filament\Resources\AlmacenStockResource\Pages;

use App\Filament\Resources\AlmacenStockResource;
use App\Models\Almacen;
use App\Models\InventarioMovimiento;
use App\Models\MotivoMovimiento;
use App\Models\Producto;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class ListAlmacenStock extends ListRecords
{
    protected static string $resource = AlmacenStockResource::class;

    protected function almacenes()
    {
        return Almacen::where('id_empresa', (int) session('id_empresa'))
            ->where('estado', 1)
            ->orderBy('id_almacen')
            ->get();
    }

    public function getTabs(): array
    {
        $tabs = [];

        foreach ($this->almacenes() as $almacen) {
            $tabs['alm-' . $almacen->codigo] = Tab::make($almacen->nombre)
                ->modifyQueryUsing(fn (Builder $query) => $query->where('almacen', $almacen->codigo));
        }

        $tabs['todos'] = Tab::make('Todos');

        return $tabs;
    }

    protected function registrarMovimiento(array $data, string $tipo): void
    {
        DB::transaction(function () use ($data, $tipo): void {
            $producto = Producto::where('id_empresa', (int) session('id_empresa'))
                ->where('id_producto', $data['id_producto'])
                ->lockForUpdate()
                ->firstOrFail();

            $anterior = (int) $producto->cantidad;
            $cant     = (int) $data['cantidad'];

            if ($tipo === 'S' && $cant > $anterior) {
                throw new \RuntimeException("Stock insuficiente. Disponible: {$anterior}.");
            }

            $nuevo = $tipo === 'I' ? $anterior + $cant : $anterior - $cant;

            $update = ['cantidad' => $nuevo];
            if ($tipo === 'I' && ! empty($data['costo'])) {
                $update['costo'] = $data['costo'];
            }
            $producto->update($update);

            InventarioMovimiento::create([
                'id_empresa'     => (int) session('id_empresa'),
                'almacen'        => $data['almacen'],
                'id_producto'    => $data['id_producto'],
                'tipo'           => $tipo,
                'id_motivo'      => $data['id_motivo'] ?? null,
                'cantidad'       => $cant,
                'stock_anterior' => $anterior,
                'stock_nuevo'    => $nuevo,
                'costo'          => $data['costo'] ?? null,
                'observacion'    => $data['observacion'] ?? null,
                'id_usuario'     => (int) auth()->user()->usuario_id,
                'fecha'          => now(),
            ]);
        });
    }

    protected function movimientoForm(string $tipo): array
    {
        return [
            Select::make('almacen')
                ->label('Almacén')
                ->options(fn () => $this->almacenes()->pluck('nombre', 'codigo')->toArray())
                ->default(fn (): ?string =>
                    str_starts_with((string) $this->activeTab, 'alm-')
                        ? substr((string) $this->activeTab, 4)
                        : null)
                ->required(),

            Select::make('id_producto')
                ->label('Producto')
                ->options(fn () => Producto::where('id_empresa', (int) session('id_empresa'))
                    ->orderBy('descripcion')
                    ->limit(500)
                    ->pluck('descripcion', 'id_producto')
                    ->toArray())
                ->searchable()
                ->live()
                ->afterStateUpdated(function ($state, callable $set): void {
                    $stock = Producto::where('id_producto', $state)->value('cantidad');
                    $set('stock_actual', (string) (int) $stock);
                })
                ->required(),

            TextInput::make('stock_actual')
                ->label('Stock actual')
                ->disabled()
                ->dehydrated(false),

            TextInput::make('cantidad')
                ->label('Cantidad')
                ->numeric()
                ->integer()
                ->minValue(1)
                ->required(),

            Select::make('id_motivo')
                ->label('Motivo')
                ->options(fn () => MotivoMovimiento::where('id_empresa', (int) session('id_empresa'))
                    ->when($tipo === 'I', fn ($q) => $q->whereIn('tipo', ['I', 'A']))
                    ->when($tipo === 'S', fn ($q) => $q->whereIn('tipo', ['S', 'A']))
                    ->pluck('nombre', 'id_motivo')
                    ->toArray()),

            TextInput::make('costo')
                ->label('Costo')
                ->numeric()
                ->minValue(0)
                ->prefix('S/')
                ->visible($tipo === 'I'),

            TextInput::make('observacion')
                ->label('Observaciones')
                ->maxLength(255),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('ingreso')
                ->label('Ingreso')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->form($this->movimientoForm('I'))
                ->action(function (array $data): void {
                    try {
                        $this->registrarMovimiento($data, 'I');
                        Notification::make()->success()->title('Ingreso registrado')->send();
                    } catch (\Throwable $e) {
                        Notification::make()->danger()->title('Error')->body($e->getMessage())->send();
                    }
                }),

            Action::make('salida')
                ->label('Salida')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('danger')
                ->form($this->movimientoForm('S'))
                ->action(function (array $data): void {
                    try {
                        $this->registrarMovimiento($data, 'S');
                        Notification::make()->success()->title('Salida registrada')->send();
                    } catch (\Throwable $e) {
                        Notification::make()->danger()->title('Error')->body($e->getMessage())->send();
                    }
                }),

            ActionGroup::make([
                Action::make('nuevo_almacen')
                    ->label('Nuevo Almacén')
                    ->icon('heroicon-o-plus')
                    ->form([
                        TextInput::make('nombre')
                            ->label('Nombre')
                            ->required()
                            ->maxLength(150),
                        TextInput::make('codigo')
                            ->label('Código')
                            ->maxLength(50),
                        TextInput::make('descripcion')
                            ->label('Descripción')
                            ->maxLength(255),
                    ])
                    ->action(function (array $data): void {
                        Almacen::create(array_merge($data, [
                            'id_empresa' => (int) session('id_empresa'),
                            'codigo'     => $data['codigo'] ?: (string) (Almacen::where('id_empresa', (int) session('id_empresa'))->max('id_almacen') + 1),
                            'estado'     => 1,
                        ]));
                        Notification::make()->success()->title('Almacén creado')->send();
                    }),

                Action::make('editar_almacen')
                    ->label('Editar Almacén')
                    ->icon('heroicon-o-pencil')
                    ->form(fn (): array => [
                        Select::make('id_almacen')
                            ->label('Almacén')
                            ->options($this->almacenes()->pluck('nombre', 'id_almacen')->toArray())
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set): void {
                                $a = Almacen::find($state);
                                $set('nombre', $a?->nombre);
                                $set('descripcion', $a?->descripcion);
                            })
                            ->required(),
                        TextInput::make('nombre')
                            ->label('Nombre')
                            ->required()
                            ->maxLength(150),
                        TextInput::make('descripcion')
                            ->label('Descripción')
                            ->maxLength(255),
                    ])
                    ->action(function (array $data): void {
                        Almacen::where('id_almacen', $data['id_almacen'])->update([
                            'nombre'      => $data['nombre'],
                            'descripcion' => $data['descripcion'],
                        ]);
                        Notification::make()->success()->title('Almacén actualizado')->send();
                    }),

                Action::make('desactivar_almacen')
                    ->label('Desactivar Almacén')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->form(fn (): array => [
                        Select::make('id_almacen')
                            ->label('Almacén')
                            ->options($this->almacenes()->pluck('nombre', 'id_almacen')->toArray())
                            ->required(),
                    ])
                    ->requiresConfirmation()
                    ->modalDescription('El almacén se desactivará. Los productos y movimientos históricos se conservan.')
                    ->action(function (array $data): void {
                        $almacen = Almacen::findOrFail($data['id_almacen']);

                        $conStock = Producto::where('id_empresa', (int) session('id_empresa'))
                            ->where('almacen', $almacen->codigo)
                            ->where('cantidad', '>', 0)
                            ->exists();

                        if ($conStock) {
                            Notification::make()->danger()
                                ->title('No se puede desactivar')
                                ->body('El almacén tiene productos con stock. Trasládalos primero.')
                                ->send();

                            return;
                        }

                        $almacen->update(['estado' => 0]);
                        Notification::make()->success()->title('Almacén desactivado')->send();
                    }),
            ])
                ->label('Almacenes')
                ->icon('heroicon-o-building-storefront')
                ->button()
                ->color('gray'),
        ];
    }
}
