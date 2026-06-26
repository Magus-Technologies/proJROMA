<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('notas_electronicas', function (Blueprint $table) {
            if (!Schema::hasColumn('notas_electronicas', 'tipo'))
                $table->string('tipo', 10)->nullable()->after('id_venta');
            if (!Schema::hasColumn('notas_electronicas', 'total'))
                $table->decimal('total', 10, 2)->default(0)->nullable()->after('numero');
            if (!Schema::hasColumn('notas_electronicas', 'enviado_sunat'))
                $table->string('enviado_sunat', 2)->default('0')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('notas_electronicas', function (Blueprint $table) {
            $table->dropColumn(array_filter(['tipo', 'total', 'enviado_sunat'], fn($c) => Schema::hasColumn('notas_electronicas', $c)));
        });
    }
};
