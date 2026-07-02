<?php
namespace App\Http\Controllers;

use App\Exports\ProveedoresExport;
use Maatwebsite\Excel\Facades\Excel;

class ProveedoresController extends Controller
{
    public function index(): \Illuminate\View\View { return view('proveedores.index'); }

    public function exportarExcel(): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        return Excel::download(
            new ProveedoresExport((int) session('id_empresa')),
            'proveedores-' . now()->format('Y-m-d') . '.xlsx',
        );
    }
}
