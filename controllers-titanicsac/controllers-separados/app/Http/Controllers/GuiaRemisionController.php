<?php
namespace App\Http\Controllers;
class GuiaRemisionController extends Controller
{
    public function index(): \Illuminate\View\View  { return view('guias.index'); }
    public function create(): \Illuminate\View\View { return view('guias.create'); }
}
