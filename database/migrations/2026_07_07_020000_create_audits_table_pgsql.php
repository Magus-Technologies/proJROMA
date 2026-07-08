<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** El registro de auditoría vive en PostgreSQL; si el servidor no tiene
     *  el driver pgsql (o la BD no está disponible) se omite sin romper el deploy.
     *  La auditoría es opcional — el trait Auditable ya ignora los fallos. */
    private function pgsqlDisponible(): bool
    {
        if (! in_array('pgsql', \PDO::getAvailableDrivers(), true)) {
            return false;
        }

        try {
            Schema::connection('pgsql_audit')->getConnection()->getPdo();

            return true;
        } catch (\Throwable) {
            return false;
        }
    }

    public function up(): void
    {
        if (! $this->pgsqlDisponible()) {
            echo "  ⏭  pgsql_audit no disponible — auditoría PostgreSQL omitida\n";

            return;
        }

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
        if (! $this->pgsqlDisponible()) {
            return;
        }

        Schema::connection('pgsql_audit')->dropIfExists('audits');
    }
};
