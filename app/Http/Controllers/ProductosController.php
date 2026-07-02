<?php
namespace App\Http\Controllers;
class ProductosController extends Controller
{
    public function escanearBarra(int $empresa, int $sucursal): \Illuminate\View\View
    {
        return view('productos.scanner', compact('empresa', 'sucursal'));
    }
}
