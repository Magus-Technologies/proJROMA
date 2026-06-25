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
        Schema::create('tarjetas', function (Blueprint $table) {
            $table->increments('id_tarjeta');
            $table->unsignedInteger('id_empresa')->index();
            $table->unsignedInteger('id_banco');
            $table->unsignedInteger('id_cuenta_bancaria')->nullable();
            $table->enum('tipo', ['CREDITO', 'DEBITO'])->default('DEBITO');
            $table->enum('marca', ['VISA', 'MASTERCARD', 'AMEX', 'DINERS'])->default('VISA');
            $table->string('ultimos_4', 4);
            $table->string('titular', 200);
            $table->date('fecha_vencimiento')->nullable();
            $table->string('estado', 2)->default('1');
            $table->timestamps();

            $table->foreign('id_banco')->references('id_banco')->on('bancos')->onDelete('cascade');
            $table->foreign('id_cuenta_bancaria')->references('id_cuenta')->on('cuentas_bancarias')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tarjetas');
    }
};
