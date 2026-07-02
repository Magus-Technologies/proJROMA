<?php
namespace App\Http\Controllers;

use App\Exports\ClientesExport;
use Maatwebsite\Excel\Facades\Excel;

class ClientesController extends Controller
{
    public function index(): \Illuminate\View\View { return view('clientes.index'); }

    public function exportarExcel(): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        return Excel::download(
            new ClientesExport((int) session('id_empresa')),
            'clientes-' . now()->format('Y-m-d') . '.xlsx',
        );
    }

    public function exportarClientesVisitaPdf(): \Illuminate\Http\Response { return response('', 200); }
}
