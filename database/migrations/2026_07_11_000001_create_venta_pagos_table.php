<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Pagos de una venta desglosados por método (pago mixto).
 *
 * Una venta al contado puede saldarse con varios métodos a la vez
 * (efectivo + Yape + transferencia). Cada fila es un método con su monto
 * y sus comprobantes. `id_dias_venta` distingue el origen:
 *   - NULL  → pago de una venta al contado.
 *   - valor → pago aplicado a una cuota de crédito (dias_ventas).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('venta_pagos')) {
            return;
        }

        Schema::create('venta_pagos', function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->integer('id_venta')->index();
            $table->integer('id_dias_venta')->nullable()->index();
            $table->string('metodo_pago', 40)->default('EFECTIVO');
            $table->decimal('monto', 10, 2)->default(0);
            $table->string('referencia', 60)->nullable();
            // Rutas de 0 a 3 comprobantes (capturas del pago) en el disco public.
            $table->json('comprobantes')->nullable();
            $table->integer('id_movimiento_caja')->nullable();
            $table->integer('id_usuario')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('venta_pagos');
    }
};
