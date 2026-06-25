<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('traslados', function (Blueprint $t) {
            $t->increments('id_traslado');
            $t->integer('id_empresa')->index();
            $t->string('almacen_origen', 50);
            $t->string('almacen_destino', 50);
            $t->dateTime('fecha');
            $t->string('observacion', 255)->nullable();
            $t->integer('id_usuario')->nullable();
            $t->char('estado', 1)->default('1');
        });

        Schema::create('traslado_detalle', function (Blueprint $t) {
            $t->increments('id_detalle');
            $t->integer('id_traslado')->index();
            $t->integer('id_producto');
            $t->integer('cantidad');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('traslado_detalle');
        Schema::dropIfExists('traslados');
    }
};
