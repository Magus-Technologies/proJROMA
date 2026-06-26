<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('productos_ventas', function (Blueprint $table) {
            if (!Schema::hasColumn('productos_ventas', 'descripcion'))
                $table->string('descripcion', 255)->nullable()->after('id_producto');
            if (!Schema::hasColumn('productos_ventas', 'total'))
                $table->decimal('total', 10, 2)->default(0)->after('precio');
            if (!Schema::hasColumn('productos_ventas', 'igv_prod'))
                $table->tinyInteger('igv_prod')->default(0)->after('total');
            if (!Schema::hasColumn('productos_ventas', 'descuento'))
                $table->decimal('descuento', 10, 2)->default(0)->after('igv_prod');
        });
    }

    public function down(): void
    {
        Schema::table('productos_ventas', function (Blueprint $table) {
            foreach (['descripcion', 'total', 'igv_prod', 'descuento'] as $col)
                if (Schema::hasColumn('productos_ventas', $col)) $table->dropColumn($col);
        });
    }
};
