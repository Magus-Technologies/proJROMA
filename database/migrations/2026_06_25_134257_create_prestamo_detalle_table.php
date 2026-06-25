<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('prestamo_detalle', function (Blueprint $table) {
            $table->unsignedInteger('id_detalle')->autoIncrement();
            $table->unsignedInteger('id_prestamo');
            $table->integer('id_producto');
            $table->integer('cantidad');
            $table->text('observacion')->nullable();
            $table->timestamps();

            $table->foreign('id_prestamo')->references('id_prestamo')->on('prestamos')->onDelete('cascade');
            $table->index(['id_prestamo']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prestamo_detalle');
    }
};
