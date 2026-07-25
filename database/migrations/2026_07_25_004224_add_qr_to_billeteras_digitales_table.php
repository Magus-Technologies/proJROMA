<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('billeteras_digitales', function (Blueprint $table) {
            $table->string('qr', 255)->nullable()->after('titular');
        });
    }

    public function down(): void
    {
        Schema::table('billeteras_digitales', function (Blueprint $table) {
            $table->dropColumn('qr');
        });
    }
};
