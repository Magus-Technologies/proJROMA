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

    /** Cabeceras de préstamos, con filtros ?desde, ?hasta, ?almacen. */
    public function listar(Request $request): JsonResponse
    {
        $emp = $this->empresa();
        $prestamos = DB::table('prestamos as pr')
            ->leftJoin('almacenes as a', function ($j) { $j->on('a.codigo', '=', 'pr.almacen')->on('a.id_empresa', '=', 'pr.id_empresa'); })
            ->leftJoin('usuarios as u', 'u.usuario_id', '=', 'pr.id_usuario')
            ->where('pr.id_empresa', $emp)
            ->when($request->filled('desde'),   fn ($q) => $q->whereDate('pr.fecha', '>=', $request->desde))
            ->when($request->filled('hasta'),   fn ($q) => $q->whereDate('pr.fecha', '<=', $request->hasta))
            ->when($request->filled('almacen'), fn ($q) => $q->where('pr.almacen', $request->almacen))
            ->orderByDesc('pr.id_prestamo')
            ->select('pr.*',
                DB::raw('COALESCE(a.nombre, pr.almacen) as almacen_nombre'),
                DB::raw("TRIM(CONCAT(COALESCE(u.nombres,''),' ',COALESCE(u.apellidos,''))) as usuario"),
                DB::raw('(SELECT COUNT(*) FROM prestamo_detalle d WHERE d.id_prestamo = pr.id_prestamo) as items'))
            ->get();

        return response()->json($prestamos);
    }

    /** Detalle (líneas) de un préstamo. */
    public function detalle(Request $request): JsonResponse
    {
        $request->validate(['id' => 'required|integer']);
        $rows = DB::table('prestamo_detalle as d')
            ->join('productos as p', 'p.id_producto', '=', 'd.id_producto')
            ->where('d.id_prestamo', $request->id)
            ->select('p.codigo', 'p.descripcion as producto', 'p.medida as unidad', 'd.cantidad', 'd.observacion')
            ->get();

        return response()->json($rows);
    }

    /** Aplica un movimiento de stock sobre la fila del producto en su almacén. */
    private function mover(int $emp, string $tipoMov, int $idProducto, int $cant, string $motivo, string $obs, int $uid, string $almacen): void
    {
        $p = Producto::where('id_empresa', $emp)->where('id_producto', $idProducto)->lockForUpdate()->firstOrFail();
        $ant = (int) $p->cantidad;
        if ($tipoMov === 'S' && $cant > $ant) {
            throw new \RuntimeException("Stock insuficiente de \"{$p->descripcion}\" (disponible: {$ant}).");
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
            'tipo'                   => 'required|in:P,R',   // P=presto, R=me prestan
            'tercero'                => 'required|string|max:150',
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
            $prestamo = Prestamo::create([
                'id_empresa' => $emp, 'tipo' => $data['tipo'], 'tercero' => $data['tercero'],
                'almacen' => $data['almacen'], 'estado' => 'P',
                'observacion' => $data['observacion'] ?? null, 'id_usuario' => $uid, 'fecha' => now(),
            ]);

            foreach ($data['detalles'] as $linea) {
                $cant = (int) $linea['cantidad'];
                if ($data['tipo'] === 'P') {
                    $this->mover($emp, 'S', $linea['id_producto'], $cant, 'Préstamo entregado', "Préstamo a {$data['tercero']}", $uid, $data['almacen']);
                } else {
                    $this->mover($emp, 'I', $linea['id_producto'], $cant, 'Préstamo recibido', "Préstamo de {$data['tercero']}", $uid, $data['almacen']);
                }
                DB::table('prestamo_detalle')->insert([
                    'id_prestamo' => $prestamo->id_prestamo, 'id_producto' => $linea['id_producto'],
                    'cantidad' => $cant, 'created_at' => now(), 'updated_at' => now(),
                ]);
            }

            DB::commit();
            return response()->json(['res' => true, 'id' => $prestamo->id_prestamo]);
        } catch (\RuntimeException $e) {
            DB::rollBack();
            return response()->json(['res' => false, 'msg' => $e->getMessage()], 409);
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Error préstamo: ' . $e->getMessage());
            return response()->json(['res' => false, 'msg' => 'Error al registrar el préstamo.'], 500);
        }
    }

    /** Líneas del préstamo con lo prestado, lo ya devuelto y lo pendiente (para el modal de devolución). */
    public function lineasDevolucion(Request $request): JsonResponse
    {
        $request->validate(['id' => 'required|integer']);
        $emp = $this->empresa();
        Prestamo::where('id_empresa', $emp)->where('id_prestamo', $request->id)->firstOrFail();

        $rows = DB::table('prestamo_detalle as d')
            ->join('productos as p', 'p.id_producto', '=', 'd.id_producto')
            ->where('d.id_prestamo', $request->id)
            ->select('d.id_producto', 'p.codigo', 'p.descripcion as producto', 'p.medida as unidad', 'd.cantidad as prestado')
            ->get();

        foreach ($rows as $r) {
            $devuelto = (int) DB::table('prestamo_devoluciones')
                ->where('id_prestamo', $request->id)->where('id_producto', $r->id_producto)->sum('cantidad');
            $r->devuelto  = $devuelto;
            $r->pendiente = (int) $r->prestado - $devuelto;
        }

        return response()->json($rows);
    }

    /** Devolución parcial o total: recibe líneas {id_producto, cantidad}. */
    public function devolver(Request $request): JsonResponse
    {
        $data = $request->validate([
            'id'                     => 'required|integer',
            'detalles'               => 'required|array|min:1',
            'detalles.*.id_producto' => 'required|integer',
            'detalles.*.cantidad'    => 'required|integer|min:1',
        ]);

        $emp = $this->empresa();
        $uid = (int) (auth()->user()->usuario_id ?? 0);

        DB::beginTransaction();
        try {
            $pr = Prestamo::where('id_empresa', $emp)->where('id_prestamo', $data['id'])->lockForUpdate()->firstOrFail();
            if ($pr->estado === 'D') {
                DB::rollBack();
                return response()->json(['res' => false, 'msg' => 'Este préstamo ya está totalmente devuelto.'], 409);
            }

            foreach ($data['detalles'] as $linea) {
                $cant = (int) $linea['cantidad'];

                // Validar que no se devuelva más de lo pendiente
                $prestado = (int) DB::table('prestamo_detalle')->where('id_prestamo', $pr->id_prestamo)->where('id_producto', $linea['id_producto'])->sum('cantidad');
                $yaDev    = (int) DB::table('prestamo_devoluciones')->where('id_prestamo', $pr->id_prestamo)->where('id_producto', $linea['id_producto'])->sum('cantidad');
                $pendiente = $prestado - $yaDev;
                if ($cant > $pendiente) {
                    DB::rollBack();
                    return response()->json(['res' => false, 'msg' => "No puedes devolver más de lo pendiente ({$pendiente})."], 409);
                }

                // Movimiento de stock inverso
                if ($pr->tipo === 'P') {
                    $this->mover($emp, 'I', $linea['id_producto'], $cant, 'Préstamo recibido', "Devolución de {$pr->tercero}", $uid, $pr->almacen);
                } else {
                    $this->mover($emp, 'S', $linea['id_producto'], $cant, 'Préstamo entregado', "Devolución a {$pr->tercero}", $uid, $pr->almacen);
                }

                DB::table('prestamo_devoluciones')->insert([
                    'id_prestamo' => $pr->id_prestamo, 'id_producto' => $linea['id_producto'],
                    'cantidad' => $cant, 'fecha' => now(), 'id_usuario' => $uid,
                ]);
            }

            // Recalcular estado: P (pendiente) · X (parcial) · D (devuelto)
            $totalPrestado = (int) DB::table('prestamo_detalle')->where('id_prestamo', $pr->id_prestamo)->sum('cantidad');
            $totalDevuelto = (int) DB::table('prestamo_devoluciones')->where('id_prestamo', $pr->id_prestamo)->sum('cantidad');
            $estado = $totalDevuelto >= $totalPrestado ? 'D' : ($totalDevuelto > 0 ? 'X' : 'P');
            $pr->update(['estado' => $estado, 'fecha_devolucion' => $estado === 'D' ? now() : null]);

            DB::commit();
            return response()->json(['res' => true, 'estado' => $estado]);
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
