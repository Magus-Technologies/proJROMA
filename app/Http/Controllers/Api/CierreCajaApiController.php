<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CajaService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class CierreCajaApiController extends Controller
{
    private function empresa(): int  { return (int) session('id_empresa'); }
    private function sucursal(): int { return (int) session('sucursal'); }
    private function usuarioId(): int { return (int) (auth()->user()->usuario_id ?? 0); }

    public function balanceSistema(int $idCaja): JsonResponse
    {
        $caja = DB::table('cajas')
            ->where('id', $idCaja)
            ->where('id_empresa', $this->empresa())
            ->first();

        if (!$caja) {
            return response()->json(['res' => false, 'msg' => 'Caja no encontrada.'], 404);
        }

        // Obtener desglose de instrumentos sumando ingresos y restando egresos
        $movs = DB::table('caja_movimientos')
            ->where('id_caja', $idCaja)
            ->where('estado', 'CONFIRMADO')
            ->get(['tipo', 'monto', 'instrumento_tipo', 'instrumento_id']);

        $desglose = [];
        // Por defecto, inicializar EFECTIVO con 0
        $desglose['EFECTIVO_'] = [
            'instrumento_tipo' => 'EFECTIVO',
            'instrumento_id' => null,
            'label' => 'Efectivo',
            'monto' => 0.0
        ];

        // Obtener catálogos de instrumentos para poner labels bonitas
        $cuentas = DB::table('cuentas_bancarias')->get()->keyBy('id_cuenta');
        $tarjetas = DB::table('tarjetas')->get()->keyBy('id_tarjeta');
        $billeteras = DB::table('billeteras_digitales')->get()->keyBy('id_billetera');

        foreach ($movs as $m) {
            $tipo = $m->instrumento_tipo ?: 'EFECTIVO';
            $id = $m->instrumento_id;
            $key = $tipo . '_' . $id;

            if (!isset($desglose[$key])) {
                $label = $tipo;
                if ($tipo === 'EFECTIVO') $label = 'Efectivo';
                elseif ($tipo === 'CUENTA_BANCARIA' && isset($cuentas[$id])) {
                    $c = $cuentas[$id];
                    $label = "Cta: {$c->banco} - " . ($c->tipo_cuenta ?? '') . " " . substr($c->numero_cuenta, -4);
                } elseif ($tipo === 'TARJETA' && isset($tarjetas[$id])) {
                    $t = $tarjetas[$id];
                    $label = "Tarj: {$t->banco} - {$t->tipo} *{$t->ultimos_4}";
                } elseif ($tipo === 'BILLETERA_DIGITAL' && isset($billeteras[$id])) {
                    $b = $billeteras[$id];
                    $label = "Bill: {$b->tipo} - {$b->titular}";
                }

                $desglose[$key] = [
                    'instrumento_tipo' => $tipo,
                    'instrumento_id' => $id,
                    'label' => $label,
                    'monto' => 0.0
                ];
            }

            $monto = (float) $m->monto;
            if ($m->tipo === 'INGRESO') {
                $desglose[$key]['monto'] += $monto;
            } else {
                $desglose[$key]['monto'] -= $monto;
            }
        }

        return response()->json([
            'res' => true,
            'saldo_sistema' => (float) $caja->saldo_actual,
            'desglose' => array_values($desglose)
        ]);
    }

    public function cerrar(Request $r, CajaService $svc): JsonResponse
    {
        $r->validate([
            'id_caja' => 'required|integer',
            'saldo_declarado' => 'required|numeric|min:0',
            'desglose' => 'nullable|array'
        ]);

        try {
            $idCierre = $svc->cerrarCaja(
                $r->id_caja,
                (float) $r->saldo_declarado,
                $r->desglose ?? [],
                $this->usuarioId()
            );
            return response()->json(['res' => true, 'id' => $idCierre]);
        } catch (\Exception $e) {
            return response()->json(['res' => false, 'msg' => $e->getMessage()], 400);
        }
    }

    public function consolidado(Request $r, CajaService $svc): JsonResponse
    {
        $r->validate([
            'id_caja_padre' => 'required|integer',
            'fecha' => 'required|date'
        ]);

        $data = $svc->consolidadoCajasHijas($r->id_caja_padre, $r->fecha);
        return response()->json($data);
    }

    public function aprobar(Request $r, CajaService $svc): JsonResponse
    {
        $r->validate([
            'id' => 'required|integer',
            'estado' => 'required|in:APROBADO,RECHAZADO',
            'observaciones' => 'nullable|string|max:500'
        ]);

        try {
            $svc->aprobarCierre($r->id, $this->usuarioId(), $r->estado, $r->observaciones);
            return response()->json(['res' => true]);
        } catch (\Exception $e) {
            return response()->json(['res' => false, 'msg' => $e->getMessage()], 400);
        }
    }

    public function historial(int $idCaja): mixed
    {
        $q = DB::table('cierre_caja as cc')
            ->leftJoin('usuarios as uc', 'uc.usuario_id', '=', 'cc.id_usuario_cierra')
            ->leftJoin('usuarios as ua', 'ua.usuario_id', '=', 'cc.id_usuario_aprueba')
            ->where('cc.id_caja', $idCaja)
            ->select(
                'cc.*',
                DB::raw('CONCAT(uc.nombres, " ", uc.apellidos) as usuario_cierra'),
                DB::raw('COALESCE(CONCAT(ua.nombres, " ", ua.apellidos), "-") as usuario_aprueba')
            );

        return DataTables::of($q->orderByDesc('cc.id'))->make(true);
    }
}
