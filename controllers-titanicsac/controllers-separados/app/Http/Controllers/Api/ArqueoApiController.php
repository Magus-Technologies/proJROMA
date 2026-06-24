<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ArqueoDiario;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ArqueoApiController extends Controller
{
    private function empresa(): int  { return (int) session('id_empresa'); }
    private function sucursal(): int { return (int) session('sucursal'); }

    public function obtenerCobrosDia(Request $request): JsonResponse
    {
        $request->validate(['fecha' => 'required|date']);
        $fecha    = $request->fecha;
        $empresa  = $this->empresa();
        $sucursal = $this->sucursal();

        $cobrosDV = DB::table('dias_ventas as dv')
            ->join('ventas as v', 'v.id_venta', '=', 'dv.id_venta')
            ->leftJoin('usuarios as u', 'u.usuario_id', '=', DB::raw('IFNULL(dv.id_usuario, v.id_vendedor)'))
            ->where('v.id_empresa', $empresa)->where('v.sucursal', $sucursal)->where('dv.estado', '1')
            ->whereDate(DB::raw('IFNULL(dv.fecha_pago_real, dv.fecha)'), $fecha)
            ->selectRaw('IFNULL(dv.id_usuario, v.id_vendedor) as id_usuario, u.usuario, dv.tipo_pago, SUM(dv.monto) as total')
            ->groupBy('id_usuario', 'u.usuario', 'dv.tipo_pago')->get();

        $cobrosCC = DB::table('cuotas_cotizacion as cc')
            ->join('cotizaciones as co', 'co.cotizacion_id', '=', 'cc.id_coti')
            ->leftJoin('usuarios as u', 'u.usuario_id', '=', DB::raw('IFNULL(cc.id_usuario, co.id_usuario)'))
            ->where('co.id_empresa', $empresa)->where('co.sucursal', $sucursal)->where('cc.estado', '1')
            ->whereDate(DB::raw('IFNULL(cc.fecha_pago_real, cc.fecha)'), $fecha)
            ->selectRaw('IFNULL(cc.id_usuario, co.id_usuario) as id_usuario, u.usuario, cc.tipo_pago, SUM(cc.monto) as total')
            ->groupBy('id_usuario', 'u.usuario', 'cc.tipo_pago')->get();

        $vendedores = [];
        foreach ([$cobrosDV, $cobrosCC] as $col) {
            foreach ($col as $row) {
                $uid = $row->id_usuario;
                if (!isset($vendedores[$uid])) {
                    $vendedores[$uid] = [
                        'usuario_id' => $uid, 'usuario' => $row->usuario ?? 'Sin nombre',
                        'efectivo' => 0.0, 'bancos' => 0.0, 'total' => 0.0,
                        'pagos_digitales_sistema' => [],
                    ];
                }
                $tp = $row->tipo_pago ?? 'Efectivo';
                if (strtolower($tp) === 'efectivo' || $tp === '')
                    $vendedores[$uid]['efectivo'] += (float) $row->total;
                else
                    $vendedores[$uid]['bancos'] += (float) $row->total;
            }
        }

        foreach ($vendedores as &$v) {
            $v['total'] = $v['efectivo'] + $v['bancos'];
        }

        return response()->json(array_values($vendedores));
    }

    public function guardar(Request $request): JsonResponse
    {
        $request->validate([
            'fecha'       => 'required|date',
            'vendedor'    => 'nullable|string|max:200',
            'vendedor_id' => 'nullable|integer',
        ]);

        $arqueo = ArqueoDiario::updateOrCreate(
            [
                'id_empresa'   => $this->empresa(),
                'sucursal'     => $this->sucursal(),
                'fecha_arqueo' => $request->fecha,
                'vendedor_id'  => $request->vendedor_id,
            ],
            [
                'vendedor'            => $request->vendedor ?? '',
                'cobros_efectivo'     => $request->cobros_efectivo     ?? 0,
                'cobros_bancos'       => $request->cobros_bancos       ?? 0,
                'ingresos_efectivo'   => $request->ingresos_efectivo   ?? 0,
                'ingresos_bancos'     => $request->ingresos_bancos     ?? 0,
                'egresos_efectivo'    => $request->egresos_efectivo    ?? 0,
                'egresos_bancos'      => $request->egresos_bancos      ?? 0,
                'diferencia_efectivo' => $request->diferencia_efectivo ?? 0,
                'diferencia_bancos'   => $request->diferencia_bancos   ?? 0,
                'cuadra_efectivo'     => $request->boolean('cuadra_efectivo') ? 1 : 0,
                'cuadra_bancos'       => $request->boolean('cuadra_bancos')   ? 1 : 0,
                'usuario_registro'    => auth()->id(),
                'fecha_creacion'      => now(),
            ]
        );

        return response()->json(['res' => true, 'id' => $arqueo->arqueo_id]);
    }

    public function get(Request $request): JsonResponse
    {
        $request->validate(['fecha' => 'required|date']);
        $arqueo = ArqueoDiario::where('id_empresa', $this->empresa())
            ->where('sucursal', $this->sucursal())
            ->where('fecha_arqueo', $request->fecha)
            ->when($request->filled('vendedor_id'), fn($q) =>
                $q->where('vendedor_id', $request->vendedor_id)
            )
            ->first();
        return response()->json($arqueo);
    }
}
