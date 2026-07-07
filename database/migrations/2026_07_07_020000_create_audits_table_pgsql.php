<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('pgsql_audit')->create('audits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('user_name')->nullable();
            $table->string('user_rol')->nullable();
            $table->unsignedBigInteger('empresa_id')->nullable();
            $table->string('event');
            $table->string('model_type');
            $table->string('model_id')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->text('url')->nullable();
            $table->string('method', 10)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::connection('pgsql_audit')->dropIfExists('audits');
    }
};
