<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('caja_chica_rendiciones', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_caja');
            $table->date('periodo_inicio');
            $table->date('periodo_fin')->nullable();
            $table->decimal('monto_fondo', 12, 2);
            $table->decimal('total_gastado', 12, 2)->default(0);
            $table->string('estado', 25)->default('ABIERTA')
                  ->comment('ABIERTA|PENDIENTE_APROBACION|APROBADA');
            $table->unsignedInteger('id_usuario_solicita')->nullable();
            $table->unsignedInteger('id_usuario_aprueba')->nullable();
            $table->unsignedInteger('id_movimiento_reposicion')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('caja_chica_rendiciones');
    }
};
