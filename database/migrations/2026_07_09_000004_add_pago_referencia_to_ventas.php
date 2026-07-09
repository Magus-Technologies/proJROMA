<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Referencia y comprobante del pago:
 *  - ventas       → para el pago al contado (un solo pago).
 *  - dias_ventas  → para cada cuota del crédito.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            if (! Schema::hasColumn('ventas', 'metodo_pago')) {
                $table->string('metodo_pago', 20)->nullable()->after('id_tipo_pago'); // EFECTIVO|YAPE|PLIN|TRANSFERENCIA|DEPOSITO
            }
            if (! Schema::hasColumn('ventas', 'pago_referencia')) {
                $table->string('pago_referencia', 60)->nullable()->after('metodo_pago');
            }
            if (! Schema::hasColumn('ventas', 'pago_voucher')) {
                $table->string('pago_voucher', 255)->nullable()->after('pago_referencia');
            }
        });

        Schema::table('dias_ventas', function (Blueprint $table) {
            if (! Schema::hasColumn('dias_ventas', 'referencia')) {
                $table->string('referencia', 60)->nullable()->after('tipo_pago');
            }
            if (! Schema::hasColumn('dias_ventas', 'voucher')) {
                $table->string('voucher', 255)->nullable()->after('referencia');
            }
        });
    }

    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->dropColumn(['metodo_pago', 'pago_referencia', 'pago_voucher']);
        });

        Schema::table('dias_ventas', function (Blueprint $table) {
            $table->dropColumn(['referencia', 'voucher']);
        });
    }
};
