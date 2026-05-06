<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\{Venta, ProductoVenta, DiasVenta, Producto, DocumentoEmpresa, Cliente};
use App\Http\Requests\Ventas\GuardarVentaRequest;
use Illuminate\Http\{Request, JsonResponse};
use Illuminate\Routing\Attributes\Middleware;
use Illuminate\Support\Facades\{DB, Log};
use Yajra\DataTables\Facades\DataTables;

#[Middleware(['auth:sanctum', 'check.empresa'])]
class VentasApiController extends Controller
{
    private function empresa(): int  { return (int) session('id_empresa'); }
    private function sucursal(): int { return (int) session('sucursal'); }

    /** DataTables server-side */
    public function listar(Request $request): mixed
    {
        $query = Venta::with(['cliente','tipoDocumento','sunat'])
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

    /** Guardar nueva venta */
    public function guardar(GuardarVentaRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();

            // Obtener y bloquear correlativo
            $tido = DocumentoEmpresa::where('id_empresa', $this->empresa())
                ->where('sucursal', $this->sucursal())
                ->where('id_tido', $data['id_tido'])
                ->lockForUpdate()
                ->firstOrFail();

            $numero = $tido->numero + 1;

            // Crear venta
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

            // Actualizar correlativo
            $tido->increment('numero');

            // Insertar detalle de productos y descontar stock
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

                Producto::where('id_producto', $item['id_producto'])
                    ->decrement('cantidad', $item['cantidad']);
            }

            // Registrar pagos / cuotas
            foreach ($data['lista_pagos'] ?? [] as $pago) {
                DiasVenta::create([
                    'id_venta'  => $venta->id_venta,
                    'fecha'     => $pago['fecha'],
                    'monto'     => $pago['monto'],
                    'estado'    => ($pago['pagado'] ?? false) ? '1' : '0',
                    'tipo_pago' => $pago['tipo_pago'] ?? 'Efectivo',
                    'id_usuario'=> auth()->id(),
                ]);
            }

            // Actualizar cliente
            Cliente::where('id_cliente', $data['id_cliente'])
                ->update([
                    'ultima_venta' => now()->toDateString(),
                    'total_venta'  => DB::raw("IFNULL(total_venta, 0) + {$data['total']}"),
                ]);

            DB::commit();

            $docCompleto = "{$tido->serie}-" . str_pad($numero, 8, '0', STR_PAD_LEFT);
            return response()->json([
                'res'      => true,
                'id_venta' => $venta->id_venta,
                'msg'      => "Venta {$docCompleto} registrada correctamente.",
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error guardar venta: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['res' => false, 'msg' => 'Error al registrar la venta.'], 500);
        }
    }

    /** Anular venta y reponer stock */
    public function anular(Request $request): JsonResponse
    {
        $request->validate(['id_venta' => 'required|integer']);

        DB::beginTransaction();
        try {
            $venta = Venta::with('productosVenta')
                ->deEmpresa($this->empresa())
                ->findOrFail($request->id_venta);

            if ($venta->estado === '0') {
                return response()->json(['res' => false, 'msg' => 'La venta ya está anulada.'], 422);
            }

            // Reponer stock
            foreach ($venta->productosVenta as $det) {
                Producto::where('id_producto', $det->id_producto)
                    ->increment('cantidad', $det->cantidad);
            }

            $venta->update(['estado' => '0']);
            DB::commit();

            return response()->json(['res' => true, 'msg' => 'Venta anulada. Stock repuesto correctamente.']);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error anular venta: ' . $e->getMessage());
            return response()->json(['res' => false, 'msg' => 'Error al anular la venta.'], 500);
        }
    }

    /** Detalle de venta */
    public function detalle(Request $request): JsonResponse
    {
        $request->validate(['id_venta' => 'required|integer']);
        $venta = Venta::with(['cliente','productosVenta.producto','tipoDocumento','pagos','sunat','vendedor'])
            ->deEmpresa($this->empresa())
            ->findOrFail($request->id_venta);
        return response()->json($venta);
    }

    /** Tipo de venta */
    public function tipoVenta(): JsonResponse
    {
        $docs = DocumentoEmpresa::where('id_empresa', $this->empresa())
            ->where('sucursal', $this->sucursal())->get();
        return response()->json($docs);
    }

    /** Buscar productos para venta */
    public function buscarProducto(Request $request, int $id): JsonResponse
    {
        $term = $request->get('term', '');
        $productos = Producto::deEmpresa($this->empresa())
            ->activos()
            ->where('almacen', $id)
            ->where(fn($q) => $q->where('descripcion','like',"%{$term}%")
                                ->orWhere('cod_barra','like',"%{$term}%")
                                ->orWhere('codigo','like',"%{$term}%"))
            ->limit(50)
            ->get(['id_producto','descripcion','precio','precio2','precio3',
                   'precio4','cantidad','cod_barra','codsunat','iscbp','codigo']);
        return response()->json($productos);
    }

    /** Buscar productos para cotización */
    public function buscarProductoCoti(Request $request): JsonResponse
    {
        $term = $request->get('term', '');
        $productos = Producto::deEmpresa($this->empresa())
            ->activos()
            ->where(fn($q) => $q->where('descripcion','like',"%{$term}%")->orWhere('cod_barra','like',"%{$term}%"))
            ->limit(50)->get();
        return response()->json($productos);
    }

    /** Cargar productos de una venta */
    public function cargarVentaProductos(Request $request): JsonResponse
    {
        $request->validate(['id_venta' => 'required|integer']);
        $detalle = ProductoVenta::with('producto')->where('id_venta', $request->id_venta)->get();
        return response()->json($detalle);
    }

    /** Cargar info completa de venta */
    public function cargarVentaDetalles(Request $request): JsonResponse
    {
        return $this->detalle($request);
    }

    /** Cargar servicios de venta */
    public function cargarVentaServicios(Request $request): JsonResponse
    {
        $request->validate(['id_venta' => 'required|integer']);
        $venta = Venta::with('productosVenta')->deEmpresa($this->empresa())->find($request->id_venta);
        return response()->json($venta?->productosVenta ?? []);
    }

    /** Editar venta de productos */
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
                    'id_venta'    => $venta->id_venta,
                    'id_producto' => $item['id_producto'],
                    'descripcion' => $item['descripcion'],
                    'cantidad'    => $item['cantidad'],
                    'precio'      => $item['precio'],
                    'total'       => $item['total'],
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

    /** Editar venta de servicio */
    public function editServicio(Request $request): JsonResponse
    {
        $request->validate(['id_venta' => 'required|integer']);
        Venta::deEmpresa($this->empresa())->findOrFail($request->id_venta)
            ->update(['observacion' => $request->observacion, 'total' => $request->total]);
        return response()->json(['res' => true]);
    }

    /** Ingreso manual de stock */
    public function ingresoAlmacen(Request $request): JsonResponse
    {
        $request->validate(['id_producto' => 'required|integer', 'cantidad' => 'required|numeric|min:0.001']);
        Producto::deEmpresa($this->empresa())->where('id_producto', $request->id_producto)
            ->increment('cantidad', $request->cantidad);
        return response()->json(['res' => true, 'msg' => 'Ingreso registrado.']);
    }

    /** Egreso manual de stock */
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

    /** Generar TXT libro de ventas SUNAT */
    public function generarTextLibroVentas(Request $request): JsonResponse
    {
        // TODO: implementar generación TXT formato SUNAT PLE 8.1
        return response()->json(['res' => true, 'msg' => 'Generación en cola.']);
    }
}
