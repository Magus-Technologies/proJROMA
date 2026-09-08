<?php

namespace App\Filament\Resources\PermissionResource\Pages;

use App\Filament\Resources\PermissionResource;
use Filament\Resources\Pages\ListRecords;

class ListPermissions extends ListRecords
{
    protected static string $resource = PermissionResource::class;

    /** Sin acciones de cabecera: un permiso no se crea desde la interfaz. */
    protected function getHeaderActions(): array
    {
        return [];
    }

    public function getSubheading(): ?string
    {
        return 'Los permisos los define el código y no se crean, editan ni eliminan aquí. '
            . 'Para dar o quitar un permiso, edita el rol en Administración → Roles.';
    }
}
