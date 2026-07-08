<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            if (! Schema::hasColumn('ventas', 'sunat_estado')) {
                $table->string('sunat_estado', 20)->default('pendiente')->after('enviado_sunat'); // pendiente|aceptado|rechazado
            }
            if (! Schema::hasColumn('ventas', 'sunat_mensaje')) {
                $table->text('sunat_mensaje')->nullable()->after('sunat_estado');
            }
            if (! Schema::hasColumn('ventas', 'hash_cpe')) {
                $table->string('hash_cpe', 100)->nullable()->after('sunat_mensaje');
            }
            if (! Schema::hasColumn('ventas', 'xml_ruta')) {
                $table->string('xml_ruta', 255)->nullable()->after('hash_cpe');
            }
            if (! Schema::hasColumn('ventas', 'cdr_ruta')) {
                $table->string('cdr_ruta', 255)->nullable()->after('xml_ruta');
            }
        });
    }

    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->dropColumn(['sunat_estado', 'sunat_mensaje', 'hash_cpe', 'xml_ruta', 'cdr_ruta']);
        });
    }
};
