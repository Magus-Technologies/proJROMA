<?php

namespace App\Exports;

use App\Models\Venta;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class VentasExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithTitle, WithColumnFormatting, WithEvents
{
    public function __construct(
        protected int $idEmpresa,
        protected int $sucursal,
        protected string $desde,
        protected string $hasta,
    ) {
    }

    public function title(): string
    {
        return 'Ventas';
    }

    public function collection()
    {
        return Venta::with(['cliente', 'tipoDocumento', 'vendedor'])
            ->where('id_empresa', $this->idEmpresa)
            ->where('sucursal', $this->sucursal)
            ->whereBetween('fecha_emision', [$this->desde, $this->hasta])
            ->orderBy('fecha_emision')
            ->orderBy('id_venta')
            ->get();
    }

    public function headings(): array
    {
        return [
            '#',
            'Tipo Doc.',
            'Documento',
            'Fecha Emisión',
            'Cliente',
            'Vendedor',
            'Tipo Pago',
            'Estado',
            'Subtotal (S/)',
            'IGV (S/)',
            'Total (S/)',
        ];
    }

    public function map($venta): array
    {
        return [
            $venta->id_venta,
            $venta->tipoDocumento?->tipo_doc ?? '',
            $venta->documento_completo,
            $venta->fecha_emision ? \Carbon\Carbon::parse($venta->fecha_emision)->format('d/m/Y') : '',
            $venta->cliente?->datos ?? '',
            $venta->vendedor?->nombre_completo ?? '',
            $venta->id_tipo_pago == 2 ? 'Crédito' : 'Contado',
            $venta->estado === '0' ? 'Anulada' : ($venta->estado === '2' ? 'Crédito' : 'Activa'),
            (float) $venta->subtotal,
            (float) $venta->igv,
            (float) $venta->total,
        ];
    }

    public function columnFormats(): array
    {
        return [
            'I' => '"S/" #,##0.00',
            'J' => '"S/" #,##0.00',
            'K' => '"S/" #,##0.00',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event): void {
                $sheet   = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();
                $lastCol = 'K';

                $sheet->getStyle("A1:{$lastCol}1")->applyFromArray([
                    'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E40AF']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getRowDimension(1)->setRowHeight(24);

                $sheet->getStyle("A1:{$lastCol}{$lastRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D1D5DB']],
                    ],
                ]);

                for ($row = 2; $row <= $lastRow; $row++) {
                    if ($row % 2 === 0) {
                        $sheet->getStyle("A{$row}:{$lastCol}{$row}")->applyFromArray([
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F3F4F6']],
                        ]);
                    }
                }

                $sheet->getStyle("A2:D{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("G2:H{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Totals row
                $totalRow = $lastRow + 1;
                $sheet->setCellValue("H{$totalRow}", 'TOTAL');
                $sheet->setCellValue("I{$totalRow}", "=SUM(I2:I{$lastRow})");
                $sheet->setCellValue("J{$totalRow}", "=SUM(J2:J{$lastRow})");
                $sheet->setCellValue("K{$totalRow}", "=SUM(K2:K{$lastRow})");
                $sheet->getStyle("H{$totalRow}:K{$totalRow}")->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DBEAFE']],
                ]);
                $sheet->getStyle("I{$totalRow}:K{$totalRow}")->getNumberFormat()->setFormatCode('"S/" #,##0.00');

                $sheet->freezePane('A2');
                $sheet->setAutoFilter("A1:{$lastCol}{$lastRow}");
            },
        ];
    }
}
