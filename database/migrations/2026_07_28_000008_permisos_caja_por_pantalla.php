<?php

use App\Models\Rol;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    /**
     * Caja queda con un permiso de "ver" por pantalla del menú, más los de sus
     * acciones. Antes Mi Caja y Movimientos compartían caja.ver, así que no se
     * podía dar una sin la otra, y varias acciones (activar una caja, asignarle
     * instrumentos, editar una caja principal, crear un método de pago) sólo
     * exigían el permiso de entrar a la pantalla.
     */
    private const NUEVOS = [
        'caja.movimientos_ver'          => 'Ver movimientos de todas las cajas',
        'caja.gestionar_estado'         => 'Activar o desactivar una caja',
        'caja.gestionar_instrumentos'   => 'Asignar métodos de pago a una caja',
        'caja.transferencias_gestionar' => 'Asignar, reasignar, anular y resolver discrepancias',
        'caja.principales_editar'       => 'Editar y crear cajas hijas',
        'caja.principales_estado'       => 'Activar o desactivar una caja principal',
        'caja.metodos_pago_gestionar'   => 'Crear, editar y activar métodos de pago',
    ];

    /** nuevo => permiso que hoy ya habilitaba esa acción */
    private const HEREDA = [
        'caja.movimientos_ver'          => 'caja.ver',
        'caja.gestionar_estado'         => 'caja.gestionar',
        'caja.gestionar_instrumentos'   => 'caja.gestionar',
        'caja.transferencias_gestionar' => 'caja.transferencias',
        'caja.principales_editar'       => 'caja.principales',
        'caja.principales_estado'       => 'caja.principales',
        'caja.metodos_pago_gestionar'   => 'caja.metodos_pago',
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

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        Permission::whereIn('name', array_keys(self::NUEVOS))->delete();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
