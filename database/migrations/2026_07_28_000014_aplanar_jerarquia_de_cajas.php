<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Las cajas dejan de tener jerarquía: ya no hay principales ni hijas, solo
 * cajas. Se elimina la columna que las relacionaba y los permisos de la
 * pantalla "Cajas Principales", que desaparece.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('cajas', 'id_caja_padre')) {
            // Soltar la FK si existe antes de tirar la columna.
            foreach (DB::select("
                SELECT CONSTRAINT_NAME AS nombre
                FROM information_schema.KEY_COLUMN_USAGE
                WHERE TABLE_SCHEMA = DATABASE()
                  AND TABLE_NAME = 'cajas'
                  AND COLUMN_NAME = 'id_caja_padre'
                  AND REFERENCED_TABLE_NAME IS NOT NULL
            ") as $fk) {
                DB::statement("ALTER TABLE `cajas` DROP FOREIGN KEY `{$fk->nombre}`");
            }

            Schema::table('cajas', function ($table): void {
                $table->dropColumn('id_caja_padre');
            });
        }

        $permisos = ['caja.principales', 'caja.principales_editar', 'caja.principales_estado'];

        $ids = DB::table('permissions')->whereIn('name', $permisos)->pluck('id');

        if ($ids->isNotEmpty()) {
            DB::table('role_has_permissions')->whereIn('permission_id', $ids)->delete();
            DB::table('model_has_permissions')->whereIn('permission_id', $ids)->delete();
            DB::table('permissions')->whereIn('id', $ids)->delete();
        }

        app()['cache']->forget('spatie.permission.cache');
    }

    public function down(): void
    {
        if (! Schema::hasColumn('cajas', 'id_caja_padre')) {
            Schema::table('cajas', function ($table): void {
                $table->unsignedBigInteger('id_caja_padre')->nullable()->after('sucursal');
            });
        }
    }
};
