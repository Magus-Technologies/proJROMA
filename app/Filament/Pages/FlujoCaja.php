<?php

namespace App\Filament\Pages;

use App\Models\CajaMovimiento;
use App\Models\DiasCompra;
use App\Models\DiasVenta;
use Carbon\Carbon;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class FlujoCaja extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationLabel = 'Flujo de Caja';
    protected static string|\UnitEnum|null $navigationGroup = 'Finanzas';
    protected static ?int $navigationSort = 2;

    protected static ?string $title = 'Flujo de Caja';
    protected string $view = 'filament.pages.flujo-caja';

    public function getData(): array
    {
        $empresa = (int) session('id_empresa', 0);
        $sucursal = (int) session('sucursal', 1);

        $saldoCaja = CajaMovimiento::where('estado', 'CONFIRMADO')
            ->whereHas('caja', fn ($q) => $q->where('id_empresa', $empresa))
            ->selectRaw("SUM(CASE WHEN tipo='INGRESO' THEN monto ELSE 0 END) - SUM(CASE WHEN tipo='EGRESO' THEN monto ELSE 0 END) as saldo")
            ->value('saldo') ?? 0;

        $hoy = now()->startOfDay();

        $cxcPorVencer = $this->saldoCxc($empresa, $sucursal, fn ($q) => $q->where('dv.fecha', '>=', $hoy));
        $cxcVencido   = $this->saldoCxc($empresa, $sucursal, fn ($q) => $q->where('dv.fecha', '<', $hoy));

        $cxPPorVencer = $this->pendienteCxp($empresa, fn ($q) => $q->where('dc.fecha', '>=', $hoy));
        $cxPVencido   = $this->pendienteCxp($empresa, fn ($q) => $q->where('dc.fecha', '<', $hoy));

        $proyeccion = collect([30, 60, 90])->mapWithKeys(function ($dias) use ($empresa, $sucursal, $hoy) {
            $hasta = (clone $hoy)->addDays($dias);
            $ingresos = $this->saldoCxc($empresa, $sucursal, fn ($q) => $q->whereBetween('dv.fecha', [$hoy, $hasta]));
            $egresos = $this->pendienteCxp($empresa, fn ($q) => $q->whereBetween('dc.fecha', [$hoy, $hasta]));
            return [$dias . ' días' => ['ingresos' => $ingresos, 'egresos' => $egresos, 'neto' => $ingresos - $egresos]];
        });

        return [
            'saldo_caja' => $saldoCaja,
            'cxc_por_vencer' => $cxcPorVencer,
            'cxc_vencido' => $cxcVencido,
            'cxc_total' => $cxcPorVencer + $cxcVencido,
            'cxp_por_vencer' => $cxPPorVencer,
            'cxp_vencido' => $cxPVencido,
            'cxp_total' => $cxPPorVencer + $cxPVencido,
            'flujo_neto' => ($cxcPorVencer + $cxcVencido) - ($cxPPorVencer + $cxPVencido),
            'proyeccion' => $proyeccion,
        ];
    }

    /**
     * Saldo pendiente de cuotas de venta (monto - abonos activos) de la
     * empresa/sucursal, con condición de fechas variable.
     */
    private function saldoCxc(int $empresa, int $sucursal, \Closure $fechas): float
    {
        $q = DB::table('dias_ventas as dv')
            ->join('ventas as v', 'v.id_venta', '=', 'dv.id_venta')
            ->where('v.id_empresa', $empresa)
            ->where('v.sucursal', $sucursal)
            ->where('v.estado', '!=', '0')
            ->where('dv.estado', '0');

        $fechas($q);

        return (float) $q->selectRaw("COALESCE(SUM(dv.monto - COALESCE((SELECT SUM(a.monto) FROM cxc_abonos a WHERE a.id_dias_venta = dv.dias_venta_id AND a.estado = 'ACTIVO'), 0)), 0) as s")
            ->value('s');
    }

    /** Cuotas de compra pendientes de la empresa, con condición de fechas variable. */
    private function pendienteCxp(int $empresa, \Closure $fechas): float
    {
        $q = DB::table('dias_compras as dc')
            ->join('compras as c', 'c.id_compra', '=', 'dc.id_compra')
            ->where('c.id_empresa', $empresa)
            ->whereNull('dc.estado');

        $fechas($q);

        return (float) $q->sum('dc.monto');
    }
}
