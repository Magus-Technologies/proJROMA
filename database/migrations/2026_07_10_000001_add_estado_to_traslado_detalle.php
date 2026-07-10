<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('traslado_detalle', 'estado')) {
            Schema::table('traslado_detalle', function (Blueprint $t) {
                // '1' = activo, '0' = anulado (permite anular líneas individuales)
                $t->char('estado', 1)->default('1')->after('stock_nuevo_destino');
            });
        }
    }

    public function down(): void
    {
        Schema::table('traslado_detalle', function (Blueprint $t) {
            $t->dropColumn('estado');
        });
    }
};
