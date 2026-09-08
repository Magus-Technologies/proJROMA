<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;

return new class extends Migration
{
    /**
     * Las 6 pantallas del módulo Finanzas no tenían permiso: cualquier usuario
     * autenticado podía abrir Utilidades, Flujo de Caja o Estado de Resultados.
     * Se crea un permiso por pantalla para poder dar acceso al flujo de caja sin
     * dar acceso al estado de resultados, que es el caso real en una empresa.
     *
     * No se asigna a ningún rol: el rol ADMIN pasa siempre por el Gate::before
     * de AppServiceProvider, y el resto se marca desde Administración → Roles.
     */
    private const PERMISOS = [
        'finanzas.utilidades'        => 'Ver utilidades',
        'finanzas.flujo_caja'        => 'Ver flujo de caja',
        'finanzas.estado_resultados' => 'Ver estado de resultados',
        'finanzas.indicadores'       => 'Ver indicadores financieros',
        'finanzas.margenes'          => 'Ver análisis de márgenes',
        'finanzas.costeo'            => 'Ver costeo y rentabilidad',
    ];

    public function up(): void
    {
        foreach (self::PERMISOS as $nombre => $descripcion) {
            Permission::firstOrCreate(
                ['name' => $nombre, 'guard_name' => 'web'],
                ['description' => $descripcion],
            );
        }

        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        Permission::whereIn('name', array_keys(self::PERMISOS))->delete();

        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
