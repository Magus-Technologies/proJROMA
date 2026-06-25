<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categorias', function (Blueprint $t) {
            $t->increments('id_categoria');
            $t->string('nombre', 150);
            $t->integer('id_empresa')->index();
            $t->char('estado', 1)->default('1');
        });

        Schema::create('marcas', function (Blueprint $t) {
            $t->increments('id_marca');
            $t->string('nombre', 150);
            $t->integer('id_empresa')->index();
            $t->char('estado', 1)->default('1');
        });

        Schema::create('subcategorias', function (Blueprint $t) {
            $t->increments('id_subcategoria');
            $t->string('nombre', 150);
            $t->integer('id_categoria')->index();
            $t->integer('id_empresa')->index();
            $t->char('estado', 1)->default('1');
        });

        Schema::create('submarcas', function (Blueprint $t) {
            $t->increments('id_submarca');
            $t->string('nombre', 150);
            $t->integer('id_marca')->index();
            $t->integer('id_empresa')->index();
            $t->char('estado', 1)->default('1');
        });

        Schema::table('productos', function (Blueprint $t) {
            $t->integer('id_categoria')->nullable()->after('codigo');
            $t->integer('id_subcategoria')->nullable()->after('id_categoria');
            $t->integer('id_marca')->nullable()->after('id_subcategoria');
            $t->integer('id_submarca')->nullable()->after('id_marca');
        });
    }

    public function down(): void
    {
        Schema::table('productos', function (Blueprint $t) {
            $t->dropColumn(['id_categoria', 'id_subcategoria', 'id_marca', 'id_submarca']);
        });
        Schema::dropIfExists('submarcas');
        Schema::dropIfExists('subcategorias');
        Schema::dropIfExists('marcas');
        Schema::dropIfExists('categorias');
    }
};
