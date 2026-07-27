<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ProductosTemplateExport implements FromArray, WithHeadings, ShouldAutoSize, WithTitle, WithEvents
{
    public function title(): string
    {
        return 'Plantilla Productos';
    }

    public function array(): array
    {
        return [
            [
                '7750123456789',            // Cod. Barra
                'P-001',                    // Código
                'ACEITE VEGETAL BOTELLA 1L', // Descripción
                'UNIDAD',                   // Unidad de Medida
                'CAJA X 12',                // Presentación
                12,                         // Unid. por Presentación
                0.95,                       // Peso (kg)
                'Abarrotes',                // Categoría
                'Aceites',                  // Subcategoría
                'Primor',                   // Marca
                'Premium',                  // Submarca
                5.50,                       // Precio
                4.80,                       // Costo
            ],
        ];
    }

    public function headings(): array
    {
        return [
            'Cod. Barra',
            'Código',
            'Descripción *',
            'Unidad de Medida',
            'Presentación',
            'Unid. por Presentación',
            'Peso (kg) *',
            'Categoría',
            'Subcategoría',
            'Marca',
            'Submarca',
            'Precio',
            'Costo',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event): void {
                $sheet = $event->sheet->getDelegate();
                $lastCol = 'M';

                $sheet->getStyle("A1:{$lastCol}1")->applyFromArray([
                    'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E40AF']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getRowDimension(1)->setRowHeight(24);

                $sheet->getStyle("A2:{$lastCol}2")->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFF3CD']],
                ]);

                $sheet->getStyle("A1:{$lastCol}2")->applyFromArray([
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D1D5DB']],
                    ],
                ]);

                $sheet->freezePane('A3');
            },
        ];
    }
}
