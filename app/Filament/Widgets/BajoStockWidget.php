<?php

namespace App\Filament\Widgets;

use App\Models\Producto;
use Filament\Widgets\Widget;

class BajoStockWidget extends Widget
{
    protected static ?int $sort = 10;
    protected int|string|array $columnSpan = 1;
    protected string $view = 'filament.widgets.bajo-stock';

    public function render(): \Illuminate\Contracts\View\View
    {
        $empresa = (int) (session('id_empresa') ?: auth()->user()?->id_empresa ?? 0);

        $bajoStock = Producto::deEmpresa($empresa)
            ->activos()
            ->bajoStock()
            ->orderBy('cantidad')
            ->limit(10)
            ->get();

        $almacenes = \Illuminate\Support\Facades\DB::table('almacenes')
            ->where('id_empresa', $empresa)
            ->pluck('nombre', 'codigo');

        return view($this->view, compact('bajoStock', 'almacenes'));
    }
}
