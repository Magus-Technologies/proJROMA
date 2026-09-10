<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Permisos de alcance: sin ellos el usuario solo ve lo suyo (sus ventas, sus
 * pedidos, las deudas de sus ventas, las compras que él registró).
 *
 * Los roles que hoy ven el módulo lo reciben activado, así nadie pierde de
 * vista lo que ya veía; el administrador lo desmarca a quien corresponda.
 */
return new class extends Migration
{
    /** permiso nuevo => permiso que ya debe tener el rol para heredarlo */
    private const PERMISOS = [
        'ventas.ver_todas'       => ['ventas.ver',       'Ver las ventas de todos los vendedores'],
        'cotizaciones.ver_todas' => ['cotizaciones.ver', 'Ver las cotizaciones y pedidos de todos'],
        'cobranzas.ver_todas'    => ['cobranzas.ver',    'Ver las cuentas por cobrar de todos'],
        'pagos.ver_todas'        => ['pagos.ver',        'Ver las cuentas por pagar de todos'],
    ];

    public function up(): void
    {
        // Quién registró la compra: sin esto no hay forma de saber cuáles
        // son "sus" cuentas por pagar.
        if (! Schema::hasColumn('compras', 'id_usuario')) {
            Schema::table('compras', function ($table): void {
                $table->unsignedBigInteger('id_usuario')->nullable()->after('id_empresa');
            });
        }

        foreach (self::PERMISOS as $nombre => [$base, $descripcion]) {
            $id = DB::table('permissions')->where('name', $nombre)->value('id');

            if (! $id) {
                $id = DB::table('permissions')->insertGetId([
                    'name'        => $nombre,
                    'guard_name'  => 'web',
                    'description' => $descripcion,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
            }

            $idBase = DB::table('permissions')->where('name', $base)->value('id');

            if (! $idBase) {
                continue;
            }

            $roles = DB::table('role_has_permissions')
                ->where('permission_id', $idBase)
                ->pluck('role_id');

            foreach ($roles as $rol) {
                DB::table('role_has_permissions')->updateOrInsert([
                    'permission_id' => $id,
                    'role_id'       => $rol,
                ]);
            }
        }

        app()['cache']->forget('spatie.permission.cache');
    }

    public function down(): void
    {
        $ids = DB::table('permissions')->whereIn('name', array_keys(self::PERMISOS))->pluck('id');

        if ($ids->isNotEmpty()) {
            DB::table('role_has_permissions')->whereIn('permission_id', $ids)->delete();
            DB::table('model_has_permissions')->whereIn('permission_id', $ids)->delete();
            DB::table('permissions')->whereIn('id', $ids)->delete();
        }

        if (Schema::hasColumn('compras', 'id_usuario')) {
            Schema::table('compras', function ($table): void {
                $table->dropColumn('id_usuario');
            });
        }

        app()['cache']->forget('spatie.permission.cache');
    }
};
