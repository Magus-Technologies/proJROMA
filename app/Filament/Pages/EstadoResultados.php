<?php

namespace App\Filament\Pages;

use App\Models\CajaMovimiento;
use App\Models\Compra;
use App\Models\ProductoVenta;
use App\Models\Venta;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class EstadoResultados extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-presentation-chart-bar';
    protected static ?string $navigationLabel = 'Estado de Resultados';
    protected static string|\UnitEnum|null $navigationGroup = 'Finanzas';
    protected static ?int $navigationSort = 8;

    protected static ?string $title = 'Estado de Resultados';
    protected string $view = 'filament.pages.estado-resultados';

    public function getData(): array
    {
        $empresa = (int) session('id_empresa', 0);
        $sucursal = (int) session('sucursal', 1);
        $desde = request('desde', now()->startOfMonth()->format('Y-m-d'));
        $hasta = request('hasta', now()->format('Y-m-d'));

        $ventas = Venta::deEmpresa($empresa)->deSucursal($sucursal)->activas()
            ->whereBetween('fecha_emision', [$desde, $hasta])->sum('total');

        $costoVentas = ProductoVenta::join('ventas as v', 'v.id_venta', '=', 'productos_ventas.id_venta')
            ->where('v.id_empresa', $empresa)
            ->where('v.sucursal', $sucursal)
            ->where('v.estado', '1')
            ->whereBetween('v.fecha_emision', [$desde, $hasta])
            ->selectRaw('SUM(productos_ventas.costo * productos_ventas.cantidad) as total')
            ->value('total') ?? 0;

        $compras = Compra::deEmpresa($empresa)
            ->whereBetween(DB::raw('STR_TO_DATE(fecha_emision, "%Y-%m-%d")'), [$desde, $hasta])
            ->sum(DB::raw('CAST(total AS DECIMAL(12,2))'));

        // Solo gastos operativos: la compra de mercadería ya está en el costo
        // de ventas (restarla aquí duplicaría el costo) y los movimientos
        // internos de caja no son gasto.
        $gastos = CajaMovimiento::where('tipo', 'EGRESO')
            ->where('estado', 'CONFIRMADO')
            ->whereHas('caja', fn ($q) => $q->where('id_empresa', $empresa))
            ->whereNotIn('categoria', Utilidades::CATEGORIAS_NO_GASTO)
            ->whereBetween('fecha', [$desde, $hasta])
            ->sum('monto');

        $utilidadBruta = $ventas - $costoVentas;
        $utilidadNeta = $utilidadBruta - $gastos;
        $margenBruto = $ventas > 0 ? round(($utilidadBruta / $ventas) * 100, 1) : 0;
        $margenNeto = $ventas > 0 ? round(($utilidadNeta / $ventas) * 100, 1) : 0;

        return [
            'ventas' => $ventas,
            'costo_ventas' => $costoVentas,
            'utilidad_bruta' => $utilidadBruta,
            'margen_bruto' => $margenBruto,
            'compras' => $compras,
            'gastos' => $gastos,
            'utilidad_neta' => $utilidadNeta,
            'margen_neto' => $margenNeto,
            'desde' => $desde,
            'hasta' => $hasta,
        ];
    }

    public function getChartData(): array
    {
        $empresa = (int) session('id_empresa', 0);
        $sucursal = (int) session('sucursal', 1);

        $meses = collect(range(5, 0))->map(fn ($i) => now()->subMonths($i));

        $ventas = $meses->map(function ($date) use ($empresa, $sucursal) {
            return Venta::deEmpresa($empresa)->deSucursal($sucursal)->activas()
                ->whereYear('fecha_emision', $date->year)
                ->whereMonth('fecha_emision', $date->month)
                ->sum('total');
        });

        $costos = $meses->map(function ($date) use ($empresa, $sucursal) {
            return ProductoVenta::join('ventas as v', 'v.id_venta', '=', 'productos_ventas.id_venta')
                ->where('v.id_empresa', $empresa)
                ->where('v.sucursal', $sucursal)
                ->where('v.estado', '1')
                ->whereYear('v.fecha_emision', $date->year)
                ->whereMonth('v.fecha_emision', $date->month)
                ->selectRaw('SUM(productos_ventas.costo * productos_ventas.cantidad) as total')
                ->value('total') ?? 0;
        });

        return [
            'labels' => $meses->map(fn ($d) => $d->translatedFormat('M Y'))->toArray(),
            'ventas' => $ventas->toArray(),
            'costos' => $costos->toArray(),
            'utilidad' => $ventas->map(fn ($v, $i) => $v - ($costos[$i] ?? 0))->toArray(),
        ];
    }
}
