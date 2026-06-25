<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Compra;
use App\Models\Producto;
use App\Models\MotivoMovimiento;
use App\Models\InventarioMovimiento;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class RecepcionApiController extends Controller
{
    private function empresa(): int { return (int) session('id_empresa'); }

    /** Compras no recepcionadas del todo (0 = pendiente, 2 = parcial). */
    public function pendientes(): JsonResponse
    {
        $rows = DB::table('compras as c')
            ->leftJoin('proveedores as p', 'p.proveedor_id', '=', 'c.id_proveedor')
            ->where('c.id_empresa', $this->empresa())
            ->where('c.recepcionado', '!=', 1)
            ->orderByDesc('c.id_compra')
            ->select(
                'c.id_compra', 'c.recepcionado',
                DB::raw("TRIM(BOTH '-' FROM CONCAT(COALESCE(c.serie,''),'-',COALESCE(c.numero,''))) as documento"),
                'c.fecha_emision', 'c.total',
                DB::raw("TRIM(COALESCE(p.razon_social, p.nombre_comercial, '-')) as proveedor"),
                DB::raw('(SELECT COUNT(*) FROM productos_compras pc WHERE pc.id_compra = c.id_compra) as items')
            )
            ->get();

        return response()->json($rows);
    }

    /** Ítems de la compra con pedido / recepcionado / pendiente. */
    public function lineas(Request $request): JsonResponse
    {
        $request->validate(['id' => 'required|integer']);
        $rows = DB::table('productos_compras as pc')
            ->join('productos as p', 'p.id_producto', '=', 'pc.id_producto')
            ->where('pc.id_compra', $request->id)
            ->select('pc.id_producto', 'p.codigo', 'p.descripcion as producto', 'p.medida as unidad',
                DB::raw('SUM(pc.cantidad) as pedido'), DB::raw('MAX(pc.costo) as costo'))
            ->groupBy('pc.id_producto', 'p.codigo', 'p.descripcion', 'p.medida')
            ->get();

        foreach ($rows as $r) {
            $recibido = (int) DB::table('recepcion_detalle as rd')
                ->join('recepciones as rc', 'rc.id_recepcion', '=', 'rd.id_recepcion')
                ->where('rc.id_compra', $request->id)->where('rd.id_producto', $r->id_producto)
                ->sum('rd.cantidad');
            $r->pedido    = (int) $r->pedido;
            $r->recibido  = $recibido;
            $r->pendiente = $r->pedido - $recibido;
        }

        return response()->json($rows);
    }

    /** Ingresa al almacén (busca por código; si no existe, clona) + movimiento Kardex. */
    private function ingresar(int $emp, string $almacen, int $sourceId, int $cant, $costo, ?int $motivo, string $obs, int $uid): void
    {
        $source = Producto::where('id_empresa', $emp)->where('id_producto', $sourceId)->firstOrFail();

        $dest = null;
        if (! empty($source->codigo)) {
            $dest = Producto::where('id_empresa', $emp)->where('almacen', $almacen)
                ->where('codigo', $source->codigo)->lockForUpdate()->first();
        }
        if ($dest) {
            $ant = (int) $dest->cantidad;
            $dest->update(['cantidad' => $ant + $cant, 'costo' => $costo ?: $dest->costo]);
        } else {
            $dest = $source->replicate();
            $dest->almacen  = $almacen;
            $dest->cantidad = $cant;
            if ($costo) $dest->costo = $costo;
            $dest->save();
            $ant = 0;
        }

        InventarioMovimiento::create([
            'id_empresa' => $emp, 'almacen' => $almacen, 'id_producto' => $dest->id_producto,
            'tipo' => 'I', 'id_motivo' => $motivo, 'cantidad' => $cant,
            'stock_anterior' => $ant, 'stock_nuevo' => $ant + $cant, 'costo' => $costo ?: null,
            'observacion' => $obs, 'id_usuario' => $uid, 'fecha' => now(),
        ]);
    }

    /** Recepción parcial o total: recibe líneas {id_producto, cantidad} + almacén destino. */
    public function recepcionar(Request $request): JsonResponse
    {
        $data = $request->validate([
            'id_compra'              => 'required|integer',
            'almacen'                => 'required',
            'observacion'            => 'nullable|string|max:200',
            'detalles'               => 'required|array|min:1',
            'detalles.*.id_producto' => 'required|integer',
            'detalles.*.cantidad'    => 'required|integer|min:1',
        ]);

        $emp = $this->empresa();
        $uid = (int) (auth()->user()->usuario_id ?? 0);

        DB::beginTransaction();
        try {
            $compra = Compra::where('id_empresa', $emp)->where('id_compra', $data['id_compra'])->lockForUpdate()->firstOrFail();
            if ((int) $compra->recepcionado === 1) {
                DB::rollBack();
                return response()->json(['res' => false, 'msg' => 'Esta compra ya está totalmente recepcionada.'], 409);
            }

            $motCompra = MotivoMovimiento::where('id_empresa', $emp)->where('tipo', 'I')->where('nombre', 'Compra')->value('id_motivo');

            // Cabecera del documento de recepción
            $idRecepcion = DB::table('recepciones')->insertGetId([
                'id_empresa'  => $emp, 'id_compra' => $compra->id_compra, 'almacen' => $data['almacen'],
                'fecha'       => now(), 'observacion' => $data['observacion'] ?? null, 'id_usuario' => $uid,
            ]);

            foreach ($data['detalles'] as $linea) {
                $cant = (int) $linea['cantidad'];

                $pedido   = (int) DB::table('productos_compras')->where('id_compra', $compra->id_compra)->where('id_producto', $linea['id_producto'])->sum('cantidad');
                $recibido = (int) DB::table('recepcion_detalle as rd')->join('recepciones as rc', 'rc.id_recepcion', '=', 'rd.id_recepcion')
                    ->where('rc.id_compra', $compra->id_compra)->where('rd.id_producto', $linea['id_producto'])->sum('rd.cantidad');
                $pendiente = $pedido - $recibido;
                if ($cant > $pendiente) {
                    DB::rollBack();
                    return response()->json(['res' => false, 'msg' => "No puedes recepcionar más de lo pendiente ({$pendiente})."], 409);
                }

                $costo = DB::table('productos_compras')->where('id_compra', $compra->id_compra)->where('id_producto', $linea['id_producto'])->value('costo');
                $this->ingresar($emp, $data['almacen'], (int) $linea['id_producto'], $cant, $costo, $motCompra, "Recepción #{$idRecepcion} (compra #{$compra->id_compra})", $uid);

                DB::table('recepcion_detalle')->insert([
                    'id_recepcion' => $idRecepcion, 'id_producto' => $linea['id_producto'], 'cantidad' => $cant,
                ]);
            }

            // Estado: 0 pendiente · 2 parcial · 1 completo
            $totalPedido   = (int) DB::table('productos_compras')->where('id_compra', $compra->id_compra)->sum('cantidad');
            $totalRecibido = (int) DB::table('recepcion_detalle as rd')->join('recepciones as rc', 'rc.id_recepcion', '=', 'rd.id_recepcion')
                ->where('rc.id_compra', $compra->id_compra)->sum('rd.cantidad');
            $estado = $totalRecibido >= $totalPedido ? 1 : ($totalRecibido > 0 ? 2 : 0);
            $compra->update(['recepcionado' => $estado]);

            DB::commit();
            return response()->json(['res' => true, 'estado' => $estado, 'id_recepcion' => $idRecepcion]);
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Error recepción: ' . $e->getMessage());
            return response()->json(['res' => false, 'msg' => 'Error al recepcionar.'], 500);
        }
    }

    /** Revierte el ingreso al almacén de una línea: resta stock + movimiento de salida (Kardex). */
    private function revertir(int $emp, string $almacen, int $sourceId, int $cant, string $obs, int $uid): void
    {
        $source = Producto::where('id_empresa', $emp)->where('id_producto', $sourceId)->first();
        if (! $source) return;

        $dest = null;
        if (! empty($source->codigo)) {
            $dest = Producto::where('id_empresa', $emp)->where('almacen', $almacen)
                ->where('codigo', $source->codigo)->lockForUpdate()->first();
        }
        // Producto sin código que ya estaba en ese almacén (no se clonó)
        if (! $dest && (string) $source->almacen === (string) $almacen) {
            $dest = $source;
        }
        if (! $dest) return;

        $ant   = (int) $dest->cantidad;
        $nuevo = max(0, $ant - $cant);
        $dest->update(['cantidad' => $nuevo]);

        InventarioMovimiento::create([
            'id_empresa' => $emp, 'almacen' => $almacen, 'id_producto' => $dest->id_producto,
            'tipo' => 'S', 'id_motivo' => null, 'cantidad' => $cant,
            'stock_anterior' => $ant, 'stock_nuevo' => $nuevo, 'costo' => null,
            'observacion' => $obs, 'id_usuario' => $uid, 'fecha' => now(),
        ]);
    }

    /** Deshace una recepción: revierte el stock, elimina el documento y recalcula el estado de la compra. */
    public function deshacer(Request $request): JsonResponse
    {
        $request->validate(['id' => 'required|integer']);
        $emp = $this->empresa();
        $uid = (int) (auth()->user()->usuario_id ?? 0);

        DB::beginTransaction();
        try {
            $rec = DB::table('recepciones')->where('id_empresa', $emp)->where('id_recepcion', $request->id)->lockForUpdate()->first();
            if (! $rec) {
                DB::rollBack();
                return response()->json(['res' => false, 'msg' => 'Recepción no encontrada.'], 404);
            }

            $detalles = DB::table('recepcion_detalle')->where('id_recepcion', $rec->id_recepcion)->get();
            foreach ($detalles as $d) {
                $this->revertir($emp, $rec->almacen, (int) $d->id_producto, (int) $d->cantidad,
                    "Deshacer recepción #{$rec->id_recepcion} (compra #{$rec->id_compra})", $uid);
            }

            DB::table('recepcion_detalle')->where('id_recepcion', $rec->id_recepcion)->delete();
            DB::table('recepciones')->where('id_recepcion', $rec->id_recepcion)->delete();

            // Recalcular estado: 0 pendiente · 2 parcial · 1 completo
            $totalPedido   = (int) DB::table('productos_compras')->where('id_compra', $rec->id_compra)->sum('cantidad');
            $totalRecibido = (int) DB::table('recepcion_detalle as rd')->join('recepciones as rc', 'rc.id_recepcion', '=', 'rd.id_recepcion')
                ->where('rc.id_compra', $rec->id_compra)->sum('rd.cantidad');
            $estado = ($totalPedido > 0 && $totalRecibido >= $totalPedido) ? 1 : ($totalRecibido > 0 ? 2 : 0);
            DB::table('compras')->where('id_compra', $rec->id_compra)->update(['recepcionado' => $estado]);

            DB::commit();
            return response()->json(['res' => true, 'estado' => $estado]);
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Error deshacer recepción: ' . $e->getMessage());
            return response()->json(['res' => false, 'msg' => 'Error al deshacer la recepción.'], 500);
        }
    }

    /** Registro de todas las recepciones (documentos) de la empresa. */
    public function registro(): JsonResponse
    {
        $emp = $this->empresa();
        $rows = DB::table('recepciones as r')
            ->leftJoin('compras as c', 'c.id_compra', '=', 'r.id_compra')
            ->leftJoin('proveedores as pv', 'pv.proveedor_id', '=', 'c.id_proveedor')
            ->leftJoin('almacenes as a', function ($j) { $j->on('a.codigo', '=', 'r.almacen')->on('a.id_empresa', '=', 'r.id_empresa'); })
            ->leftJoin('usuarios as u', 'u.usuario_id', '=', 'r.id_usuario')
            ->where('r.id_empresa', $emp)
            ->orderByDesc('r.id_recepcion')
            ->select('r.id_recepcion', 'r.id_compra', 'r.fecha', 'r.observacion',
                DB::raw("TRIM(BOTH '-' FROM CONCAT(COALESCE(c.serie,''),'-',COALESCE(c.numero,''))) as compra_doc"),
                DB::raw("TRIM(COALESCE(pv.razon_social, pv.nombre_comercial, '-')) as proveedor"),
                DB::raw('COALESCE(a.nombre, r.almacen) as almacen_nombre'),
                DB::raw("TRIM(CONCAT(COALESCE(u.nombres,''),' ',COALESCE(u.apellidos,''))) as usuario"),
                DB::raw('(SELECT COUNT(*) FROM recepcion_detalle d WHERE d.id_recepcion = r.id_recepcion) as items'))
            ->get();

        return response()->json($rows);
    }

    /** Detalle (líneas) de un documento de recepción. */
    public function detalleRecepcion(Request $request): JsonResponse
    {
        $request->validate(['id' => 'required|integer']);
        $rows = DB::table('recepcion_detalle as d')
            ->join('productos as p', 'p.id_producto', '=', 'd.id_producto')
            ->where('d.id_recepcion', $request->id)
            ->select('p.codigo', 'p.descripcion as producto', 'p.medida as unidad', 'd.cantidad')
            ->get();

        return response()->json($rows);
    }

    /** Documentos de recepción (cabecera + detalle) de una compra. */
    public function historial(Request $request): JsonResponse
    {
        $request->validate(['id' => 'required|integer']);
        $recs = DB::table('recepciones')
            ->where('id_empresa', $this->empresa())->where('id_compra', $request->id)
            ->orderBy('id_recepcion')->get();

        foreach ($recs as $r) {
            $r->detalles = DB::table('recepcion_detalle as rd')
                ->join('productos as p', 'p.id_producto', '=', 'rd.id_producto')
                ->where('rd.id_recepcion', $r->id_recepcion)
                ->select('p.descripcion as producto', 'rd.cantidad')->get();
        }

        return response()->json($recs);
    }

    /** Elimina una compra (solo si aún no se recepcionó nada). */
    public function eliminar(Request $request): JsonResponse
    {
        $request->validate(['id' => 'required|integer']);
        $compra = Compra::where('id_empresa', $this->empresa())->where('id_compra', $request->id)->firstOrFail();

        if ((int) $compra->recepcionado !== 0) {
            return response()->json(['res' => false, 'msg' => 'No se puede eliminar: la compra ya tiene recepciones.'], 409);
        }

        DB::table('productos_compras')->where('id_compra', $compra->id_compra)->delete();
        $compra->delete();
        return response()->json(['res' => true]);
    }
}
