<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cxc_abonos', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('id_dias_venta');
            $table->unsignedInteger('id_venta');
            $table->date('fecha');
            $table->decimal('monto', 12, 2);
            $table->string('metodo_pago', 20)->default('EFECTIVO');
            $table->string('referencia', 60)->nullable();
            $table->unsignedInteger('id_movimiento_caja')->nullable();
            $table->unsignedInteger('id_usuario');
            $table->string('estado', 10)->default('ACTIVO'); // ACTIVO | ANULADO
            $table->string('motivo_anulacion', 200)->nullable();
            $table->timestamps();

            $table->index('id_dias_venta');
            $table->index('id_venta');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cxc_abonos');
    }
};
