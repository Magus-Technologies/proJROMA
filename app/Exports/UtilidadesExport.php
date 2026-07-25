<?php

namespace App\Exports;

use App\Models\Cliente;
use App\Models\Producto;
use App\Models\ProductoVenta;
use App\Models\RutaVendedor;
use App\Models\TmsMercado;
use App\Models\Venta;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class UtilidadesExport implements WithMultipleSheets, ShouldAutoSize
{
    public function __construct(
        protected int $idEmpresa,
        protected int $sucursal,
        protected string $desde,
        protected string $hasta,
    ) {}

    public function sheets(): array
    {
        $data = $this->loadData();

        return [
            new UtilidadesSheetExport('Resumen', $this->buildResumen($data)),
            new UtilidadesSheetExport('Por Productos', $data['por_producto'], [
                'Producto', 'Cantidad', 'Venta S/.', 'Costo S/.', 'Utilidad S/.', 'Margen %',
            ], [2, 3, 4], [5]),
            new UtilidadesSheetExport('Por Ventas', $data['por_venta'], [
                'Documento', 'Fecha', 'Cliente', 'Venta S/.', 'Costo S/.', 'Utilidad S/.', 'Margen %',
            ], [3, 4, 5], [6]),
            new UtilidadesSheetExport('Por Mercados', $data['por_mercado'], [
                'Mercado', 'Cantidad', 'Venta S/.', 'Costo S/.', 'Utilidad S/.', 'Margen %',
            ], [2, 3, 4], [5]),
            new UtilidadesSheetExport('Por Rutas', $data['por_ruta'], [
                'Ruta', 'Cantidad', 'Venta S/.', 'Costo S/.', 'Utilidad S/.', 'Margen %',
            ], [2, 3, 4], [5]),
            new UtilidadesSheetExport('Por Fechas', $data['por_fecha'], [
                'Fecha', 'Cantidad', 'Venta S/.', 'Costo S/.', 'Utilidad S/.', 'Margen %',
            ], [2, 3, 4], [5]),
        ];
    }

    private function loadData(): array
    {
        $empresa = $this->idEmpresa;
        $sucursal = $this->sucursal;

        $idsVentas = Venta::deEmpresa($empresa)->deSucursal($sucursal)->activas()
            ->whereBetween('fecha_emision', [$this->desde, $this->hasta])
            ->pluck('id_venta');

        $productos = ProductoVenta::whereIn('id_venta', $idsVentas)
            ->selectRaw('id_producto, id_venta, SUM(cantidad) as cantidad, SUM(total) as venta, SUM(costo * cantidad) as costo')
            ->groupBy('id_producto', 'id_venta')
            ->get();

        $ventas = Venta::whereIn('id_venta', $productos->pluck('id_venta')->unique())->get()->keyBy('id_venta');
        $clientes = Cliente::whereIn('id_cliente', $ventas->pluck('id_cliente')->unique())->get()->keyBy('id_cliente');
        $rutas = RutaVendedor::whereIn('id_ruta', $clientes->pluck('id_ruta')->filter()->unique())->get()->keyBy('id_ruta');
        $mercados = TmsMercado::whereIn('id', $clientes->pluck('mercado')->filter()->unique())->get()->keyBy('id');
        $productosInfo = Producto::whereIn('id_producto', $productos->pluck('id_producto')->unique())->get()->keyBy('id_producto');

        $total_venta = $productos->sum('venta');
        $total_costo = $productos->sum('costo');

        return [
            'por_producto' => $productos->groupBy('id_producto')->map(fn ($items, $idProd) => $this->mapRow($items, $productosInfo->get($idProd)?->descripcion ?? "Producto #{$idProd}"))->sortByDesc('utilidad')->values()->toArray(),
            'por_venta' => $productos->groupBy('id_venta')->map(fn ($items, $idVta) => $this->mapVentaRow($items, $ventas->get($idVta), $clientes))->sortByDesc('utilidad')->values()->toArray(),
            'por_mercado' => $this->groupByRelation($productos, $ventas, $clientes, $mercados, 'mercado', 'nombre', 'Sin mercado')->toArray(),
            'por_ruta' => $this->groupByRelation($productos, $ventas, $clientes, $rutas, 'id_ruta', 'nombre', 'Sin ruta')->toArray(),
            'por_fecha' => $productos->groupBy(fn ($item) => $ventas->get($item->id_venta)?->fecha_emision?->format('Y-m-d') ?? 'Sin fecha')
                ->map(fn ($items, $fecha) => ['fecha' => $fecha] + $this->calc($items))->sortByDesc('fecha')->values()->toArray(),
            'total_venta' => $total_venta,
            'total_costo' => $total_costo,
            'productos' => $productos->toArray(),
            'ventas' => $ventas->toArray(),
        ];
    }

    private function mapRow($items, string $label): array
    {
        $venta = $items->sum('venta');
        $costo = $items->sum('costo');
        return [
            'label' => $label,
            'cantidad' => $items->sum('cantidad'),
            'venta' => round($venta, 2),
            'costo' => round($costo, 2),
            'utilidad' => round($venta - $costo, 2),
            'margen' => $venta > 0 ? round((($venta - $costo) / $venta) * 100, 1) : 0,
        ];
    }

    private function mapVentaRow($items, $v, $clientes): array
    {
        $c = $v ? $clientes->get($v->id_cliente) : null;
        $venta = $items->sum('venta');
        $costo = $items->sum('costo');
        return [
            'documento' => $v ? "{$v->serie}-{$v->numero}" : "#{$v?->id_venta}",
            'fecha' => $v?->fecha_emision?->format('d/m/Y') ?? '',
            'cliente' => $c?->datos ?? 'N/D',
            'venta' => round($venta, 2),
            'costo' => round($costo, 2),
            'utilidad' => round($venta - $costo, 2),
            'margen' => $venta > 0 ? round((($venta - $costo) / $venta) * 100, 1) : 0,
        ];
    }

    private function calc($items): array
    {
        $venta = $items->sum('venta');
        $costo = $items->sum('costo');
        return [
            'cantidad' => $items->sum('cantidad'),
            'venta' => round($venta, 2),
            'costo' => round($costo, 2),
            'utilidad' => round($venta - $costo, 2),
            'margen' => $venta > 0 ? round((($venta - $costo) / $venta) * 100, 1) : 0,
        ];
    }

    private function groupByRelation($productos, $ventas, $clientes, $lookup, $field, $nameField, $default): \Illuminate\Support\Collection
    {
        return $productos->groupBy(function ($item) use ($ventas, $clientes, $lookup, $field, $nameField, $default) {
            $v = $ventas->get($item->id_venta);
            $c = $v ? $clientes->get($v->id_cliente) : null;
            $r = $c && $c->$field ? $lookup->get($c->$field) : null;
            return $r?->$nameField ?? $default;
        })->map(fn ($items, $nombre) => ['label' => $nombre] + $this->calc($items))
          ->sortByDesc('utilidad')->values();
    }

    private function buildResumen(array $data): array
    {
        $totalVenta = $data['total_venta'];
        $totalCosto = $data['total_costo'];
        $totalUtilidad = $totalVenta - $totalCosto;
        return [
            ['Indicador', 'Valor'],
            ['Ventas', 'S/ ' . number_format($totalVenta, 2)],
            ['Costo Total', 'S/ ' . number_format($totalCosto, 2)],
            ['Utilidad', 'S/ ' . number_format($totalUtilidad, 2)],
            ['Margen General', ($totalVenta > 0 ? round(($totalUtilidad / $totalVenta) * 100, 1) : 0) . '%'],
            ['Ventas realizadas', count($data['ventas'])],
            ['Período', $this->desde . ' al ' . $this->hasta],
        ];
    }
}

class UtilidadesSheetExport implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithHeadings, \Maatwebsite\Excel\Concerns\ShouldAutoSize, \Maatwebsite\Excel\Concerns\WithTitle, \Maatwebsite\Excel\Concerns\WithEvents
{
    public function __construct(
        protected string $title,
        protected array $data,
        protected array $headers = [],
        protected array $colsMoneda = [],
        protected array $colsPct = [],
    ) {}

    public function title(): string { return $this->title; }

    public function headings(): array { return $this->headers; }

    public function array(): array
    {
        if ($this->title === 'Resumen') {
            return array_slice($this->data, 1);
        }

        $moneda = $this->colsMoneda;
        $pct = $this->colsPct;
        return array_map(function ($row) use ($moneda, $pct) {
            $out = [];
            foreach ($this->headers as $i => $h) {
                $keys = array_keys($this->headers);
                $key = $keys[$i] ?? $i;
                $val = $row[array_keys($row)[$i]] ?? '';
                if (in_array($i, $moneda)) {
                    $out[] = $val;
                } elseif (in_array($i, $pct)) {
                    $out[] = $val;
                } else {
                    $out[] = $val;
                }
            }
            return $out;
        }, $this->data);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event): void {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();
                $lastCol = $sheet->getHighestColumn();

                $sheet->getStyle("A1:{$lastCol}1")->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E40AF']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getRowDimension(1)->setRowHeight(22);

                $sheet->getStyle("A1:{$lastCol}{$lastRow}")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D1D5DB']]],
                ]);

                for ($row = 2; $row <= $lastRow; $row++) {
                    if ($row % 2 === 0) {
                        $sheet->getStyle("A{$row}:{$lastCol}{$row}")->applyFromArray([
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F3F4F6']],
                        ]);
                    }
                }

                $sheet->freezePane('A2');
            },
        ];
    }
}
