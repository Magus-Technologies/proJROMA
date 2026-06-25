<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Reemplaza la tabla plana por cabecera + detalle
        Schema::dropIfExists('compra_recepciones');

        Schema::create('recepciones', function (Blueprint $t) {
            $t->increments('id_recepcion');
            $t->integer('id_empresa')->index();
            $t->integer('id_compra')->index();
            $t->string('almacen', 50);
            $t->dateTime('fecha');
            $t->string('observacion', 255)->nullable();
            $t->integer('id_usuario')->nullable();
        });

        Schema::create('recepcion_detalle', function (Blueprint $t) {
            $t->increments('id_detalle');
            $t->integer('id_recepcion')->index();
            $t->integer('id_producto');
            $t->integer('cantidad');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recepcion_detalle');
        Schema::dropIfExists('recepciones');
    }
};
