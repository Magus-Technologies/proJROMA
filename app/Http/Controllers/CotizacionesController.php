<?php
namespace App\Http\Controllers;

class CotizacionesController extends Controller
{
    public function edit(int $id): \Illuminate\View\View { return view('cotizaciones.edit', compact('id')); }
}
