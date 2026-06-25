<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('caja_movimientos', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_caja');
            $table->date('fecha');
            $table->string('tipo', 10)->comment('INGRESO|EGRESO');
            $table->string('categoria', 30)
                  ->comment('VENTA|COMPRA|GASTO_OP|REPOSICION|RENDICION|AJUSTE|APERTURA|MANUAL');
            $table->string('descripcion', 245)->nullable();
            $table->decimal('monto', 12, 2);
            $table->string('instrumento_tipo', 30)->nullable();
            $table->unsignedInteger('instrumento_id')->nullable();
            $table->decimal('saldo_anterior', 14, 2)->default(0);
            $table->decimal('saldo_posterior', 14, 2)->default(0);
            $table->string('origen_tipo', 50)->nullable();
            $table->unsignedInteger('origen_id')->nullable();
            $table->unsignedInteger('id_usuario');
            $table->string('estado', 15)->default('CONFIRMADO')->comment('CONFIRMADO|ANULADO');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('caja_movimientos');
    }
};
