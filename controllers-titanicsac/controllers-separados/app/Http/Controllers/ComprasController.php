<?php
namespace App\Http\Controllers;
class ComprasController extends Controller
{
    public function index(): \Illuminate\View\View    { return view('compras.index'); }
    public function create(): \Illuminate\View\View   { return view('compras.create'); }
    public function pagos(): \Illuminate\View\View    { return view('compras.pagos'); }
}
