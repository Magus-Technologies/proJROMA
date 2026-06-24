<?php
namespace App\Http\Controllers;
class CotizacionesController extends Controller
{
    public function index(): \Illuminate\View\View  { return view('cotizaciones.index'); }
    public function create(): \Illuminate\View\View { return view('cotizaciones.create'); }
    public function edit(int $id): \Illuminate\View\View { return view('cotizaciones.edit', compact('id')); }
    public function cuotas(int $id): \Illuminate\View\View { return view('cotizaciones.cuotas', compact('id')); }
}
