<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tms_despacho_costos', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_despacho');
            $table->string('concepto', 120)->comment('COMBUSTIBLE|PEAJE|VIATICOS|OTRO o texto libre');
            $table->decimal('monto', 12, 2);
            $table->unsignedInteger('id_caja')->nullable()->comment('Caja a la que se cargó el egreso, si aplica');
            $table->unsignedInteger('id_movimiento_caja')->nullable();
            $table->unsignedInteger('id_usuario')->nullable();
            $table->timestamps();

            $table->foreign('id_despacho')->references('id')->on('tms_despachos')->onDelete('cascade');
            $table->index('id_despacho');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tms_despacho_costos');
    }
};
