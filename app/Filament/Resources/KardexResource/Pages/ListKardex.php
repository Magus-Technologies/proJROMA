<?php

namespace App\Filament\Resources\KardexResource\Pages;

use App\Filament\Concerns\ExportaTabla;
use App\Filament\Resources\KardexResource;
use App\Filament\Resources\KardexResource\Widgets\KardexStats;
use App\Models\InventarioMovimiento;
use Filament\Resources\Pages\ListRecords;

class ListKardex extends ListRecords
{
    use ExportaTabla;

    protected static string $resource = KardexResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            KardexStats::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return $this->accionesDeDescarga();
    }

    /**
     * El kardex no lleva fila de totales: sumar ingresos y salidas en una sola
     * columna de cantidad daría un número sin significado contable.
     */
    protected function datosParaExportar(): array
    {
        $almacenes = KardexResource::almacenes();

        $filas = $this->getFilteredSortedTableQuery()
            ->with(['producto', 'motivo', 'usuario'])
            ->get()
            ->map(fn (InventarioMovimiento $m): array => [
                self::fechaLegible($m->fecha),
                $almacenes[$m->almacen] ?? ($m->almacen ?: '—'),
                $m->producto->descripcion ?? '—',
                $m->tipo === 'I' ? 'Ingreso' : 'Salida',
                $m->motivo->nombre ?? '—',
                (int) $m->cantidad,
                (int) $m->stock_anterior,
                (int) $m->stock_nuevo,
                KardexResource::costo($m, 'anterior'),
                KardexResource::costo($m, 'actual'),
                $m->observacion ?: '',
                $m->usuario->nombres ?? '—',
            ])
            ->toArray();

        return [
            'titulo'    => 'Kardex de Inventario',
            'periodo'   => $this->periodoExportado(),
            'slug'      => 'kardex',
            'cabeceras' => [
                'Fecha', 'Almacén', 'Producto', 'Tipo', 'Motivo', 'Cant.',
                'Stock ant.', 'Stock nuevo', 'Costo ant.', 'Costo actual',
                'Observación', 'Usuario',
            ],
            'filas'             => $filas,
            'columnasMoneda'    => [8, 9],
            'ultimaFilaEsTotal' => false,
        ];
    }

    /** fecha no está casteada en el modelo: puede llegar como string. */
    private static function fechaLegible(mixed $fecha): string
    {
        return blank($fecha) ? '—' : \Illuminate\Support\Carbon::parse($fecha)->format('d/m/Y H:i');
    }

    /** Deja constancia en el reporte del rango de fechas filtrado, si lo hay. */
    private function periodoExportado(): string
    {
        $fecha = $this->tableFilters['fecha'] ?? [];
        $desde = $fecha['desde'] ?? null;
        $hasta = $fecha['hasta'] ?? null;

        return match (true) {
            $desde && $hasta => 'Del ' . $desde . ' al ' . $hasta,
            (bool) $desde    => 'Desde ' . $desde,
            (bool) $hasta    => 'Hasta ' . $hasta,
            default          => 'Todos los movimientos',
        };
    }
}
