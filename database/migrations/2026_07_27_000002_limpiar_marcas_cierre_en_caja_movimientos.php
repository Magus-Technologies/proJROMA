<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Elimina las "marcas" de CIERRE y CUADRE del historial de movimientos.
     * Eran filas de monto 0 que no movían dinero (el cierre real vive en la
     * tabla cierre_caja) y solo ensuciaban la pantalla de Movimientos.
     * Borrarlas no altera ningún saldo.
     */
    public function up(): void
    {
        DB::table('caja_movimientos')
            ->whereIn('categoria', ['CIERRE', 'CUADRE'])
            ->where('monto', 0)
            ->delete();
    }

    public function down(): void
    {
        // No restaurable: eran marcas informativas sin efecto en saldos.
    }
};
