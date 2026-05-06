<?php
// database/migrations/2024_01_01_000002_add_security_columns.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Añade columnas de seguridad a tablas existentes.
 * NO elimina ni altera datos existentes.
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── Columnas de seguridad en usuarios ─────────────────────────────
        Schema::table('usuarios', function (Blueprint $table) {
            if (!Schema::hasColumn('usuarios', 'remember_token')) {
                $table->string('remember_token', 100)->nullable()->after('clave');
            }
            if (!Schema::hasColumn('usuarios', 'rotativo')) {
                $table->tinyInteger('rotativo')->default(0)->after('sucursal')
                      ->comment('1 = puede entrar a cualquier sucursal');
            }
            if (!Schema::hasColumn('usuarios', 'available_status')) {
                $table->tinyInteger('available_status')->default(1)->after('rotativo')
                      ->comment('1 = disponible para login');
            }
            if (!Schema::hasColumn('usuarios', 'created_at')) {
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
            }
        });

        // ── Índices de performance en ventas ──────────────────────────────
        try {
            Schema::table('ventas', function (Blueprint $table) {
                $table->index(['id_empresa','sucursal','estado','fecha_emision'], 'idx_venta_empresa');
                $table->index(['id_cliente'], 'idx_venta_cliente');
                $table->index(['id_vendedor'], 'idx_venta_vendedor');
            });
        } catch (\Exception $e) { /* índice ya existe */ }

        // ── Índices en productos ───────────────────────────────────────────
        try {
            Schema::table('productos', function (Blueprint $table) {
                $table->index(['id_empresa','estado','almacen'], 'idx_prod_empresa');
                $table->index(['cod_barra'], 'idx_prod_barra');
                $table->index(['codigo'], 'idx_prod_codigo');
            });
        } catch (\Exception $e) {}

        // ── Índices en clientes ────────────────────────────────────────────
        try {
            Schema::table('clientes', function (Blueprint $table) {
                $table->index(['id_empresa'], 'idx_cli_empresa');
                $table->index(['documento'], 'idx_cli_doc');
            });
        } catch (\Exception $e) {}

        // ── Índices en cotizaciones ────────────────────────────────────────
        try {
            Schema::table('cotizaciones', function (Blueprint $table) {
                $table->index(['id_empresa','estado'], 'idx_coti_empresa');
            });
        } catch (\Exception $e) {}
    }

    public function down(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $cols = ['remember_token','rotativo','available_status','created_at','updated_at'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('usuarios', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
