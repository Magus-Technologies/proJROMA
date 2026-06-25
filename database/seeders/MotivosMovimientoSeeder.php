<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MotivosMovimientoSeeder extends Seeder
{
    /**
     * Motivos base por empresa. Idempotente (updateOrInsert): se puede correr en
     * producción cuantas veces sea necesario sin duplicar.
     *
     * es_sistema = 1  → motivos que el sistema usa automáticamente (Compra, Venta,
     *                   Traslado, Préstamo). NO se pueden eliminar desde la UI.
     * es_sistema = 0  → motivos manuales / de ajuste (editables y borrables).
     */
    public function run(): void
    {
        // [nombre, tipo (I/S), es_sistema]
        $motivos = [
            // ── Automáticos (no eliminables) ──
            ['Compra',              'I', 1],
            ['Traslado entrada',    'I', 1],
            ['Préstamo recibido',   'I', 1],
            ['Venta',               'S', 1],
            ['Traslado salida',     'S', 1],
            ['Préstamo entregado',  'S', 1],
            // ── Manuales / ajustes (editables) ──
            ['Carga inicial',        'I', 0],
            ['Ajuste positivo',      'I', 0],
            ['Devolución de cliente','I', 0],
            ['Ajuste negativo',      'S', 0],
            ['Merma / pérdida',      'S', 0],
            ['Consumo interno',      'S', 0],
        ];

        foreach (DB::table('empresas')->pluck('id_empresa') as $empresa) {
            foreach ($motivos as [$nombre, $tipo, $sistema]) {
                DB::table('motivos_movimiento')->updateOrInsert(
                    ['id_empresa' => $empresa, 'nombre' => $nombre, 'tipo' => $tipo],
                    ['es_sistema' => $sistema, 'estado' => '1']
                );
            }
        }
    }
}
