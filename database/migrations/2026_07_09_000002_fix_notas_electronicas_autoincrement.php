<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * El PK legacy `nota_id` es INT NOT NULL sin AUTO_INCREMENT ni default:
     * cualquier INSERT falla en MySQL estricto (o guarda 0 en modo laxo).
     */
    public function up(): void
    {
        // 1) Reasignar filas rotas (nota_id = 0) antes de convertir la columna.
        $siguiente = ((int) DB::table('notas_electronicas')->where('nota_id', '>', 0)->max('nota_id')) + 1;

        while (DB::table('notas_electronicas')->where('nota_id', 0)->exists()) {
            DB::table('notas_electronicas')->where('nota_id', 0)->limit(1)->update(['nota_id' => $siguiente]);
            $siguiente++;
        }

        // 2) Convertir el PK en AUTO_INCREMENT.
        DB::statement('ALTER TABLE notas_electronicas MODIFY nota_id INT NOT NULL AUTO_INCREMENT');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE notas_electronicas MODIFY nota_id INT NOT NULL');
    }
};
