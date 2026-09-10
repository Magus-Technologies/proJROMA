<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

/**
 * URL pública del sistema. Es la que se imprime en los comprobantes para que
 * el cliente entre a validar el suyo; hasta ahora salía la del servidor, que
 * en local o detrás de un proxy no es la que el cliente puede abrir.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('empresas', 'web')) {
            Schema::table('empresas', function ($table): void {
                $table->string('web', 191)->nullable()->after('email');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('empresas', 'web')) {
            Schema::table('empresas', function ($table): void {
                $table->dropColumn('web');
            });
        }
    }
};
