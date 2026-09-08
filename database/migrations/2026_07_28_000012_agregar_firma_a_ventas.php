<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Foto de la boleta firmada por el cliente al recibir la mercadería.
     * Sirve como respaldo de la entrega: el cliente firma el documento impreso
     * y esa imagen queda adjunta a la venta.
     */
    public function up(): void
    {
        if (Schema::hasColumn('ventas', 'firma')) {
            return;
        }

        Schema::table('ventas', function (Blueprint $table) {
            $table->string('firma', 255)->nullable()->after('observacion');
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('ventas', 'firma')) {
            return;
        }

        Schema::table('ventas', function (Blueprint $table) {
            $table->dropColumn('firma');
        });
    }
};
