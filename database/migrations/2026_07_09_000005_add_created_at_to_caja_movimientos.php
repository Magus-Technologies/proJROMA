<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('caja_movimientos', 'created_at')) {
            DB::statement('ALTER TABLE caja_movimientos ADD created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP');

            // Movimientos históricos: usar su fecha (a medianoche) como referencia
            DB::statement('UPDATE caja_movimientos SET created_at = fecha WHERE created_at IS NULL');
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('caja_movimientos', 'created_at')) {
            Schema::table('caja_movimientos', fn ($table) => $table->dropColumn('created_at'));
        }
    }
};
