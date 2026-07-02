<?php

namespace App\Filament\Resources\PrestamoResource\Pages;

use App\Filament\Resources\PrestamoResource;
use App\Models\Almacen;
use App\Models\Producto;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListPrestamos extends ListRecords
{
    protected static string $resource = PrestamoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('nuevo_prestamo')
                ->label('Nuevo Préstamo')
                ->icon('heroicon-o-plus')
                ->color('primary')
                ->form([
                    Select::make('tipo')
                        ->label('Tipo')
                        ->options([
                            'P' => 'Presté (sale stock)',
                            'R' => 'Me prestaron (entra stock)',
                        ])
                        ->default('P')
                        ->required(),

                    TextInput::make('tercero')
                        ->label('Tercero')
                        ->required()
                        ->maxLength(150),

                    Select::make('almacen')
                        ->label('Almacén')
                        ->options(fn () => Almacen::where('id_empresa', (int) session('id_empresa'))
                            ->where('estado', 1)
                            ->pluck('nombre', 'codigo')
                            ->toArray())
                        ->required(),

                    Repeater::make('detalles')
                        ->label('Productos')
                        ->schema([
                            Select::make('id_producto')
                                ->label('Producto')
                                ->options(fn () => Producto::where('id_empresa', (int) session('id_empresa'))
                                    ->orderBy('descripcion')
                                    ->limit(500)
                                    ->get()
                                    ->mapWithKeys(fn (Producto $p) => [
                                        $p->id_producto => "{$p->descripcion} (stock: {$p->cantidad})",
                                    ])
                                    ->toArray())
                                ->searchable()
                                ->required()
                                ->columnSpan(2),

                            TextInput::make('cantidad')
                                ->label('Cantidad')
                                ->numeric()
                                ->integer()
                                ->minValue(1)
                                ->required(),
                        ])
                        ->columns(3)
                        ->minItems(1)
                        ->defaultItems(1),

                    TextInput::make('observacion')
                        ->label('Observación')
                        ->maxLength(200),
                ])
                ->action(function (array $data): void {
                    try {
                        $prestamo = PrestamoResource::crearPrestamo($data);
                        Notification::make()->success()
                            ->title("Préstamo #{$prestamo->id_prestamo} registrado")
                            ->send();
                    } catch (\Throwable $e) {
                        Notification::make()->danger()->title('Error en préstamo')->body($e->getMessage())->send();
                    }
                }),
        ];
    }
}
