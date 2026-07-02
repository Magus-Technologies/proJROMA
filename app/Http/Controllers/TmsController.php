<?php

namespace App\Http\Controllers;

class TmsController extends Controller
{
    public function mercados(): \Illuminate\View\View    { return view('tms.mercados'); }
    public function vehiculos(): \Illuminate\View\View   { return view('tms.vehiculos'); }
    public function conductores(): \Illuminate\View\View { return view('tms.conductores'); }
    public function rutas(): \Illuminate\View\View       { return view('tms.rutas'); }
    public function armarDespacho(): \Illuminate\View\View { return view('tms.armar-despacho'); }
    public function despachos(): \Illuminate\View\View    { return view('tms.despachos'); }
}
