<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('traslado_detalle', function (Blueprint $t) {
            $t->integer('stock_ant_origen')->default(0);
            $t->integer('stock_nuevo_origen')->default(0);
            $t->integer('stock_ant_destino')->default(0);
            $t->integer('stock_nuevo_destino')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('traslado_detalle', function (Blueprint $t) {
            $t->dropColumn(['stock_ant_origen', 'stock_nuevo_origen', 'stock_ant_destino', 'stock_nuevo_destino']);
        });
    }
};
