<?php

namespace App\Filament\Widgets;

use App\Models\Venta;
use Filament\Widgets\Widget;

class UltimasVentasWidget extends Widget
{
    protected static ?int $sort = 4;
    protected int|string|array $columnSpan = 2;
    protected string $view = 'filament.widgets.ultimas-ventas';

    public function render(): \Illuminate\Contracts\View\View
    {
        $empresa  = (int) (session('id_empresa') ?: auth()->user()?->id_empresa ?? 0);
        $sucursal = (int) (session('sucursal')   ?: auth()->user()?->sucursal   ?? 1);

        $ultimasVentas = Venta::with(['cliente', 'tipoDocumento'])
            ->deEmpresa($empresa)
            ->deSucursal($sucursal)
            ->orderByDesc('id_venta')
            ->limit(8)
            ->get();

        return view($this->view, compact('ultimasVentas'));
    }
}
