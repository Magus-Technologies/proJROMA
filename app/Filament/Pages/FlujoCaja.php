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

        $saldoCaja = CajaMovimiento::where('estado', 'CONFIRMADO')
            ->selectRaw("SUM(CASE WHEN tipo='INGRESO' THEN monto ELSE 0 END) - SUM(CASE WHEN tipo='EGRESO' THEN monto ELSE 0 END) as saldo")
            ->value('saldo') ?? 0;

        $hoy = now()->startOfDay();

        $cxcPorVencer = DiasVenta::where('estado', '0')
            ->where('fecha', '>=', $hoy)
            ->sum('monto');

        $cxcVencido = DiasVenta::where('estado', '0')
            ->where('fecha', '<', $hoy)
            ->sum('monto');

        $cxPPorVencer = DiasCompra::whereNull('estado')
            ->where('fecha', '>=', $hoy)
            ->sum('monto');

        $cxPVencido = DiasCompra::whereNull('estado')
            ->where('fecha', '<', $hoy)
            ->sum('monto');

        $proyeccion = collect([30, 60, 90])->mapWithKeys(function ($dias) use ($empresa, $hoy) {
            $hasta = (clone $hoy)->addDays($dias);
            $ingresos = DiasVenta::where('estado', '0')
                ->whereBetween('fecha', [$hoy, $hasta])
                ->sum('monto');
            $egresos = DiasCompra::whereNull('estado')
                ->whereBetween('fecha', [$hoy, $hasta])
                ->sum('monto');
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
}
