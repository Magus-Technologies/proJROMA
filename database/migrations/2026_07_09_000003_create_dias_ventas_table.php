<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dias_ventas', function (Blueprint $table) {
            $table->integer('dias_venta_id', true)->primary();
            $table->integer('id_venta')->nullable()->index();
            $table->integer('id_usuario')->nullable()->index();
            $table->integer('id_caja_empresa')->nullable();
            $table->decimal('monto', 10, 3)->nullable();
            $table->date('fecha')->nullable();
            $table->char('estado', 1)->default('0');
            $table->string('tipo_pago', 200)->nullable();
            $table->string('referencia', 60)->nullable();
            $table->string('voucher', 255)->nullable();
            $table->datetime('fecha_pago_real')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dias_ventas');
    }
};
