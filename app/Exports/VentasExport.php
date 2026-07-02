<?php

namespace App\Exports;

use App\Models\Venta;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class VentasExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    public function __construct(
        protected int $idEmpresa,
        protected int $sucursal,
        protected string $desde,
        protected string $hasta,
    ) {
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
            'Subtotal',
            'IGV',
            'Total',
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
}
