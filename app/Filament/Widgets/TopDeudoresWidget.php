<?php

namespace App\Filament\Widgets;

use Carbon\Carbon;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\DB;

class TopDeudoresWidget extends Widget
{
    protected static ?int $sort = 7;
    protected int|string|array $columnSpan = 1;
    protected string $view = 'filament.widgets.top-deudores';

    public function render(): \Illuminate\Contracts\View\View
    {
        $empresa  = (int) (session('id_empresa') ?: auth()->user()?->id_empresa ?? 0);
        $sucursal = (int) (session('sucursal')   ?: auth()->user()?->sucursal   ?? 1);

        $cuotas = DB::table('dias_ventas as dv')
            ->join('ventas as v', 'v.id_venta', '=', 'dv.id_venta')
            ->leftJoin('clientes as c', 'c.id_cliente', '=', 'v.id_cliente')
            ->where('v.id_empresa', $empresa)
            ->where('v.sucursal', $sucursal)
            ->where('v.estado', '!=', '0')
            ->where('dv.estado', '0')
            ->selectRaw("COALESCE(c.datos, '— Sin cliente —') as nombre, dv.fecha, dv.monto - COALESCE((SELECT SUM(a.monto) FROM cxc_abonos a WHERE a.id_dias_venta = dv.dias_venta_id AND a.estado = 'ACTIVO'), 0) as saldo")
            ->get();

        $hoy = now()->startOfDay();

        $topDeudores = $cuotas
            ->filter(fn ($c) => (float) $c->saldo > 0.001)
            ->groupBy('nombre')
            ->map(function ($grupo, $nombre) use ($hoy) {
                $atraso = $grupo->max(function ($c) use ($hoy) {
                    $vencimiento = Carbon::parse($c->fecha)->startOfDay();

                    return $vencimiento->lessThan($hoy) ? (int) $vencimiento->diffInDays($hoy) : 0;
                });

                return (object) [
                    'nombre' => $nombre,
                    'saldo'  => (float) $grupo->sum('saldo'),
                    'cuotas' => $grupo->count(),
                    'atraso' => $atraso,
                ];
            })
            ->sortByDesc('saldo')
            ->take(5)
            ->values();

        return view($this->view, compact('topDeudores'));
    }
}
