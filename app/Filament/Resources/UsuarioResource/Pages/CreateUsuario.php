<?php

namespace App\Filament\Resources\UsuarioResource\Pages;

use App\Filament\Resources\UsuarioResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUsuario extends CreateRecord
{
    protected static string $resource = UsuarioResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['id_empresa']   = (int) session('id_empresa');
        $data['sucursal']     = (int) session('sucursal', 1);
        // Columnas NOT NULL sin default heredadas del esquema viejo
        $data['fecha_inicio'] = $data['fecha_inicio'] ?? now()->toDateString();
        $data['fecha_salida'] = $data['fecha_salida'] ?? '2030-12-31';
        $data['funciones']    = $data['funciones'] ?? '';
        return $data;
    }
}
