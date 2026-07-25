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

class ClientesTemplateExport implements FromArray, WithHeadings, ShouldAutoSize, WithTitle, WithEvents
{
    public function title(): string
    {
        return 'Plantilla Clientes';
    }

    public function array(): array
    {
        return [
            ['20123456789', 'EJEMPLO DE CLIENTE SAC', 'Av. Ejemplo 123', 'Lima', '999888777', 'cliente@ejemplo.com', 'Mercado Norte'],
        ];
    }

    public function headings(): array
    {
        return [
            'RUC/DNI',
            'Nombre / Razón Social *',
            'Dirección',
            'Distrito',
            'Teléfono',
            'Email',
            'Mercado / Zona',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event): void {
                $sheet = $event->sheet->getDelegate();
                $lastCol = 'G';

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
