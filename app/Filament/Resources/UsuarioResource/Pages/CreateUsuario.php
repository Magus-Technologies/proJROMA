<?php

namespace App\Filament\Resources\UsuarioResource\Pages;

use App\Filament\Resources\UsuarioResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUsuario extends CreateRecord
{
    protected static string $resource = UsuarioResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['id_empresa'] = (int) session('id_empresa');
        $data['sucursal']   = (int) session('sucursal', 1);
        return $data;
    }
}
