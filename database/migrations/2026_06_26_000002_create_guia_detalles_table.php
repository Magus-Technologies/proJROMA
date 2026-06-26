<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('guia_detalles')) return;

        Schema::create('guia_detalles', function (Blueprint $table) {
            $table->increments('guia_detalle_id');
            $table->unsignedInteger('id_guia')->nullable()->index();
            $table->unsignedInteger('id_producto')->nullable();
            $table->string('detalles', 200)->nullable();
            $table->string('unidad', 10)->nullable();
            $table->integer('cantidad')->nullable();
            $table->double('precio', 20, 5)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guia_detalles');
    }
};
