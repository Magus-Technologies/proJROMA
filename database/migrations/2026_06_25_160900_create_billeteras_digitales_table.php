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
        Schema::create('billeteras_digitales', function (Blueprint $table) {
            $table->increments('id_billetera');
            $table->unsignedInteger('id_empresa')->index();
            $table->string('tipo', 20);
            $table->string('telefono', 15)->nullable();
            $table->string('titular', 200);
            $table->string('estado', 2)->default('1');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('billeteras_digitales');
    }
};
