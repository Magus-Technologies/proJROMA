<?php
namespace App\Http\Controllers;
class ProveedoresController extends Controller
{
    public function index(): \Illuminate\View\View { return view('proveedores.index'); }
}
