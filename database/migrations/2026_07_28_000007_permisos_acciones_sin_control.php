<?php

use App\Models\Rol;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    /**
     * Acciones que se ejecutaban sin comprobar ningún permiso: enviar a SUNAT,
     * anular comprobantes, anular ajustes/traslados/despachos, importar en
     * masa, perdonar una deuda de cierre y ver los códigos QR de cobro.
     *
     * Bastaba con tener acceso a la pantalla para hacer cualquiera de ellas,
     * aunque varias son irreversibles o tienen efecto tributario.
     */
    private const NUEVOS = [
        'ventas.sunat'             => 'Enviar a SUNAT, regenerar XML y descargar CDR',
        'guias.sunat'              => 'Enviar a SUNAT, regenerar XML y descargar CDR',
        'guias.anular'             => 'Anular guías de remisión',
        'notas.sunat'              => 'Enviar a SUNAT, regenerar XML y descargar CDR',
        'notas.anular'             => 'Anular notas electrónicas',
        'cotizaciones.anular'      => 'Anular cotizaciones',
        'almacen_ajustes.anular'   => 'Anular ajustes',
        'almacen_traslados.anular' => 'Anular traslados',
        'tms_despachos.cerrar'     => 'Cerrar despachos',
        'tms_despachos.anular'     => 'Anular despachos',
        'clientes.importar'        => 'Importar clientes desde Excel',
        'productos.importar'       => 'Importar productos desde Excel',
        'caja.cancelar_deuda'      => 'Cancelar (perdonar) una deuda de cierre',
        'qr.ver'                   => 'Ver códigos QR de cobro',
    ];

    /**
     * Quien ya podía hacer la acción la conserva: cada permiso nuevo se otorga
     * a los roles que tienen el permiso del que dependía en la práctica.
     * nuevo => permiso que hoy habilitaba esa acción
     */
    private const HEREDA = [
        'ventas.sunat'             => 'ventas.crear',
        'guias.sunat'              => 'guias.crear',
        'guias.anular'             => 'guias.crear',
        'notas.sunat'              => 'notas.crear',
        'notas.anular'             => 'notas.crear',
        'cotizaciones.anular'      => 'cotizaciones.editar',
        'almacen_ajustes.anular'   => 'almacen_ajustes.crear',
        'almacen_traslados.anular' => 'almacen_traslados.crear',
        'tms_despachos.cerrar'     => 'tms_despachos.editar',
        'tms_despachos.anular'     => 'tms_despachos.editar',
        'clientes.importar'        => 'clientes.crear',
        'productos.importar'       => 'productos.crear',
        'caja.cancelar_deuda'      => 'caja.cierre_aprobar',
        'qr.ver'                   => 'caja.ver',
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
