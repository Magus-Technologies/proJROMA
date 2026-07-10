<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('clientes', 'ubigeo')) {
            Schema::table('clientes', function (Blueprint $table): void {
                $table->char('ubigeo', 6)->nullable()->after('distrito');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('clientes', 'ubigeo')) {
            Schema::table('clientes', function (Blueprint $table): void {
                $table->dropColumn('ubigeo');
            });
        }
    }
};
