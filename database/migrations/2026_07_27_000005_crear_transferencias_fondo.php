<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Asignación de fondos bóveda → caja hija. El fondo de apertura de una
     * caja ya no "nace de la nada": sale de una caja principal mediante una
     * asignación trazable que el cajero confirma al contar el efectivo.
     */
    public function up(): void
    {
        Schema::create('transferencias_fondo', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_caja_origen');
            $table->unsignedBigInteger('id_caja_destino');
            $table->unsignedInteger('id_usuario_asigna');
            $table->unsignedInteger('id_usuario_cajero');
            $table->decimal('monto', 12, 2);
            $table->decimal('monto_contado', 12, 2)->nullable();
            $table->string('estado', 15)->default('ASIGNADA'); // ASIGNADA | APLICADA | RECHAZADA | ANULADA
            $table->string('discrepancia_estado', 15)->nullable(); // PENDIENTE | RESUELTA
            $table->string('discrepancia_resolucion', 20)->nullable(); // AJUSTE_BOVEDA | PERDIDA
            $table->unsignedInteger('id_usuario_resuelve')->nullable();
            $table->unsignedBigInteger('id_movimiento_egreso')->nullable();
            $table->string('observaciones', 255)->nullable();
            $table->timestamps();

            $table->index(['id_caja_destino', 'estado']);
        });

        Schema::table('caja_aperturas', function (Blueprint $table) {
            $table->unsignedBigInteger('id_transferencia')->nullable()->after('id_caja');
        });
    }

    public function down(): void
    {
        Schema::table('caja_aperturas', function (Blueprint $table) {
            $table->dropColumn('id_transferencia');
        });
        Schema::dropIfExists('transferencias_fondo');
    }
};
