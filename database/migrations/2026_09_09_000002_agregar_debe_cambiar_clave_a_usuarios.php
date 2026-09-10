<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

/**
 * Marca a los usuarios que entraron con una clave inicial puesta por el
 * administrador: al iniciar sesión no pueden hacer nada más que cambiarla.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('usuarios', 'debe_cambiar_clave')) {
            Schema::table('usuarios', function ($table): void {
                $table->boolean('debe_cambiar_clave')->default(false)->after('clave');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('usuarios', 'debe_cambiar_clave')) {
            Schema::table('usuarios', function ($table): void {
                $table->dropColumn('debe_cambiar_clave');
            });
        }
    }
};
