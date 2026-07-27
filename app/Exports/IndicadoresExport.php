<?php

namespace App\Exports;

use App\Filament\Pages\IndicadoresFinancieros;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class IndicadoresExport implements FromArray, WithTitle, WithEvents
{
    private array $secciones;

    /** Números de fila (1-based) para aplicar estilos en AfterSheet */
    private array $filasSeccion = [];
    private array $filasCabecera = [];
    /** [fila => estado] para colorear la columna Valor */
    private array $filasItem = [];
    private int $filaNota = 0;

    private const COLOR_ESTADO = [
        'ok'       => '047857', // emerald-700
        'atencion' => 'B45309', // amber-700
        'riesgo'   => 'B91C1C', // red-700
        'info'     => '374151', // gray-700
    ];

    public function __construct(private string $empresaNombre)
    {
        $this->secciones = app(IndicadoresFinancieros::class)->getSecciones();
    }

    public function title(): string
    {
        return 'Indicadores';
    }

    public function array(): array
    {
        $rows = [];
        $rows[] = ['INDICADORES FINANCIEROS — ' . mb_strtoupper($this->empresaNombre)];
        $rows[] = ['Período: ' . ucfirst(now()->translatedFormat('F Y')) . '  |  Generado el ' . now()->format('d/m/Y H:i')];
        $rows[] = [''];

        foreach ($this->secciones as $sec) {
            $this->filasSeccion[] = count($rows) + 1;
            $rows[] = [mb_strtoupper($sec['titulo'])];

            $this->filasCabecera[] = count($rows) + 1;
            $rows[] = ['Indicador', 'Valor', 'Cómo se calcula', 'Interpretación', 'Estado'];

            foreach ($sec['items'] as $item) {
                $this->filasItem[count($rows) + 1] = $item['estado'];
                $rows[] = [
                    $item['label'],
                    $item['valor'],
                    $item['formula'],
                    $item['nota'],
                    match ($item['estado']) {
                        'ok' => 'Óptimo',
                        'atencion' => 'Atención',
                        'riesgo' => 'Riesgo',
                        default => 'Informativo',
                    },
                ];
            }

            $rows[] = [''];
        }

        $this->filaNota = count($rows) + 1;
        $rows[] = ['* Indicadores calculados automáticamente desde Ventas, Compras, Inventario y Caja del mes en curso. Son aproximaciones operativas de gestión, no reemplazan a los estados financieros contables.'];

        return $rows;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event): void {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();

                // Anchos fijos (autosize no va bien con textos largos de notas)
                foreach (['A' => 24, 'B' => 18, 'C' => 38, 'D' => 68, 'E' => 13] as $col => $w) {
                    $sheet->getColumnDimension($col)->setWidth($w);
                }

                // Título principal
                $sheet->mergeCells('A1:E1');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '1E40AF']],
                ]);
                $sheet->mergeCells('A2:E2');
                $sheet->getStyle('A2')->getFont()->setSize(9)->getColor()->setRGB('6B7280');

                // Títulos de sección
                foreach ($this->filasSeccion as $fila) {
                    $sheet->mergeCells("A{$fila}:E{$fila}");
                    $sheet->getStyle("A{$fila}")->applyFromArray([
                        'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E40AF']],
                    ]);
                    $sheet->getRowDimension($fila)->setRowHeight(20);
                }

                // Cabeceras de tabla
                foreach ($this->filasCabecera as $fila) {
                    $sheet->getStyle("A{$fila}:E{$fila}")->applyFromArray([
                        'font' => ['bold' => true, 'size' => 9, 'color' => ['rgb' => '374151']],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E5E7EB']],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D1D5DB']]],
                    ]);
                }

                // Filas de indicadores
                foreach ($this->filasItem as $fila => $estado) {
                    $color = self::COLOR_ESTADO[$estado] ?? self::COLOR_ESTADO['info'];

                    $sheet->getStyle("A{$fila}:E{$fila}")->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E5E7EB']]],
                        'alignment' => ['vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                    ]);
                    $sheet->getStyle("A{$fila}")->getFont()->setBold(true);
                    $sheet->getStyle("B{$fila}")->applyFromArray([
                        'font' => ['bold' => true, 'color' => ['rgb' => $color]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                    ]);
                    $sheet->getStyle("C{$fila}")->getFont()->setSize(8)->getColor()->setRGB('9CA3AF');
                    $sheet->getStyle("D{$fila}")->getFont()->setSize(9);
                    $sheet->getStyle("E{$fila}")->applyFromArray([
                        'font' => ['bold' => true, 'size' => 9, 'color' => ['rgb' => $color]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    ]);
                }

                // Nota metodológica
                if ($this->filaNota > 0) {
                    $sheet->mergeCells("A{$this->filaNota}:E{$this->filaNota}");
                    $sheet->getStyle("A{$this->filaNota}")->applyFromArray([
                        'font' => ['italic' => true, 'size' => 8, 'color' => ['rgb' => '9CA3AF']],
                        'alignment' => ['wrapText' => true],
                    ]);
                    $sheet->getRowDimension($this->filaNota)->setRowHeight(28);
                }

                $sheet->freezePane('A4');
                $sheet->getStyle("A1:E{$lastRow}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            },
        ];
    }
}
