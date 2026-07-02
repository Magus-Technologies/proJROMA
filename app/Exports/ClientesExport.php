<?php

namespace App\Exports;

use App\Models\Cliente;
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
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class ClientesExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithTitle, WithColumnFormatting, WithEvents
{
    public function __construct(
        protected int $idEmpresa,
    ) {
    }

    public function title(): string
    {
        return 'Clientes';
    }

    public function collection()
    {
        return Cliente::where('id_empresa', $this->idEmpresa)
            ->orderBy('datos')
            ->get();
    }

    public function headings(): array
    {
        return [
            '#',
            'RUC/DNI',
            'Nombre / Razón Social',
            'Dirección',
            'Distrito',
            'Teléfono',
            'Email',
            'Última venta',
            'Total vendido (S/)',
        ];
    }

    public function map($cliente): array
    {
        return [
            $cliente->id_cliente,
            $cliente->documento,
            $cliente->datos,
            $cliente->direccion,
            $cliente->distrito,
            $cliente->telefono,
            $cliente->email,
            $cliente->ultima_venta?->format('d/m/Y') ?? '',
            (float) $cliente->total_venta,
        ];
    }

    public function columnFormats(): array
    {
        return [
            'I' => '"S/" #,##0.00',
            'B' => NumberFormat::FORMAT_TEXT,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event): void {
                $sheet   = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();
                $lastCol = 'I';

                // Header: bold white on dark blue, centered
                $sheet->getStyle("A1:{$lastCol}1")->applyFromArray([
                    'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E40AF']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getRowDimension(1)->setRowHeight(24);

                // Borders on the whole table
                $sheet->getStyle("A1:{$lastCol}{$lastRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D1D5DB']],
                    ],
                ]);

                // Zebra striping on data rows
                for ($row = 2; $row <= $lastRow; $row++) {
                    if ($row % 2 === 0) {
                        $sheet->getStyle("A{$row}:{$lastCol}{$row}")->applyFromArray([
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F3F4F6']],
                        ]);
                    }
                }

                // Center the narrow columns
                $sheet->getStyle("A2:B{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("H2:H{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Freeze header + enable filters
                $sheet->freezePane('A2');
                $sheet->setAutoFilter("A1:{$lastCol}{$lastRow}");
            },
        ];
    }
}
