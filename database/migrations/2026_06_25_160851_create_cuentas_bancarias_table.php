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
        Schema::create('cuentas_bancarias', function (Blueprint $table) {
            $table->increments('id_cuenta');
            $table->unsignedInteger('id_empresa')->index();
            $table->unsignedInteger('id_banco');
            $table->enum('tipo_cuenta', ['CC', 'CA', 'CTS', 'AHORRO'])->default('CC');
            $table->string('numero_cuenta', 30)->nullable();
            $table->string('cci', 30)->nullable();
            $table->enum('moneda', ['PEN', 'USD'])->default('PEN');
            $table->string('titular', 200);
            $table->string('estado', 2)->default('1');
            $table->timestamps();

            $table->foreign('id_banco')->references('id_banco')->on('bancos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cuentas_bancarias');
    }
};
