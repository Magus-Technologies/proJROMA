<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('caja_movimientos', 'referencia')) {
            Schema::table('caja_movimientos', function (Blueprint $table) {
                $table->string('referencia', 60)->nullable()->after('instrumento_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('caja_movimientos', 'referencia')) {
            Schema::table('caja_movimientos', fn (Blueprint $table) => $table->dropColumn('referencia'));
        }
    }
};
