<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Cada línea de cotización registra cuándo fue agregada, para poder
 * distinguir los aumentos posteriores (mismo producto, nueva línea)
 * y despachar solo lo faltante.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('productos_cotis', function (Blueprint $table) {
            $table->dateTime('fecha_registro')->nullable()->after('presenta_cnt');
            $table->unsignedInteger('id_usuario')->nullable()->after('fecha_registro');
        });

        // Las líneas existentes heredan la fecha de registro de su cotización.
        DB::statement('
            UPDATE productos_cotis pc
            JOIN cotizaciones c ON c.cotizacion_id = pc.id_coti
            SET pc.fecha_registro = COALESCE(c.fecha_registro, c.fecha),
                pc.id_usuario     = c.id_usuario
        ');
    }

    public function down(): void
    {
        Schema::table('productos_cotis', function (Blueprint $table) {
            $table->dropColumn(['fecha_registro', 'id_usuario']);
        });
    }
};
