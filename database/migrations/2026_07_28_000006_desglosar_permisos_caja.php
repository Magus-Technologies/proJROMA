<?php

use App\Models\Rol;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    /**
     * Caja tenía 4 permisos para 6 pantallas y 24 acciones. En la práctica eran
     * dos: caja.ver y caja.gestionar, esta última una llave maestra que abría
     * Gestión de Cajas, Cierres, Transferencias y Métodos de Pago a la vez.
     * Además "Cajas Principales" no tenía ningún control de acceso, y anular un
     * movimiento de caja solo exigía caja.ver.
     *
     * Se desglosa en un permiso por pantalla más uno por acción que mueve o
     * borra dinero.
     */
    private const NUEVOS = [
        'caja.principales'          => 'Ver cajas principales',
        'caja.cierres'              => 'Ver cierres de caja',
        'caja.transferencias'       => 'Ver transferencias de fondos',
        'caja.metodos_pago'         => 'Gestionar bancos, cuentas, tarjetas y billeteras',
        'caja.aperturar'            => 'Aperturar la caja',
        'caja.cerrar'               => 'Cerrar la caja',
        'caja.movimiento_registrar' => 'Registrar ingresos y egresos manuales',
        'caja.movimiento_anular'    => 'Anular un movimiento de caja',
        'caja.cierre_aprobar'       => 'Aprobar o rechazar un cierre',
    ];

    /**
     * Quien administraba la caja sigue administrándola: recibe todo lo que
     * caja.gestionar le daba antes, para que nadie se quede sin trabajar.
     */
    private const HEREDA_DE_GESTIONAR = [
        'caja.principales', 'caja.cierres', 'caja.transferencias', 'caja.metodos_pago',
        'caja.aperturar', 'caja.cerrar', 'caja.movimiento_registrar',
        'caja.movimiento_anular', 'caja.cierre_aprobar',
    ];

    /**
     * Cajas Principales solo comprobaba caja.ver por dentro, así que quien
     * tenía caja.ver ya la veía. Se le conserva ese acceso, y nada más:
     * anular movimientos NO se hereda de caja.ver aunque antes fuera posible,
     * porque eso era precisamente el agujero que se está cerrando.
     */
    private const HEREDA_DE_VER = [
        'caja.principales',
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

            $aOtorgar = collect()
                ->when($tiene->contains('caja.gestionar'), fn ($c) => $c->merge(self::HEREDA_DE_GESTIONAR))
                ->when($tiene->contains('caja.ver'), fn ($c) => $c->merge(self::HEREDA_DE_VER))
                ->unique()
                ->reject(fn (string $p): bool => $tiene->contains($p));

            // El modelo Rol es legacy: implementa syncPermissions() pero no
            // givePermissionTo(), así que se sincroniza la unión.
            if ($aOtorgar->isNotEmpty()) {
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
