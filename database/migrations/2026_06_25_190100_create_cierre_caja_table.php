<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Opcional: Eliminar tabla antigua de rendiciones
        Schema::dropIfExists('caja_chica_rendiciones');

        Schema::create('cierre_caja', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_caja');
            $table->date('fecha');
            $table->decimal('saldo_declarado', 14, 2);
            $table->decimal('saldo_sistema', 14, 2);
            $table->text('desglose_instrumentos')->nullable();
            $table->string('estado', 20)->default('PENDIENTE');
            $table->unsignedInteger('id_usuario_cierra');
            $table->unsignedInteger('id_usuario_aprueba')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cierre_caja');
    }
};
