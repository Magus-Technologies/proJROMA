<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    /**
     * Permisos que ningún punto del código puede llegar a comprobar:
     *
     *  - permisos.crear/editar/borrar → el catálogo de permisos es de solo
     *    lectura por diseño; esas acciones no existen ni deben existir.
     *  - caja.arqueo, empresas.gestionar, usuarios.gestionar → nunca tuvieron
     *    grupo en el seeder ni pantalla que los use.
     *
     * Un permiso que se puede marcar en un rol pero no controla nada es peor
     * que no tenerlo: hace creer que algo está restringido cuando no lo está.
     */
    private const PERMISOS = [
        'permisos.crear',
        'permisos.editar',
        'permisos.borrar',
        'caja.arqueo',
        'empresas.gestionar',
        'usuarios.gestionar',
    ];

    public function up(): void
    {
        Permission::whereIn('name', self::PERMISOS)->delete();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        foreach (self::PERMISOS as $permiso) {
            Permission::findOrCreate($permiso, 'web');
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
