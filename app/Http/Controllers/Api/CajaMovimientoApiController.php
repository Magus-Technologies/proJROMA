<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CajaService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class CajaMovimientoApiController extends Controller
{
    private function empresa(): int  { return (int) session('id_empresa'); }
    private function sucursal(): int { return (int) session('sucursal'); }
    private function usuarioId(): int { return (int) (auth()->user()->usuario_id ?? 0); }

    /**
     * Listar movimientos de una caja.
     */
    public function listar(Request $r, int $idCaja): mixed
    {
        $q = DB::table('caja_movimientos')
            ->where('id_caja', $idCaja);

        if ($r->filled('instrumento')) {
            $q->where('instrumento_tipo', $r->instrumento);
        }

        if ($r->filled('categoria')) {
            $q->where('categoria', $r->categoria);
        }

        if ($r->filled('fecha_desde')) {
            $q->whereDate('fecha', '>=', $r->fecha_desde);
        }
        if ($r->filled('fecha_hasta')) {
            $q->whereDate('fecha', '<=', $r->fecha_hasta);
        }

        $q->leftJoin('usuarios as u', 'u.usuario_id', '=', 'caja_movimientos.id_usuario')
          ->select('caja_movimientos.*', DB::raw('COALESCE(CONCAT(u.nombres, " ", u.apellidos), "-") as usuario'));

        return DataTables::of($q->orderByDesc('caja_movimientos.id'))->make(true);
    }

    /**
     * Registrar movimiento manual (INGRESO/EGRESO).
     */
    public function guardar(Request $r, CajaService $svc): JsonResponse
    {
        $r->validate([
            'id_caja'   => 'required|integer',
            'tipo'      => 'required|in:INGRESO,EGRESO',
            'monto'     => 'required|numeric|min:0.01',
            'categoria' => 'nullable|string|max:30',
        ]);

        try {
            $id = $svc->registrarMovimiento([
                'id_caja'          => $r->id_caja,
                'fecha'            => $r->fecha ?? now()->toDateString(),
                'tipo'             => $r->tipo,
                'categoria'        => $r->categoria ?? 'MANUAL',
                'descripcion'      => $r->descripcion,
                'monto'            => $r->monto,
                'instrumento_tipo' => $r->instrumento_tipo ?? null,
                'instrumento_id'   => $r->instrumento_id ?? null,
                'id_usuario'       => $this->usuarioId(),
            ]);
            return response()->json(['res' => true, 'id' => $id]);
        } catch (\RuntimeException $e) {
            return response()->json(['res' => false, 'msg' => $e->getMessage()], 400);
        }
    }

    /**
     * Editar movimiento (descripción, fecha, monto, instrumento).
     * Si cambia el monto, recalcula saldos posteriores.
     */
    public function editar(Request $r, CajaService $svc): JsonResponse
    {
        $r->validate([
            'id'       => 'required|integer',
            'monto'    => 'nullable|numeric|min:0.01',
            'fecha'    => 'nullable|date',
            'descripcion' => 'nullable|string|max:245',
        ]);

        try {
            $mov = DB::table('caja_movimientos')->where('id', $r->id)->first();
            if (!$mov || $mov->estado === 'ANULADO') {
                return response()->json(['res' => false, 'msg' => 'Movimiento no encontrado o anulado.'], 400);
            }

            $updates = [];
            if ($r->filled('descripcion')) $updates['descripcion'] = $r->descripcion;
            if ($r->filled('fecha')) $updates['fecha'] = $r->fecha;
            if ($r->filled('instrumento_tipo')) $updates['instrumento_tipo'] = $r->instrumento_tipo;
            if ($r->filled('instrumento_id')) $updates['instrumento_id'] = $r->instrumento_id;

            // Si cambia el monto, recalcular saldos
            if ($r->filled('monto') && abs((float)$r->monto - (float)$mov->monto) > 0.001) {
                DB::transaction(function () use ($r, $mov, $svc) {
                    $nuevoMonto = (float) $r->monto;
                    $montoOriginal = (float) $mov->monto;
                    $diferencia = $nuevoMonto - $montoOriginal;

                    // Actualizar el movimiento
                    DB::table('caja_movimientos')->where('id', $mov->id)->update([
                        'monto' => $nuevoMonto,
                        'saldo_posterior' => DB::raw('saldo_posterior + ' . $diferencia),
                    ]);

                    // Recalcular saldo_actual de la caja
                    $caja = DB::table('cajas')->where('id', $mov->id_caja)->lockForUpdate()->first();
                    DB::table('cajas')->where('id', $mov->id_caja)->update([
                        'saldo_actual' => $caja->saldo_actual + $diferencia,
                    ]);

                    // Recalcular saldos de movimientos posteriores
                    $movsPosteriores = DB::table('caja_movimientos')
                        ->where('id_caja', $mov->id_caja)
                        ->where('id', '>', $mov->id)
                        ->where('estado', 'CONFIRMADO')
                        ->orderBy('id')
                        ->get();

                    foreach ($movsPosteriores as $mp) {
                        $nuevoSaldoAnterior = (float) DB::table('caja_movimientos')
                            ->where('id', $mp->id - 1)
                            ->value('saldo_posterior');

                        $nuevoSaldoPosterior = $mp->tipo === 'INGRESO'
                            ? $nuevoSaldoAnterior + (float) $mp->monto
                            : $nuevoSaldoAnterior - (float) $mp->monto;

                        DB::table('caja_movimientos')->where('id', $mp->id)->update([
                            'saldo_anterior' => $nuevoSaldoAnterior,
                            'saldo_posterior' => $nuevoSaldoPosterior,
                        ]);
                    }
                });

                return response()->json(['res' => true, 'msg' => 'Movimiento actualizado con recalculo de saldos.']);
            }

            if (!empty($updates)) {
                DB::table('caja_movimientos')->where('id', $r->id)->update($updates);
            }

            return response()->json(['res' => true, 'msg' => 'Movimiento actualizado.']);
        } catch (\Exception $e) {
            return response()->json(['res' => false, 'msg' => $e->getMessage()], 400);
        }
    }

    /**
     * Anular movimiento.
     */
    public function anular(Request $r, CajaService $svc): JsonResponse
    {
        $r->validate(['id' => 'required|integer']);
        try {
            $svc->anularMovimiento($r->id);
            return response()->json(['res' => true]);
        } catch (\RuntimeException $e) {
            return response()->json(['res' => false, 'msg' => $e->getMessage()], 400);
        }
    }
}
