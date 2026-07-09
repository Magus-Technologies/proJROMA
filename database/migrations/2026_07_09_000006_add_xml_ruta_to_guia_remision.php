<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * La guía ya guardaba hash, nombre_xml, ticket y estado GRE, pero nunca el
 * archivo XML en disco. Sin él no se puede revisar el comprobante antes de
 * enviarlo a SUNAT (el flujo GRE es asíncrono: enviás y recién después sabés).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('guia_remision', function (Blueprint $table) {
            if (! Schema::hasColumn('guia_remision', 'xml_ruta')) {
                $table->string('xml_ruta', 255)->nullable()->after('nombre_xml');
            }
            if (! Schema::hasColumn('guia_remision', 'cdr_ruta')) {
                $table->string('cdr_ruta', 255)->nullable()->after('xml_ruta');
            }
        });
    }

    public function down(): void
    {
        Schema::table('guia_remision', function (Blueprint $table) {
            $table->dropColumn(['xml_ruta', 'cdr_ruta']);
        });
    }
};
