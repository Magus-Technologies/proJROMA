<?php

namespace App\Filament\Pages;

use App\Models\CajaMovimiento;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\ProductoVenta;
use App\Models\RutaVendedor;
use App\Models\TmsMercado;
use App\Models\User;
use App\Models\Venta;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class Utilidades extends Page
{
    /**
     * Categorías de egresos de caja que NO son gasto operativo:
     * COMPRA es mercadería (ya entra a la utilidad como costo de ventas
     * al venderse) y CIERRE/APERTURA/TRANSFERENCIA son movimientos
     * internos entre cajas.
     */
    public const CATEGORIAS_NO_GASTO = ['COMPRA', 'CIERRE', 'APERTURA', 'TRANSFERENCIA'];
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationLabel = 'Utilidades';
    protected static string|\UnitEnum|null $navigationGroup = 'Finanzas';
    protected static ?int $navigationSort = 6;

    protected static ?string $title = 'Utilidades';
    protected string $view = 'filament.pages.utilidades';

    public function getData(): array
    {
        $empresa = (int) session('id_empresa', 0);
        $sucursal = (int) session('sucursal', 1);
        $desde = request('desde', now()->startOfMonth()->format('Y-m-d'));
        $hasta = request('hasta', now()->format('Y-m-d'));
        $tab = request('tab', 'productos');

        $idsVentas = Venta::deEmpresa($empresa)->deSucursal($sucursal)->activas()
            ->whereBetween('fecha_emision', [$desde, $hasta])
            ->pluck('id_venta');

        $totalLinea = ProductoVenta::sqlTotalLinea();
        $productos = ProductoVenta::whereIn('id_venta', $idsVentas)
            ->selectRaw("
                id_producto,
                id_venta,
                SUM(cantidad) as cantidad,
                SUM({$totalLinea}) as venta,
                SUM(costo * cantidad) as costo
            ")
            ->groupBy('id_producto', 'id_venta')
            ->get();

        $totalVenta = $productos->sum('venta');
        $totalCosto = $productos->sum('costo');
        $totalUtilidad = $totalVenta - $totalCosto;
        $margenGeneral = $totalVenta > 0 ? round(($totalUtilidad / $totalVenta) * 100, 1) : 0;

        // ── Cascada del Estado de Resultados del período ──────────────
        // Utilidad bruta (arriba) − gastos operativos = utilidad operativa.
        // Gastos financieros e impuestos aún no se registran en el sistema,
        // por lo que la utilidad neta se muestra como estimada.
        $gastosOperativos = (float) CajaMovimiento::where('tipo', 'EGRESO')
            ->where('estado', 'CONFIRMADO')
            ->whereHas('caja', fn ($q) => $q->where('id_empresa', $empresa))
            ->whereNotIn('categoria', self::CATEGORIAS_NO_GASTO)
            ->whereBetween('fecha', [$desde, $hasta])
            ->sum('monto');

        $utilidadOperativa = $totalUtilidad - $gastosOperativos;
        $utilidadNeta = $utilidadOperativa; // − financieros − impuestos (no registrados)
        $margenNeto = $totalVenta > 0 ? round(($utilidadNeta / $totalVenta) * 100, 1) : 0;

        $ventasIds = $productos->pluck('id_venta')->unique();
        $ventas = Venta::whereIn('id_venta', $ventasIds)->get()->keyBy('id_venta');
        $clientes = Cliente::whereIn('id_cliente', $ventas->pluck('id_cliente')->unique())->get()->keyBy('id_cliente');
        $rutas = RutaVendedor::whereIn('id_ruta', $clientes->pluck('id_ruta')->filter()->unique())->get()->keyBy('id_ruta');
        $mercados = TmsMercado::whereIn('id', $clientes->pluck('mercado')->filter()->unique())->get()->keyBy('id');
        $vendedores = User::whereIn('usuario_id', $ventas->pluck('id_vendedor')->filter()->unique())->get()->keyBy('usuario_id');

        $productosInfo = Producto::whereIn('id_producto', $productos->pluck('id_producto')->unique())->get()->keyBy('id_producto');

        $porProducto = $productos->groupBy('id_producto')->map(function ($items, $idProd) use ($productosInfo) {
            $p = $productosInfo->get($idProd);
            $venta = $items->sum('venta');
            $costo = $items->sum('costo');
            return [
                'id' => $idProd,
                'descripcion' => $p?->descripcion ?? "Producto #{$idProd}",
                'venta' => $venta,
                'costo' => $costo,
                'utilidad' => $venta - $costo,
                'margen' => $venta > 0 ? round((($venta - $costo) / $venta) * 100, 1) : 0,
                'cantidad' => $items->sum('cantidad'),
            ];
        })->sortByDesc('utilidad')->values();

        $porVenta = $productos->groupBy('id_venta')->map(function ($items, $idVta) use ($ventas, $clientes) {
            $v = $ventas->get($idVta);
            $c = $v ? $clientes->get($v->id_cliente) : null;
            $venta = $items->sum('venta');
            $costo = $items->sum('costo');
            return [
                'id_venta' => $idVta,
                'documento' => $v ? "{$v->serie}-{$v->numero}" : "#{$idVta}",
                'fecha' => $v?->fecha_emision?->format('d/m/Y') ?? '',
                'cliente' => $c?->datos ?? 'N/D',
                'venta' => $venta,
                'costo' => $costo,
                'utilidad' => $venta - $costo,
                'margen' => $venta > 0 ? round((($venta - $costo) / $venta) * 100, 1) : 0,
            ];
        })->sortByDesc('utilidad')->values();

        $porMercado = $productos->groupBy(function ($item) use ($ventas, $clientes, $mercados) {
            $v = $ventas->get($item->id_venta);
            $c = $v ? $clientes->get($v->id_cliente) : null;
            $m = $c && $c->mercado ? $mercados->get($c->mercado) : null;
            return $m?->nombre ?? 'Sin mercado';
        })->map(function ($items, $nombre) {
            $venta = $items->sum('venta');
            $costo = $items->sum('costo');
            return [
                'mercado' => $nombre,
                'venta' => $venta,
                'costo' => $costo,
                'utilidad' => $venta - $costo,
                'margen' => $venta > 0 ? round((($venta - $costo) / $venta) * 100, 1) : 0,
                'cantidad' => $items->sum('cantidad'),
            ];
        })->sortByDesc('utilidad')->values();

        $porRuta = $productos->groupBy(function ($item) use ($ventas, $clientes, $rutas) {
            $v = $ventas->get($item->id_venta);
            $c = $v ? $clientes->get($v->id_cliente) : null;
            $r = $c && $c->id_ruta ? $rutas->get($c->id_ruta) : null;
            return $r?->nombre ?? 'Sin ruta';
        })->map(function ($items, $nombre) {
            $venta = $items->sum('venta');
            $costo = $items->sum('costo');
            return [
                'ruta' => $nombre,
                'venta' => $venta,
                'costo' => $costo,
                'utilidad' => $venta - $costo,
                'margen' => $venta > 0 ? round((($venta - $costo) / $venta) * 100, 1) : 0,
                'cantidad' => $items->sum('cantidad'),
            ];
        })->sortByDesc('utilidad')->values();

        $porFecha = $productos->groupBy(function ($item) use ($ventas) {
            $v = $ventas->get($item->id_venta);
            return $v?->fecha_emision?->format('Y-m-d') ?? 'Sin fecha';
        })->map(function ($items, $fecha) {
            $venta = $items->sum('venta');
            $costo = $items->sum('costo');
            return [
                'fecha' => $fecha,
                'venta' => $venta,
                'costo' => $costo,
                'utilidad' => $venta - $costo,
                'margen' => $venta > 0 ? round((($venta - $costo) / $venta) * 100, 1) : 0,
                'cantidad' => $items->sum('cantidad'),
            ];
        })->sortByDesc('fecha')->values();

        return [
            'total_venta' => $totalVenta,
            'total_costo' => $totalCosto,
            'total_utilidad' => $totalUtilidad,
            'margen_general' => $margenGeneral,
            'gastos_operativos' => $gastosOperativos,
            'utilidad_operativa' => $utilidadOperativa,
            'utilidad_neta' => $utilidadNeta,
            'margen_neto' => $margenNeto,
            'total_ventas_count' => $ventas->count(),
            'total_productos_count' => $productosInfo->count(),
            'tab' => $tab,
            'desde' => $desde,
            'hasta' => $hasta,
            'por_venta' => $porVenta,
            'por_producto' => $porProducto,
            'por_mercado' => $porMercado,
            'por_ruta' => $porRuta,
            'por_fecha' => $porFecha,
        ];
    }
}
