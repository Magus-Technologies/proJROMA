<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GuiaDetalle;
use App\Models\GuiaRemision;
use App\Models\ProductoVenta;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Yajra\DataTables\Facades\DataTables;

class GuiaRemisionApiController extends Controller
{
    private function empresa(): int  { return (int) session('id_empresa'); }
    private function sucursal(): int { return (int) session('sucursal'); }

    public function listar(Request $request): mixed
    {
        $query = GuiaRemision::with('venta.cliente')
            ->where('id_empresa', $this->empresa())
            ->where('sucursal', $this->sucursal());

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha_emision', '>=', $request->fecha_desde);
        }
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha_emision', '<=', $request->fecha_hasta);
        }

        return DataTables::of($query)
            ->addColumn('documento',      fn($g) => ($g->serie ?? 'T001') . '-' . str_pad($g->numero ?? 0, 8, '0', STR_PAD_LEFT))
            ->addColumn('cliente_nombre', fn($g) => $g->venta?->cliente?->datos ?? '-')
            ->addColumn('fecha',          fn($g) => $g->fecha_emision?->format('Y-m-d') ?? '-')
            ->make(true);
    }

    public function buscarVenta(Request $request): JsonResponse
    {
        $request->validate(['term' => 'required|string']);
        $term = $request->term;

        $ventas = DB::table('ventas as v')
            ->leftJoin('clientes as c', 'c.id_cliente', '=', 'v.id_cliente')
            ->where('v.id_empresa', $this->empresa())
            ->where('v.sucursal', $this->sucursal())
            ->where('v.estado', '1')
            ->where(function ($q) use ($term) {
                $q->where('v.serie', 'like', "%{$term}%")
                  ->orWhere(DB::raw("CONCAT(v.serie,'-',LPAD(v.numero,8,'0'))"), 'like', "%{$term}%")
                  ->orWhere('c.datos', 'like', "%{$term}%");
            })
            ->select('v.id_venta', 'v.serie', 'v.numero', 'v.total', 'v.fecha_emision',
                     'c.datos as cliente_nombre', 'c.direccion as cliente_direccion')
            ->limit(20)
            ->get();

        return response()->json($ventas);
    }

    public function cargarVenta(Request $request): JsonResponse
    {
        $request->validate(['id_venta' => 'required|integer']);
        $productos = ProductoVenta::with('producto')
            ->where('id_venta', $request->id_venta)
            ->get()
            ->map(fn($p) => [
                'id_producto' => $p->id_producto,
                'detalles'    => $p->descripcion,
                'cantidad'    => $p->cantidad,
                'precio'      => $p->precio,
                'unidad'      => 'NIU',
            ]);
        return response()->json($productos);
    }

    public function guardar(Request $request): JsonResponse
    {
        $request->validate([
            'id_venta'    => 'required|integer',
            'fecha_emision' => 'required|date',
            'productos'   => 'required|array|min:1',
        ]);

        DB::beginTransaction();
        try {
            $numero = (GuiaRemision::where('id_empresa', $this->empresa())
                ->where('sucursal', $this->sucursal())
                ->max('numero') ?? 0) + 1;

            $guia = GuiaRemision::create([
                'id_venta'         => $request->id_venta,
                'fecha_emision'    => $request->fecha_emision,
                'dir_llegada'      => $request->dir_llegada ?? null,
                'ubigeo'           => $request->ubigeo ?? null,
                'tipo_transporte'  => $request->tipo_transporte ?? '1',
                'ruc_transporte'   => $request->ruc_transporte ?? null,
                'razon_transporte' => $request->razon_transporte ?? null,
                'vehiculo'         => $request->vehiculo ?? null,
                'chofer_brevete'   => $request->chofer_brevete ?? null,
                'peso'             => $request->peso ?? 0,
                'nro_bultos'       => $request->nro_bultos ?? 1,
                'serie'            => 'T001',
                'numero'           => $numero,
                'estado'           => '1',
                'enviado_sunat'    => '0',
                'id_empresa'       => $this->empresa(),
                'sucursal'         => $this->sucursal(),
            ]);

            foreach ($request->productos as $item) {
                GuiaDetalle::create([
                    'id_guia'    => $guia->id_guia_remision,
                    'id_producto'=> $item['id_producto'],
                    'detalles'   => $item['detalles'],
                    'unidad'     => $item['unidad'] ?? 'NIU',
                    'cantidad'   => $item['cantidad'],
                    'precio'     => $item['precio'] ?? 0,
                ]);
            }

            DB::commit();

            $doc = 'T001-' . str_pad($numero, 8, '0', STR_PAD_LEFT);
            return response()->json(['res' => true, 'id_guia' => $guia->id_guia_remision,
                'msg' => "Guía {$doc} registrada correctamente."]);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['res' => false, 'msg' => 'Error al registrar la guía: ' . $e->getMessage()], 500);
        }
    }

    public function detalle(Request $request): JsonResponse
    {
        $request->validate(['id_guia' => 'required|integer']);
        $guia = GuiaRemision::with(['venta.cliente', 'detalles'])
            ->where('id_empresa', $this->empresa())
            ->findOrFail($request->id_guia);
        return response()->json($guia);
    }

    public function anular(Request $request): JsonResponse
    {
        $request->validate(['id_guia' => 'required|integer']);
        $guia = GuiaRemision::where('id_empresa', $this->empresa())->findOrFail($request->id_guia);
        if ($guia->estado === '0') {
            return response()->json(['res' => false, 'msg' => 'La guía ya está anulada.'], 422);
        }
        $guia->update(['estado' => '0']);
        return response()->json(['res' => true, 'msg' => 'Guía anulada correctamente.']);
    }
}
