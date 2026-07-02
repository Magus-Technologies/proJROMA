<?php

namespace App\Http\Controllers;

use App\Models\Empresa;

class VentasController extends Controller
{
    private function empresa(): int { return (int) session('id_empresa'); }

    public function notaElectronica(): \Illuminate\View\View
    {
        $empresa = Empresa::find($this->empresa());
        return view('ventas.nota-electronica', compact('empresa'));
    }
}
