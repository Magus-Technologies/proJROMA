<?php

namespace App\Filament\Resources\RoleResource\Pages;

use App\Filament\Resources\RoleResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRole extends CreateRecord
{
    protected static string $resource = RoleResource::class;

    protected function afterCreate(): void
    {
        // Sincronizar siempre, incluso con lista vacía: un rol puede
        // crearse sin ningún permiso marcado.
        $this->record->syncPermissions($this->collectPermissionNames());
    }

    protected function collectPermissionNames(): array
    {
        $data = $this->form->getRawState();
        $names = [];
        foreach ($data as $key => $value) {
            if (str_starts_with($key, 'permisos_') && is_array($value)) {
                $names = array_merge($names, $value);
            }
        }
        return $names;
    }
}
