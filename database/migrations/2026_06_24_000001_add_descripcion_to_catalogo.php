<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['categorias', 'subcategorias', 'marcas', 'submarcas'] as $tabla) {
            Schema::table($tabla, function (Blueprint $t) {
                $t->string('descripcion', 255)->nullable()->after('nombre');
            });
        }
    }

    public function down(): void
    {
        foreach (['categorias', 'subcategorias', 'marcas', 'submarcas'] as $tabla) {
            Schema::table($tabla, function (Blueprint $t) {
                $t->dropColumn('descripcion');
            });
        }
    }
};
