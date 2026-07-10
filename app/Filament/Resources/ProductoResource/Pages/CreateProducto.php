<?php
namespace App\Filament\Resources\ProductoResource\Pages;
use App\Filament\Resources\ProductoResource;
use Filament\Resources\Pages\CreateRecord;
class CreateProducto extends CreateRecord
{
    protected static string $resource = ProductoResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['id_empresa'] = (int) session('id_empresa');
        $data['sucursal']   = (int) session('sucursal', 1);

        // Columnas NOT NULL heredadas del sistema legacy que el form no pide
        $data['ultima_salida'] = $data['ultima_salida'] ?? now()->toDateString();
        $data['codsunat']      = $data['codsunat'] ?? '-';

        return $data;
    }
}
