<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Devuelve a su empresa los registros que nacieron con id_empresa = 0.
     *
     * Al quedar la empresa inactiva, el login dejaba de poner id_empresa en
     * sesión pero permitía entrar igual. Los recursos del panel filtran por
     * (int) session('id_empresa'), que sin sesión vale 0: las pantallas se
     * veían vacías y todo lo que se creaba nacía con id_empresa = 0.
     *
     * Va fila por fila a propósito. Varias tablas tienen índices únicos que
     * incluyen id_empresa (por ejemplo presentaciones sobre id_empresa+nombre):
     * si el huérfano duplica algo que ya existe en la empresa, un UPDATE masivo
     * aborta y deja la reparación a medias. Aquí ese caso se informa y se deja
     * la fila intacta, para decidirlo a mano.
     */
    public function up(): void
    {
        $empresas = DB::table('empresas')->pluck('id_empresa');

        if ($empresas->count() !== 1) {
            echo PHP_EOL . '  [!] Hay ' . $empresas->count() . ' empresas: no se reasigna nada.' . PHP_EOL
                . '      Con más de una no se puede deducir a cuál pertenece cada huérfano.' . PHP_EOL;

            return;
        }

        $idEmpresa = (int) $empresas->first();

        // La empresa quedó inactiva y eso dejó a TODOS los usuarios fuera: el
        // login no encuentra empresa activa y no puede iniciar sesión. Siendo la
        // única empresa del sistema, con usuarios trabajando en ella, estar
        // inactiva no tiene lectura válida.
        $estado = DB::table('empresas')->where('id_empresa', $idEmpresa)->value('estado');

        if ((string) $estado !== '1') {
            DB::table('empresas')->where('id_empresa', $idEmpresa)->update(['estado' => '1']);
            echo PHP_EOL . '  Empresa ' . $idEmpresa . ' reactivada (estaba en '
                . var_export($estado, true) . '): sin esto nadie puede entrar.' . PHP_EOL . PHP_EOL;
        }

        // El alias es necesario: MySQL devuelve TABLE_NAME en mayúsculas y
        // MariaDB en minúsculas, así que pluck('table_name') falla en uno u otro.
        $tablas = DB::table('information_schema.columns')
            ->where('table_schema', DB::getDatabaseName())
            ->where('column_name', 'id_empresa')
            ->orderBy('table_name')
            ->select(DB::raw('table_name AS nombre_tabla'))
            ->pluck('nombre_tabla');

        $reasignados = 0;
        $duplicados  = [];

        foreach ($tablas as $tabla) {
            if (! Schema::hasTable($tabla)) {
                continue;
            }

            $pk = $this->clavePrimaria($tabla);

            if ($pk === null) {
                echo sprintf('  [!] %s no tiene clave primaria simple: se omite.', $tabla) . PHP_EOL;
                continue;
            }

            $huerfanos = DB::table($tabla)
                ->where(fn ($q) => $q->where('id_empresa', 0)->orWhereNull('id_empresa'))
                ->pluck($pk);

            if ($huerfanos->isEmpty()) {
                continue;
            }

            $ok = 0;

            foreach ($huerfanos as $id) {
                try {
                    DB::table($tabla)->where($pk, $id)->update(['id_empresa' => $idEmpresa]);
                    $ok++;
                } catch (UniqueConstraintViolationException) {
                    // Ya existe uno igual en la empresa: es un duplicado creado
                    // durante la falla. Se deja como está para no romper nada
                    // que pudiera estar apuntándole.
                    $duplicados[] = "{$tabla} ({$pk}={$id})";
                }
            }

            if ($ok > 0) {
                $reasignados += $ok;
                echo sprintf('  %-32s %d registro(s)', $tabla, $ok) . PHP_EOL;
            }
        }

        echo PHP_EOL . '  ' . $reasignados . ' registro(s) devueltos a la empresa ' . $idEmpresa . '.' . PHP_EOL;

        if ($duplicados) {
            echo PHP_EOL . '  [!] ' . count($duplicados) . ' quedaron sin reasignar por duplicar un registro existente:' . PHP_EOL;
            foreach ($duplicados as $d) {
                echo '      - ' . $d . PHP_EOL;
            }
            echo '      Revisalos a mano: probablemente haya que borrarlos.' . PHP_EOL;
        }
    }

    /** Nombre de la clave primaria si es de una sola columna. */
    private function clavePrimaria(string $tabla): ?string
    {
        $columnas = DB::table('information_schema.key_column_usage')
            ->where('table_schema', DB::getDatabaseName())
            ->where('table_name', $tabla)
            ->where('constraint_name', 'PRIMARY')
            ->select(DB::raw('column_name AS nombre_columna'))
            ->pluck('nombre_columna');

        return $columnas->count() === 1 ? $columnas->first() : null;
    }

    /**
     * No se revierte: no queda registro de qué filas tenían id_empresa = 0, y
     * devolverlas a 0 volvería a esconderlas. Si hace falta, se restaura de un
     * respaldo.
     */
    public function down(): void
    {
        //
    }
};
