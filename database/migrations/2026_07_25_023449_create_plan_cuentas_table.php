<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plan_cuentas', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique();
            $table->string('nombre', 200);
            $table->enum('tipo', ['activo', 'pasivo', 'patrimonio', 'ingreso', 'costo', 'gasto']);
            $table->unsignedTinyInteger('nivel')->default(1);
            $table->foreignId('padre_id')->nullable()->constrained('plan_cuentas')->nullOnDelete();
            $table->boolean('estado')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plan_cuentas');
    }
};
