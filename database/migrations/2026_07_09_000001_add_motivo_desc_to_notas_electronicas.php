<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notas_electronicas', function (Blueprint $table) {
            // La columna legacy `motivo` es INT y no puede guardar la descripción.
            // SUNAT exige el texto del motivo (des_motivo) además del código.
            if (! Schema::hasColumn('notas_electronicas', 'motivo_desc')) {
                $table->string('motivo_desc', 255)->nullable()->after('motivo');
            }
        });
    }

    public function down(): void
    {
        Schema::table('notas_electronicas', function (Blueprint $table) {
            $table->dropColumn('motivo_desc');
        });
    }
};
