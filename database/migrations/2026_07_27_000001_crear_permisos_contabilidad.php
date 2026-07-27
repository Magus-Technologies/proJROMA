<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    /** @var string[] */
    private array $permisos = [
        'contabilidad.ver',
        'contabilidad.anular',
    ];

    public function up(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach ($this->permisos as $permiso) {
            Permission::findOrCreate($permiso, 'web');
        }
    }

    public function down(): void
    {
        Permission::whereIn('name', $this->permisos)->delete();
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
