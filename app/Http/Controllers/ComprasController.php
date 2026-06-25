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

        // Modo edición (solo compras aún no recepcionadas)
        $compra = null;
        $items  = collect();
        if ($id = request('id')) {
            $compra = DB::table('compras')->where('id_empresa', $emp)->where('id_compra', $id)->where('recepcionado', 0)->first();
            if ($compra) {
                $items = DB::table('productos_compras as pc')
                    ->join('productos as p', 'p.id_producto', '=', 'pc.id_producto')
                    ->where('pc.id_compra', $id)
                    ->select('pc.id_producto', 'p.descripcion', 'pc.cantidad', 'pc.costo')->get()
                    ->map(fn ($i) => [
                        'id_producto' => (int) $i->id_producto,
                        'descripcion' => $i->descripcion,
                        'cantidad'    => (float) $i->cantidad,
                        'costo'       => (float) $i->costo,
                    ])->values();
            }
        }

        return view('compras.create', compact('proveedores', 'tiposDoc', 'tiposPago', 'compra', 'items'));
    }
}
