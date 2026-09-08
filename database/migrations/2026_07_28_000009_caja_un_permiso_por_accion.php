<?php

use App\Models\Rol;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    /**
     * Regla del módulo Caja: cada submódulo lleva su permiso de ver más UNO por
     * cada acción propia. Quedaban tres desvíos:
     *
     *  - Movimientos usaba caja.movimiento_registrar, que es de Mi Caja: no se
     *    podía dar de alta un movimiento en una pantalla sin darlo en la otra.
     *  - Asignaciones de Fondo comprimía asignar, reasignar, anular, rechazar y
     *    resolver discrepancias en un único caja.transferencias_gestionar.
     *  - Métodos de Pago comprimía crear, editar y activar en uno solo.
     */
    private const NUEVOS = [
        'caja.movimientos_registrar'       => 'Registrar ingresos y egresos desde Movimientos',
        'caja.cierres_consolidado'         => 'Generar el cuadre consolidado',
        'caja.transferencias_asignar'      => 'Asignar fondos a una caja',
        'caja.transferencias_reasignar'    => 'Reasignar una asignación',
        'caja.transferencias_anular'       => 'Anular una asignación',
        'caja.transferencias_rechazar'     => 'Rechazar una asignación',
        'caja.transferencias_discrepancia' => 'Resolver una discrepancia',
        'caja.metodos_pago_crear'          => 'Crear métodos de pago',
        'caja.metodos_pago_editar'         => 'Editar métodos de pago',
        'caja.metodos_pago_estado'         => 'Activar o desactivar un método de pago',
    ];

    /** nuevo => permiso que hasta ahora habilitaba esa acción */
    private const HEREDA = [
        'caja.movimientos_registrar'       => 'caja.movimiento_registrar',
        'caja.cierres_consolidado'         => 'caja.cierres',
        'caja.transferencias_asignar'      => 'caja.transferencias_gestionar',
        'caja.transferencias_reasignar'    => 'caja.transferencias_gestionar',
        'caja.transferencias_anular'       => 'caja.transferencias_gestionar',
        'caja.transferencias_rechazar'     => 'caja.transferencias_gestionar',
        'caja.transferencias_discrepancia' => 'caja.transferencias_gestionar',
        'caja.metodos_pago_crear'          => 'caja.metodos_pago_gestionar',
        'caja.metodos_pago_editar'         => 'caja.metodos_pago_gestionar',
        'caja.metodos_pago_estado'         => 'caja.metodos_pago_gestionar',
    ];

    /** Los genéricos que quedan sin uso una vez repartidas sus acciones. */
    private const OBSOLETOS = [
        'caja.transferencias_gestionar',
        'caja.metodos_pago_gestionar',
    ];

    public function up(): void
    {
        foreach (self::NUEVOS as $nombre => $descripcion) {
            Permission::firstOrCreate(
                ['name' => $nombre, 'guard_name' => 'web'],
                ['description' => $descripcion],
            );
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (Rol::with('permissions')->get() as $rol) {
            $tiene = $rol->permissions->pluck('name');

            $aOtorgar = collect(self::HEREDA)
                ->filter(fn (string $origen, string $nuevo): bool =>
                    $tiene->contains($origen) && ! $tiene->contains($nuevo))
                ->keys();

            if ($aOtorgar->isNotEmpty()) {
                // Rol es legacy: tiene syncPermissions() pero no givePermissionTo()
                $rol->syncPermissions($tiene->merge($aOtorgar)->unique()->all());
            }
        }

        Permission::whereIn('name', self::OBSOLETOS)->delete();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        Permission::whereIn('name', array_keys(self::NUEVOS))->delete();

        foreach (self::OBSOLETOS as $permiso) {
            Permission::findOrCreate($permiso, 'web');
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
