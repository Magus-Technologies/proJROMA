<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->integer('usuario_id', true);
            $table->integer('id_empresa');
            $table->integer('id_rol');
            $table->string('num_doc');
            $table->string('usuario');
            $table->string('clave');
            $table->string('email')->nullable();
            $table->string('nombres');
            $table->string('apellidos');
            $table->string('rubro');
            $table->integer('sucursal');
            $table->string('telefono')->nullable();
            $table->string('estado', 2)->default('1');
            $table->text('mensaje')->nullable();
            $table->string('token_reset')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};
