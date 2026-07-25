<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asientos_contables', function (Blueprint $table) {
            $table->id();
            $table->string('numero', 20)->unique();
            $table->date('fecha');
            $table->string('glosa', 500);
            $table->enum('tipo', ['apertura', 'operaciones', 'ajuste', 'cierre'])->default('operaciones');
            $table->enum('estado', ['provisional', 'definitivo', 'anulado'])->default('provisional');
            $table->decimal('total_debe', 14, 2)->default(0);
            $table->decimal('total_haber', 14, 2)->default(0);
            $table->unsignedInteger('user_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asientos_contables');
    }
};
