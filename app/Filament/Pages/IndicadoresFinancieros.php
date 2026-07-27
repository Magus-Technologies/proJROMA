<?php

namespace App\Filament\Pages;

use App\Models\CajaMovimiento;
use App\Models\DiasCompra;
use App\Models\DiasVenta;
use App\Models\InventarioMovimiento;
use App\Models\Producto;
use App\Models\ProductoVenta;
use App\Models\Venta;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class IndicadoresFinancieros extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar-square';
    protected static ?string $navigationLabel = 'Indicadores Financieros';
    protected static string|\UnitEnum|null $navigationGroup = 'Finanzas';
    protected static ?int $navigationSort = 3;

    protected static ?string $title = 'Indicadores Financieros';
    protected string $view = 'filament.pages.indicadores-financieros';

    public function getData(): array
    {
        $empresa = (int) session('id_empresa', 0);
        $sucursal = (int) session('sucursal', 1);

        $ventasMes = Venta::deEmpresa($empresa)->deSucursal($sucursal)->activas()
            ->delMes()->sum('total');

        $costoMes = ProductoVenta::join('ventas as v', 'v.id_venta', '=', 'productos_ventas.id_venta')
            ->where('v.id_empresa', $empresa)
            ->where('v.sucursal', $sucursal)
            ->where('v.estado', '1')
            ->whereMonth('v.fecha_emision', now()->month)
            ->whereYear('v.fecha_emision', now()->year)
            ->selectRaw('SUM(productos_ventas.costo * productos_ventas.cantidad) as total')
            ->value('total') ?? 0;

        $gastosMes = CajaMovimiento::where('tipo', 'EGRESO')
            ->where('estado', 'CONFIRMADO')
            ->whereHas('caja', fn ($q) => $q->where('id_empresa', $empresa))
            ->whereMonth('fecha', now()->month)
            ->whereYear('fecha', now()->year)
            ->sum('monto');

        $utilidadBruta = $ventasMes - $costoMes;
        $utilidadNeta = $utilidadBruta - $gastosMes;
        $margenBruto = $ventasMes > 0 ? round(($utilidadBruta / $ventasMes) * 100, 1) : 0;
        $margenNeto = $ventasMes > 0 ? round(($utilidadNeta / $ventasMes) * 100, 1) : 0;

        $saldoCaja = CajaMovimiento::where('estado', 'CONFIRMADO')
            ->whereHas('caja', fn ($q) => $q->where('id_empresa', $empresa))
            ->selectRaw("SUM(CASE WHEN tipo='INGRESO' THEN monto ELSE 0 END) - SUM(CASE WHEN tipo='EGRESO' THEN monto ELSE 0 END) as saldo")
            ->value('saldo') ?? 0;

        $cxPTotal = DiasCompra::whereNull('estado')
            ->whereHas('compra', fn ($q) => $q->where('id_empresa', $empresa))
            ->sum('monto');
        $liquidez = $cxPTotal > 0 ? round($saldoCaja / $cxPTotal, 2) : 0;

        // Valor del inventario = costo unitario × stock (no solo el costo unitario)
        $inventarioCosto = (float) Producto::where('id_empresa', $empresa)
            ->selectRaw('COALESCE(SUM(costo * cantidad), 0) as total')
            ->value('total');
        $rotacionInventario = $inventarioCosto > 0 ? round($costoMes / $inventarioCosto, 2) : 0;

        $roi = ($costoMes + $gastosMes) > 0
            ? round(($utilidadNeta / ($costoMes + $gastosMes)) * 100, 1)
            : 0;

        $ebitda = $utilidadNeta;
        $margenContribucion = $ventasMes > 0 ? ($utilidadBruta / $ventasMes) : 0;
        $puntoEquilibrio = $margenContribucion > 0
            ? round($gastosMes / $margenContribucion, 2)
            : 0;

        $ventas12Meses = Venta::deEmpresa($empresa)->deSucursal($sucursal)->activas()
            ->where('fecha_emision', '>=', now()->subMonths(12))
            ->sum('total');

        return [
            'ventas_mes' => $ventasMes,
            'costo_mes' => $costoMes,
            'gastos_mes' => $gastosMes,
            'utilidad_bruta' => $utilidadBruta,
            'utilidad_neta' => $utilidadNeta,
            'margen_bruto' => $margenBruto,
            'margen_neto' => $margenNeto,
            'saldo_caja' => $saldoCaja,
            'cxp_total' => $cxPTotal,
            'liquidez' => $liquidez,
            'inventario_costo' => $inventarioCosto,
            'rotacion_inventario' => $rotacionInventario,
            'roi' => $roi,
            'ebitda' => $ebitda,
            'punto_equilibrio' => $puntoEquilibrio,
            'ventas_12m' => $ventas12Meses,
        ];
    }
}
