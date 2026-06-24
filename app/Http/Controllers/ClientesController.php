<?php
namespace App\Http\Controllers;
class ClientesController extends Controller
{
    public function index(): \Illuminate\View\View { return view('clientes.index'); }
    public function exportarExcel(): \Illuminate\Http\Response { return response('', 200); }
    public function exportarClientesVisitaPdf(): \Illuminate\Http\Response { return response('', 200); }
}
