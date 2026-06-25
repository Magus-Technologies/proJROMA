<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('compra_recepciones', function (Blueprint $t) {
            $t->increments('id_recepcion');
            $t->integer('id_compra')->index();
            $t->integer('id_producto');
            $t->integer('cantidad');
            $t->string('almacen', 50);
            $t->dateTime('fecha');
            $t->integer('id_usuario')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('compra_recepciones');
    }
};
