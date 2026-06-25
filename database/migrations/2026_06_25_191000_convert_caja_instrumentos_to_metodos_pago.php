<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Los métodos de pago asignables a una caja pasan a ser:
     * EFECTIVO, TRANSFERENCIA (cuenta bancaria vinculada) y BILLETERA_DIGITAL.
     * Se renombra el antiguo CUENTA_BANCARIA -> TRANSFERENCIA y se eliminan
     * las asignaciones de TARJETA (ya no es un método válido para la caja).
     */
    public function up(): void
    {
        DB::table('caja_instrumentos')
            ->where('instrumento_tipo', 'CUENTA_BANCARIA')
            ->update(['instrumento_tipo' => 'TRANSFERENCIA']);

        DB::table('caja_instrumentos')
            ->where('instrumento_tipo', 'TARJETA')
            ->delete();
    }

    public function down(): void
    {
        DB::table('caja_instrumentos')
            ->where('instrumento_tipo', 'TRANSFERENCIA')
            ->update(['instrumento_tipo' => 'CUENTA_BANCARIA']);
    }
};
