<?php

namespace App\Filament\Concerns;

use App\Exports\ReporteGenericoExport;
use App\Models\Empresa;
use App\Services\PdfService;
use Filament\Actions\Action;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Acciones de descarga (Excel y PDF) para una pantalla de listado.
 *
 * Lo que se descarga es EXACTAMENTE lo que el usuario está viendo: la página
 * arma las filas desde getFilteredSortedTableQuery(), así que la pestaña
 * activa, la búsqueda, los filtros y el orden se respetan. Un reporte que
 * ignora los filtros de la pantalla no sirve para cuadrar nada.
 *
 * La página que use este trait define datosParaExportar() devolviendo:
 *   titulo, periodo, slug, cabeceras, filas, columnasMoneda, ultimaFilaEsTotal
 *
 * Ambos formatos comparten esa misma estructura, para que el Excel y el PDF
 * de una pantalla nunca se desincronicen.
 */
trait ExportaTabla
{
    /** @return array{titulo: string, periodo: string, slug: string, cabeceras: array, filas: array, columnasMoneda?: array, ultimaFilaEsTotal?: bool} */
    abstract protected function datosParaExportar(): array;

    /** @return array<Action> */
    protected function accionesDeDescarga(): array
    {
        return [
            Action::make('exportar_excel')
                ->label('Excel')
                ->icon('heroicon-o-table-cells')
                ->color('success')
                ->tooltip('Descarga lo que estás viendo, con los filtros aplicados')
                ->action(fn () => $this->descargarExcel()),

            Action::make('exportar_pdf')
                ->label('PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('danger')
                ->tooltip('Descarga lo que estás viendo, con los filtros aplicados')
                ->action(fn () => $this->descargarPdf()),
        ];
    }

    protected function descargarExcel()
    {
        $d = $this->datosParaExportar();

        return Excel::download(
            new ReporteGenericoExport(
                $d['titulo'],
                $d['cabeceras'],
                $d['filas'],
                $d['columnasMoneda'] ?? [],
                $d['ultimaFilaEsTotal'] ?? false,
            ),
            $d['slug'] . '-' . now()->format('Y-m-d') . '.xlsx',
        );
    }

    protected function descargarPdf()
    {
        $d = $this->datosParaExportar();

        return PdfService::a4()
            ->setPaper(841.89, 595.28, 'landscape')
            ->generar('pdf.reporte-generico', [
                'titulo'            => $d['titulo'],
                'periodo'           => $d['periodo'],
                'cabeceras'         => $d['cabeceras'],
                'filas'             => $d['filas'],
                'columnasMoneda'    => $d['columnasMoneda'] ?? [],
                'ultimaFilaEsTotal' => $d['ultimaFilaEsTotal'] ?? false,
                'empresa'           => Empresa::find((int) session('id_empresa')),
            ], $d['slug'] . '-' . now()->format('Y-m-d') . '.pdf');
    }
}
