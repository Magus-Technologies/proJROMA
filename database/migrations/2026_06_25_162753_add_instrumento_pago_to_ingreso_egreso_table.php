<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('ingreso_egreso')) return;
        Schema::table('ingreso_egreso', function (Blueprint $table) {
            if (!Schema::hasColumn('ingreso_egreso', 'instrumento_tipo')) {
                $table->string('instrumento_tipo', 30)->nullable();
                $table->unsignedInteger('instrumento_id')->nullable();
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('ingreso_egreso')) return;
        Schema::table('ingreso_egreso', function (Blueprint $table) {
            $table->dropColumn(['instrumento_tipo', 'instrumento_id']);
        });
    }
};
