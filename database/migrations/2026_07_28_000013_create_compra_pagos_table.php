<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Pago mixto de una compra: una fila por método usado.
     *
     * Antes la compra guardaba un único instrumento en compras.instrumento_tipo,
     * así que no se podía pagar una parte en efectivo y otra por transferencia.
     * Es la misma estructura que venta_pagos, para que compras y ventas se
     * traten igual.
     */
    public function up(): void
    {
        if (Schema::hasTable('compra_pagos')) {
            return;
        }

        Schema::create('compra_pagos', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('id_compra')->index();
            $table->string('metodo_pago', 40);
            $table->decimal('monto', 10, 2);
            $table->string('referencia', 60)->nullable();
            $table->longText('comprobantes')->nullable();
            $table->unsignedInteger('id_movimiento_caja')->nullable();
            $table->unsignedInteger('id_usuario')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('compra_pagos');
    }
};
