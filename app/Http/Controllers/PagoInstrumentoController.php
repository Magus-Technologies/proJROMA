<?php

namespace App\Http\Controllers;

class PagoInstrumentoController extends Controller
{
    public function index(): \Illuminate\View\View
    {
        return view('pagos.instrumentos');
    }
}
