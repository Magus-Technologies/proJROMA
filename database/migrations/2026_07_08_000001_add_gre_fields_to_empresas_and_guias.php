<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Credenciales GRE (OAuth2) de SUNAT para Guías de Remisión ──────────
        Schema::table('empresas', function (Blueprint $table) {
            if (! Schema::hasColumn('empresas', 'gre_client_id')) {
                $table->string('gre_client_id', 255)->nullable()->after('clave_sol');
            }
            if (! Schema::hasColumn('empresas', 'gre_client_secret')) {
                $table->string('gre_client_secret', 255)->nullable()->after('gre_client_id');
            }
        });

        // ── Campos GRE que exige SUNAT en la guía ──────────────────────────────
        Schema::table('guia_remision', function (Blueprint $table) {
            // Motivo del traslado
            if (! Schema::hasColumn('guia_remision', 'motivo_traslado')) {
                $table->string('motivo_traslado', 2)->default('01')->after('id_venta'); // 01=Venta
            }
            if (! Schema::hasColumn('guia_remision', 'descripcion_motivo')) {
                $table->string('descripcion_motivo', 255)->nullable()->after('motivo_traslado');
            }
            // Traslado / peso
            if (! Schema::hasColumn('guia_remision', 'fecha_traslado')) {
                $table->date('fecha_traslado')->nullable()->after('fecha_emision');
            }
            if (! Schema::hasColumn('guia_remision', 'und_peso_total')) {
                $table->string('und_peso_total', 5)->default('KGM')->after('peso');
            }
            // Punto de partida (origen)
            if (! Schema::hasColumn('guia_remision', 'ubigeo_partida')) {
                $table->string('ubigeo_partida', 6)->nullable()->after('und_peso_total');
            }
            if (! Schema::hasColumn('guia_remision', 'dir_partida')) {
                $table->string('dir_partida', 255)->nullable()->after('ubigeo_partida');
            }
            // Conductor (transporte privado)
            if (! Schema::hasColumn('guia_remision', 'conductor_tipo_doc')) {
                $table->string('conductor_tipo_doc', 1)->nullable()->after('chofer_brevete');
            }
            if (! Schema::hasColumn('guia_remision', 'conductor_documento')) {
                $table->string('conductor_documento', 15)->nullable()->after('conductor_tipo_doc');
            }
            if (! Schema::hasColumn('guia_remision', 'conductor_nombres')) {
                $table->string('conductor_nombres', 150)->nullable()->after('conductor_documento');
            }
            if (! Schema::hasColumn('guia_remision', 'conductor_apellidos')) {
                $table->string('conductor_apellidos', 150)->nullable()->after('conductor_nombres');
            }
            if (! Schema::hasColumn('guia_remision', 'conductor_licencia')) {
                $table->string('conductor_licencia', 30)->nullable()->after('conductor_apellidos');
            }
            // Transportista (transporte público)
            if (! Schema::hasColumn('guia_remision', 'transportista_nro_mtc')) {
                $table->string('transportista_nro_mtc', 30)->nullable()->after('razon_transporte');
            }
            // Resultado del envío a SUNAT
            if (! Schema::hasColumn('guia_remision', 'estado_gre')) {
                $table->string('estado_gre', 20)->default('pendiente')->after('enviado_sunat'); // pendiente|enviado|aceptado|rechazado
            }
            if (! Schema::hasColumn('guia_remision', 'ticket_sunat')) {
                $table->string('ticket_sunat', 100)->nullable()->after('estado_gre');
            }
            if (! Schema::hasColumn('guia_remision', 'codigo_sunat')) {
                $table->string('codigo_sunat', 20)->nullable()->after('ticket_sunat');
            }
            if (! Schema::hasColumn('guia_remision', 'mensaje_sunat')) {
                $table->text('mensaje_sunat')->nullable()->after('codigo_sunat');
            }
            if (! Schema::hasColumn('guia_remision', 'cdr_url')) {
                $table->string('cdr_url', 255)->nullable()->after('mensaje_sunat');
            }
        });
    }

    public function down(): void
    {
        Schema::table('empresas', function (Blueprint $table) {
            $table->dropColumn(['gre_client_id', 'gre_client_secret']);
        });

        Schema::table('guia_remision', function (Blueprint $table) {
            $table->dropColumn([
                'motivo_traslado', 'descripcion_motivo', 'fecha_traslado', 'und_peso_total',
                'ubigeo_partida', 'dir_partida',
                'conductor_tipo_doc', 'conductor_documento', 'conductor_nombres',
                'conductor_apellidos', 'conductor_licencia', 'transportista_nro_mtc',
                'estado_gre', 'ticket_sunat', 'codigo_sunat', 'mensaje_sunat', 'cdr_url',
            ]);
        });
    }
};
