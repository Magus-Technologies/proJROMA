<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cotizacion;
use App\Models\ProductoCoti;
use App\Models\CuotaCotizacion;
use App\Models\DocumentoEmpresa;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class CotizacionesApiController extends Controller
{
    private function empresa(): int  { return (int) session('id_empresa'); }
    private function sucursal(): int { return (int) session('sucursal'); }

    public function listar(Request $request): mixed
    {
        $query = DB::table('cotizaciones as c')
            ->leftJoin('clientes as cl', 'cl.id_cliente', '=', 'c.id_cliente')
            ->where('c.id_empresa', $this->empresa())
            ->select([
                'c.cotizacion_id',
                'c.numero',
                'c.fecha',
                'cl.datos as cliente_nombre',
                'c.total',
                'c.estado',
            ]);

        return DataTables::of($query)
            ->addColumn('estado', fn ($r) => $r->estado ?? '0')
            ->make(true);
    }

    public function tipoDocumento(): JsonResponse
    {
        $docs = DB::table('documentos_empresas')
            ->where('id_empresa', $this->empresa())
            ->where('sucursal', $this->sucursal())
            ->get();
        $map = [1=>'Boleta', 2=>'Factura', 3=>'Factura Export.', 4=>'Factura Nota', 6=>'Nota Venta', 11=>'Ticket'];
        $result = $docs->map(fn($d) => [
            'id_tido' => $d->id_tido,
            'tipo_doc' => $map[$d->id_tido] ?? "Tipo {$d->id_tido}",
            'serie' => $d->serie,
            'numero' => $d->numero,
        ]);
        return response()->json($result);
    }

    public function buscarProducto(Request $request): JsonResponse
    {
        $term = $request->get('term', '');
        return response()->json(
            \App\Models\Producto::deEmpresa($this->empresa())->activos()
                ->where(fn($q) => $q->where('descripcion','like',"%{$term}%")
                    ->orWhere('cod_barra','like',"%{$term}%")
                    ->orWhere('codigo','like',"%{$term}%"))
                ->limit(50)
                ->get(['id_producto','descripcion','precio','precio2','precio3',
                       'precio4','cantidad','cod_barra','iscbp','codigo','costo'])
        );
    }

    public function detalle(Request $request): JsonResponse
    {
        $request->validate(['id_cotizacion' => 'required|integer']);
        $coti = Cotizacion::with(['cliente','productos','cuotas','usuario'])
            ->deEmpresa($this->empresa())->findOrFail($request->id_cotizacion);
        return response()->json($coti);
    }

    public function guardar(Request $request): JsonResponse
    {
        $request->validate([
            'id_cliente'   => 'required|integer',
            'id_tipo_pago' => 'required|integer',
            'fecha'        => 'required|date',
            'productos'    => 'required|array|min:1',
            'total'        => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $tido = DocumentoEmpresa::where('id_empresa', $this->empresa())
                ->where('sucursal', $this->sucursal())
                ->where('id_tido', 6)
                ->lockForUpdate()
                ->first();

            if (!$tido) {
                return response()->json(['res' => false, 'msg' => 'No hay serie de cotización configurada.'], 422);
            }

            $numero = $tido->numero + 1;

            $coti = Cotizacion::create([
                'numero'         => $numero,
                'id_tido'        => 6,
                'id_tipo_pago'   => $request->id_tipo_pago,
                'fecha'          => $request->fecha,
                'dias_pagos'     => $request->dias_pagos ?? null,
                'direccion'      => $request->direccion ?? null,
                'id_cliente'     => $request->id_cliente,
                'total'          => $request->total,
                'estado'         => '1',
                'id_empresa'     => $this->empresa(),
                'sucursal'       => $this->sucursal(),
                'usar_precio'    => 1,
                'moneda'         => 1,
                'id_usuario'     => auth()->id(),
                'observacion'    => $request->observacion ?? null,
                'fecha_registro' => now(),
            ]);

            $tido->increment('numero');

            foreach ($request->productos as $item) {
                DB::table('productos_cotis')->insert([
                    'id_coti'    => $coti->cotizacion_id,
                    'id_producto'=> $item['id_producto'],
                    'cantidad'   => $item['cantidad'],
                    'precio'     => $item['precio'],
                    'costo'      => $item['costo'] ?? 0,
                    'medida'     => $item['medida'] ?? 'Unidad',
                    'presenta'   => $item['presenta'] ?? 1,
                    'presenta_cnt' => $item['presenta_cnt'] ?? 1,
                ]);
            }

            if ($request->id_tipo_pago == 2 && $request->has('cuotas')) {
                foreach ($request->cuotas as $cuota) {
                    CuotaCotizacion::create([
                        'id_coti'     => $coti->cotizacion_id,
                        'id_usuario'  => auth()->id(),
                        'monto'       => $cuota['monto'],
                        'fecha'       => $cuota['fecha'],
                        'estado'      => '0',
                        'tipo_pago'   => $cuota['tipo_pago'] ?? 'EFECTIVO',
                    ]);
                }
            }

            DB::commit();

            $doc = "{$tido->serie}-" . str_pad($numero, 8, '0', STR_PAD_LEFT);
            return response()->json([
                'res' => true,
                'id_cotizacion' => $coti->cotizacion_id,
                'msg' => "Cotización {$doc} registrada correctamente.",
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error guardar cotización: ' . $e->getMessage());
            return response()->json(['res' => false, 'msg' => 'Error al registrar la cotización.'], 500);
        }
    }

    public function editar(Request $request): JsonResponse
    {
        $request->validate([
            'id_cotizacion' => 'required|integer',
            'productos'     => 'required|array|min:1',
            'total'         => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $coti = Cotizacion::deEmpresa($this->empresa())->findOrFail($request->id_cotizacion);

            if ($coti->estado === '0') {
                return response()->json(['res' => false, 'msg' => 'No se puede editar una cotización anulada.'], 422);
            }

            $coti->update([
                'id_tipo_pago' => $request->id_tipo_pago ?? $coti->id_tipo_pago,
                'fecha'        => $request->fecha ?? $coti->fecha,
                'id_cliente'   => $request->id_cliente ?? $coti->id_cliente,
                'total'        => $request->total,
                'observacion'  => $request->observacion ?? $coti->observacion,
            ]);

            DB::table('productos_cotis')->where('id_coti', $coti->cotizacion_id)->delete();
            foreach ($request->productos as $item) {
                DB::table('productos_cotis')->insert([
                    'id_coti'    => $coti->cotizacion_id,
                    'id_producto'=> $item['id_producto'],
                    'cantidad'   => $item['cantidad'],
                    'precio'     => $item['precio'],
                    'costo'      => $item['costo'] ?? 0,
                    'medida'     => $item['medida'] ?? 'Unidad',
                    'presenta'   => $item['presenta'] ?? 1,
                    'presenta_cnt' => $item['presenta_cnt'] ?? 1,
                ]);
            }

            if ($request->has('cuotas')) {
                CuotaCotizacion::where('id_coti', $coti->cotizacion_id)->delete();
                foreach ($request->cuotas as $cuota) {
                    CuotaCotizacion::create([
                        'id_coti'     => $coti->cotizacion_id,
                        'id_usuario'  => auth()->id(),
                        'monto'       => $cuota['monto'],
                        'fecha'       => $cuota['fecha'],
                        'estado'      => $cuota['estado'] ?? '0',
                        'tipo_pago'   => $cuota['tipo_pago'] ?? 'EFECTIVO',
                    ]);
                }
            }

            DB::commit();
            return response()->json(['res' => true, 'msg' => 'Cotización actualizada.']);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error editar cotización: ' . $e->getMessage());
            return response()->json(['res' => false, 'msg' => 'Error al editar la cotización.'], 500);
        }
    }

    public function anular(Request $request): JsonResponse
    {
        $request->validate(['id_cotizacion' => 'required|integer']);

        $coti = Cotizacion::deEmpresa($this->empresa())->findOrFail($request->id_cotizacion);
        if ($coti->estado === '0') {
            return response()->json(['res' => false, 'msg' => 'La cotización ya está anulada.'], 422);
        }
        $coti->update(['estado' => '0']);
        return response()->json(['res' => true, 'msg' => 'Cotización anulada.']);
    }

    public function cuotas(Request $request): JsonResponse
    {
        $request->validate(['id_cotizacion' => 'required|integer']);
        $cuotas = CuotaCotizacion::where('id_coti', $request->id_cotizacion)
            ->with('usuario')->get();
        return response()->json($cuotas);
    }
}
