<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('caja_cierre_deudas', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('id_cierre');
            $table->unsignedInteger('id_caja');
            $table->unsignedInteger('id_usuario');            // trabajador que debe
            $table->decimal('monto', 12, 2);
            $table->string('estado', 15)->default('PENDIENTE'); // PENDIENTE | DESCONTADO
            $table->string('observaciones', 255)->nullable();
            $table->unsignedInteger('id_usuario_registra')->nullable();
            $table->timestamps();

            $table->index('id_cierre');
            $table->index(['id_usuario', 'estado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('caja_cierre_deudas');
    }
};
