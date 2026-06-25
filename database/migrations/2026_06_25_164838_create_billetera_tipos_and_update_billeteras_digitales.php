<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Crear billetera_tipos
        Schema::create('billetera_tipos', function (Blueprint $table) {
            $table->tinyIncrements('id');
            $table->unsignedInteger('id_empresa');
            $table->string('nombre', 30);
            $table->string('estado', 2)->default('1');
        });

        // 2. Sembrar tipos por defecto
        DB::table('billetera_tipos')->insert([
            ['id_empresa' => 1, 'nombre' => 'Yape'],
            ['id_empresa' => 1, 'nombre' => 'Plin'],
            ['id_empresa' => 1, 'nombre' => 'Tunki'],
            ['id_empresa' => 1, 'nombre' => 'Agora'],
            ['id_empresa' => 1, 'nombre' => 'BIM'],
            ['id_empresa' => 1, 'nombre' => 'Otro'],
        ]);

        // 3. Agregar columnas nuevas
        Schema::table('billeteras_digitales', function (Blueprint $table) {
            if (!Schema::hasColumn('billeteras_digitales', 'id_cuenta_bancaria')) {
                $table->unsignedInteger('id_cuenta_bancaria')->nullable()->after('id_empresa');
            }
            if (!Schema::hasColumn('billeteras_digitales', 'id_billetera_tipo')) {
                $table->unsignedTinyInteger('id_billetera_tipo')->nullable()->after('id_cuenta_bancaria');
            }
        });

        // 4. Migrar datos viejos
        $map = [
            'YAPE'  => 1, 'PLIN' => 2, 'TUNKI' => 3,
            'AGORA' => 4, 'BIM'  => 5, 'OTRO'  => 6,
        ];
        DB::table('billeteras_digitales')->whereNull('id_billetera_tipo')->orderBy('id_billetera')->chunk(100, function ($rows) use ($map) {
            foreach ($rows as $row) {
                $tipoId = $map[strtoupper($row->tipo)] ?? 6;
                DB::table('billeteras_digitales')
                    ->where('id_billetera', $row->id_billetera)
                    ->update(['id_billetera_tipo' => $tipoId]);
            }
        });

        // 5. id_billetera_tipo → NOT NULL
        Schema::table('billeteras_digitales', function (Blueprint $table) {
            $table->unsignedTinyInteger('id_billetera_tipo')->nullable(false)->change();
        });

        // 6. Eliminar columna vieja tipo
        if (Schema::hasColumn('billeteras_digitales', 'tipo')) {
            Schema::table('billeteras_digitales', function (Blueprint $table) {
                $table->dropColumn('tipo');
            });
        }
    }

    public function down(): void
    {
        Schema::table('billeteras_digitales', function (Blueprint $table) {
            if (!Schema::hasColumn('billeteras_digitales', 'tipo')) {
                $table->string('tipo', 20)->nullable()->after('id_empresa');
            }
            $table->dropColumn(['id_billetera_tipo', 'id_cuenta_bancaria']);
        });
        Schema::dropIfExists('billetera_tipos');
    }
};
