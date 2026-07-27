<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Los cierres PENDIENTES registrados con la lógica antigua guardaron
     * como saldo_sistema el acumulado histórico de la caja (absurdo para
     * un cuadre de turno). Se recalculan contra su apertura: fondo +
     * ingresos − egresos del turno, y se vincula id_apertura.
     * Los cierres ya aprobados/rechazados no se tocan (su historia,
     * ajustes y deudas ya se generaron con los números de su momento).
     */
    public function up(): void
    {
        $cierres = DB::table('cierre_caja')
            ->where('estado', 'PENDIENTE')
            ->whereNull('id_apertura')
            ->get();

        foreach ($cierres as $cierre) {
            $apertura = DB::table('caja_aperturas')
                ->where('id_caja', $cierre->id_caja)
                ->when($cierre->created_at, fn ($q) => $q->where('created_at', '<=', $cierre->created_at))
                ->orderByDesc('id')
                ->first();

            if (! $apertura) {
                continue;
            }

            $saldoTurno = (float) DB::table('caja_movimientos')
                ->where('id_caja', $cierre->id_caja)
                ->where('estado', 'CONFIRMADO')
                ->where('created_at', '>=', $apertura->created_at)
                ->when($cierre->created_at, fn ($q) => $q->where('created_at', '<=', $cierre->created_at))
                ->selectRaw("COALESCE(SUM(CASE WHEN tipo = 'INGRESO' THEN monto ELSE -monto END), 0) as s")
                ->value('s');

            DB::table('cierre_caja')->where('id', $cierre->id)->update([
                'id_apertura'   => $apertura->id,
                'saldo_sistema' => $saldoTurno,
                'updated_at'    => now(),
            ]);
        }
    }

    public function down(): void
    {
        // No restaurable: los valores antiguos eran incorrectos.
    }
};
