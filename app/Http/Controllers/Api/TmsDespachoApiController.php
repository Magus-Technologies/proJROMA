<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CajaService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class TmsDespachoApiController extends Controller
{
    /** id_tido que representa un "pedido" (Nota de Venta). */
    private const TIDO_PEDIDO = 6;

    private function empresa(): int  { return (int) session('id_empresa'); }
    private function sucursal(): int { return (int) session('sucursal'); }
    private function usuarioId(): int { return (int) (auth()->user()->usuario_id ?? 0); }

    // ── Opciones para los selects ───────────────────────────────────────────
    public function opciones(): JsonResponse
    {
        $rutas = DB::table('tms_rutas')
            ->where('id_empresa', $this->empresa())->where('sucursal', $this->sucursal())
            ->where('estado', 1)->orderBy('nombre')->get(['id', 'nombre']);

        $vehiculos = DB::table('tms_vehiculos')
            ->where('id_empresa', $this->empresa())->where('sucursal', $this->sucursal())
            ->where('estado', 1)->orderBy('placa')
            ->get(['id', 'placa', 'tipo', 'capacidad_kg']);

        $conductores = DB::table('tms_conductores')
            ->where('id_empresa', $this->empresa())->where('sucursal', $this->sucursal())
            ->where('estado', 1)->orderBy('nombres')->get(['id', 'nombres']);

        return response()->json(compact('rutas', 'vehiculos', 'conductores'));
    }

    /** IDs de clientes que pertenecen a los puntos de una ruta (mercados + tiendas). */
    private function clientesDeRuta(int $idRuta): array
    {
        $puntos = DB::table('tms_ruta_puntos')->where('id_ruta', $idRuta)->get();

        $mercados = $puntos->where('tipo', 'MERCADO')->pluck('id_mercado')->filter()->all();
        $tiendas  = $puntos->where('tipo', 'TIENDA')->pluck('id_cliente')->filter()->all();

        $deMercados = [];
        if ($mercados) {
            $deMercados = DB::table('clientes')
                ->where('id_empresa', $this->empresa())
                ->whereIn('mercado', $mercados)
                ->pluck('id_cliente')->all();
        }

        return array_values(array_unique(array_merge($deMercados, $tiendas)));
    }

    /** Peso por pedido = Σ(cantidad × peso_bruto) de sus líneas. */
    private function pesosPorPedido(array $cotizacionIds): array
    {
        if (!$cotizacionIds) return [];

        return DB::table('productos_cotis as pc')
            ->join('productos as p', 'p.id_producto', '=', 'pc.id_producto')
            ->whereIn('pc.id_coti', $cotizacionIds)
            ->groupBy('pc.id_coti')
            ->select('pc.id_coti', DB::raw('SUM(pc.cantidad * COALESCE(p.peso_bruto, 0)) as peso'))
            ->pluck('peso', 'pc.id_coti')->all();
    }

    /** Cotizaciones ya tomadas por un despacho no anulado. */
    private function pedidosYaDespachados(): array
    {
        return DB::table('tms_despacho_pedidos as dp')
            ->join('tms_despachos as d', 'd.id', '=', 'dp.id_despacho')
            ->where('d.estado', '<>', 'ANULADO')
            ->pluck('dp.id_cotizacion')->all();
    }

    // ── Jalar pedidos pendientes de una ruta + fecha ───────────────────────
    public function pedidosPendientes(Request $r): JsonResponse
    {
        $r->validate([
            'id_ruta'     => 'required|integer',
            'fecha_desde' => 'required|date',
            'fecha_hasta' => 'required|date',
        ]);

        $clientes = $this->clientesDeRuta($r->id_ruta);
        if (!$clientes) {
            return response()->json(['res' => true, 'pedidos' => [], 'resumen' => $this->resumenVacio(), 'vehiculos' => []]);
        }

        $yaDespachados = $this->pedidosYaDespachados();

        $pedidos = DB::table('cotizaciones as c')
            ->join('clientes as cl', 'cl.id_cliente', '=', 'c.id_cliente')
            ->leftJoin('tms_mercados as m', 'm.id', '=', 'cl.mercado')
            ->where('c.id_empresa', $this->empresa())
            ->whereIn('c.id_cliente', $clientes)
            ->whereDate('c.fecha', '>=', $r->fecha_desde)
            ->whereDate('c.fecha', '<=', $r->fecha_hasta)
            ->where('c.id_tido', self::TIDO_PEDIDO)
            ->when($yaDespachados, fn ($q) => $q->whereNotIn('c.cotizacion_id', $yaDespachados))
            ->orderBy('cl.mercado')
            ->select(
                'c.cotizacion_id', 'c.numero', 'c.fecha', 'c.total', 'c.id_cliente',
                'cl.datos as cliente', 'cl.mercado as id_mercado',
                DB::raw("COALESCE(m.nombre, CASE WHEN cl.mercado > 0 THEN CONCAT('Mercado ', cl.mercado) ELSE 'Tienda' END) as mercado")
            )
            ->get();

        $pesos = $this->pesosPorPedido($pedidos->pluck('cotizacion_id')->all());

        $pesoTotal = 0;
        $pedidos->each(function ($p) use ($pesos, &$pesoTotal) {
            $p->peso = round((float) ($pesos[$p->cotizacion_id] ?? 0), 2);
            $pesoTotal += $p->peso;
        });

        $resumen = [
            'pedidos'    => $pedidos->count(),
            'puntos'     => $pedidos->pluck('id_cliente')->unique()->count(),
            'mercados'   => $pedidos->pluck('id_mercado')->filter()->unique()->count(),
            'peso_total' => round($pesoTotal, 2),
            'monto_total'=> round((float) $pedidos->sum('total'), 2),
        ];

        // Vehículos cuya capacidad aguanta el peso total y están libres esa fecha
        $ocupados = DB::table('tms_despachos')
            ->where('id_empresa', $this->empresa())
            ->whereIn('estado', ['CARGADO', 'EN_RUTA'])
            ->whereDate('fecha_reparto', $r->fecha_hasta)
            ->pluck('id_vehiculo')->all();

        $vehiculos = DB::table('tms_vehiculos')
            ->where('id_empresa', $this->empresa())->where('sucursal', $this->sucursal())
            ->where('estado', 1)
            ->where('capacidad_kg', '>=', $pesoTotal)
            ->when($ocupados, fn ($q) => $q->whereNotIn('id', $ocupados))
            ->orderBy('capacidad_kg')
            ->get(['id', 'placa', 'tipo', 'capacidad_kg']);

        return response()->json([
            'res'       => true,
            'pedidos'   => $pedidos,
            'resumen'   => $resumen,
            'vehiculos' => $vehiculos,
        ]);
    }

    private function resumenVacio(): array
    {
        return ['pedidos' => 0, 'puntos' => 0, 'mercados' => 0, 'peso_total' => 0, 'monto_total' => 0];
    }

    // ── Crear despacho ──────────────────────────────────────────────────────
    public function guardar(Request $r): JsonResponse
    {
        $r->validate([
            'id_ruta'       => 'required|integer',
            'fecha_reparto' => 'required|date',
            'id_vehiculo'   => 'required|integer',
            'id_conductor'  => 'required|integer',
            'pedidos'       => 'required|array|min:1',
            'pedidos.*'     => 'integer',
            'observaciones' => 'nullable|string|max:255',
        ]);

        // Validaciones de pertenencia a la empresa
        $veh = DB::table('tms_vehiculos')->where('id', $r->id_vehiculo)->where('id_empresa', $this->empresa())->first();
        if (!$veh) return response()->json(['res' => false, 'msg' => 'Vehículo no encontrado.'], 404);
        $con = DB::table('tms_conductores')->where('id', $r->id_conductor)->where('id_empresa', $this->empresa())->first();
        if (!$con) return response()->json(['res' => false, 'msg' => 'Conductor no encontrado.'], 404);

        // Disponibilidad: ni el vehículo ni el conductor pueden estar en otro despacho activo esa fecha
        $activos = ['PLANIFICADO', 'CARGADO', 'EN_RUTA'];
        $vehOcupado = DB::table('tms_despachos')
            ->where('id_empresa', $this->empresa())
            ->whereIn('estado', $activos)
            ->whereDate('fecha_reparto', $r->fecha_reparto)
            ->where('id_vehiculo', $r->id_vehiculo)
            ->exists();
        if ($vehOcupado) {
            return response()->json(['res' => false, 'msg' => 'El vehículo ya tiene un despacho ese día.'], 409);
        }
        $conOcupado = DB::table('tms_despachos')
            ->where('id_empresa', $this->empresa())
            ->whereIn('estado', $activos)
            ->whereDate('fecha_reparto', $r->fecha_reparto)
            ->where('id_conductor', $r->id_conductor)
            ->exists();
        if ($conOcupado) {
            return response()->json(['res' => false, 'msg' => 'El conductor ya tiene un despacho ese día.'], 409);
        }

        // Re-leer los pedidos del lado servidor (no confiar en montos/pesos del cliente)
        $yaDespachados = $this->pedidosYaDespachados();
        $choque = array_intersect($r->pedidos, $yaDespachados);
        if ($choque) {
            return response()->json(['res' => false, 'msg' => 'Algunos pedidos ya están en otro despacho.'], 409);
        }

        $rows = DB::table('cotizaciones as c')
            ->join('clientes as cl', 'cl.id_cliente', '=', 'c.id_cliente')
            ->where('c.id_empresa', $this->empresa())
            ->whereIn('c.cotizacion_id', $r->pedidos)
            ->select('c.cotizacion_id', 'c.total', 'c.id_cliente', 'cl.mercado as id_mercado')
            ->get();

        if ($rows->isEmpty()) return response()->json(['res' => false, 'msg' => 'No hay pedidos válidos.'], 422);

        $pesos = $this->pesosPorPedido($rows->pluck('cotizacion_id')->all());
        $pesoTotal = 0;
        foreach ($rows as $row) { $pesoTotal += (float) ($pesos[$row->cotizacion_id] ?? 0); }

        // Advertencias (no bloquean, pero informan)
        $excedeCapacidad = $pesoTotal > (float) $veh->capacidad_kg;
        $advertencias = [];
        if ($excedeCapacidad) {
            $advertencias[] = 'El peso (' . round($pesoTotal, 2) . ' kg) supera la capacidad del vehículo (' . round((float) $veh->capacidad_kg, 2) . ' kg).';
        }
        $f = $r->fecha_reparto;
        if ($veh->soat_vence && $veh->soat_vence < $f)               $advertencias[] = 'El SOAT del vehículo está vencido.';
        if ($veh->rev_tecnica_vence && $veh->rev_tecnica_vence < $f)  $advertencias[] = 'La revisión técnica del vehículo está vencida.';
        if ($con->licencia_vence && $con->licencia_vence < $f)        $advertencias[] = 'La licencia del conductor está vencida.';

        return DB::transaction(function () use ($r, $rows, $pesos, $pesoTotal, $excedeCapacidad, $advertencias) {
            $id = DB::table('tms_despachos')->insertGetId([
                'id_empresa'          => $this->empresa(),
                'sucursal'            => $this->sucursal(),
                'fecha_reparto'       => $r->fecha_reparto,
                'id_ruta'             => $r->id_ruta,
                'id_vehiculo'         => $r->id_vehiculo,
                'id_conductor'        => $r->id_conductor,
                'peso_total'          => round($pesoTotal, 2),
                'estado'              => 'PLANIFICADO',
                'observaciones'       => $r->observaciones ?? null,
                'id_usuario_creacion' => $this->usuarioId(),
                'created_at'          => now(),
                'updated_at'          => now(),
            ]);

            DB::table('tms_despachos')->where('id', $id)->update(['codigo' => 'DSP-' . str_pad((string) $id, 6, '0', STR_PAD_LEFT)]);

            $orden = 1;
            $detalles = [];
            foreach ($rows as $row) {
                $detalles[] = [
                    'id_despacho'    => $id,
                    'id_cotizacion'  => $row->cotizacion_id,
                    'id_cliente'     => $row->id_cliente,
                    'id_mercado'     => $row->id_mercado ?: null,
                    'peso'           => round((float) ($pesos[$row->cotizacion_id] ?? 0), 2),
                    'monto'          => round((float) $row->total, 2),
                    'orden'          => $orden++,
                    'estado_entrega' => 'PENDIENTE',
                ];
            }
            DB::table('tms_despacho_pedidos')->insert($detalles);

            return response()->json([
                'res' => true,
                'id'  => $id,
                'peso_total' => round($pesoTotal, 2),
                'excede_capacidad' => $excedeCapacidad,
                'advertencias' => $advertencias,
            ]);
        });
    }

    // ── Listado de despachos ────────────────────────────────────────────────
    public function listar(Request $r): mixed
    {
        $q = DB::table('tms_despachos as d')
            ->leftJoin('tms_rutas as ru', 'ru.id', '=', 'd.id_ruta')
            ->leftJoin('tms_vehiculos as v', 'v.id', '=', 'd.id_vehiculo')
            ->leftJoin('tms_conductores as co', 'co.id', '=', 'd.id_conductor')
            ->where('d.id_empresa', $this->empresa())
            ->where('d.sucursal', $this->sucursal())
            ->select(
                'd.id', 'd.codigo', 'd.fecha_reparto', 'd.peso_total', 'd.estado',
                DB::raw("COALESCE(ru.nombre, '-') as ruta"),
                DB::raw("COALESCE(v.placa, '-') as vehiculo"),
                DB::raw("COALESCE(co.nombres, '-') as conductor"),
                DB::raw('(SELECT COUNT(*) FROM tms_despacho_pedidos dp WHERE dp.id_despacho = d.id) as pedidos')
            );

        return DataTables::of($q)->make(true);
    }

    public function detalle(int $id): JsonResponse
    {
        $despacho = DB::table('tms_despachos as d')
            ->leftJoin('tms_rutas as ru', 'ru.id', '=', 'd.id_ruta')
            ->leftJoin('tms_vehiculos as v', 'v.id', '=', 'd.id_vehiculo')
            ->leftJoin('tms_conductores as co', 'co.id', '=', 'd.id_conductor')
            ->where('d.id', $id)->where('d.id_empresa', $this->empresa())
            ->select('d.*', 'ru.nombre as ruta', 'v.placa as vehiculo', 'v.capacidad_kg', 'co.nombres as conductor')
            ->first();

        if (!$despacho) return response()->json(['res' => false, 'msg' => 'No encontrado.'], 404);

        $pedidos = DB::table('tms_despacho_pedidos as dp')
            ->leftJoin('clientes as cl', 'cl.id_cliente', '=', 'dp.id_cliente')
            ->leftJoin('tms_mercados as m', 'm.id', '=', 'dp.id_mercado')
            ->leftJoin('cotizaciones as c', 'c.cotizacion_id', '=', 'dp.id_cotizacion')
            ->where('dp.id_despacho', $id)
            ->orderBy('dp.orden')
            ->select(
                'dp.id', 'dp.orden', 'dp.peso', 'dp.monto', 'dp.estado_entrega', 'dp.motivo_rechazo',
                'c.numero',
                DB::raw("COALESCE(cl.datos, '-') as cliente"),
                DB::raw("COALESCE(cl.direccion, '-') as direccion"),
                DB::raw("COALESCE(m.nombre, 'Tienda') as mercado")
            )
            ->get();

        return response()->json(['res' => true, 'despacho' => $despacho, 'pedidos' => $pedidos]);
    }

    // ── Reporte de despacho (RES DESPACHO: por artículo + por cliente) ──────
    public function reporte(int $id): JsonResponse
    {
        $despacho = DB::table('tms_despachos as d')
            ->leftJoin('tms_rutas as ru', 'ru.id', '=', 'd.id_ruta')
            ->leftJoin('tms_vehiculos as v', 'v.id', '=', 'd.id_vehiculo')
            ->leftJoin('tms_conductores as co', 'co.id', '=', 'd.id_conductor')
            ->where('d.id', $id)->where('d.id_empresa', $this->empresa())
            ->select('d.id', 'd.codigo', 'd.fecha_reparto', 'd.peso_total', 'd.estado',
                'ru.nombre as ruta', 'v.placa as vehiculo', 'co.nombres as conductor')
            ->first();

        if (!$despacho) return response()->json(['res' => false, 'msg' => 'No encontrado.'], 404);

        $cotIds = DB::table('tms_despacho_pedidos')->where('id_despacho', $id)->pluck('id_cotizacion')->all();

        // Consolidado POR ARTÍCULO (hoja de carga): código, descripción, cantidad, kilos
        $porArticulo = collect();
        if ($cotIds) {
            $porArticulo = DB::table('productos_cotis as pc')
                ->join('productos as p', 'p.id_producto', '=', 'pc.id_producto')
                ->whereIn('pc.id_coti', $cotIds)
                ->groupBy('p.id_producto', 'p.codigo', 'p.descripcion')
                ->select(
                    'p.codigo', 'p.descripcion',
                    DB::raw('SUM(pc.cantidad) as cantidad'),
                    DB::raw('SUM(pc.cantidad * COALESCE(p.peso_bruto, 0)) as kilos')
                )
                ->orderBy('p.descripcion')
                ->get();
        }

        // Consolidado POR CLIENTE/ENTIDAD: doc, denominación, total S/
        $porCliente = DB::table('tms_despacho_pedidos as dp')
            ->leftJoin('clientes as cl', 'cl.id_cliente', '=', 'dp.id_cliente')
            ->where('dp.id_despacho', $id)
            ->groupBy('dp.id_cliente', 'cl.documento', 'cl.datos')
            ->select(
                'cl.documento',
                DB::raw("COALESCE(cl.datos, '-') as denominacion"),
                DB::raw('COUNT(*) as pedidos'),
                DB::raw('SUM(dp.peso) as kilos'),
                DB::raw('SUM(dp.monto) as total')
            )
            ->orderBy('cl.datos')
            ->get();

        $totales = [
            'cantidad' => round((float) $porArticulo->sum('cantidad'), 2),
            'kilos'    => round((float) $porArticulo->sum('kilos'), 2),
            'total'    => round((float) $porCliente->sum('total'), 2),
            'clientes' => $porCliente->count(),
        ];

        return response()->json([
            'res'          => true,
            'despacho'     => $despacho,
            'por_articulo' => $porArticulo,
            'por_cliente'  => $porCliente,
            'totales'      => $totales,
        ]);
    }

    // ── Cambiar estado del despacho ─────────────────────────────────────────
    public function cambiarEstado(Request $r): JsonResponse
    {
        $r->validate(['id' => 'required|integer', 'accion' => 'required|in:CARGAR,SALIR,CERRAR,ANULAR']);

        $d = DB::table('tms_despachos')->where('id', $r->id)->where('id_empresa', $this->empresa())->first();
        if (!$d) return response()->json(['res' => false, 'msg' => 'No encontrado.'], 404);

        $transiciones = [
            'CARGAR' => ['de' => ['PLANIFICADO'], 'a' => 'CARGADO'],
            'SALIR'  => ['de' => ['CARGADO'], 'a' => 'EN_RUTA'],
            'CERRAR' => ['de' => ['EN_RUTA'], 'a' => 'CERRADO'],
            'ANULAR' => ['de' => ['PLANIFICADO', 'CARGADO'], 'a' => 'ANULADO'],
        ];
        $t = $transiciones[$r->accion];

        if (!in_array($d->estado, $t['de'], true)) {
            return response()->json(['res' => false, 'msg' => "No se puede {$r->accion} un despacho en estado {$d->estado}."], 409);
        }

        DB::table('tms_despachos')->where('id', $r->id)->update(['estado' => $t['a'], 'updated_at' => now()]);
        return response()->json(['res' => true, 'estado' => $t['a']]);
    }

    // ── Reordenar el orden de visita de los puntos ──────────────────────────
    public function reordenar(Request $r): JsonResponse
    {
        $r->validate([
            'id_despacho' => 'required|integer',
            'orden'       => 'required|array|min:1',
            'orden.*'     => 'integer',
        ]);

        $d = DB::table('tms_despachos')->where('id', $r->id_despacho)->where('id_empresa', $this->empresa())->first();
        if (!$d) return response()->json(['res' => false, 'msg' => 'Despacho no encontrado.'], 404);

        if (in_array($d->estado, ['CERRADO', 'ANULADO'], true)) {
            return response()->json(['res' => false, 'msg' => 'No se puede reordenar un despacho cerrado o anulado.'], 409);
        }

        DB::transaction(function () use ($r) {
            foreach (array_values($r->orden) as $i => $idPedido) {
                DB::table('tms_despacho_pedidos')
                    ->where('id', $idPedido)
                    ->where('id_despacho', $r->id_despacho)
                    ->update(['orden' => $i + 1]);
            }
        });

        return response()->json(['res' => true]);
    }

    // ── Registrar entrega de un punto ───────────────────────────────────────
    public function registrarEntrega(Request $r): JsonResponse
    {
        $r->validate([
            'id'             => 'required|integer',
            'estado_entrega' => 'required|in:ENTREGADO,RECHAZADO,PARCIAL',
            'motivo_rechazo' => 'nullable|string|max:255',
        ]);

        $row = DB::table('tms_despacho_pedidos as dp')
            ->join('tms_despachos as d', 'd.id', '=', 'dp.id_despacho')
            ->where('dp.id', $r->id)->where('d.id_empresa', $this->empresa())
            ->select('dp.id', 'd.estado as estado_despacho')
            ->first();
        if (!$row) return response()->json(['res' => false, 'msg' => 'No encontrado.'], 404);

        if (!in_array($row->estado_despacho, ['EN_RUTA', 'CARGADO'], true)) {
            return response()->json(['res' => false, 'msg' => 'El despacho debe estar cargado o en ruta para registrar entregas.'], 409);
        }

        DB::table('tms_despacho_pedidos')->where('id', $r->id)->update([
            'estado_entrega' => $r->estado_entrega,
            'motivo_rechazo' => $r->estado_entrega === 'RECHAZADO' ? ($r->motivo_rechazo ?? null) : null,
            'hora_entrega'   => now(),
        ]);

        return response()->json(['res' => true]);
    }

    // ── Costos del viaje ────────────────────────────────────────────────────
    public function costos(int $id): JsonResponse
    {
        $despacho = DB::table('tms_despachos')->where('id', $id)->where('id_empresa', $this->empresa())->first();
        if (!$despacho) return response()->json(['res' => false, 'msg' => 'No encontrado.'], 404);

        $costos = DB::table('tms_despacho_costos as c')
            ->leftJoin('cajas as ca', 'ca.id', '=', 'c.id_caja')
            ->where('c.id_despacho', $id)
            ->orderBy('c.id')
            ->select('c.id', 'c.concepto', 'c.monto', 'c.id_caja', 'c.id_movimiento_caja',
                DB::raw("COALESCE(ca.nombre, '-') as caja"))
            ->get();

        return response()->json([
            'res'   => true,
            'costos'=> $costos,
            'total' => round((float) $costos->sum('monto'), 2),
        ]);
    }

    public function agregarCosto(Request $r, CajaService $svc): JsonResponse
    {
        $r->validate([
            'id_despacho' => 'required|integer',
            'concepto'    => 'required|string|max:120',
            'monto'       => 'required|numeric|min:0.01',
            'id_caja'     => 'nullable|integer',
        ]);

        $despacho = DB::table('tms_despachos')->where('id', $r->id_despacho)->where('id_empresa', $this->empresa())->first();
        if (!$despacho) return response()->json(['res' => false, 'msg' => 'Despacho no encontrado.'], 404);

        $idMovimiento = null;

        // Si se indica una caja, registrar el costo como EGRESO reutilizando el flujo de caja
        if ($r->filled('id_caja')) {
            $caja = DB::table('cajas')->where('id', $r->id_caja)->where('id_empresa', $this->empresa())->first();
            if (!$caja) return response()->json(['res' => false, 'msg' => 'Caja no encontrada.'], 404);

            try {
                $idMovimiento = $svc->registrarMovimiento([
                    'id_caja'     => $r->id_caja,
                    'fecha'       => now()->toDateString(),
                    'tipo'        => 'EGRESO',
                    'categoria'   => 'TMS',
                    'descripcion' => 'Costo despacho ' . ($despacho->codigo ?? $despacho->id) . ': ' . $r->concepto,
                    'monto'       => $r->monto,
                    'id_usuario'  => $this->usuarioId(),
                ]);
            } catch (\RuntimeException $e) {
                return response()->json(['res' => false, 'msg' => $e->getMessage()], 400);
            }
        }

        $id = DB::table('tms_despacho_costos')->insertGetId([
            'id_despacho'        => $r->id_despacho,
            'concepto'           => $r->concepto,
            'monto'              => $r->monto,
            'id_caja'            => $r->id_caja ?? null,
            'id_movimiento_caja' => $idMovimiento,
            'id_usuario'         => $this->usuarioId(),
            'created_at'         => now(),
            'updated_at'         => now(),
        ]);

        return response()->json(['res' => true, 'id' => $id]);
    }

    public function quitarCosto(Request $r, CajaService $svc): JsonResponse
    {
        $r->validate(['id' => 'required|integer']);

        $costo = DB::table('tms_despacho_costos as c')
            ->join('tms_despachos as d', 'd.id', '=', 'c.id_despacho')
            ->where('c.id', $r->id)->where('d.id_empresa', $this->empresa())
            ->select('c.id', 'c.id_movimiento_caja')
            ->first();
        if (!$costo) return response()->json(['res' => false, 'msg' => 'No encontrado.'], 404);

        // Si tenía movimiento en caja, anularlo para restaurar el saldo
        if ($costo->id_movimiento_caja) {
            try { $svc->anularMovimiento($costo->id_movimiento_caja); }
            catch (\RuntimeException $e) { /* ya anulado: continuar con la eliminación */ }
        }

        DB::table('tms_despacho_costos')->where('id', $costo->id)->delete();

        return response()->json(['res' => true]);
    }
}
