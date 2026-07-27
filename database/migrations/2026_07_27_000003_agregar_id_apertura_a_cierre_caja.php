<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Vincula cada cierre con la apertura de su turno, para poder mostrar
     * los movimientos exactos del turno (apertura → cierre) y cuadrar
     * contra el efectivo esperado de ese turno.
     */
    public function up(): void
    {
        Schema::table('cierre_caja', function (Blueprint $table) {
            $table->unsignedBigInteger('id_apertura')->nullable()->after('id_caja');
        });
    }

    public function down(): void
    {
        Schema::table('cierre_caja', function (Blueprint $table) {
            $table->dropColumn('id_apertura');
        });
    }
};
