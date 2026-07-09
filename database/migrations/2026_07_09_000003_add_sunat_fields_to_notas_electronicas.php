<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notas_electronicas', function (Blueprint $table) {
            if (! Schema::hasColumn('notas_electronicas', 'sunat_estado')) {
                $table->string('sunat_estado', 20)->default('pendiente')->after('enviado_sunat'); // pendiente|aceptado|rechazado
            }
            if (! Schema::hasColumn('notas_electronicas', 'sunat_mensaje')) {
                $table->text('sunat_mensaje')->nullable()->after('sunat_estado');
            }
            if (! Schema::hasColumn('notas_electronicas', 'xml_ruta')) {
                $table->string('xml_ruta', 255)->nullable()->after('sunat_mensaje');
            }
            if (! Schema::hasColumn('notas_electronicas', 'cdr_ruta')) {
                $table->string('cdr_ruta', 255)->nullable()->after('xml_ruta');
            }
        });
    }

    public function down(): void
    {
        Schema::table('notas_electronicas', function (Blueprint $table) {
            $table->dropColumn(['sunat_estado', 'sunat_mensaje', 'xml_ruta', 'cdr_ruta']);
        });
    }
};
