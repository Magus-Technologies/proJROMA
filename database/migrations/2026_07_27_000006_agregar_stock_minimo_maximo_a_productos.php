<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Stock mínimo y máximo POR FILA de producto (= por almacén, ya que
     * cada producto tiene una fila por almacén). El mínimo alimenta la
     * alerta de Bajo Stock; el máximo es referencia de tope para compras.
     */
    public function up(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->unsignedInteger('stock_minimo')->default(0)->after('cantidad');
            $table->unsignedInteger('stock_maximo')->nullable()->after('stock_minimo');
        });

        // Los productos existentes conservan el comportamiento anterior:
        // la alerta saltaba con stock <= 5.
        DB::table('productos')->update(['stock_minimo' => 5]);
    }

    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropColumn(['stock_minimo', 'stock_maximo']);
        });
    }
};
