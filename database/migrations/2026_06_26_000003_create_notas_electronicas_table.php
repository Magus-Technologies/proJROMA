<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('notas_electronicas')) {
            Schema::table('notas_electronicas', function (Blueprint $table) {
                if (!Schema::hasColumn('notas_electronicas', 'cod_motivo'))
                    $table->string('cod_motivo', 5)->default('01')->after('motivo');
                if (!Schema::hasColumn('notas_electronicas', 'fecha_emision'))
                    $table->date('fecha_emision')->nullable();
                if (!Schema::hasColumn('notas_electronicas', 'hash'))
                    $table->string('hash', 255)->nullable();
                if (!Schema::hasColumn('notas_electronicas', 'nombre_xml'))
                    $table->string('nombre_xml', 255)->nullable();
            });
            return;
        }

        Schema::create('notas_electronicas', function (Blueprint $table) {
            $table->increments('id_nota');
            $table->unsignedInteger('id_venta')->index();
            $table->string('tipo', 10);
            $table->string('cod_motivo', 5)->default('01');
            $table->string('motivo', 255);
            $table->unsignedInteger('id_empresa')->index();
            $table->unsignedInteger('sucursal')->default(1);
            $table->string('serie', 10);
            $table->unsignedInteger('numero');
            $table->decimal('total', 10, 2)->default(0);
            $table->date('fecha_emision')->nullable();
            $table->string('estado', 2)->default('1');
            $table->string('enviado_sunat', 2)->default('0');
            $table->string('hash', 255)->nullable();
            $table->string('nombre_xml', 255)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notas_electronicas');
    }
};
