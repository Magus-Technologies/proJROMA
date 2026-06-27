<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('caja_aperturas', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_caja');
            $table->date('fecha');
            $table->decimal('monto_total', 14, 2)->default(0);
            $table->string('estado', 20)->default('ABIERTA')->comment('ABIERTA|CERRADA');
            $table->unsignedInteger('id_usuario_apertura');
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->foreign('id_caja')->references('id')->on('cajas');
        });

        Schema::create('caja_apertura_detalles', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_apertura');
            $table->decimal('denominacion', 10, 2);
            $table->string('tipo', 10)->comment('BILLETE|MONEDA');
            $table->unsignedInteger('cantidad');
            $table->decimal('subtotal', 14, 2);

            $table->foreign('id_apertura')->references('id')->on('caja_aperturas')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('caja_apertura_detalles');
        Schema::dropIfExists('caja_aperturas');
    }
};
