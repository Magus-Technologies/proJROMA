<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Maestro de mercados ────────────────────────────────────────────
        Schema::create('tms_mercados', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_empresa');
            $table->unsignedInteger('sucursal');
            $table->string('nombre', 120);
            $table->string('direccion', 245);
            $table->string('referencia', 245)->nullable();
            $table->string('distrito', 120)->nullable();
            $table->string('telefono', 20)->nullable();
            $table->tinyInteger('estado')->default(1);
            $table->timestamps();
            $table->index(['id_empresa', 'sucursal']);
        });

        // ── Maestro de vehículos (flota) ───────────────────────────────────
        Schema::create('tms_vehiculos', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_empresa');
            $table->unsignedInteger('sucursal');
            $table->string('placa', 15);
            $table->string('tipo', 15)->default('CAMIONETA')->comment('CAMIONETA|FURGONETA|CAMION|MOTO|OTRO');
            $table->string('marca', 60)->nullable();
            $table->string('modelo', 60)->nullable();
            $table->smallInteger('anio')->nullable();
            $table->decimal('capacidad_kg', 10, 2)->default(0);
            $table->decimal('tara_kg', 10, 2)->nullable();
            $table->decimal('largo_m', 6, 2)->nullable();
            $table->decimal('ancho_m', 6, 2)->nullable();
            $table->decimal('alto_m', 6, 2)->nullable();
            $table->decimal('capacidad_m3', 8, 2)->nullable();
            $table->date('soat_vence')->nullable();
            $table->date('rev_tecnica_vence')->nullable();
            $table->tinyInteger('estado')->default(1);
            $table->timestamps();
            $table->index(['id_empresa', 'sucursal']);
        });

        // ── Maestro de conductores ─────────────────────────────────────────
        Schema::create('tms_conductores', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_empresa');
            $table->unsignedInteger('sucursal');
            $table->unsignedInteger('id_usuario')->nullable();
            $table->string('nombres', 120);
            $table->string('documento', 15)->nullable();
            $table->string('licencia', 30)->nullable();
            $table->string('licencia_categoria', 10)->nullable();
            $table->date('licencia_vence')->nullable();
            $table->string('telefono', 20)->nullable();
            $table->tinyInteger('estado')->default(1);
            $table->timestamps();
            $table->index(['id_empresa', 'sucursal']);
        });

        // ── Maestro de rutas ───────────────────────────────────────────────
        Schema::create('tms_rutas', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_empresa');
            $table->unsignedInteger('sucursal');
            $table->string('nombre', 120);
            $table->string('descripcion', 245)->nullable();
            $table->tinyInteger('estado')->default(1);
            $table->timestamps();
            $table->index(['id_empresa', 'sucursal']);
        });

        // ── Puntos que componen una ruta (mercado o tienda) ────────────────
        Schema::create('tms_ruta_puntos', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_ruta');
            $table->string('tipo', 10)->comment('MERCADO|TIENDA');
            $table->unsignedInteger('id_mercado')->nullable();
            $table->unsignedInteger('id_cliente')->nullable();
            $table->integer('orden')->default(0);

            $table->foreign('id_ruta')->references('id')->on('tms_rutas')->onDelete('cascade');
            $table->index('id_ruta');
        });

        // ── Despacho (cabecera del viaje/reparto) ──────────────────────────
        Schema::create('tms_despachos', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_empresa');
            $table->unsignedInteger('sucursal');
            $table->string('codigo', 20)->nullable();
            $table->date('fecha_reparto');
            $table->unsignedInteger('id_ruta');
            $table->unsignedInteger('id_vehiculo');
            $table->unsignedInteger('id_conductor');
            $table->decimal('peso_total', 12, 2)->default(0);
            $table->string('estado', 15)->default('PLANIFICADO')->comment('PLANIFICADO|CARGADO|EN_RUTA|CERRADO|ANULADO');
            $table->string('observaciones', 255)->nullable();
            $table->unsignedInteger('id_usuario_creacion')->nullable();
            $table->timestamps();
            $table->index(['id_empresa', 'sucursal', 'fecha_reparto']);
        });

        // ── Pedidos jalados al despacho (detalle) ──────────────────────────
        Schema::create('tms_despacho_pedidos', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_despacho');
            $table->unsignedInteger('id_cotizacion');
            $table->unsignedInteger('id_cliente');
            $table->unsignedInteger('id_mercado')->nullable();
            $table->decimal('peso', 12, 2)->default(0);
            $table->decimal('monto', 12, 2)->default(0);
            $table->integer('orden')->default(0);
            $table->string('estado_entrega', 15)->default('PENDIENTE')->comment('PENDIENTE|ENTREGADO|RECHAZADO|PARCIAL');
            $table->string('motivo_rechazo', 255)->nullable();
            $table->dateTime('hora_entrega')->nullable();

            $table->foreign('id_despacho')->references('id')->on('tms_despachos')->onDelete('cascade');
            $table->index('id_despacho');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tms_despacho_pedidos');
        Schema::dropIfExists('tms_despachos');
        Schema::dropIfExists('tms_ruta_puntos');
        Schema::dropIfExists('tms_rutas');
        Schema::dropIfExists('tms_conductores');
        Schema::dropIfExists('tms_vehiculos');
        Schema::dropIfExists('tms_mercados');
    }
};
