<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tms_mercados', function (Blueprint $table) {
            $table->string('ubigeo', 6)->nullable()->after('distrito');
        });
    }

    public function down(): void
    {
        Schema::table('tms_mercados', function (Blueprint $table) {
            $table->dropColumn('ubigeo');
        });
    }
};
