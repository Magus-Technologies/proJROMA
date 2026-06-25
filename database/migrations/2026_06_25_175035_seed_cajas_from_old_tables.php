<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Obtener empresa/sucursal desde caja_empresa_old_schema
        $combos = DB::table('caja_empresa_old_schema')
            ->select('id_empresa', 'sucursal')
            ->whereNotNull('id_empresa')
            ->distinct()
            ->get();

        foreach ($combos as $c) {
            $existe = DB::table('cajas')
                ->where('id_empresa', $c->id_empresa)
                ->where('sucursal', $c->sucursal)
                ->exists();

            if ($existe) continue;

            // Caja Principal
            $idGeneral = DB::table('cajas')->insertGetId([
                'id_empresa'  => $c->id_empresa,
                'sucursal'    => $c->sucursal,
                'nombre'      => 'Caja Principal',
                'tipo'        => 'GENERAL',
                'saldo_actual'=> 0,
                'moneda'      => 'PEN',
                'estado'      => 'ACTIVA',
            ]);

            // Caja Chica
            $idChica = DB::table('cajas')->insertGetId([
                'id_empresa'    => $c->id_empresa,
                'sucursal'      => $c->sucursal,
                'nombre'        => 'Caja Chica',
                'tipo'          => 'CHICA',
                'id_caja_padre' => $idGeneral,
                'saldo_actual'  => 0,
                'moneda'        => 'PEN',
                'estado'        => 'ACTIVA',
            ]);

            // Migrar caja_empresa_old_schema -> caja_movimientos (Caja Principal)
            $rows = DB::table('caja_empresa_old_schema')
                ->where('id_empresa', $c->id_empresa)
                ->where('sucursal', $c->sucursal)
                ->orderBy('caja_id')
                ->get();

            $saldo = 0;
            foreach ($rows as $r) {
                $monto = (float) ($r->entrada ?: $r->salida ?: 0);
                if ($monto <= 0) continue;
                $tipo = $r->entrada ? 'INGRESO' : 'EGRESO';
                $saldoAnterior = $saldo;
                $saldo = $tipo === 'INGRESO' ? $saldo + $monto : $saldo - $monto;

                DB::table('caja_movimientos')->insert([
                    'id_caja'          => $idGeneral,
                    'fecha'            => $r->fecha ? date('Y-m-d', strtotime($r->fecha)) : now()->toDateString(),
                    'tipo'             => $tipo,
                    'categoria'        => 'MANUAL',
                    'descripcion'      => $r->detalle ?? 'Migrado',
                    'monto'            => $monto,
                    'instrumento_tipo' => $r->instrumento_tipo ?? 'EFECTIVO',
                    'instrumento_id'   => $r->instrumento_id ?? null,
                    'saldo_anterior'   => $saldoAnterior,
                    'saldo_posterior'  => $saldo,
                    'id_usuario'       => $r->id_usuario ?? 1,
                    'estado'           => 'CONFIRMADO',
                ]);
            }

            DB::table('cajas')->where('id', $idGeneral)->update(['saldo_actual' => $saldo]);
        }
    }

    public function down(): void {}
};
