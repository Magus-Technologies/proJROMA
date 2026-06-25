<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use App\Models\MotivoMovimiento;
use App\Models\InventarioMovimiento;
use App\Models\Almacen;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class MovimientoApiController extends Controller
{
    private function empresa(): int { return (int) session('id_empresa'); }

    /** Motivos generados por flujos automáticos (no son ajustes manuales). */
    private const AUTOMATIZADOS = ['Compra', 'Venta', 'Traslado entrada', 'Traslado salida', 'Préstamo entregado', 'Préstamo recibido'];

    /** Motivos activos. ?tipo=I|S · ?ajuste=1 excluye los motivos de flujos automáticos. */
    public function motivos(Request $request): JsonResponse
    {
        return response()->json(
            MotivoMovimiento::where('id_empresa', $this->empresa())
                ->where('estado', '1')
                ->when($request->filled('tipo'), fn ($q) => $q->where('tipo', $request->tipo))
                ->when($request->boolean('ajuste'), fn ($q) => $q->whereNotIn('nombre', self::AUTOMATIZADOS))
                ->orderBy('nombre')
                ->get()
        );
    }

    /** Productos del catálogo (todos los almacenes) o filtrados por almacén si se pasa ?almacen= */
    public function productosAlmacen(Request $request): JsonResponse
    {
        $query = Producto::where('id_empresa', $this->empresa())
            ->where('estado', '1')
            ->orderBy('descripcion');

        if ($request->filled('almacen') && !$request->boolean('todos')) {
            $query->where('almacen', $request->almacen);
        }

        return response()->json(
            $query->get(['id_producto', 'codigo', 'descripcion', 'cantidad', 'costo', 'almacen'])
        );
    }

    /** Lista de movimientos (Kardex), más recientes primero. */
    public function listar(): JsonResponse
    {
        $rows = DB::table('inventario_movimientos as m')
            ->leftJoin('productos as p', 'p.id_producto', '=', 'm.id_producto')
            ->leftJoin('motivos_movimiento as mo', 'mo.id_motivo', '=', 'm.id_motivo')
            ->leftJoin('almacenes as a', function ($j) {
                $j->on('a.codigo', '=', 'm.almacen')->on('a.id_empresa', '=', 'm.id_empresa');
            })
            ->where('m.id_empresa', $this->empresa())
            ->orderByDesc('m.id_movimiento')
            ->limit(1000)
            ->select(
                'm.*',
                'p.descripcion as producto',
                'p.codigo as producto_codigo',
                'mo.nombre as motivo',
                DB::raw('COALESCE(a.nombre, m.almacen) as almacen_nombre')
            )
            ->get();

        return response()->json($rows);
    }

    /** Solo los AJUSTES (cuadres): movimientos que no provienen de flujos automáticos. */
    public function ajustes(): JsonResponse
    {
        $rows = DB::table('inventario_movimientos as m')
            ->leftJoin('productos as p', 'p.id_producto', '=', 'm.id_producto')
            ->leftJoin('motivos_movimiento as mo', 'mo.id_motivo', '=', 'm.id_motivo')
            ->leftJoin('almacenes as a', function ($j) {
                $j->on('a.codigo', '=', 'm.almacen')->on('a.id_empresa', '=', 'm.id_empresa');
            })
            ->where('m.id_empresa', $this->empresa())
            ->where(function ($q) {
                $q->whereNotIn('mo.nombre', self::AUTOMATIZADOS)->orWhereNull('mo.nombre');
            })
            ->orderByDesc('m.id_movimiento')
            ->limit(1000)
            ->select('m.*', 'p.descripcion as producto', 'mo.nombre as motivo', DB::raw('COALESCE(a.nombre, m.almacen) as almacen_nombre'))
            ->get();

        return response()->json($rows);
    }

    /** Registra un ingreso/salida y actualiza el stock del producto en su almacén. */
    public function guardar(Request $request): JsonResponse
    {
        $data = $request->validate([
            'almacen'      => 'required',
            'id_producto'  => 'required|integer',
            'tipo'         => 'required|in:I,S',
            'id_motivo'    => 'nullable|integer',
            'cantidad'     => 'required|integer|min:1',
            'costo'        => 'nullable|numeric|min:0',
            'id_proveedor' => 'nullable|integer',
            'observacion'  => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            $producto = Producto::where('id_empresa', $this->empresa())
                ->where('id_producto', $data['id_producto'])
                ->lockForUpdate()
                ->firstOrFail();

            $anterior = (int) $producto->cantidad;
            $cant     = (int) $data['cantidad'];

            if ($data['tipo'] === 'S' && $cant > $anterior) {
                DB::rollBack();
                return response()->json([
                    'res' => false,
                    'msg' => "Stock insuficiente. Disponible: {$anterior}.",
                ], 409);
            }

            $nuevo = $data['tipo'] === 'I' ? $anterior + $cant : $anterior - $cant;

            $update = ['cantidad' => $nuevo];
            // Al ingresar con costo, actualiza el costo del producto
            if ($data['tipo'] === 'I' && !empty($data['costo'])) {
                $update['costo'] = $data['costo'];
            }
            $producto->update($update);

            InventarioMovimiento::create([
                'id_empresa'     => $this->empresa(),
                'almacen'        => $data['almacen'],
                'id_producto'    => $data['id_producto'],
                'tipo'           => $data['tipo'],
                'id_motivo'      => $data['id_motivo'] ?? null,
                'cantidad'       => $cant,
                'stock_anterior' => $anterior,
                'stock_nuevo'    => $nuevo,
                'costo'          => $data['costo'] ?? null,
                'id_proveedor'   => $data['id_proveedor'] ?? null,
                'observacion'    => $data['observacion'] ?? null,
                'id_usuario'     => (int) (auth()->user()->usuario_id ?? 0),
                'fecha'          => now(),
            ]);

            DB::commit();
            return response()->json(['res' => true, 'stock_nuevo' => $nuevo]);
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Error movimiento inventario: ' . $e->getMessage());
            return response()->json(['res' => false, 'msg' => 'Error al registrar el movimiento.'], 500);
        }
    }

    /** Guarda múltiples productos en un solo ajuste (batch). Cada producto especifica su nuevo_stock. */
    public function guardarBatch(Request $request): JsonResponse
    {
        $data = $request->validate([
            'almacen'      => 'required',
            'id_motivo'    => 'nullable|integer',
            'observacion'  => 'nullable|string|max:255',
            'productos'    => 'required|array|min:1',
            'productos.*.id_producto' => 'required|integer',
            'productos.*.nuevo_stock' => 'required|integer|min:0',
        ]);

        $emp  = $this->empresa();
        $uid  = (int) (auth()->user()->usuario_id ?? 0);
        $obs  = trim($data['observacion'] ?? '');
        $movs = [];

        DB::beginTransaction();
        try {
            foreach ($data['productos'] as $item) {
                    $idProducto = $item['id_producto'];
                    $nuevo      = (int) $item['nuevo_stock'];

                    // Buscar producto en el almacén destino
                    $producto = Producto::where('id_empresa', $emp)
                        ->where('id_producto', $idProducto)
                        ->where('almacen', $data['almacen'])
                        ->lockForUpdate()
                        ->first();

                    // Si no existe en el almacén destino, clonarlo
                    if (!$producto) {
                        $origen = Producto::where('id_empresa', $emp)
                            ->where('id_producto', $idProducto)
                            ->lockForUpdate()
                            ->first();

                        if (!$origen) {
                            DB::rollBack();
                            return response()->json(['res' => false, 'msg' => "Producto ID {$idProducto} no encontrado."], 404);
                        }

                        $producto = $origen->replicate();
                        $producto->almacen  = $data['almacen'];
                        $producto->cantidad = 0;
                        $producto->save();
                        $idProducto = $producto->id_producto;
                    }

                    $anterior  = (int) $producto->cantidad;
                    $diferencia = $nuevo - $anterior;

                if ($diferencia === 0) continue; // sin cambio

                $tipo = $diferencia > 0 ? 'I' : 'S';
                $cant = abs($diferencia);

                if ($tipo === 'S' && $cant > $anterior) {
                    DB::rollBack();
                    return response()->json([
                        'res' => false,
                        'msg' => "Stock insuficiente para {$producto->descripcion}. Disponible: {$anterior}.",
                    ], 409);
                }

                $producto->update(['cantidad' => $nuevo]);

                $movs[] = InventarioMovimiento::create([
                    'id_empresa'     => $emp,
                    'almacen'        => $data['almacen'],
                    'id_producto'    => $idProducto,
                    'tipo'           => $tipo,
                    'id_motivo'      => $data['id_motivo'] ?? null,
                    'cantidad'       => $cant,
                    'stock_anterior' => $anterior,
                    'stock_nuevo'    => $nuevo,
                    'costo'          => null,
                    'id_proveedor'   => null,
                    'observacion'    => $obs,
                    'id_usuario'     => $uid,
                    'fecha'          => now(),
                ]);
            }

            DB::commit();
            return response()->json(['res' => true, 'count' => count($movs)]);
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Error guardarBatch: ' . $e->getMessage());
            return response()->json(['res' => false, 'msg' => 'Error al registrar el ajuste múltiple.'], 500);
        }
    }

    /** Deshace un ajuste manual: revierte el stock y elimina el movimiento. */
    public function anular(Request $request): JsonResponse
    {
        $request->validate(['id' => 'required|integer']);
        $emp = $this->empresa();

        DB::beginTransaction();
        try {
            $mov = InventarioMovimiento::where('id_empresa', $emp)->where('id_movimiento', $request->id)->lockForUpdate()->firstOrFail();

            $motNombre = $mov->id_motivo ? MotivoMovimiento::where('id_motivo', $mov->id_motivo)->value('nombre') : null;
            if ($motNombre && in_array($motNombre, self::AUTOMATIZADOS)) {
                DB::rollBack();
                return response()->json(['res' => false, 'msg' => 'Solo se pueden deshacer ajustes manuales (no compras, ventas ni traslados).'], 409);
            }

            $p = Producto::where('id_empresa', $emp)->where('id_producto', $mov->id_producto)->lockForUpdate()->firstOrFail();
            $ant = (int) $p->cantidad;
            $cant = (int) $mov->cantidad;

            if ($mov->tipo === 'I') {
                if ($ant < $cant) {
                    DB::rollBack();
                    return response()->json(['res' => false, 'msg' => "No se puede deshacer: el stock ya fue utilizado (actual: {$ant})."], 409);
                }
                $p->update(['cantidad' => $ant - $cant]);
            } else {
                $p->update(['cantidad' => $ant + $cant]);
            }

            $mov->delete();
            DB::commit();
            return response()->json(['res' => true]);
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Error anular ajuste: ' . $e->getMessage());
            return response()->json(['res' => false, 'msg' => 'Error al deshacer el ajuste.'], 500);
        }
    }

    /** Traslado entre almacenes: salida del origen + ingreso al destino. */
    public function traslado(Request $request): JsonResponse
    {
        $data = $request->validate([
            'almacen_origen'  => 'required',
            'almacen_destino' => 'required|different:almacen_origen',
            'id_producto'     => 'required|integer',
            'cantidad'        => 'required|integer|min:1',
            'observacion'     => 'nullable|string|max:200',
        ]);

        $emp  = $this->empresa();
        $cant = (int) $data['cantidad'];

        DB::beginTransaction();
        try {
            $origen = Producto::where('id_empresa', $emp)
                ->where('id_producto', $data['id_producto'])
                ->where('almacen', $data['almacen_origen'])
                ->lockForUpdate()
                ->firstOrFail();

            if ($cant > (int) $origen->cantidad) {
                DB::rollBack();
                return response()->json(['res' => false, 'msg' => "Stock insuficiente en el origen. Disponible: {$origen->cantidad}."], 409);
            }

            $motSal = MotivoMovimiento::where('id_empresa', $emp)->where('tipo', 'S')->where('nombre', 'Traslado salida')->value('id_motivo');
            $motIng = MotivoMovimiento::where('id_empresa', $emp)->where('tipo', 'I')->where('nombre', 'Traslado entrada')->value('id_motivo');
            $nomOrig = Almacen::where('id_empresa', $emp)->where('codigo', $data['almacen_origen'])->value('nombre') ?? $data['almacen_origen'];
            $nomDest = Almacen::where('id_empresa', $emp)->where('codigo', $data['almacen_destino'])->value('nombre') ?? $data['almacen_destino'];
            $obs = trim($data['observacion'] ?? '');
            $uid = (int) (auth()->user()->usuario_id ?? 0);

            // ── Salida del origen ──
            $antO   = (int) $origen->cantidad;
            $nuevoO = $antO - $cant;
            $origen->update(['cantidad' => $nuevoO]);
            InventarioMovimiento::create([
                'id_empresa' => $emp, 'almacen' => $data['almacen_origen'], 'id_producto' => $origen->id_producto,
                'tipo' => 'S', 'id_motivo' => $motSal, 'cantidad' => $cant,
                'stock_anterior' => $antO, 'stock_nuevo' => $nuevoO, 'costo' => $origen->costo,
                'observacion' => trim("Traslado a {$nomDest}. {$obs}"), 'id_usuario' => $uid, 'fecha' => now(),
            ]);

            // ── Ingreso al destino (busca por código; si no existe, clona el producto) ──
            $dest = null;
            if (!empty($origen->codigo)) {
                $dest = Producto::where('id_empresa', $emp)->where('almacen', $data['almacen_destino'])
                    ->where('codigo', $origen->codigo)->lockForUpdate()->first();
            }
            if ($dest) {
                $antD   = (int) $dest->cantidad;
                $nuevoD = $antD + $cant;
                $dest->update(['cantidad' => $nuevoD]);
            } else {
                $dest = $origen->replicate();
                $dest->almacen  = $data['almacen_destino'];
                $dest->cantidad = $cant;
                $dest->save();
                $antD = 0; $nuevoD = $cant;
            }
            InventarioMovimiento::create([
                'id_empresa' => $emp, 'almacen' => $data['almacen_destino'], 'id_producto' => $dest->id_producto,
                'tipo' => 'I', 'id_motivo' => $motIng, 'cantidad' => $cant,
                'stock_anterior' => $antD, 'stock_nuevo' => $nuevoD, 'costo' => $dest->costo,
                'observacion' => trim("Traslado desde {$nomOrig}. {$obs}"), 'id_usuario' => $uid, 'fecha' => now(),
            ]);

            DB::commit();
            return response()->json(['res' => true]);
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Error traslado: ' . $e->getMessage());
            return response()->json(['res' => false, 'msg' => 'Error al realizar el traslado.'], 500);
        }
    }
}
