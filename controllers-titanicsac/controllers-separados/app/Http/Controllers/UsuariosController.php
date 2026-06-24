<?php
namespace App\Http\Controllers;
class UsuariosController extends Controller
{
    public function index(): \Illuminate\View\View        { return view('usuarios.index'); }
    public function adminEmpresas(): \Illuminate\View\View{ return view('usuarios.empresas'); }
}
