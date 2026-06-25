<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CajaService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class RendicionApiController extends Controller
{
    private function empresa(): int  { return (int) session('id_empresa'); }
    private function usuarioId(): int { return (int) (auth()->user()->usuario_id ?? 0); }

    /**
     * Obtener la rendición activa de una caja chica (o crearla).
     */
    public function activa(int $idCaja): JsonResponse
    {
        $rend = DB::table('caja_chica_rendiciones')
            ->where('id_caja', $idCaja)
            ->whereIn('estado', ['ABIERTA', 'PENDIENTE_APROBACION'])
            ->first();

        if (!$rend) {
            $caja = DB::table('cajas')->where('id', $idCaja)->first();
            if (!$caja) return response()->json(['res' => false, 'msg' => 'Caja no encontrada.'], 404);

            $id = DB::table('caja_chica_rendiciones')->insertGetId([
                'id_caja'       => $idCaja,
                'periodo_inicio'=> now()->toDateString(),
                'monto_fondo'   => $caja->monto_fondo_fijo ?? 0,
                'total_gastado' => 0,
                'estado'        => 'ABIERTA',
                'id_usuario_solicita' => $this->usuarioId(),
            ]);
            $rend = DB::table('caja_chica_rendiciones')->where('id', $id)->first();
        }

        // Calcular total gastado desde movimientos EGRESO (confirmados) desde periodo_inicio
        $gastado = DB::table('caja_movimientos')
            ->where('id_caja', $idCaja)
            ->where('tipo', 'EGRESO')
            ->where('estado', 'CONFIRMADO')
            ->whereDate('fecha', '>=', $rend->periodo_inicio)
            ->sum('monto');

        return response()->json([
            'id'            => $rend->id,
            'periodo_inicio'=> $rend->periodo_inicio,
            'monto_fondo'   => (float) $rend->monto_fondo,
            'total_gastado' => (float) $gastado,
            'estado'        => $rend->estado,
        ]);
    }

    /**
     * Solicitar aprobación de rendición.
     */
    public function solicitarAprobacion(Request $r): JsonResponse
    {
        $r->validate(['id' => 'required|integer']);

        $rend = DB::table('caja_chica_rendiciones')->where('id', $r->id)->first();
        if (!$rend) return response()->json(['res' => false, 'msg' => 'No encontrada.'], 404);

        $gastado = DB::table('caja_movimientos')
            ->where('id_caja', $rend->id_caja)
            ->where('tipo', 'EGRESO')
            ->where('estado', 'CONFIRMADO')
            ->whereDate('fecha', '>=', $rend->periodo_inicio)
            ->sum('monto');

        DB::table('caja_chica_rendiciones')->where('id', $r->id)->update([
            'total_gastado'    => $gastado,
            'periodo_fin'      => now()->toDateString(),
            'estado'           => 'PENDIENTE_APROBACION',
        ]);

        return response()->json(['res' => true, 'total_gastado' => $gastado]);
    }

    /**
     * Aprobar rendición: genera EGRESO en caja padre + INGRESO en caja chica.
     */
    public function aprobar(Request $r, CajaService $svc): JsonResponse
    {
        $r->validate(['id' => 'required|integer']);

        return DB::transaction(function () use ($r, $svc) {
            $rend = DB::table('caja_chica_rendiciones')
                ->where('id', $r->id)
                ->where('estado', 'PENDIENTE_APROBACION')
                ->first();

            if (!$rend) return response()->json(['res' => false, 'msg' => 'No pendiente de aprobación.'], 400);

            $cajaChica = DB::table('cajas')->where('id', $rend->id_caja)->first();
            if (!$cajaChica || !$cajaChica->id_caja_padre) {
                return response()->json(['res' => false, 'msg' => 'La caja chica no tiene caja padre.'], 400);
            }

            $monto = (float) $rend->monto_fondo;

            // Egreso en Caja Principal (reposición)
            $idMov = $svc->registrarMovimiento([
                'id_caja'     => $cajaChica->id_caja_padre,
                'tipo'        => 'EGRESO',
                'categoria'   => 'REPOSICION',
                'descripcion' => "Reposición fondo caja chica: {$cajaChica->nombre}",
                'monto'       => $monto,
                'id_usuario'  => $this->usuarioId(),
            ]);

            // Ingreso en Caja Chica (reintegra fondo)
            $svc->registrarMovimiento([
                'id_caja'     => $rend->id_caja,
                'tipo'        => 'INGRESO',
                'categoria'   => 'REPOSICION',
                'descripcion' => "Reintegro fondo aprobado",
                'monto'       => $monto,
                'id_usuario'  => $this->usuarioId(),
            ]);

            DB::table('caja_chica_rendiciones')->where('id', $r->id)->update([
                'estado'                  => 'APROBADA',
                'id_usuario_aprueba'      => $this->usuarioId(),
                'id_movimiento_reposicion'=> $idMov,
            ]);

            return response()->json(['res' => true]);
        });
    }

    /**
     * Historial de rendiciones de una caja chica.
     */
    public function historial(int $idCaja): mixed
    {
        return \Yajra\DataTables\Facades\DataTables::of(
            DB::table('caja_chica_rendiciones')
                ->where('id_caja', $idCaja)
                ->orderByDesc('id')
        )->make(true);
    }
}
