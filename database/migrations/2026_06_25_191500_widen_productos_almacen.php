<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * `productos.almacen` era char(1), pensado para códigos de 1 carácter (1,2,3).
     * Al crear almacenes con códigos descriptivos (ej. "AL-PR1") la recepción
     * fallaba con "Data too long for column 'almacen'". Se amplía a varchar(50)
     * para igualar a las demás tablas (recepciones, inventario_movimientos, etc.).
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE productos MODIFY almacen VARCHAR(50) NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE productos MODIFY almacen CHAR(1) NULL");
    }
};
