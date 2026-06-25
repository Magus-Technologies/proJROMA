<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('compras', function (Blueprint $table) {
            if (!Schema::hasColumn('compras', 'instrumento_tipo')) {
                $table->string('instrumento_tipo', 30)->nullable()->after('id_tipo_pago')
                      ->comment('EFECTIVO | CUENTA_BANCARIA | TARJETA | BILLETERA_DIGITAL');
                $table->unsignedInteger('instrumento_id')->nullable()->after('instrumento_tipo');
            }
        });
    }

    public function down(): void
    {
        Schema::table('compras', function (Blueprint $table) {
            $table->dropColumn(['instrumento_tipo', 'instrumento_id']);
        });
    }
};
