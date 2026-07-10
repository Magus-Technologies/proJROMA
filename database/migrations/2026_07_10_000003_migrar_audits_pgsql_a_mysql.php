<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Copia el histórico de auditoría desde la antigua conexión PostgreSQL
     * (pgsql_audit) a la tabla audits de MySQL. Si Postgres no está
     * disponible (ej. producción nunca lo tuvo), no falla: simplemente
     * no hay nada que copiar.
     */
    public function up(): void
    {
        try {
            $antiguos = DB::connection('pgsql_audit')->table('audits')->orderBy('id')->get();
        } catch (\Throwable $e) {
            return; // sin Postgres accesible: nada que migrar
        }

        foreach ($antiguos->chunk(200) as $lote) {
            DB::table('audits')->insertOrIgnore($lote->map(fn ($a) => [
                'id'         => $a->id,
                'user_id'    => $a->user_id,
                'user_name'  => $a->user_name,
                'user_rol'   => $a->user_rol,
                'empresa_id' => $a->empresa_id,
                'event'      => $a->event,
                'model_type' => $a->model_type,
                'model_id'   => $a->model_id,
                'old_values' => $a->old_values,
                'new_values' => $a->new_values,
                'ip_address' => $a->ip_address,
                'user_agent' => $a->user_agent,
                'url'        => $a->url,
                'method'     => $a->method,
                'created_at' => $a->created_at,
                'updated_at' => $a->updated_at,
            ])->toArray());
        }
    }

    public function down(): void
    {
        // El histórico sigue en PostgreSQL; no hay nada que revertir aquí.
    }
};
