<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * El despacho pasa a repartir VENTAS, no pedidos.
 *
 * Antes solo entraban cotizaciones de tipo pedido ya convertidas, así que una
 * venta hecha directo en mostrador no se podía repartir nunca. Ahora la línea
 * del despacho apunta a la venta; la cotización queda como referencia del
 * pedido que la originó, cuando lo hubo.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('tms_despacho_pedidos', 'id_venta')) {
            Schema::table('tms_despacho_pedidos', function ($table): void {
                $table->unsignedInteger('id_venta')->nullable()->after('id_cotizacion');
                $table->index('id_venta');
            });
        }

        // Las líneas que ya existen apuntan a la cotización: se les completa
        // la venta que esa cotización generó.
        DB::statement("
            UPDATE tms_despacho_pedidos dp
            JOIN cotizaciones c ON c.cotizacion_id = dp.id_cotizacion
            SET dp.id_venta = c.id_venta
            WHERE dp.id_venta IS NULL AND c.id_venta IS NOT NULL
        ");

        // Una venta de mostrador no tiene cotización detrás.
        DB::statement('ALTER TABLE tms_despacho_pedidos MODIFY id_cotizacion INT UNSIGNED NULL');
    }

    public function down(): void
    {
        if (Schema::hasColumn('tms_despacho_pedidos', 'id_venta')) {
            Schema::table('tms_despacho_pedidos', function ($table): void {
                $table->dropIndex(['id_venta']);
                $table->dropColumn('id_venta');
            });
        }
    }
};
