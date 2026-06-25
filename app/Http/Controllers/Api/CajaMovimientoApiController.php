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
