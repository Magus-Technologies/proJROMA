<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('arqueos_diarios', function (Blueprint $table) {
            if (!Schema::hasColumn('arqueos_diarios', 'id_caja')) {
                $table->unsignedInteger('id_caja')->nullable()->after('arqueo_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('arqueos_diarios', function (Blueprint $table) {
            $table->dropColumn('id_caja');
        });
    }
};
