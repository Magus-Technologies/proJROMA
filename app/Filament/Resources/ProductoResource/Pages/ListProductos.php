<?php
namespace App\Filament\Resources\ProductoResource\Pages;
use App\Filament\Resources\ProductoResource;
use App\Filament\Resources\ProductoResource\Widgets\ProductoStats;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
class ListProductos extends ListRecords {
    protected static string $resource = ProductoResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            ProductoStats::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->mutateDataUsing(function (array $data): array {
                    $data['id_empresa'] = (int) session('id_empresa');
                    $data['sucursal']   = (int) session('sucursal', 1);

                    // Columnas NOT NULL heredadas del sistema legacy que el form no pide
                    $data['ultima_salida'] = $data['ultima_salida'] ?? now()->toDateString();
                    $data['codsunat']      = $data['codsunat'] ?? '-';

                    return $data;
                }),
        ];
    }
}
