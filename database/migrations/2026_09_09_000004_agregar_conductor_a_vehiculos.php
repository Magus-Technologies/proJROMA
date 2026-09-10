<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

/**
 * Conductor a cargo del vehículo: quién lo tiene asignado habitualmente.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('tms_vehiculos', 'id_conductor')) {
            Schema::table('tms_vehiculos', function ($table): void {
                $table->unsignedBigInteger('id_conductor')->nullable()->after('id_tipo');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('tms_vehiculos', 'id_conductor')) {
            Schema::table('tms_vehiculos', function ($table): void {
                $table->dropColumn('id_conductor');
            });
        }
    }
};
