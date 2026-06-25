<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('caja_instrumentos', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_caja');
            $table->string('instrumento_tipo', 30);
            $table->unsignedInteger('instrumento_id')->nullable();
            $table->string('estado', 10)->default('ACTIVO');
            $table->unique(['id_caja', 'instrumento_tipo', 'instrumento_id'], 'ci_uniq');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('caja_instrumentos');
    }
};
