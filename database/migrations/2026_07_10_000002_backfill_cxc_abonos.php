<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Los cobros históricos (cuotas pagadas antes del sistema de abonos)
     * se registran como un abono ACTIVO por el total, para que Mis Cobros
     * y el historial de abonos muestren todo en un solo libro.
     */
    public function up(): void
    {
        $pagadas = DB::table('dias_ventas as dv')
            ->leftJoin('cxc_abonos as a', 'a.id_dias_venta', '=', 'dv.dias_venta_id')
            ->leftJoin('ventas as v', 'v.id_venta', '=', 'dv.id_venta')
            ->where('dv.estado', '1')
            ->whereNull('a.id')
            ->select('dv.dias_venta_id', 'dv.id_venta', 'dv.monto', 'dv.tipo_pago',
                'dv.referencia', 'dv.fecha', 'dv.fecha_pago_real', 'dv.id_usuario', 'v.id_vendedor')
            ->get();

        // Fechas legacy pueden venir como 0000-00-00: usar la primera válida
        $fechaValida = function (?string $f): ?string {
            return ($f && ! str_starts_with($f, '0000')) ? substr($f, 0, 10) : null;
        };

        foreach ($pagadas->chunk(500) as $lote) {
            DB::table('cxc_abonos')->insert($lote->map(fn ($c) => [
                'id_dias_venta'      => $c->dias_venta_id,
                'id_venta'           => $c->id_venta,
                'fecha'              => $fechaValida($c->fecha_pago_real) ?? $fechaValida($c->fecha) ?? now()->toDateString(),
                'monto'              => $c->monto,
                'metodo_pago'        => mb_substr($c->tipo_pago ?: 'EFECTIVO', 0, 20),
                'referencia'         => $c->referencia,
                'id_movimiento_caja' => null, // histórico: su movimiento ya existe aparte
                'id_usuario'         => $c->id_usuario ?: ($c->id_vendedor ?: 0),
                'estado'             => 'ACTIVO',
                'created_at'         => now(),
                'updated_at'         => now(),
            ])->toArray());
        }
    }

    public function down(): void
    {
        DB::table('cxc_abonos')->whereNull('id_movimiento_caja')->delete();
    }
};
