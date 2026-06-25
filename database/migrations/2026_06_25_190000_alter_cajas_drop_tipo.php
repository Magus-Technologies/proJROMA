<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cajas', function (Blueprint $table) {
            $table->dropColumn(['tipo', 'monto_fondo_fijo']);
        });
    }

    public function down(): void
    {
        Schema::table('cajas', function (Blueprint $table) {
            $table->string('tipo', 10)->nullable()->comment('GENERAL|CHICA|VENDEDOR');
            $table->decimal('monto_fondo_fijo', 12, 2)->nullable();
        });
    }
};
