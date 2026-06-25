<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Renombrar tablas viejas (schema incorrecto) ────────────────────
        Schema::rename('caja_empresa', 'caja_empresa_old_schema');
        Schema::rename('ingreso_egreso', 'ingreso_egreso_old_schema');

        // ── Nueva caja_empresa (Registro de Caja) ──────────────────────────
        Schema::create('caja_empresa', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_empresa');
            $table->integer('sucursal');
            $table->date('fecha');
            $table->string('tipo', 20)->comment('INGRESO | EGRESO');
            $table->string('descripcion', 245);
            $table->decimal('monto', 12, 2);
            $table->string('instrumento_tipo', 30)->nullable()
                  ->comment('EFECTIVO | CUENTA_BANCARIA | TARJETA | BILLETERA_DIGITAL');
            $table->unsignedInteger('instrumento_id')->nullable();
            $table->integer('id_usuario')->nullable();
        });

        // ── Nueva ingreso_egreso (Caja Chica) ─────────────────────────────
        Schema::create('ingreso_egreso', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_empresa');
            $table->integer('sucursal');
            $table->date('fecha');
            $table->string('tipo', 20)->comment('INGRESO | EGRESO');
            $table->string('descripcion', 245);
            $table->decimal('monto', 12, 2);
            $table->string('instrumento_tipo', 30)->nullable()
                  ->comment('EFECTIVO | CUENTA_BANCARIA | TARJETA | BILLETERA_DIGITAL');
            $table->unsignedInteger('instrumento_id')->nullable();
            $table->integer('id_usuario')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('caja_empresa');
        Schema::dropIfExists('ingreso_egreso');
        Schema::rename('caja_empresa_old_schema', 'caja_empresa');
        Schema::rename('ingreso_egreso_old_schema', 'ingreso_egreso');
    }
};
