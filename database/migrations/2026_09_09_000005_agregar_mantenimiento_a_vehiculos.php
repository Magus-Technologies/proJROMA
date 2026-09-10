<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

/**
 * Próximo mantenimiento del vehículo, al lado del SOAT y la revisión técnica.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('tms_vehiculos', 'mantenimiento_vence')) {
            Schema::table('tms_vehiculos', function ($table): void {
                $table->date('mantenimiento_vence')->nullable()->after('rev_tecnica_vence');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('tms_vehiculos', 'mantenimiento_vence')) {
            Schema::table('tms_vehiculos', function ($table): void {
                $table->dropColumn('mantenimiento_vence');
            });
        }
    }
};
