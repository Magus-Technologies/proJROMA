<?php
namespace App\Http\Controllers;
class DevolucionesController extends Controller
{
    public function index(): \Illuminate\View\View          { return view('devoluciones.index'); }
    public function exportarExcel(): \Illuminate\Http\Response { return response('', 200); }
}
