<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    /**
     * Se retiró el módulo de Contabilidad: ya no existen las pantallas de Plan
     * de Cuentas, Libro Diario, Libro Mayor ni Balance General, así que estos
     * permisos no controlan nada. Se eliminan para que el catálogo de permisos
     * refleje solo lo que el código realmente comprueba.
     *
     * Las tablas plan_cuentas, asientos_contables y asientos_detalle NO se
     * tocan: se conservan con sus datos por si el módulo vuelve.
     */
    private const PERMISOS = [
        'contabilidad.ver',
        'contabilidad.anular',
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
