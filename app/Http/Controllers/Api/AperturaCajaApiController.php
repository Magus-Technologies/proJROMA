<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class AperturaCajaApiController extends Controller
{
    private function empresa(): int  { return (int) session('id_empresa'); }
    private function sucursal(): int { return (int) session('sucursal'); }
    private function usuarioId(): int { return (int) (auth()->user()->usuario_id ?? 0); }

    public function cajasDisponibles(): JsonResponse
    {
        $cajas = DB::table('cajas')
            ->where('id_empresa', $this->empresa())
            ->where('sucursal', $this->sucursal())
            ->where('estado', 'ACTIVA')
            ->whereNotNull('id_caja_padre')
            ->get(['id', 'nombre', 'saldo_actual']);

        return response()->json(['cajas' => $cajas]);
    }

    public function guardar(Request $r): JsonResponse
    {
        $r->validate([
            'id_caja' => 'required|integer',
            'fecha' => 'required|date',
            'detalles' => 'required|array|min:1',
            'detalles.*.denominacion' => 'required|numeric|min:0',
            'detalles.*.tipo' => 'required|in:BILLETE,MONEDA',
            'detalles.*.cantidad' => 'required|integer|min:0',
            'observaciones' => 'nullable|string|max:500',
        ]);

        $caja = DB::table('cajas')
            ->where('id', $r->id_caja)
            ->where('id_empresa', $this->empresa())
            ->where('estado', 'ACTIVA')
            ->first();

        if (!$caja) {
            return response()->json(['res' => false, 'msg' => 'Caja no encontrada.'], 404);
        }

        // Solo el responsable asignado puede aperturar su propia caja
        if ((int) $caja->id_usuario_responsable !== $this->usuarioId()) {
            return response()->json(['res' => false, 'msg' => 'Solo el responsable puede aperturar esta caja.'], 403);
        }

        // Evitar una segunda apertura mientras haya una sin cerrar
        $yaAbierta = DB::table('caja_aperturas')
            ->where('id_caja', $r->id_caja)
            ->where('estado', 'ABIERTA')
            ->exists();

        if ($yaAbierta) {
            return response()->json(['res' => false, 'msg' => 'Esta caja ya tiene una apertura activa. Ciérrala antes de volver a aperturar.'], 409);
        }

        $montoTotal = 0;
        foreach ($r->detalles as $d) {
            $montoTotal += ((float) $d['denominacion']) * ((int) $d['cantidad']);
        }

        return DB::transaction(function () use ($r, $montoTotal) {
            $id = DB::table('caja_aperturas')->insertGetId([
                'id_caja' => $r->id_caja,
                'fecha' => $r->fecha,
                'monto_total' => $montoTotal,
                'estado' => 'ABIERTA',
                'id_usuario_apertura' => $this->usuarioId(),
                'observaciones' => $r->observaciones ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $detalles = [];
            foreach ($r->detalles as $d) {
                if ((int) $d['cantidad'] > 0) {
                    $detalles[] = [
                        'id_apertura' => $id,
                        'denominacion' => $d['denominacion'],
                        'tipo' => $d['tipo'],
                        'cantidad' => $d['cantidad'],
                        'subtotal' => ((float) $d['denominacion']) * ((int) $d['cantidad']),
                    ];
                }
            }

            if (!empty($detalles)) {
                DB::table('caja_apertura_detalles')->insert($detalles);
            }

            return response()->json(['res' => true, 'id' => $id, 'monto_total' => $montoTotal]);
        });
    }

    public function historial(int $idCaja): JsonResponse
    {
        $aperturas = DB::table('caja_aperturas as ap')
            ->leftJoin('usuarios as u', 'u.usuario_id', '=', 'ap.id_usuario_apertura')
            ->where('ap.id_caja', $idCaja)
            ->select(
                'ap.*',
                DB::raw("COALESCE(NULLIF(CONCAT_WS(' ', u.nombres, u.apellidos), ''), '-') as usuario_apertura")
            )
            ->orderByDesc('ap.id')
            ->get();

        return response()->json(['data' => $aperturas]);
    }

    public function ultima(int $idCaja): JsonResponse
    {
        $apertura = DB::table('caja_aperturas')
            ->where('id_caja', $idCaja)
            ->orderByDesc('id')
            ->first();

        if (!$apertura) {
            return response()->json(['res' => false, 'msg' => 'Sin aperturas previas.'], 404);
        }

        $detalles = DB::table('caja_apertura_detalles')
            ->where('id_apertura', $apertura->id)
            ->get();

        return response()->json([
            'res' => true,
            'apertura' => $apertura,
            'detalles' => $detalles,
        ]);
    }
}
