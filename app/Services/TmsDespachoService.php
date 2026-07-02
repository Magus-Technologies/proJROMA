<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TmsDespachoService
{
    /** id_tido que representa un "pedido" (Nota de Venta). */
    public const TIDO_PEDIDO = 6;

    /** IDs de clientes que pertenecen a los puntos de una ruta (mercados + tiendas). */
    public function clientesDeRuta(int $idRuta, int $empresa): array
    {
        $puntos = DB::table('tms_ruta_puntos')->where('id_ruta', $idRuta)->get();

        $mercados = $puntos->where('tipo', 'MERCADO')->pluck('id_mercado')->filter()->all();
        $tiendas  = $puntos->where('tipo', 'TIENDA')->pluck('id_cliente')->filter()->all();

        $deMercados = [];
        if ($mercados) {
            $deMercados = DB::table('clientes')
                ->where('id_empresa', $empresa)
                ->whereIn('mercado', $mercados)
                ->pluck('id_cliente')->all();
        }

        return array_values(array_unique(array_merge($deMercados, $tiendas)));
    }

    /** Peso por pedido = Σ(cantidad × peso_bruto) de sus líneas. */
    public function pesosPorPedido(array $cotizacionIds): array
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
    public function pedidosYaDespachados(): array
    {
        return DB::table('tms_despacho_pedidos as dp')
            ->join('tms_despachos as d', 'd.id', '=', 'dp.id_despacho')
            ->where('d.estado', '<>', 'ANULADO')
            ->pluck('dp.id_cotizacion')->all();
    }

    /** Pedidos pendientes de una ruta en un rango de fechas, con su peso. */
    public function pedidosPendientes(int $idRuta, string $desde, string $hasta, int $empresa): Collection
    {
        $clientes = $this->clientesDeRuta($idRuta, $empresa);
        if (!$clientes) return collect();

        $yaDespachados = $this->pedidosYaDespachados();

        $pedidos = DB::table('cotizaciones as c')
            ->join('clientes as cl', 'cl.id_cliente', '=', 'c.id_cliente')
            ->leftJoin('tms_mercados as m', 'm.id', '=', 'cl.mercado')
            ->where('c.id_empresa', $empresa)
            ->whereIn('c.id_cliente', $clientes)
            ->whereDate('c.fecha', '>=', $desde)
            ->whereDate('c.fecha', '<=', $hasta)
            ->where('c.id_tido', self::TIDO_PEDIDO)
            ->when($yaDespachados, fn ($q) => $q->whereNotIn('c.cotizacion_id', $yaDespachados))
            ->orderBy('cl.mercado')
            ->select(
                'c.cotizacion_id', 'c.numero', 'c.fecha', 'c.total', 'c.id_cliente',
                'cl.mercado as id_mercado', 'cl.datos as cliente',
                DB::raw("COALESCE(m.nombre, CASE WHEN cl.mercado > 0 THEN CONCAT('Mercado ', cl.mercado) ELSE 'Tienda' END) as mercado")
            )
            ->get();

        $pesos = $this->pesosPorPedido($pedidos->pluck('cotizacion_id')->all());
        $pedidos->each(fn ($p) => $p->peso = round((float) ($pesos[$p->cotizacion_id] ?? 0), 2));

        return $pedidos;
    }

    /**
     * Crea un despacho a partir de una ruta, fecha, vehículo, conductor y lista de pedidos.
     * Devuelve ['id', 'peso_total', 'advertencias'] o lanza \RuntimeException.
     */
    public function crear(array $d, int $empresa, int $sucursal, int $usuarioId): array
    {
        $veh = DB::table('tms_vehiculos')->where('id', $d['id_vehiculo'])->where('id_empresa', $empresa)->first();
        if (!$veh) throw new \RuntimeException('Vehículo no encontrado.');
        $con = DB::table('tms_conductores')->where('id', $d['id_conductor'])->where('id_empresa', $empresa)->first();
        if (!$con) throw new \RuntimeException('Conductor no encontrado.');

        $activos = ['PLANIFICADO', 'CARGADO', 'EN_RUTA'];
        $vehOcupado = DB::table('tms_despachos')->where('id_empresa', $empresa)
            ->whereIn('estado', $activos)->whereDate('fecha_reparto', $d['fecha_reparto'])
            ->where('id_vehiculo', $d['id_vehiculo'])->exists();
        if ($vehOcupado) throw new \RuntimeException('El vehículo ya tiene un despacho ese día.');

        $conOcupado = DB::table('tms_despachos')->where('id_empresa', $empresa)
            ->whereIn('estado', $activos)->whereDate('fecha_reparto', $d['fecha_reparto'])
            ->where('id_conductor', $d['id_conductor'])->exists();
        if ($conOcupado) throw new \RuntimeException('El conductor ya tiene un despacho ese día.');

        $pedidosIds = array_values(array_map('intval', $d['pedidos'] ?? []));
        if (!$pedidosIds) throw new \RuntimeException('Selecciona al menos un pedido.');

        $choque = array_intersect($pedidosIds, $this->pedidosYaDespachados());
        if ($choque) throw new \RuntimeException('Algunos pedidos ya están en otro despacho.');

        $rows = DB::table('cotizaciones as c')
            ->join('clientes as cl', 'cl.id_cliente', '=', 'c.id_cliente')
            ->where('c.id_empresa', $empresa)
            ->whereIn('c.cotizacion_id', $pedidosIds)
            ->select('c.cotizacion_id', 'c.total', 'c.id_cliente', 'cl.mercado as id_mercado')
            ->get();
        if ($rows->isEmpty()) throw new \RuntimeException('No hay pedidos válidos.');

        $pesos = $this->pesosPorPedido($rows->pluck('cotizacion_id')->all());
        $pesoTotal = 0.0;
        foreach ($rows as $row) { $pesoTotal += (float) ($pesos[$row->cotizacion_id] ?? 0); }

        $advertencias = [];
        if ($pesoTotal > (float) $veh->capacidad_kg) {
            $advertencias[] = 'El peso (' . round($pesoTotal, 2) . ' kg) supera la capacidad del vehículo (' . round((float) $veh->capacidad_kg, 2) . ' kg).';
        }
        $f = $d['fecha_reparto'];
        if ($veh->soat_vence && $veh->soat_vence < $f)              $advertencias[] = 'El SOAT del vehículo está vencido.';
        if ($veh->rev_tecnica_vence && $veh->rev_tecnica_vence < $f) $advertencias[] = 'La revisión técnica del vehículo está vencida.';
        if ($con->licencia_vence && $con->licencia_vence < $f)       $advertencias[] = 'La licencia del conductor está vencida.';

        $id = DB::transaction(function () use ($d, $rows, $pesos, $pesoTotal, $empresa, $sucursal, $usuarioId) {
            $id = DB::table('tms_despachos')->insertGetId([
                'id_empresa'          => $empresa,
                'sucursal'            => $sucursal,
                'fecha_reparto'       => $d['fecha_reparto'],
                'id_ruta'             => $d['id_ruta'],
                'id_vehiculo'         => $d['id_vehiculo'],
                'id_conductor'        => $d['id_conductor'],
                'peso_total'          => round($pesoTotal, 2),
                'estado'              => 'PLANIFICADO',
                'observaciones'       => $d['observaciones'] ?? null,
                'id_usuario_creacion' => $usuarioId,
                'created_at'          => now(),
                'updated_at'          => now(),
            ]);

            DB::table('tms_despachos')->where('id', $id)
                ->update(['codigo' => 'DSP-' . str_pad((string) $id, 6, '0', STR_PAD_LEFT)]);

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

            return $id;
        });

        return ['id' => $id, 'peso_total' => round($pesoTotal, 2), 'advertencias' => $advertencias];
    }

    /** Consolidados del reporte RES DESPACHO. */
    public function reporte(int $idDespacho): array
    {
        $cotIds = DB::table('tms_despacho_pedidos')->where('id_despacho', $idDespacho)->pluck('id_cotizacion')->all();

        $porArticulo = collect();
        if ($cotIds) {
            $porArticulo = DB::table('productos_cotis as pc')
                ->join('productos as p', 'p.id_producto', '=', 'pc.id_producto')
                ->whereIn('pc.id_coti', $cotIds)
                ->groupBy('p.id_producto', 'p.codigo', 'p.descripcion')
                ->select('p.codigo', 'p.descripcion',
                    DB::raw('SUM(pc.cantidad) as cantidad'),
                    DB::raw('SUM(pc.cantidad * COALESCE(p.peso_bruto, 0)) as kilos'))
                ->orderBy('p.descripcion')
                ->get();
        }

        $porCliente = DB::table('tms_despacho_pedidos as dp')
            ->leftJoin('clientes as cl', 'cl.id_cliente', '=', 'dp.id_cliente')
            ->where('dp.id_despacho', $idDespacho)
            ->groupBy('dp.id_cliente', 'cl.documento', 'cl.datos')
            ->select('cl.documento', DB::raw("COALESCE(cl.datos, '-') as denominacion"),
                DB::raw('COUNT(*) as pedidos'), DB::raw('SUM(dp.peso) as kilos'), DB::raw('SUM(dp.monto) as total'))
            ->orderBy('cl.datos')
            ->get();

        return ['por_articulo' => $porArticulo, 'por_cliente' => $porCliente];
    }
}
