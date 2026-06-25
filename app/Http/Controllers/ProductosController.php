<?php
namespace App\Http\Controllers;
class ProductosController extends Controller
{
    public function index(): \Illuminate\View\View     { return view('productos.index'); }      // Registro de Productos (catálogo)
    public function create(): \Illuminate\View\View    { return view('productos.create'); }
    public function recepcion(): \Illuminate\View\View { return view('productos.recepcion'); }   // Recepción de compras → entrada al almacén
    public function almacen(): \Illuminate\View\View   { return view('productos.almacen'); }     // Existencias por almacén
    public function kardex(): \Illuminate\View\View    { return view('productos.kardex'); }      // Movimientos
    public function ajustes(): \Illuminate\View\View   { return view('productos.ajustes'); }     // Cuadres / Ajustes de stock
    public function traslado(): \Illuminate\View\View  { return view('productos.traslado'); }    // Traslado de stock entre almacenes
    public function prestamos(): \Illuminate\View\View { return view('productos.prestamos'); }   // Préstamos con terceros
    public function escanearBarra(int $empresa, int $sucursal): \Illuminate\View\View
    {
        return view('productos.scanner', compact('empresa', 'sucursal'));
    }
}
