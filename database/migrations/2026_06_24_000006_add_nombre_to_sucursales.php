<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sucursales', function (Blueprint $t) {
            if (! Schema::hasColumn('sucursales', 'nombre')) $t->string('nombre', 150)->nullable()->after('empresa_id');
            if (! Schema::hasColumn('sucursales', 'estado')) $t->char('estado', 1)->default('1');
        });

        // La PK no tenía AUTO_INCREMENT (impide insertar). Asegurarlo.
        try { DB::statement('ALTER TABLE sucursales MODIFY id_sucursal INT NOT NULL AUTO_INCREMENT'); } catch (\Throwable $e) {}

        // Sembrar sucursales por empresa según los códigos usados por sus usuarios (o 1,2,3)
        foreach (DB::table('empresas')->pluck('id_empresa') as $emp) {
            $cods = DB::table('usuarios')->where('id_empresa', $emp)->distinct()->pluck('sucursal')
                ->filter(fn ($c) => (int) $c > 0)->map(fn ($c) => (int) $c)->unique()->values();
            if ($cods->isEmpty()) $cods = collect([1, 2, 3]);

            foreach ($cods as $cod) {
                $existe = DB::table('sucursales')->where('empresa_id', $emp)->where('cod_sucursal', $cod)->exists();
                if (! $existe) {
                    DB::table('sucursales')->insert([
                        'empresa_id'   => $emp,
                        'cod_sucursal' => $cod,
                        'nombre'       => "Sucursal {$cod}",
                        'direccion'    => '',
                        'distrito'     => '',
                        'provincia'    => '',
                        'departamento' => '',
                        'ubigeo'       => '',
                        'estado'       => '1',
                    ]);
                }
            }
        }
    }

    public function down(): void
    {
        Schema::table('sucursales', function (Blueprint $t) {
            $t->dropColumn(['nombre', 'estado']);
        });
    }
};
