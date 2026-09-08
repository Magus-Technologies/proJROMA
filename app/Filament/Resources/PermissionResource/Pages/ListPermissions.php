<?php

namespace App\Filament\Resources\PermissionResource\Pages;

use App\Filament\Concerns\ExportaTabla;
use App\Filament\Resources\PermissionResource;
use Filament\Resources\Pages\ListRecords;
use Spatie\Permission\Models\Permission;

class ListPermissions extends ListRecords
{
    use ExportaTabla;

    protected static string $resource = PermissionResource::class;

    /** Un permiso no se crea desde la interfaz: solo se consulta y se descarga. */
    protected function getHeaderActions(): array
    {
        return $this->accionesDeDescarga();
    }

    public function getSubheading(): ?string
    {
        return 'Los permisos los define el código y no se crean, editan ni eliminan aquí. '
            . 'Para dar o quitar un permiso, edita el rol en Administración → Roles.';
    }

    /**
     * Reporte de auditoría: qué permisos existen, qué habilita cada uno y qué
     * roles lo tienen. La columna de roles va con nombres, no con un conteo:
     * en una revisión de accesos lo que se pregunta es QUIÉN puede hacer algo.
     */
    protected function datosParaExportar(): array
    {
        $catalogo = PermissionResource::catalogo();

        $filas = $this->getFilteredSortedTableQuery()
            ->with('roles')
            ->get()
            ->map(function (Permission $p) use ($catalogo): array {
                $roles = $p->roles->pluck('nombre')->sort()->implode(', ');

                return [
                    $catalogo[$p->name]['grupo'] ?? 'Sin agrupar',
                    $p->name,
                    $catalogo[$p->name]['descripcion'] ?? 'Sin descripción en el seeder',
                    $p->roles->count(),
                    $roles ?: 'NINGUNO',
                ];
            })
            ->toArray();

        return [
            'titulo'            => 'Catálogo de Permisos',
            'periodo'           => $this->periodoExportado(),
            'slug'              => 'permisos',
            'cabeceras'         => ['Módulo', 'Permiso', 'Qué permite', 'N° roles', 'Roles que lo tienen'],
            'filas'             => $filas,
            'columnasMoneda'    => [],
            'ultimaFilaEsTotal' => false,
        ];
    }

    /** Deja constancia en el reporte de qué recorte se exportó. */
    private function periodoExportado(): string
    {
        $modulo = $this->tableFilters['grupo']['value'] ?? null;
        $asignacion = $this->tableFilters['asignacion']['value'] ?? null;

        $partes = [];

        $partes[] = filled($modulo) ? 'Módulo: ' . $modulo : 'Todos los módulos';

        if ($asignacion === 'asignados') {
            $partes[] = 'solo asignados a algún rol';
        } elseif ($asignacion === 'huerfanos') {
            $partes[] = 'solo sin ningún rol';
        }

        return implode(' · ', $partes);
    }
}
