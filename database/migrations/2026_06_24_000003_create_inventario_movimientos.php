<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('motivos_movimiento', function (Blueprint $t) {
            $t->increments('id_motivo');
            $t->string('nombre', 120);
            $t->char('tipo', 1);              // I = ingreso, S = salida
            $t->tinyInteger('es_sistema')->default(0);
            $t->integer('id_empresa')->index();
            $t->char('estado', 1)->default('1');
        });

        Schema::create('inventario_movimientos', function (Blueprint $t) {
            $t->increments('id_movimiento');
            $t->integer('id_empresa')->index();
            $t->string('almacen', 50);        // código de almacén (coincide con productos.almacen)
            $t->integer('id_producto')->index();
            $t->char('tipo', 1);              // I / S
            $t->integer('id_motivo')->nullable();
            $t->integer('cantidad');
            $t->integer('stock_anterior')->default(0);
            $t->integer('stock_nuevo')->default(0);
            $t->decimal('costo', 12, 4)->nullable();
            $t->integer('id_proveedor')->nullable();
            $t->string('observacion', 255)->nullable();
            $t->integer('id_usuario')->nullable();
            $t->dateTime('fecha');
        });

        // Sembrar motivos por empresa
        $ingresos = ['Carga inicial', 'Compra', 'Ajuste positivo', 'Devolución de cliente', 'Traslado entrada'];
        $salidas  = ['Venta', 'Ajuste negativo', 'Merma / pérdida', 'Traslado salida', 'Consumo interno'];
        $sistema  = ['Compra', 'Venta'];

        foreach (DB::table('empresas')->pluck('id_empresa') as $emp) {
            foreach ($ingresos as $n) {
                DB::table('motivos_movimiento')->insert(['nombre' => $n, 'tipo' => 'I', 'es_sistema' => in_array($n, $sistema) ? 1 : 0, 'id_empresa' => $emp, 'estado' => '1']);
            }
            foreach ($salidas as $n) {
                DB::table('motivos_movimiento')->insert(['nombre' => $n, 'tipo' => 'S', 'es_sistema' => in_array($n, $sistema) ? 1 : 0, 'id_empresa' => $emp, 'estado' => '1']);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('inventario_movimientos');
        Schema::dropIfExists('motivos_movimiento');
    }
};
