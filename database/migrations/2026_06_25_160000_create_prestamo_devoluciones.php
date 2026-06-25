<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prestamo_devoluciones', function (Blueprint $t) {
            $t->increments('id_devolucion');
            $t->integer('id_prestamo')->index();
            $t->integer('id_producto');
            $t->integer('cantidad');
            $t->dateTime('fecha');
            $t->integer('id_usuario')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prestamo_devoluciones');
    }
};
