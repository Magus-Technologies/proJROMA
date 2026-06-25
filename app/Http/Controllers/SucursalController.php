<?php
namespace App\Http\Controllers;

class SucursalController extends Controller
{
    public function index(): \Illuminate\View\View { return view('sucursales.index'); }
}
