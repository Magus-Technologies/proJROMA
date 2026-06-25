<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('almacenes', function (Blueprint $t) {
            $t->increments('id_almacen');
            $t->string('nombre', 150);
            $t->string('codigo', 50)->nullable();
            $t->string('descripcion', 255)->nullable();
            $t->integer('id_sucursal')->nullable();   // null = almacén global de la empresa
            $t->integer('id_empresa')->index();
            $t->char('estado', 1)->default('1');
        });

        // Sembrar los 3 almacenes actuales (1,2,3) por cada empresa, para mantener continuidad
        foreach (DB::table('empresas')->pluck('id_empresa') as $emp) {
            for ($i = 1; $i <= 3; $i++) {
                DB::table('almacenes')->insert([
                    'nombre'     => "Almacén $i",
                    'codigo'     => (string) $i,
                    'id_empresa' => $emp,
                    'estado'     => '1',
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('almacenes');
    }
};
