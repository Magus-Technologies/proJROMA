<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('compras', function (Blueprint $t) {
            $t->tinyInteger('recepcionado')->default(0)->after('total');
        });

        // Las compras históricas ya tienen su stock cargado → marcarlas como recepcionadas
        DB::table('compras')->update(['recepcionado' => 1]);
    }

    public function down(): void
    {
        Schema::table('compras', function (Blueprint $t) {
            $t->dropColumn('recepcionado');
        });
    }
};
