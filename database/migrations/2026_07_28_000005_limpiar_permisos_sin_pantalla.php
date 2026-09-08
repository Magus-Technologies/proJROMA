<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    /**
     * Última tanda de permisos que no controlan nada:
     *
     *  - reportes.ver          → no existe pantalla de reportes en el panel.
     *  - reportes_compras.pdf  → el PDF de compra se controla con compras.pdf,
     *                            tanto el botón como la ruta. Este quedaba duplicado.
     *  - productos.exportar    → productos no tiene exportación (sí importación).
     *  - cobranzas_deudas.ver  → no existe pantalla de reporte de deudas.
     */
    private const PERMISOS = [
        'reportes.ver',
        'reportes_compras.pdf',
        'productos.exportar',
        'cobranzas_deudas.ver',
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
