<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Venta;
use App\Models\ProductoVenta;
use App\Models\DiasVenta;
use App\Models\Producto;
use App\Models\DocumentoEmpresa;
use App\Models\Cliente;
use App\Models\MotivoMovimiento;
use App\Models\InventarioMovimiento;
use App\Http\Requests\Ventas\GuardarVentaRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class VentasApiController extends Controller
{
    private function empresa(): int  { return (int) session('id_empresa'); }
    private function sucursal(): int { return (int) session('sucursal'); }

    /** id del motivo "Venta" (para el Kardex). */
    private function motivoVenta(): ?int
    {
        return MotivoMovimiento::where('id_empresa', $this->empresa())->where('nombre', 'Venta')->value('id_motivo');
    }

    /** Registra un movimiento en el Kardex (trazabilidad de stock). */
    private function kardex(int $idProd, int $cant, string $tipo, int $ant, int $nuevo, ?string $almacen, $costo, ?int $motivo, string $obs): void
    {
        InventarioMovimiento::create([
            'id_empresa' => $this->empresa(), 'almacen' => $almacen ?? '', 'id_producto' => $idProd, 'tipo' => $tipo,
            'id_motivo' => $motivo, 'cantidad' => $cant, 'stock_anterior' => $ant, 'stock_nuevo' => $nuevo,
            'costo' => $costo, 'observacion' => $obs, 'id_usuario' => (int) (auth()->user()->usuario_id ?? 0), 'fecha' => now(),
        ]);
    }

    public function listar(Request $request): mixed
    {
        $query = Venta::with(['cliente', 'tipoDocumento', 'sunat'])
            ->deEmpresa($this->empresa())
            ->deSucursal($this->sucursal());

        return DataTables::of($query)
            ->addColumn('documento',      fn($v) => $v->documento_completo)
            ->addColumn('cliente_nombre', fn($v) => $v->cliente?->datos ?? '-')
            ->addColumn('tipo_doc',       fn($v) => $v->tipoDocumento?->tipo_doc ?? '-')
            ->addColumn('estado_sunat',   fn($v) => $v->sunat?->estado_sunat ?? 'NO ENVIADO')
            ->addColumn('acciones',       fn($v) => $v->id_venta)
            ->make(true);
    }

    public function guardar(GuardarVentaRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();

            $tido = DocumentoEmpresa::where('id_empresa', $this->empresa())
                ->where('sucursal', $this->sucursal())
                ->where('id_tido', $data['id_tido'])
                ->lockForUpdate()
                ->firstOrFail();

            $numero = $tido->numero + 1;

            $venta = Venta::create([
                'id_tido'           => $data['id_tido'],
                'id_tipo_pago'      => $data['id_tipo_pago'],
                'fecha_emision'     => $data['fecha'],
                'fecha_vencimiento' => $data['fecha_vencimiento'] ?? $data['fecha'],
                'dias_pagos'        => $data['dias_pagos'] ?? null,
                'direccion'         => $data['direccion'] ?? '-',
                'serie'             => $tido->serie,
                'numero'            => $numero,
                'id_cliente'        => $data['id_cliente'],
                'total'             => $data['total'],
                'subtotal'          => $data['subtotal'] ?? round($data['total'] / 1.18, 2),
                'igv'               => $data['igv'] ?? 0.18,
                'apli_igv'          => $data['apli_igv'] ?? '1',
                'estado'            => '1',
                'enviado_sunat'     => '0',
                'id_empresa'        => $this->empresa(),
                'sucursal'          => $this->sucursal(),
                'id_vendedor'       => auth()->id(),
                'observacion'       => $data['observacion'] ?? null,
                'medoto_pago_id'    => $data['metodo_pago'] ?? null,
            ]);

            $tido->increment('numero');

            $motVenta = $this->motivoVenta();
            $docVenta = "{$tido->serie}-" . str_pad($numero, 8, '0', STR_PAD_LEFT);

            foreach ($data['productos'] as $item) {
                ProductoVenta::create([
                    'id_venta'    => $venta->id_venta,
                    'id_producto' => $item['id_producto'],
                    'descripcion' => $item['descripcion'],
                    'cantidad'    => $item['cantidad'],
                    'precio'      => $item['precio'],
                    'total'       => $item['total'],
                    'igv_prod'    => $item['igv_prod'] ?? 0,
                    'descuento'   => $item['descuento'] ?? 0,
                ]);

                $p = Producto::find($item['id_producto']);
                if ($p) {
                    $ant  = (int) $p->cantidad;
                    $cant = (int) $item['cantidad'];
                    $p->decrement('cantidad', $item['cantidad']);
                    $this->kardex($p->id_producto, $cant, 'S', $ant, $ant - $cant, $p->almacen, $p->costo, $motVenta, "Venta {$docVenta}");
                }
            }

            foreach ($data['lista_pagos'] ?? [] as $pago) {
                DiasVenta::create([
                    'id_venta'   => $venta->id_venta,
                    'fecha'      => $pago['fecha'],
                    'monto'      => $pago['monto'],
                    'estado'     => ($pago['pagado'] ?? false) ? '1' : '0',
                    'tipo_pago'  => $pago['tipo_pago'] ?? 'Efectivo',
                    'id_usuario' => auth()->id(),
                ]);
            }

            Cliente::where('id_cliente', $data['id_cliente'])->update([
                'ultima_venta' => now()->toDateString(),
                'total_venta'  => DB::raw("IFNULL(total_venta, 0) + {$data['total']}"),
            ]);

            DB::commit();

            $doc = "{$tido->serie}-" . str_pad($numero, 8, '0', STR_PAD_LEFT);
            return response()->json(['res' => true, 'id_venta' => $venta->id_venta,
                'msg' => "Venta {$doc} registrada correctamente."]);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error guardar venta: ' . $e->getMessage());
            return response()->json(['res' => false, 'msg' => 'Error al registrar la venta.'], 500);
        }
    }

    public function anular(Request $request): JsonResponse
    {
        $request->validate(['id_venta' => 'required|integer']);
        DB::beginTransaction();
        try {
            $venta = Venta::with('productosVenta')->deEmpresa($this->empresa())->findOrFail($request->id_venta);
            if ($venta->estado === '0') {
                return response()->json(['res' => false, 'msg' => 'La venta ya está anulada.'], 422);
            }
            $motVenta = $this->motivoVenta();
            $docVenta = "{$venta->serie}-" . str_pad($venta->numero, 8, '0', STR_PAD_LEFT);
            foreach ($venta->productosVenta as $det) {
                $p = Producto::find($det->id_producto);
                if ($p) {
                    $ant  = (int) $p->cantidad;
                    $cant = (int) $det->cantidad;
                    $p->increment('cantidad', $det->cantidad);
                    $this->kardex($p->id_producto, $cant, 'I', $ant, $ant + $cant, $p->almacen, $p->costo, $motVenta, "Anulación de venta {$docVenta}");
                }
            }
            $venta->update(['estado' => '0']);
            DB::commit();
            return response()->json(['res' => true, 'msg' => 'Venta anulada. Stock repuesto correctamente.']);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['res' => false, 'msg' => 'Error al anular la venta.'], 500);
        }
    }

    public function detalle(Request $request): JsonResponse
    {
        $request->validate(['id_venta' => 'required|integer']);
        $venta = Venta::with(['cliente','productosVenta.producto','tipoDocumento','pagos','sunat','vendedor'])
            ->deEmpresa($this->empresa())->findOrFail($request->id_venta);
        return response()->json($venta);
    }

    public function tipoVenta(): JsonResponse
    {
        $docs = DocumentoEmpresa::where('id_empresa', $this->empresa())
            ->where('sucursal', $this->sucursal())->get();
        return response()->json($docs);
    }

    public function buscarProducto(Request $request, int $id): JsonResponse
    {
        $term = $request->get('term', '');
        return response()->json(
            Producto::deEmpresa($this->empresa())->activos()->where('almacen', $id)
                ->where(fn($q) => $q->where('descripcion','like',"%{$term}%")
                    ->orWhere('cod_barra','like',"%{$term}%")
                    ->orWhere('codigo','like',"%{$term}%"))
                ->limit(50)
                ->get(['id_producto','descripcion','precio','precio2','precio3',
                       'precio4','cantidad','cod_barra','codsunat','iscbp','codigo'])
        );
    }

    public function buscarProductoCoti(Request $request): JsonResponse
    {
        $term = $request->get('term', '');
        return response()->json(
            Producto::deEmpresa($this->empresa())->activos()
                ->where(fn($q) => $q->where('descripcion','like',"%{$term}%")
                    ->orWhere('cod_barra','like',"%{$term}%"))
                ->limit(50)->get()
        );
    }

    public function cargarVentaProductos(Request $request): JsonResponse
    {
        $request->validate(['id_venta' => 'required|integer']);
        return response()->json(
            ProductoVenta::with('producto')->where('id_venta', $request->id_venta)->get()
        );
    }

    public function cargarVentaDetalles(Request $request): JsonResponse
    {
        return $this->detalle($request);
    }

    public function cargarVentaServicios(Request $request): JsonResponse
    {
        $request->validate(['id_venta' => 'required|integer']);
        $venta = Venta::with('productosVenta')->deEmpresa($this->empresa())->find($request->id_venta);
        return response()->json($venta?->productosVenta ?? []);
    }

    public function editProducto(Request $request): JsonResponse
    {
        $request->validate(['id_venta' => 'required|integer', 'productos' => 'required|array|min:1']);
        DB::beginTransaction();
        try {
            $venta = Venta::with('productosVenta')->deEmpresa($this->empresa())->findOrFail($request->id_venta);
            foreach ($venta->productosVenta as $det) {
                Producto::where('id_producto', $det->id_producto)->increment('cantidad', $det->cantidad);
            }
            ProductoVenta::where('id_venta', $venta->id_venta)->delete();
            $total = 0;
            foreach ($request->productos as $item) {
                ProductoVenta::create([
                    'id_venta' => $venta->id_venta, 'id_producto' => $item['id_producto'],
                    'descripcion' => $item['descripcion'], 'cantidad' => $item['cantidad'],
                    'precio' => $item['precio'], 'total' => $item['total'],
                ]);
                $total += $item['total'];
                Producto::where('id_producto', $item['id_producto'])->decrement('cantidad', $item['cantidad']);
            }
            $venta->update(['total' => $total]);
            DB::commit();
            return response()->json(['res' => true, 'msg' => 'Venta actualizada.']);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['res' => false, 'msg' => 'Error al editar.'], 500);
        }
    }

    public function editServicio(Request $request): JsonResponse
    {
        $request->validate(['id_venta' => 'required|integer']);
        Venta::deEmpresa($this->empresa())->findOrFail($request->id_venta)
            ->update(['observacion' => $request->observacion, 'total' => $request->total]);
        return response()->json(['res' => true]);
    }

    public function ingresoAlmacen(Request $request): JsonResponse
    {
        $request->validate(['id_producto' => 'required|integer', 'cantidad' => 'required|numeric|min:0.001']);
        Producto::deEmpresa($this->empresa())->where('id_producto', $request->id_producto)
            ->increment('cantidad', $request->cantidad);
        return response()->json(['res' => true, 'msg' => 'Ingreso registrado.']);
    }

    public function egresoAlmacen(Request $request): JsonResponse
    {
        $request->validate(['id_producto' => 'required|integer', 'cantidad' => 'required|numeric|min:0.001']);
        $p = Producto::deEmpresa($this->empresa())->where('id_producto', $request->id_producto)->firstOrFail();
        if ($p->cantidad < $request->cantidad) {
            return response()->json(['res' => false, 'msg' => 'Stock insuficiente.'], 422);
        }
        $p->decrement('cantidad', $request->cantidad);
        return response()->json(['res' => true, 'msg' => 'Egreso registrado.']);
    }

    public function generarTextLibroVentas(Request $request): JsonResponse
    {
        return response()->json(['res' => true, 'msg' => 'Generación en cola.']);
    }
}
