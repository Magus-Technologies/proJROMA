<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * La cuenta bancaria es el ÚNICO contenedor de saldo del sistema:
     * billeteras digitales y tarjetas de débito solo son canales de acceso
     * y heredan el saldo de la cuenta a la que apuntan.
     *
     * saldo_inicial + fecha_corte permiten que el saldo calculado cuadre con
     * el estado de cuenta real del banco: se parte del saldo declarado a la
     * fecha de corte y solo se suman los movimientos posteriores.
     * Sin fecha_corte se consideran todos los movimientos registrados.
     */
    public function up(): void
    {
        Schema::table('cuentas_bancarias', function (Blueprint $table) {
            $table->decimal('saldo_inicial', 14, 2)->default(0)->after('moneda');
            $table->date('fecha_corte')->nullable()->after('saldo_inicial');
        });
    }

    public function down(): void
    {
        Schema::table('cuentas_bancarias', function (Blueprint $table) {
            $table->dropColumn(['saldo_inicial', 'fecha_corte']);
        });
    }
};
