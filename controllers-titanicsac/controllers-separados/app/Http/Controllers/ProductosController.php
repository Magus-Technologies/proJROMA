<?php
namespace App\Http\Controllers;
class ProductosController extends Controller
{
    public function index(): \Illuminate\View\View      { return view('productos.index'); }
    public function create(): \Illuminate\View\View     { return view('productos.create'); }
    public function intercambio(): \Illuminate\View\View{ return view('productos.intercambio'); }
    public function escanearBarra(int $empresa, int $sucursal): \Illuminate\View\View
    {
        return view('productos.scanner', compact('empresa', 'sucursal'));
    }
}
