<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class PagosApiController extends Controller
{
    private function empresa(): int
    {
        return (int) session('id_empresa');
    }

    public function listar(Request $request): mixed
    {
        $empresa = $this->empresa();

        $query = DB::table('compras as c')
            ->join('proveedores as p', 'p.proveedor_id', '=', 'c.id_proveedor')
            ->join('documentos_sunat as ds', 'ds.id_tido', '=', 'c.id_tido')
            ->leftJoin('tipo_pago as tp', 'tp.tipo_pago_id', '=', 'c.id_tipo_pago')
            ->leftJoin(DB::raw('(SELECT id_compra, COALESCE(SUM(monto), 0) as total_pagado FROM dias_compras GROUP BY id_compra) as dc'), 'dc.id_compra', '=', 'c.id_compra')
            ->where('c.id_empresa', $empresa)
            ->where('c.id_tipo_pago', 2)
            ->select(
                'c.id_compra',
                'c.serie',
                'c.numero',
                'c.fecha_emision',
                'c.fecha_vencimiento',
                'c.total',
                'c.moneda',
                'p.proveedor_id',
                DB::raw('COALESCE(p.razon_social, p.nombre_comercial, "") as proveedor_nombre'),
                'ds.nombre as tipo_doc',
                'tp.nombre as tipo_pago_nombre',
                DB::raw('COALESCE(dc.total_pagado, 0) as total_pagado')
            );

        return DataTables::of($query)
            ->addColumn('documento', fn($r) => trim(($r->serie ?? '') . '-' . ($r->numero ?? ''), '-'))
            ->addColumn('total', fn($r) => (float) $r->total)
            ->addColumn('total_pagado', fn($r) => (float) $r->total_pagado)
            ->addColumn('saldo_pendiente', fn($r) => max(0, (float) $r->total - (float) $r->total_pagado))
            ->addColumn('estado', function ($r) {
                $saldo = max(0, (float) $r->total - (float) $r->total_pagado);
                if ($saldo <= 0) {
                    return '<span class="inline-block rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-bold text-emerald-700">Pagado</span>';
                }
                if ((float) $r->total_pagado > 0) {
                    return '<span class="inline-block rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-bold text-amber-700">Parcial</span>';
                }
                return '<span class="inline-block rounded-full bg-red-100 px-2 py-0.5 text-[10px] font-bold text-red-700">Pendiente</span>';
            })
            ->addColumn('acciones', fn($r) => $r->id_compra)
            ->filterColumn('tipo_doc', fn($q, $keyword) =>
                $q->where('ds.nombre', 'like', "%{$keyword}%")
            )
            ->filterColumn('serie', fn($q, $keyword) =>
                $q->where('c.serie', 'like', "%{$keyword}%")
            )
            ->filterColumn('numero', fn($q, $keyword) =>
                $q->where('c.numero', 'like', "%{$keyword}%")
            )
            ->filterColumn('proveedor_nombre', fn($q, $keyword) =>
                $q->where(function($q) use ($keyword) {
                    $q->where('p.razon_social', 'like', "%{$keyword}%")
                      ->orWhere('p.nombre_comercial', 'like', "%{$keyword}%");
                })
            )
            ->filterColumn('tipo_pago_nombre', fn($q, $keyword) =>
                $q->where('tp.nombre', 'like', "%{$keyword}%")
            )
            ->orderColumn('tipo_doc', 'ds.nombre $1')
            ->orderColumn('serie', 'c.serie $1')
            ->orderColumn('numero', 'c.numero $1')
            ->rawColumns(['estado', 'acciones'])
            ->make(true);
    }

    public function historial(Request $request): JsonResponse
    {
        $idCompra = (int) $request->get('id_compra');

        $pagos = DB::table('dias_compras')
            ->where('id_compra', $idCompra)
            ->orderBy('fecha', 'desc')
            ->orderBy('dias_compra_id', 'desc')
            ->get();

        $compra = DB::table('compras')
            ->join('proveedores', 'proveedores.proveedor_id', '=', 'compras.id_proveedor')
            ->where('compras.id_compra', $idCompra)
            ->select(
                'compras.*',
                DB::raw('COALESCE(proveedores.razon_social, proveedores.nombre_comercial, "") as proveedor_nombre')
            )
            ->first();

        $totalPagado = DB::table('dias_compras')->where('id_compra', $idCompra)->sum('monto');
        $total = (float) ($compra->total ?? 0);
        $saldo = max(0, $total - (float) $totalPagado);

        return response()->json([
            'res' => true,
            'compra' => $compra,
            'pagos' => $pagos,
            'total_pagado' => (float) $totalPagado,
            'saldo_pendiente' => $saldo,
        ]);
    }

    public function registrarPago(Request $request): JsonResponse
    {
        $data = $request->validate([
            'id_compra'          => 'required|integer',
            'monto'              => 'required|numeric|min:0.01',
            'fecha'              => 'required|date',
            'instrumento_tipo'   => 'nullable|string|max:30',
            'instrumento_id'     => 'nullable|integer',
        ]);

        $empresa = $this->empresa();

        $compra = DB::table('compras')
            ->where('id_empresa', $empresa)
            ->where('id_compra', $data['id_compra'])
            ->first();

        if (!$compra) {
            return response()->json(['res' => false, 'msg' => 'Compra no encontrada.'], 404);
        }

        $totalPagado = (float) DB::table('dias_compras')->where('id_compra', $data['id_compra'])->sum('monto');
        $total = (float) $compra->total;
        $saldo = max(0, $total - $totalPagado);

        if ((float) $data['monto'] > $saldo) {
            return response()->json([
                'res' => false,
                'msg' => 'El monto excede el saldo pendiente (S/ ' . number_format($saldo, 2) . ').'
            ], 422);
        }

        DB::table('dias_compras')->insert([
            'id_compra'        => $data['id_compra'],
            'monto'            => $data['monto'],
            'fecha'            => $data['fecha'],
            'estado'           => '1',
            'instrumento_tipo' => $data['instrumento_tipo'] ?? null,
            'instrumento_id'   => $data['instrumento_id'] ?? null,
        ]);

        return response()->json(['res' => true, 'msg' => 'Pago registrado correctamente.']);
    }

    public function editarPago(Request $request): JsonResponse
    {
        $data = $request->validate([
            'dias_compra_id'     => 'required|integer',
            'monto'              => 'required|numeric|min:0.01',
            'fecha'              => 'required|date',
            'instrumento_tipo'   => 'nullable|string|max:30',
            'instrumento_id'     => 'nullable|integer',
        ]);

        $pago = DB::table('dias_compras')->where('dias_compra_id', $data['dias_compra_id'])->first();
        if (!$pago) {
            return response()->json(['res' => false, 'msg' => 'Pago no encontrado.'], 404);
        }

        DB::table('dias_compras')
            ->where('dias_compra_id', $data['dias_compra_id'])
            ->update([
                'monto'            => $data['monto'],
                'fecha'            => $data['fecha'],
                'instrumento_tipo' => $data['instrumento_tipo'] ?? null,
                'instrumento_id'   => $data['instrumento_id'] ?? null,
            ]);

        return response()->json(['res' => true, 'msg' => 'Pago actualizado correctamente.']);
    }

    public function eliminarPago(Request $request): JsonResponse
    {
        $data = $request->validate([
            'dias_compra_id' => 'required|integer',
        ]);

        $pago = DB::table('dias_compras')->where('dias_compra_id', $data['dias_compra_id'])->first();
        if (!$pago) {
            return response()->json(['res' => false, 'msg' => 'Pago no encontrado.'], 404);
        }

        DB::table('dias_compras')
            ->where('dias_compra_id', $data['dias_compra_id'])
            ->update(['estado' => '0']);

        return response()->json(['res' => true, 'msg' => 'Pago anulado correctamente.']);
    }
}
