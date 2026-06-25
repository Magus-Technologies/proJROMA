<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class CajaInstrumentoApiController extends Controller
{
    private function empresa(): int { return (int) session('id_empresa'); }

    public function listar(Request $r, int $idCaja): mixed
    {
        return DataTables::of(
            DB::table('caja_instrumentos as ci')
                ->leftJoin('cuentas_bancarias as cb', function ($j) {
                    $j->on('cb.id_cuenta', '=', 'ci.instrumento_id')
                      ->where('ci.instrumento_tipo', '=', 'TRANSFERENCIA');
                })
                ->leftJoin('bancos as cbb', 'cbb.id_banco', '=', 'cb.id_banco')
                ->leftJoin('billeteras_digitales as bd', function ($j) {
                    $j->on('bd.id_billetera', '=', 'ci.instrumento_id')
                      ->where('ci.instrumento_tipo', '=', 'BILLETERA_DIGITAL');
                })
                ->leftJoin('billetera_tipos as bt', 'bt.id', '=', 'bd.id_billetera_tipo')
                ->where('ci.id_caja', $idCaja)
                ->select('ci.*',
                    DB::raw("CASE ci.instrumento_tipo
                        WHEN 'EFECTIVO' THEN 'Efectivo'
                        WHEN 'TRANSFERENCIA' THEN CONCAT('Transferencia: ', COALESCE(cbb.nombre, ''), ' ', COALESCE(cb.numero_cuenta, ''))
                        WHEN 'BILLETERA_DIGITAL' THEN CONCAT(COALESCE(bt.nombre, 'Billetera'), ' - ', COALESCE(bd.titular, ''))
                        WHEN 'CUENTA_BANCARIA' THEN 'Cuenta bancaria'
                        WHEN 'TARJETA' THEN 'Tarjeta'
                    END as instrumento_label"))
        )->make(true);
    }

    public function asignar(Request $r): JsonResponse
    {
        $r->validate([
            'id_caja'          => 'required|integer',
            'instrumento_tipo' => 'required|in:EFECTIVO,TRANSFERENCIA,BILLETERA_DIGITAL',
            'instrumento_id'   => 'nullable|integer',
        ]);

        // Transferencia y billetera exigen una cuenta/billetera vinculada; efectivo no
        if ($r->instrumento_tipo !== 'EFECTIVO' && !$r->instrumento_id) {
            return response()->json(['res' => false, 'msg' => 'Selecciona la cuenta o billetera vinculada.']);
        }

        // Validar que la caja sea hija (tenga padre)
        $caja = DB::table('cajas')->where('id', $r->id_caja)->first();
        if (!$caja || !$caja->id_caja_padre) {
            return response()->json(['res' => false, 'msg' => 'Solo las cajas hijas pueden tener métodos de pago asignados.']);
        }

        $existe = DB::table('caja_instrumentos')
            ->where('id_caja', $r->id_caja)
            ->where('instrumento_tipo', $r->instrumento_tipo)
            ->where('instrumento_id', $r->instrumento_id)
            ->exists();

        if ($existe) return response()->json(['res' => false, 'msg' => 'Ya está asignado.']);

        DB::table('caja_instrumentos')->insert([
            'id_caja'          => $r->id_caja,
            'instrumento_tipo' => $r->instrumento_tipo,
            'instrumento_id'   => $r->instrumento_id ?? null,
            'estado'           => 'ACTIVO',
        ]);

        return response()->json(['res' => true]);
    }

    public function quitar(Request $r): JsonResponse
    {
        $r->validate(['id' => 'required|integer']);
        DB::table('caja_instrumentos')->where('id', $r->id)->delete();
        return response()->json(['res' => true]);
    }

    public function disponibles(int $idCaja): JsonResponse
    {
        $caja = DB::table('cajas')->where('id', $idCaja)->first();
        if (!$caja) return response()->json([]);

        $yaAsignados = DB::table('caja_instrumentos')
            ->where('id_caja', $idCaja)
            ->get()
            ->map(fn($ci) => $ci->instrumento_tipo . '_' . ($ci->instrumento_id ?? 'null'))
            ->toArray();

        $empresa = $this->empresa();

        // Métodos de pago permitidos para una caja: Efectivo, Transferencia y Billetera digital.
        $result = [
            'efectivo_disponible' => !in_array('EFECTIVO_null', $yaAsignados),
            'cuentas'             => [],
            'billeteras'          => [],
        ];

        // Cuentas bancarias → método "Transferencia" (cuenta vinculada)
        $cuentas = DB::table('cuentas_bancarias as cb')
            ->join('bancos as b', 'b.id_banco', '=', 'cb.id_banco')
            ->where('cb.id_empresa', $empresa)->where('cb.estado', '1')
            ->select('cb.id_cuenta', DB::raw("CONCAT(b.nombre, ' - ', cb.tipo_cuenta, ' ', cb.numero_cuenta) as label"))
            ->get();
        foreach ($cuentas as $c) {
            if (!in_array('TRANSFERENCIA_' . $c->id_cuenta, $yaAsignados)) {
                $result['cuentas'][] = ['id' => $c->id_cuenta, 'label' => $c->label];
            }
        }

        // Billeteras (cada una ya trae su cuenta bancaria vinculada)
        $billeteras = DB::table('billeteras_digitales as bd')
            ->join('billetera_tipos as bt', 'bt.id', '=', 'bd.id_billetera_tipo')
            ->where('bd.id_empresa', $empresa)->where('bd.estado', '1')
            ->select('bd.id_billetera', 'bt.nombre as tipo', 'bd.titular')
            ->get();
        foreach ($billeteras as $b) {
            if (!in_array('BILLETERA_DIGITAL_' . $b->id_billetera, $yaAsignados)) {
                $result['billeteras'][] = ['id' => $b->id_billetera, 'label' => $b->tipo . ' - ' . $b->titular];
            }
        }

        return response()->json($result);
    }

    public function porCaja(int $idCaja): JsonResponse
    {
        $items = DB::table('caja_instrumentos')
            ->where('id_caja', $idCaja)
            ->where('estado', 'ACTIVO')
            ->get(['id', 'instrumento_tipo', 'instrumento_id']);

        return response()->json($items);
    }
}
