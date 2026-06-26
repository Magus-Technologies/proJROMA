<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE dias_compras
            ADD COLUMN `id_caja` INT NULL AFTER `estado`,
            ADD COLUMN `instrumento_tipo` VARCHAR(30) NULL AFTER `id_caja`,
            ADD COLUMN `instrumento_id` INT NULL AFTER `instrumento_tipo`");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE dias_compras
            DROP COLUMN `instrumento_id`,
            DROP COLUMN `instrumento_tipo`,
            DROP COLUMN `id_caja`");
    }
};
