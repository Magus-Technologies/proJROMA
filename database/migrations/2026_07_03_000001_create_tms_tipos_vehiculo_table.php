<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tms_tipos_vehiculo', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_empresa');
            $table->string('nombre', 60);
            $table->tinyInteger('estado')->default(1);
            $table->timestamps();

            $table->unique(['id_empresa', 'nombre']);
        });

        Schema::table('tms_vehiculos', function (Blueprint $table) {
            $table->unsignedInteger('id_tipo')->nullable()->after('tipo');

            $table->foreign('id_tipo')->references('id')->on('tms_tipos_vehiculo');
        });

        $empresas = DB::table('tms_vehiculos')->distinct()->pluck('id_empresa');

        $tiposExistentes = DB::table('tms_vehiculos')
            ->distinct()->pluck('tipo');

        foreach ($empresas as $idEmpresa) {
            foreach ($tiposExistentes as $tipo) {
                if (blank($tipo)) continue;

                $idTipo = DB::table('tms_tipos_vehiculo')->insertGetId([
                    'id_empresa' => $idEmpresa,
                    'nombre'     => strtoupper(trim($tipo)),
                    'estado'     => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                DB::table('tms_vehiculos')
                    ->where('id_empresa', $idEmpresa)
                    ->where('tipo', $tipo)
                    ->update(['id_tipo' => $idTipo]);
            }
        }

        Schema::table('tms_vehiculos', function (Blueprint $table) {
            $table->dropColumn('tipo');
        });
    }

    public function down(): void
    {
        Schema::table('tms_vehiculos', function (Blueprint $table) {
            $table->string('tipo', 15)->default('CAMIONETA')->after('placa');
        });

        DB::table('tms_vehiculos')
            ->join('tms_tipos_vehiculo', 'tms_vehiculos.id_tipo', '=', 'tms_tipos_vehiculo.id')
            ->update(['tms_vehiculos.tipo' => DB::raw('tms_tipos_vehiculo.nombre')]);

        Schema::table('tms_vehiculos', function (Blueprint $table) {
            $table->dropForeign(['id_tipo']);
            $table->dropColumn('id_tipo');
        });

        Schema::dropIfExists('tms_tipos_vehiculo');
    }
};
