<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Compra;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class ComprasApiController extends Controller
{
    private function empresa(): int  { return (int) session('id_empresa'); }
    private function sucursal(): int { return (int) session('sucursal'); }

    public function listar(Request $request): mixed
    {
        $query = Compra::with(['proveedor', 'tipoDocumento'])
            ->deEmpresa($this->empresa());

        return DataTables::of($query)
            ->addColumn('documento',        fn ($c) => trim(($c->serie ?? '') . '-' . ($c->numero ?? ''), '-'))
            ->addColumn('proveedor_nombre', fn ($c) => trim($c->proveedor?->razon_social
                                                        ?? $c->proveedor?->nombre_comercial
                                                        ?? $c->proveedor?->nombre
                                                        ?? '-'))
            ->addColumn('tipo_doc',         fn ($c) => $c->tipoDocumento?->tipo_doc ?? '-')
            ->addColumn('acciones',         fn ($c) => $c->id_compra)
            ->make(true);
    }

    /** Registra una compra (cabecera + ítems). Queda pendiente de recepción (recepcionado=0). */
    public function guardar(Request $request): JsonResponse
    {
        $data = $request->validate([
            'id_proveedor'           => 'required|integer',
            'id_tido'                => 'required|integer',
            'id_tipo_pago'           => 'nullable|integer',
            'fecha'                  => 'required',
            'serie'                  => 'nullable|string|max:50',
            'numero'                 => 'nullable|string|max:50',
            'observacion'            => 'nullable|string|max:200',
            'total'                  => 'required|numeric|min:0',
            'productos'              => 'required|array|min:1',
            'productos.*.id_producto' => 'required|integer',
            'productos.*.cantidad'   => 'required|numeric|min:0.01',
            'productos.*.costo'      => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $compra = Compra::create([
                'id_proveedor'      => $data['id_proveedor'],
                'id_tido'           => $data['id_tido'],
                'id_tipo_pago'      => $data['id_tipo_pago'] ?? 1,
                'fecha_emision'     => $data['fecha'],
                'fecha_vencimiento' => $data['fecha'],
                'direccion'         => $data['observacion'] ?? '',
                'serie'             => $data['serie'] ?? '',
                'numero'            => $data['numero'] ?? '',
                'total'             => $data['total'],
                'id_empresa'        => $this->empresa(),
                'sucursal'          => $this->sucursal(),
                'moneda'            => 'S',
                'recepcionado'      => 0,
            ]);

            foreach ($data['productos'] as $p) {
                DB::table('productos_compras')->insert([
                    'id_compra'   => $compra->id_compra,
                    'id_producto' => $p['id_producto'],
                    'cantidad'    => $p['cantidad'],
                    'costo'       => $p['costo'] ?? 0,
                    'precio'      => $p['costo'] ?? 0,
                ]);
            }

            DB::commit();
            return response()->json(['res' => true, 'id' => $compra->id_compra]);
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Error guardar compra: ' . $e->getMessage());
            return response()->json(['res' => false, 'msg' => 'Error al registrar la compra.'], 500);
        }
    }

    /** Edita una compra no recepcionada (cabecera + reemplaza ítems). */
    public function editar(Request $request): JsonResponse
    {
        $data = $request->validate([
            'id_compra'              => 'required|integer',
            'id_proveedor'           => 'required|integer',
            'id_tido'                => 'required|integer',
            'id_tipo_pago'           => 'nullable|integer',
            'fecha'                  => 'required',
            'serie'                  => 'nullable|string|max:50',
            'numero'                 => 'nullable|string|max:50',
            'observacion'            => 'nullable|string|max:200',
            'total'                  => 'required|numeric|min:0',
            'productos'              => 'required|array|min:1',
            'productos.*.id_producto' => 'required|integer',
            'productos.*.cantidad'   => 'required|numeric|min:0.01',
            'productos.*.costo'      => 'nullable|numeric|min:0',
        ]);

        $compra = Compra::where('id_empresa', $this->empresa())->where('id_compra', $data['id_compra'])->first();
        if (! $compra || (int) $compra->recepcionado !== 0) {
            return response()->json(['res' => false, 'msg' => 'No se puede editar: la compra ya tiene recepciones o no existe.'], 409);
        }

        DB::beginTransaction();
        try {
            $compra->update([
                'id_proveedor' => $data['id_proveedor'], 'id_tido' => $data['id_tido'],
                'id_tipo_pago' => $data['id_tipo_pago'] ?? 1, 'fecha_emision' => $data['fecha'],
                'fecha_vencimiento' => $data['fecha'], 'direccion' => $data['observacion'] ?? '',
                'serie' => $data['serie'] ?? '', 'numero' => $data['numero'] ?? '', 'total' => $data['total'],
            ]);

            DB::table('productos_compras')->where('id_compra', $compra->id_compra)->delete();
            foreach ($data['productos'] as $p) {
                DB::table('productos_compras')->insert([
                    'id_compra' => $compra->id_compra, 'id_producto' => $p['id_producto'],
                    'cantidad' => $p['cantidad'], 'costo' => $p['costo'] ?? 0, 'precio' => $p['costo'] ?? 0,
                ]);
            }

            DB::commit();
            return response()->json(['res' => true, 'id' => $compra->id_compra]);
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Error editar compra: ' . $e->getMessage());
            return response()->json(['res' => false, 'msg' => 'Error al editar la compra.'], 500);
        }
    }
}
