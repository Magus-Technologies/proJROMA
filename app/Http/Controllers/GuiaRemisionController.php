<?php
namespace App\Http\Controllers;
class GuiaRemisionController extends Controller
{
    public function create(): \Illuminate\View\View { return view('guias.create'); }
}
