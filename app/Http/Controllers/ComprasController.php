<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class ComprasController extends Controller
{
    public function index(): \Illuminate\View\View { return view('compras.index'); }
    public function pagos(): \Illuminate\View\View { return view('compras.pagos'); }

    public function create(): \Illuminate\View\View
    {
        $emp = (int) session('id_empresa');

        $proveedores = DB::table('proveedores')->where('id_empresa', $emp)
            ->orderBy('razon_social')->get(['proveedor_id', 'razon_social', 'ruc']);
        // Solo Factura (2), Boleta (1) y Nota de Venta (6), en ese orden
        $tiposDoc  = DB::table('documentos_sunat')->whereIn('id_tido', [2, 1, 6])
            ->orderByRaw('FIELD(id_tido, 2, 1, 6)')->get(['id_tido', 'nombre']);
        $tiposPago = DB::table('tipo_pago')->get(['tipo_pago_id', 'nombre']);

        return view('compras.create', compact('proveedores', 'tiposDoc', 'tiposPago'));
    }
}
