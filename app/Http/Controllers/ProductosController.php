<?php
namespace App\Http\Controllers;

use App\Exports\ProductosTemplateExport;
use Maatwebsite\Excel\Facades\Excel;

class ProductosController extends Controller
{
    public function escanearBarra(int $empresa, int $sucursal): \Illuminate\View\View
    {
        return view('productos.scanner', compact('empresa', 'sucursal'));
    }

    public function descargarPlantilla(): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        return Excel::download(
            new ProductosTemplateExport,
            'plantilla-productos.xlsx',
        );
    }
}
