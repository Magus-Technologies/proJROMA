<?php
namespace App\Http\Controllers;
class CajaController extends Controller
{
    public function registros(): \Illuminate\View\View { return view('caja.registros'); }
    public function flujo(): \Illuminate\View\View     { return view('caja.flujo'); }
    public function miCaja(): \Illuminate\View\View    { return view('caja.micaja'); }
}
