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

    /** Compras pendientes de recepcionar. */
    public function pendientes(): JsonResponse
    {
        $rows = DB::table('compras as c')
            ->leftJoin('proveedores as p', 'p.proveedor_id', '=', 'c.id_proveedor')
            ->where('c.id_empresa', $this->empresa())
            ->where('c.recepcionado', 0)
            ->orderByDesc('c.id_compra')
            ->select(
                'c.id_compra',
                DB::raw("TRIM(BOTH '-' FROM CONCAT(COALESCE(c.serie,''),'-',COALESCE(c.numero,''))) as documento"),
                'c.fecha_emision',
                'c.total',
                DB::raw("TRIM(COALESCE(p.razon_social, p.nombre_comercial, '-')) as proveedor"),
                DB::raw('(SELECT COUNT(*) FROM productos_compras pc WHERE pc.id_compra = c.id_compra) as items')
            )
            ->get();

        return response()->json($rows);
    }

    /** Recepciona una compra: ingresa cada ítem al almacén elegido y marca la compra. */
    public function recepcionar(Request $request): JsonResponse
    {
        $data = $request->validate([
            'id_compra' => 'required|integer',
            'almacen'   => 'required',
        ]);

        $emp = $this->empresa();

        DB::beginTransaction();
        try {
            $compra = Compra::where('id_empresa', $emp)->where('id_compra', $data['id_compra'])->lockForUpdate()->firstOrFail();
            if ((int) $compra->recepcionado === 1) {
                DB::rollBack();
                return response()->json(['res' => false, 'msg' => 'Esta compra ya fue recepcionada.'], 409);
            }

            $items = DB::table('productos_compras')->where('id_compra', $compra->id_compra)->get();
            if ($items->isEmpty()) {
                DB::rollBack();
                return response()->json(['res' => false, 'msg' => 'La compra no tiene productos.'], 409);
            }

            $motCompra = MotivoMovimiento::where('id_empresa', $emp)->where('tipo', 'I')->where('nombre', 'Compra')->value('id_motivo');
            $uid = (int) (auth()->user()->usuario_id ?? 0);
            $procesados = 0;

            foreach ($items as $it) {
                $cant = (int) $it->cantidad;
                if ($cant <= 0) continue;

                $source = Producto::where('id_empresa', $emp)->where('id_producto', $it->id_producto)->first();
                if (! $source) continue;

                // Producto en el almacén destino: por código; si no existe, se clona
                $dest = null;
                if (! empty($source->codigo)) {
                    $dest = Producto::where('id_empresa', $emp)->where('almacen', $data['almacen'])
                        ->where('codigo', $source->codigo)->lockForUpdate()->first();
                }
                if ($dest) {
                    $ant = (int) $dest->cantidad;
                    $nuevo = $ant + $cant;
                    $dest->update(['cantidad' => $nuevo, 'costo' => $it->costo ?: $dest->costo]);
                } else {
                    $dest = $source->replicate();
                    $dest->almacen  = $data['almacen'];
                    $dest->cantidad = $cant;
                    if ($it->costo) $dest->costo = $it->costo;
                    $dest->save();
                    $ant = 0; $nuevo = $cant;
                }

                InventarioMovimiento::create([
                    'id_empresa' => $emp, 'almacen' => $data['almacen'], 'id_producto' => $dest->id_producto,
                    'tipo' => 'I', 'id_motivo' => $motCompra, 'cantidad' => $cant,
                    'stock_anterior' => $ant, 'stock_nuevo' => $nuevo, 'costo' => $it->costo ?: null,
                    'id_proveedor' => $compra->id_proveedor,
                    'observacion' => "Recepción compra #{$compra->id_compra}",
                    'id_usuario' => $uid, 'fecha' => now(),
                ]);
                $procesados++;
            }

            $compra->update(['recepcionado' => 1]);
            DB::commit();
            return response()->json(['res' => true, 'items' => $procesados]);
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Error recepción: ' . $e->getMessage());
            return response()->json(['res' => false, 'msg' => 'Error al recepcionar la compra.'], 500);
        }
    }
}
