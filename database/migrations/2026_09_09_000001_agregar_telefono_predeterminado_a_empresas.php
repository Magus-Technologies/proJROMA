<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Cuál de los tres teléfonos de la empresa es el que sale impreso en los PDF.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('empresas', 'telefono_predeterminado')) {
            Schema::table('empresas', function ($table): void {
                $table->string('telefono_predeterminado', 10)->nullable()->after('telefono3');
            });
        }

        // Las empresas que ya existen siguen imprimiendo el primer teléfono.
        DB::table('empresas')->whereNull('telefono_predeterminado')->update([
            'telefono_predeterminado' => 'telefono',
        ]);
    }

    public function down(): void
    {
        if (Schema::hasColumn('empresas', 'telefono_predeterminado')) {
            Schema::table('empresas', function ($table): void {
                $table->dropColumn('telefono_predeterminado');
            });
        }
    }
};
