<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * El formulario de Empresa tiene un campo "Certificado Digital" desde
     * siempre, pero la tabla nunca tuvo dónde guardarlo. El archivo se subía
     * bien al disco privado y la ruta se descartaba en silencio: Eloquent
     * ignora los atributos que no existen como columna, así que la pantalla
     * decía "Guardado" y al reabrir el campo salía vacío, dejando además
     * archivos huérfanos en storage/app/private/certificados.
     */
    public function up(): void
    {
        if (Schema::hasColumn('empresas', 'certificado')) {
            return;
        }

        Schema::table('empresas', function (Blueprint $table) {
            $table->string('certificado', 255)->nullable()->after('gre_client_secret');
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('empresas', 'certificado')) {
            return;
        }

        Schema::table('empresas', function (Blueprint $table) {
            $table->dropColumn('certificado');
        });
    }
};
