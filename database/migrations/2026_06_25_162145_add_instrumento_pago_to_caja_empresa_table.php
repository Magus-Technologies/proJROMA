<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('caja_empresa')) return;
        Schema::table('caja_empresa', function (Blueprint $table) {
            if (!Schema::hasColumn('caja_empresa', 'instrumento_tipo')) {
                $table->string('instrumento_tipo', 30)->nullable();
                $table->unsignedInteger('instrumento_id')->nullable();
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('caja_empresa')) return;
        Schema::table('caja_empresa', function (Blueprint $table) {
            $table->dropColumn(['instrumento_tipo', 'instrumento_id']);
        });
    }
};
