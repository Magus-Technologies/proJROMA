<?php

namespace App\Filament\Resources\RoleResource\Pages;

use App\Filament\Resources\RoleResource;
use Database\Seeders\PermissionSeeder;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditRole extends EditRecord
{
    protected static string $resource = RoleResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $record = $this->record;
        $assigned = $record->permissions->pluck('name')->toArray();
        $groups = PermissionSeeder::groups();

        foreach ($groups as $groupLabel => $permissions) {
            $fieldKey = RoleResource::sanitizeKey($groupLabel);
            $groupPerms = array_keys($permissions);
            $data[$fieldKey] = array_intersect($groupPerms, $assigned);
        }

        return $data;
    }

    protected function afterSave(): void
    {
        $permissions = $this->collectPermissionNames();
        if (!empty($permissions)) {
            $this->record->syncPermissions($permissions);
        }
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
