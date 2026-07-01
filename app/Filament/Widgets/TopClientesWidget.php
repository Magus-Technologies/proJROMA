<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\DB;

class TopClientesWidget extends Widget
{
    protected static ?int $sort = 3;
    protected int|string|array $columnSpan = 1;
    protected string $view = 'filament.widgets.top-clientes';

    public function render(): \Illuminate\Contracts\View\View
    {
        $empresa  = (int) (session('id_empresa') ?: auth()->user()?->id_empresa ?? 0);
        $sucursal = (int) (session('sucursal')   ?: auth()->user()?->sucursal   ?? 1);

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

        return view($this->view, compact('topClientes'));
    }
}
