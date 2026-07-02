<?php

namespace App\Filament\Resources\ProveedorResource\Pages;

use App\Filament\Resources\ProveedorResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProveedor extends CreateRecord
{
    protected static string $resource = ProveedorResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['id_empresa']       = (int) session('id_empresa');
        $data['fecha_create']     = now();
        $data['estado']           = 1;
        $data['nombre_comercial'] = $data['nombre_comercial'] ?? '';
        $data['direccion']        = $data['direccion'] ?? '';
        $data['direccion2']       = '';
        $data['telefono']         = $data['telefono'] ?? '';
        $data['telefono2']        = '';
        $data['email']            = $data['email'] ?? '';

        return $data;
    }
}
