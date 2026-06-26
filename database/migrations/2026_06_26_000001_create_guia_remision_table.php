<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('guia_remision')) return;

        Schema::create('guia_remision', function (Blueprint $table) {
            $table->increments('id_guia_remision');
            $table->unsignedInteger('id_venta');
            $table->date('fecha_emision')->nullable();
            $table->string('dir_llegada', 245)->nullable();
            $table->string('ubigeo', 6)->nullable();
            $table->char('tipo_transporte', 1)->nullable();
            $table->string('ruc_transporte', 45)->nullable();
            $table->string('razon_transporte', 245)->nullable();
            $table->string('vehiculo', 45)->nullable();
            $table->string('chofer_brevete', 45)->nullable();
            $table->char('enviado_sunat', 1)->nullable();
            $table->string('hash', 45)->nullable();
            $table->string('nombre_xml', 245)->nullable();
            $table->string('serie', 4)->nullable();
            $table->integer('numero')->nullable();
            $table->double('peso', 8, 2)->nullable();
            $table->integer('nro_bultos')->nullable();
            $table->char('estado', 1)->nullable();
            $table->unsignedInteger('id_empresa')->nullable();
            $table->unsignedInteger('sucursal')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guia_remision');
    }
};
