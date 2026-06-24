<?php
namespace App\Http\Controllers;
class ArqueoDiarioController extends Controller
{
    public function index(): \Illuminate\View\View { return view('arqueo.index'); }
}
