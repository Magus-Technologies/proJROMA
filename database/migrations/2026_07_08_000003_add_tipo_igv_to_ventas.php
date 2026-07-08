<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            if (! Schema::hasColumn('ventas', 'tipo_igv')) {
                // gravado | exonerado | inafecto  (aplica a todo el comprobante)
                $table->string('tipo_igv', 12)->default('gravado')->after('apli_igv');
            }
        });
    }

    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->dropColumn('tipo_igv');
        });
    }
};
