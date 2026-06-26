<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            if (!Schema::hasColumn('ventas', 'subtotal'))
                $table->decimal('subtotal', 10, 2)->default(0)->after('total');
            if (!Schema::hasColumn('ventas', 'id_vendedor'))
                $table->unsignedInteger('id_vendedor')->nullable()->after('sucursal');
        });
    }

    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            if (Schema::hasColumn('ventas', 'subtotal'))  $table->dropColumn('subtotal');
        });
    }
};
