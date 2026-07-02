<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Maestros del catálogo de productos: unidades de medida y presentaciones (cómo compra).
 * Se siembran con los valores de texto que ya existen en productos.medida / productos.presentaciones.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('unidades_medida', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_empresa');
            $table->string('nombre', 60);
            $table->string('abreviatura', 15)->nullable();
            $table->tinyInteger('estado')->default(1);
            $table->timestamps();
            $table->unique(['id_empresa', 'nombre']);
        });

        Schema::create('presentaciones', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_empresa');
            $table->string('nombre', 60);
            $table->tinyInteger('estado')->default(1);
            $table->timestamps();
            $table->unique(['id_empresa', 'nombre']);
        });

        $this->seedFrom('medida', 'unidades_medida');
        $this->seedFrom('presentaciones', 'presentaciones');
    }

    private function seedFrom(string $col, string $tabla): void
    {
        $vistos = [];

        DB::table('productos')
            ->whereNotNull($col)->where($col, '<>', '')
            ->select('id_empresa', $col)
            ->distinct()
            ->orderBy('id_empresa')
            ->chunk(500, function ($rows) use ($col, $tabla, &$vistos) {
                foreach ($rows as $r) {
                    $nombre = trim((string) $r->$col);
                    if ($nombre === '') continue;
                    $key = $r->id_empresa . '|' . mb_strtolower($nombre);
                    if (isset($vistos[$key])) continue;
                    $vistos[$key] = true;

                    DB::table($tabla)->insertOrIgnore([
                        'id_empresa' => $r->id_empresa,
                        'nombre'     => $nombre,
                        'estado'     => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('presentaciones');
        Schema::dropIfExists('unidades_medida');
    }
};
