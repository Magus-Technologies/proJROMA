<?php
namespace App\Http\Controllers;
class CobranzasController extends Controller
{
    public function index(): \Illuminate\View\View          { return view('cobranzas.index'); }
    public function deudas(): \Illuminate\View\View         { return view('cobranzas.deudas'); }
    public function cuentasPorCobrar(): \Illuminate\View\View { return view('cobranzas.cuentas'); }
    public function misCobros(): \Illuminate\View\View      { return view('cobranzas.miscobros'); }
    public function exportarExcel(): \Illuminate\Http\Response { return response('', 200); }
}
