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
                ->where('ci.id_caja', $idCaja)
                ->select('ci.*',
                    DB::raw("CASE ci.instrumento_tipo
                        WHEN 'EFECTIVO' THEN 'Efectivo'
                        WHEN 'CUENTA_BANCARIA' THEN 'Cuenta bancaria'
                        WHEN 'TARJETA' THEN 'Tarjeta'
                        WHEN 'BILLETERA_DIGITAL' THEN 'Billetera digital'
                    END as instrumento_label"))
        )->make(true);
    }

    public function asignar(Request $r): JsonResponse
    {
        $r->validate([
            'id_caja'          => 'required|integer',
            'instrumento_tipo' => 'required|in:EFECTIVO,CUENTA_BANCARIA,TARJETA,BILLETERA_DIGITAL',
        ]);

        // Validar que la caja sea hija (tenga padre)
        $caja = DB::table('cajas')->where('id', $r->id_caja)->first();
        if (!$caja || !$caja->id_caja_padre) {
            return response()->json(['res' => false, 'msg' => 'Solo las cajas hijas pueden tener instrumentos asignados.']);
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

        $result = [];

        // Efectivo siempre disponible
        if (!in_array('EFECTIVO_null', $yaAsignados)) {
            $result[] = ['tipo' => 'EFECTIVO', 'id' => null, 'label' => 'Efectivo'];
        }

        $empresa = $this->empresa();

        // Cuentas bancarias
        $cuentas = DB::table('cuentas_bancarias as cb')
            ->join('bancos as b', 'b.id_banco', '=', 'cb.id_banco')
            ->where('cb.id_empresa', $empresa)->where('cb.estado', '1')
            ->select('cb.id_cuenta', DB::raw("CONCAT(b.nombre, ' - ', cb.tipo_cuenta, ' ', cb.numero_cuenta) as label"))
            ->get();
        foreach ($cuentas as $c) {
            if (!in_array('CUENTA_BANCARIA_' . $c->id_cuenta, $yaAsignados)) {
                $result[] = ['tipo' => 'CUENTA_BANCARIA', 'id' => $c->id_cuenta, 'label' => $c->label];
            }
        }

        // Tarjetas
        $tarjetas = DB::table('tarjetas as t')
            ->join('bancos as b', 'b.id_banco', '=', 't.id_banco')
            ->where('t.id_empresa', $empresa)->where('t.estado', '1')
            ->select('t.id_tarjeta', DB::raw("CONCAT(b.nombre, ' ', t.marca, ' *', t.ultimos_4) as label"))
            ->get();
        foreach ($tarjetas as $t) {
            if (!in_array('TARJETA_' . $t->id_tarjeta, $yaAsignados)) {
                $result[] = ['tipo' => 'TARJETA', 'id' => $t->id_tarjeta, 'label' => $t->label];
            }
        }

        // Billeteras
        $billeteras = DB::table('billeteras_digitales as bd')
            ->join('billetera_tipos as bt', 'bt.id', '=', 'bd.id_billetera_tipo')
            ->where('bd.id_empresa', $empresa)->where('bd.estado', '1')
            ->select('bd.id_billetera', 'bt.nombre as tipo', 'bd.titular')
            ->get();
        foreach ($billeteras as $b) {
            if (!in_array('BILLETERA_DIGITAL_' . $b->id_billetera, $yaAsignados)) {
                $result[] = ['tipo' => 'BILLETERA_DIGITAL', 'id' => $b->id_billetera, 'label' => $b->tipo . ' - ' . $b->titular];
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
