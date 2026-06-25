<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cajas', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_empresa');
            $table->unsignedInteger('sucursal');
            $table->string('nombre', 100);
            $table->string('tipo', 10)->comment('GENERAL|CHICA|VENDEDOR');
            $table->unsignedInteger('id_usuario_responsable')->nullable();
            $table->unsignedInteger('id_caja_padre')->nullable();
            $table->decimal('monto_fondo_fijo', 12, 2)->nullable();
            $table->decimal('saldo_actual', 14, 2)->default(0);
            $table->string('moneda', 3)->default('PEN');
            $table->string('estado', 10)->default('ACTIVA');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cajas');
    }
};
