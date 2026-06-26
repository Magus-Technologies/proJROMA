<?php
namespace App\Http\Controllers;
class CajaController extends Controller
{
    public function gestion(): \Illuminate\View\View { return view('caja.gestion'); }
    public function movimientos(int $idCaja = 0): \Illuminate\View\View
    {
        return view('caja.movimientos', ['idCaja' => $idCaja]);
    }
    public function rendiciones(): \Illuminate\View\View { return view('caja.rendiciones'); }
    public function miCaja(): \Illuminate\View\View    { return view('caja.micaja'); }
    public function apertura(): \Illuminate\View\View { return view('caja.apertura'); }
}
