<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use App\Models\Prestamo;
use App\Models\MotivoMovimiento;
use App\Models\InventarioMovimiento;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class PrestamoApiController extends Controller
{
    private function empresa(): int { return (int) session('id_empresa'); }

    public function listar(): JsonResponse
    {
        $rows = DB::table('prestamos as pr')
            ->leftJoin('productos as p', 'p.id_producto', '=', 'pr.id_producto')
            ->leftJoin('almacenes as a', function ($j) {
                $j->on('a.codigo', '=', 'pr.almacen')->on('a.id_empresa', '=', 'pr.id_empresa');
            })
            ->where('pr.id_empresa', $this->empresa())
            ->orderByDesc('pr.id_prestamo')
            ->select('pr.*', 'p.descripcion as producto', DB::raw('COALESCE(a.nombre, pr.almacen) as almacen_nombre'))
            ->get();

        return response()->json($rows);
    }

    /** Aplica un movimiento de stock sobre la fila del producto en su almacén. */
    private function mover(int $emp, string $tipoMov, int $idProducto, int $cant, string $motivo, string $obs, int $uid, string $almacen): void
    {
        $p = Producto::where('id_empresa', $emp)->where('id_producto', $idProducto)->lockForUpdate()->firstOrFail();
        $ant = (int) $p->cantidad;
        if ($tipoMov === 'S' && $cant > $ant) {
            throw new \RuntimeException("Stock insuficiente. Disponible: {$ant}.");
        }
        $nuevo = $tipoMov === 'I' ? $ant + $cant : $ant - $cant;
        $p->update(['cantidad' => $nuevo]);

        $idMotivo = MotivoMovimiento::where('id_empresa', $emp)->where('tipo', $tipoMov)->where('nombre', $motivo)->value('id_motivo');
        InventarioMovimiento::create([
            'id_empresa' => $emp, 'almacen' => $almacen, 'id_producto' => $idProducto, 'tipo' => $tipoMov,
            'id_motivo' => $idMotivo, 'cantidad' => $cant, 'stock_anterior' => $ant, 'stock_nuevo' => $nuevo,
            'costo' => $p->costo, 'observacion' => $obs, 'id_usuario' => $uid, 'fecha' => now(),
        ]);
    }

    public function guardar(Request $request): JsonResponse
    {
        $data = $request->validate([
            'tipo'        => 'required|in:P,R',     // P=presto, R=me prestan
            'tercero'     => 'required|string|max:150',
            'almacen'     => 'required',
            'id_producto' => 'required|integer',
            'cantidad'    => 'required|integer|min:1',
            'observacion' => 'nullable|string|max:200',
        ]);

        $emp = $this->empresa();
        $uid = (int) (auth()->user()->usuario_id ?? 0);
        $cant = (int) $data['cantidad'];

        DB::beginTransaction();
        try {
            if ($data['tipo'] === 'P') {
                $this->mover($emp, 'S', $data['id_producto'], $cant, 'Préstamo entregado', "Préstamo a {$data['tercero']}. " . ($data['observacion'] ?? ''), $uid, $data['almacen']);
            } else {
                $this->mover($emp, 'I', $data['id_producto'], $cant, 'Préstamo recibido', "Préstamo de {$data['tercero']}. " . ($data['observacion'] ?? ''), $uid, $data['almacen']);
            }

            Prestamo::create([
                'id_empresa' => $emp, 'tipo' => $data['tipo'], 'tercero' => $data['tercero'],
                'id_producto' => $data['id_producto'], 'almacen' => $data['almacen'], 'cantidad' => $cant,
                'estado' => 'P', 'observacion' => $data['observacion'] ?? null, 'id_usuario' => $uid, 'fecha' => now(),
            ]);

            DB::commit();
            return response()->json(['res' => true]);
        } catch (\RuntimeException $e) {
            DB::rollBack();
            return response()->json(['res' => false, 'msg' => $e->getMessage()], 409);
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Error préstamo: ' . $e->getMessage());
            return response()->json(['res' => false, 'msg' => 'Error al registrar el préstamo.'], 500);
        }
    }

    public function devolver(Request $request): JsonResponse
    {
        $request->validate(['id' => 'required|integer']);
        $emp = $this->empresa();
        $uid = (int) (auth()->user()->usuario_id ?? 0);

        DB::beginTransaction();
        try {
            $pr = Prestamo::where('id_empresa', $emp)->where('id_prestamo', $request->id)->lockForUpdate()->firstOrFail();
            if ($pr->estado === 'D') {
                DB::rollBack();
                return response()->json(['res' => false, 'msg' => 'Este préstamo ya fue devuelto.'], 409);
            }

            if ($pr->tipo === 'P') {
                // Lo que presté me lo devuelven → entra
                $this->mover($emp, 'I', $pr->id_producto, (int) $pr->cantidad, 'Préstamo recibido', "Devolución de {$pr->tercero}.", $uid, $pr->almacen);
            } else {
                // Lo que me prestaron lo devuelvo → sale
                $this->mover($emp, 'S', $pr->id_producto, (int) $pr->cantidad, 'Préstamo entregado', "Devolución a {$pr->tercero}.", $uid, $pr->almacen);
            }

            $pr->update(['estado' => 'D', 'fecha_devolucion' => now()]);
            DB::commit();
            return response()->json(['res' => true]);
        } catch (\RuntimeException $e) {
            DB::rollBack();
            return response()->json(['res' => false, 'msg' => $e->getMessage()], 409);
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Error devolución préstamo: ' . $e->getMessage());
            return response()->json(['res' => false, 'msg' => 'Error al registrar la devolución.'], 500);
        }
    }
}
