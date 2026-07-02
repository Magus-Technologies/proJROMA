<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Siembra tms_mercados a partir de los valores existentes en clientes.mercado,
 * preservando el número como id para que clientes.mercado = tms_mercados.id.
 * Los nombres/direcciones se completan luego desde el CRUD de Mercados.
 */
return new class extends Migration
{
    public function up(): void
    {
        $rows = DB::table('clientes')
            ->where('mercado', '>', 0)
            ->select('mercado', DB::raw('MIN(id_empresa) as id_empresa'))
            ->groupBy('mercado')
            ->get();

        foreach ($rows as $r) {
            DB::table('tms_mercados')->insertOrIgnore([
                'id'         => $r->mercado,
                'id_empresa' => $r->id_empresa,
                'sucursal'   => 1,
                'nombre'     => 'Mercado ' . $r->mercado,
                'direccion'  => '(por definir)',
                'estado'     => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        // No se revierte el sembrado para no perder ediciones posteriores del usuario.
    }
};
