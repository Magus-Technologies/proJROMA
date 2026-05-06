<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ArqueoDiario;
use Illuminate\Http\{Request, JsonResponse};
use Illuminate\Routing\Attributes\Middleware;
use Illuminate\Support\Facades\DB;

#[Middleware(['auth:sanctum', 'check.empresa'])]
class ArqueoApiController extends Controller
{
    private function empresa(): int  { return (int) session('id_empresa'); }
    private function sucursal(): int { return (int) session('sucursal'); }

    /** Obtener cobros del día agrupados por vendedor */
    public function obtenerCobrosDia(Request $request): JsonResponse
    {
        $request->validate(['fecha' => 'required|date']);
        $fecha    = $request->fecha;
        $empresa  = $this->empresa();
        $sucursal = $this->sucursal();

        $cobrosDV = DB::table('dias_ventas as dv')
            ->join('ventas as v', 'v.id_venta', '=', 'dv.id_venta')
            ->leftJoin('usuarios as u', 'u.usuario_id', '=', DB::raw('IFNULL(dv.id_usuario, v.id_vendedor)'))
            ->where('v.id_empresa', $empresa)->where('v.sucursal', $sucursal)->where('dv.estado','1')
            ->whereDate(DB::raw('IFNULL(dv.fecha_pago_real, dv.fecha)'), $fecha)
            ->selectRaw('IFNULL(dv.id_usuario, v.id_vendedor) as id_usuario, u.usuario, dv.tipo_pago, SUM(dv.monto) as total')
            ->groupBy('id_usuario','u.usuario','dv.tipo_pago')->get();

        $cobrosCC = DB::table('cuotas_cotizacion as cc')
            ->join('cotizaciones as co', 'co.cotizacion_id', '=', 'cc.id_coti')
            ->leftJoin('usuarios as u', 'u.usuario_id', '=', DB::raw('IFNULL(cc.id_usuario, co.id_usuario)'))
            ->where('co.id_empresa', $empresa)->where('co.sucursal', $sucursal)->where('cc.estado','1')
            ->whereDate(DB::raw('IFNULL(cc.fecha_pago_real, cc.fecha)'), $fecha)
            ->selectRaw('IFNULL(cc.id_usuario, co.id_usuario) as id_usuario, u.usuario, cc.tipo_pago, SUM(cc.monto) as total')
            ->groupBy('id_usuario','u.usuario','cc.tipo_pago')->get();

        $vendedores = [];
        foreach ([$cobrosDV, $cobrosCC] as $col) {
            foreach ($col as $row) {
                $uid = $row->id_usuario;
                if (!isset($vendedores[$uid])) {
                    $vendedores[$uid] = ['usuario_id'=>$uid,'usuario'=>$row->usuario ?? 'Sin nombre','efectivo'=>0.0,'bancos'=>0.0,'total'=>0.0,'pagos_digitales_sistema'=>[]];
                }
                $tp = $row->tipo_pago ?? 'Efectivo';
                if (strtolower($tp) === 'efectivo' || $tp === '') $vendedores[$uid]['efectivo'] += (float) $row->total;
                else $vendedores[$uid]['bancos'] += (float) $row->total;
            }
        }

        // Pagos digitales detalle
        $pagosDigitales = DB::table(function($sub) use ($fecha, $empresa, $sucursal) {
            $sub->from('dias_ventas as dv')
                ->join('ventas as v','v.id_venta','=','dv.id_venta')
                ->leftJoin('clientes as c','c.id_cliente','=','v.id_cliente')
                ->where('v.id_empresa',$empresa)->where('v.sucursal',$sucursal)->where('dv.estado','1')
                ->whereDate(DB::raw('IFNULL(dv.fecha_pago_real, dv.fecha)'),$fecha)
                ->whereRaw("TRIM(LOWER(IFNULL(dv.tipo_pago,''))) NOT IN ('efectivo','')")
                ->selectRaw("dv.id_usuario as id_usuario_pago, v.id_vendedor, dv.tipo_pago, dv.monto, IFNULL(c.datos,'SIN CLIENTE') as cliente_nombre")
                ->unionAll(
                    DB::table('cuotas_cotizacion as cc')
                    ->join('cotizaciones as co','co.cotizacion_id','=','cc.id_coti')
                    ->leftJoin('clientes as c','c.id_cliente','=','co.id_cliente')
                    ->where('co.id_empresa',$empresa)->where('co.sucursal',$sucursal)->where('cc.estado','1')
                    ->whereDate(DB::raw('IFNULL(cc.fecha_pago_real, cc.fecha)'),$fecha)
                    ->whereRaw("TRIM(LOWER(IFNULL(cc.tipo_pago,''))) NOT IN ('efectivo','')")
                    ->selectRaw("cc.id_usuario as id_usuario_pago, co.id_usuario as id_vendedor, cc.tipo_pago, cc.monto, IFNULL(c.datos,'SIN CLIENTE') as cliente_nombre")
                );
        },'t')->get();

        foreach ($pagosDigitales as $row) {
            foreach (array_unique([$row->id_usuario_pago, $row->id_vendedor]) as $uid) {
                if (empty($uid)) continue;
                if (!isset($vendedores[$uid])) {
                    $vendedores[$uid] = ['usuario_id'=>$uid,'usuario'=>'Usuario '.$uid,'efectivo'=>0.0,'bancos'=>0.0,'total'=>0.0,'pagos_digitales_sistema'=>[]];
                }
                $vendedores[$uid]['pagos_digitales_sistema'][] = ['cliente_nombre'=>$row->cliente_nombre,'tipo_pago'=>$row->tipo_pago,'monto'=>(float)$row->monto];
            }
        }

        foreach ($vendedores as &$v) { $v['total'] = $v['efectivo'] + $v['bancos']; }

        return response()->json(array_values($vendedores));
    }

    /** Guardar arqueo diario */
    public function guardar(Request $request): JsonResponse
    {
        $request->validate([
            'fecha'               => 'required|date',
            'vendedor'            => 'nullable|string|max:200',
            'vendedor_id'         => 'nullable|integer',
            'cobros_efectivo'     => 'nullable|numeric',
            'cobros_bancos'       => 'nullable|numeric',
            'ingresos_efectivo'   => 'nullable|numeric',
            'ingresos_bancos'     => 'nullable|numeric',
            'egresos_efectivo'    => 'nullable|numeric',
            'egresos_bancos'      => 'nullable|numeric',
            'diferencia_efectivo' => 'nullable|numeric',
            'diferencia_bancos'   => 'nullable|numeric',
            'cuadra_efectivo'     => 'nullable|boolean',
            'cuadra_bancos'       => 'nullable|boolean',
        ]);

        $arqueo = ArqueoDiario::updateOrCreate(
            ['id_empresa' => $this->empresa(), 'sucursal' => $this->sucursal(), 'fecha_arqueo' => $request->fecha, 'vendedor_id' => $request->vendedor_id],
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

    /** Obtener arqueo existente por fecha y vendedor */
    public function get(Request $request): JsonResponse
    {
        $request->validate(['fecha' => 'required|date']);
        $arqueo = ArqueoDiario::where('id_empresa', $this->empresa())
            ->where('sucursal', $this->sucursal())
            ->where('fecha_arqueo', $request->fecha)
            ->when($request->filled('vendedor_id'), fn($q) => $q->where('vendedor_id', $request->vendedor_id))
            ->first();
        return response()->json($arqueo);
    }
}
