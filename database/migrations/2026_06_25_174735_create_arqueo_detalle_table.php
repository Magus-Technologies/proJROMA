<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('arqueo_detalle', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_arqueo');
            $table->string('instrumento_tipo', 30);
            $table->unsignedInteger('instrumento_id')->nullable();
            $table->decimal('monto_sistema', 12, 2);
            $table->decimal('monto_contado', 12, 2)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('arqueo_detalle');
    }
};
