<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prestamos', function (Blueprint $t) {
            $t->increments('id_prestamo');
            $t->integer('id_empresa')->index();
            $t->char('tipo', 1);                 // P = prestado (yo presto), R = recibido (me prestan)
            $t->string('tercero', 150);          // empresa / proveedor externo
            $t->integer('id_producto');
            $t->string('almacen', 50);
            $t->integer('cantidad');
            $t->char('estado', 1)->default('P'); // P = pendiente, D = devuelto
            $t->string('observacion', 255)->nullable();
            $t->integer('id_usuario')->nullable();
            $t->dateTime('fecha');
            $t->dateTime('fecha_devolucion')->nullable();
        });

        // Motivos de préstamo (de sistema)
        foreach (DB::table('empresas')->pluck('id_empresa') as $emp) {
            DB::table('motivos_movimiento')->insert([
                ['nombre' => 'Préstamo entregado', 'tipo' => 'S', 'es_sistema' => 1, 'id_empresa' => $emp, 'estado' => '1'],
                ['nombre' => 'Préstamo recibido',  'tipo' => 'I', 'es_sistema' => 1, 'id_empresa' => $emp, 'estado' => '1'],
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('prestamos');
        DB::table('motivos_movimiento')->whereIn('nombre', ['Préstamo entregado', 'Préstamo recibido'])->delete();
    }
};
