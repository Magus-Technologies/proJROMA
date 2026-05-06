<?php

namespace App\Http\Controllers;

use App\Models\{Venta, Cliente, Producto, Compra, Cotizacion};
use Illuminate\Http\Request;
use Illuminate\Routing\Attributes\Middleware;
use Illuminate\Support\Facades\DB;

// ── Laravel 13: #[Middleware] a nivel de clase ────────────────────────────
#[Middleware(['auth', 'check.empresa', 'session.timeout'])]
class DashboardController extends Controller
{
    public function index(): \Illuminate\View\View
    {
        $empresa  = (int) session('id_empresa');
        $sucursal = (int) session('sucursal');

        // ── KPIs ──────────────────────────────────────────────────────────
        $ventasMes = Venta::deEmpresa($empresa)
            ->deSucursal($sucursal)
            ->activas()
            ->delMes()
            ->sum('total');

        $comprasMes = Compra::deEmpresa($empresa)
            ->delMes()
            ->sum('total');

        $clientesTotales = Cliente::deEmpresa($empresa)->count();

        $pedidosPendientes = Cotizacion::deEmpresa($empresa)
            ->pendientes()
            ->count();

        // ── Bajo stock ────────────────────────────────────────────────────
        $bajoStock = Producto::deEmpresa($empresa)
            ->activos()
            ->bajoStock(5)
            ->orderBy('cantidad')
            ->limit(10)
            ->get();

        // ── Ventas diarias (30 días) ───────────────────────────────────────
        $ventasDiarias = Venta::deEmpresa($empresa)
            ->deSucursal($sucursal)
            ->activas()
            ->where('fecha_emision', '>=', now()->subDays(30))
            ->selectRaw('DATE(fecha_emision) as fecha, SUM(total) as total')
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get();

        // ── Top 5 clientes del mes ─────────────────────────────────────────
        $topClientes = DB::table('ventas as v')
            ->join('clientes as c', 'c.id_cliente', '=', 'v.id_cliente')
            ->where('v.id_empresa', $empresa)
            ->where('v.sucursal', $sucursal)
            ->where('v.estado', '1')
            ->whereMonth('v.fecha_emision', now()->month)
            ->whereYear('v.fecha_emision', now()->year)
            ->selectRaw('c.datos as nombre, SUM(v.total) as total')
            ->groupBy('c.id_cliente', 'c.datos')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // ── Últimas ventas ────────────────────────────────────────────────
        $ultimasVentas = Venta::with(['cliente', 'tipoDocumento'])
            ->deEmpresa($empresa)
            ->deSucursal($sucursal)
            ->orderByDesc('id_venta')
            ->limit(8)
            ->get();

        return view('dashboard.index', compact(
            'ventasMes', 'comprasMes', 'clientesTotales', 'pedidosPendientes',
            'bajoStock', 'ventasDiarias', 'topClientes', 'ultimasVentas'
        ));
    }
}
