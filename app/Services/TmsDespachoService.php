<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * El despacho reparte VENTAS: boletas, facturas y notas de venta por igual.
 *
 * Antes partía de las cotizaciones de tipo pedido ya convertidas, así que una
 * venta hecha directo en mostrador no se podía repartir nunca. Ahora la línea
 * del despacho apunta a la venta y lo único que la deja afuera es estar
 * anulada o ya estar en otro despacho.
 */
class TmsDespachoService
{
    /** id_tido que representa un "pedido" (Nota de Venta). */
    public const TIDO_PEDIDO = 6;

    /** Estado de venta anulada: esas no se reparten. */
    public const ESTADO_ANULADA = '0';

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

    /** Peso por venta = Σ(cantidad × peso_bruto) de sus líneas. */
    public function pesosPorVenta(array $ventaIds): array
    {
        if (!$ventaIds) return [];

        return DB::table('productos_ventas as pv')
            ->join('productos as p', 'p.id_producto', '=', 'pv.id_producto')
            ->whereIn('pv.id_venta', $ventaIds)
            ->groupBy('pv.id_venta')
            ->select('pv.id_venta', DB::raw('SUM(pv.cantidad * COALESCE(p.peso_bruto, 0)) as peso'))
            ->pluck('peso', 'pv.id_venta')->all();
    }

    /** Ventas ya tomadas por un despacho no anulado. */
    public function pedidosYaDespachados(): array
    {
        return DB::table('tms_despacho_pedidos as dp')
            ->join('tms_despachos as d', 'd.id', '=', 'dp.id_despacho')
            ->where('d.estado', '<>', 'ANULADO')
            ->whereNotNull('dp.id_venta')
            ->pluck('dp.id_venta')->all();
    }

    /**
     * Ventas de la ruta pendientes de reparto en un rango de fechas, con su peso.
     *
     * Entra cualquier tipo de documento —boleta, factura o nota de venta— y no
     * importa si ya se envió a SUNAT: lo que se reparte es la mercadería.
     */
    public function pedidosPendientes(int $idRuta, string $desde, string $hasta, int $empresa): Collection
    {
        $clientes = $this->clientesDeRuta($idRuta, $empresa);
        if (!$clientes) return collect();

        $yaDespachados = $this->pedidosYaDespachados();

        $ventas = DB::table('ventas as v')
            ->join('clientes as cl', 'cl.id_cliente', '=', 'v.id_cliente')
            ->leftJoin('tms_mercados as m', 'm.id', '=', 'cl.mercado')
            ->leftJoin('documentos_sunat as ds', 'ds.id_tido', '=', 'v.id_tido')
            ->where('v.id_empresa', $empresa)
            ->whereIn('v.id_cliente', $clientes)
            ->whereDate('v.fecha_emision', '>=', $desde)
            ->whereDate('v.fecha_emision', '<=', $hasta)
            ->where('v.estado', '<>', self::ESTADO_ANULADA)
            ->when($yaDespachados, fn ($q) => $q->whereNotIn('v.id_venta', $yaDespachados))
            ->orderBy('cl.mercado')
            ->select(
                'v.id_venta', 'v.serie', 'v.numero', 'v.fecha_emision as fecha', 'v.total', 'v.id_cliente',
                'cl.mercado as id_mercado', 'cl.datos as cliente',
                DB::raw("COALESCE(ds.abreviatura, '') as tipo_doc"),
                DB::raw("COALESCE(m.nombre, CASE WHEN cl.mercado > 0 THEN CONCAT('Mercado ', cl.mercado) ELSE 'Tienda' END) as mercado")
            )
            ->get();

        $pesos = $this->pesosPorVenta($ventas->pluck('id_venta')->all());
        $ventas->each(function ($v) use ($pesos): void {
            $v->peso = round((float) ($pesos[$v->id_venta] ?? 0), 2);
            $v->documento = trim($v->serie . '-' . str_pad((string) $v->numero, 8, '0', STR_PAD_LEFT), '-');
        });

        return $ventas;
    }

    /**
     * Crea un despacho a partir de una ruta, fecha, vehículo, conductor y lista
     * de ventas. Devuelve ['id', 'peso_total', 'advertencias'] o lanza \RuntimeException.
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

        $ventaIds = array_values(array_map('intval', $d['pedidos'] ?? []));
        if (!$ventaIds) throw new \RuntimeException('Selecciona al menos una venta.');

        $choque = array_intersect($ventaIds, $this->pedidosYaDespachados());
        if ($choque) throw new \RuntimeException('Algunas ventas ya están en otro despacho.');

        $rows = DB::table('ventas as v')
            ->join('clientes as cl', 'cl.id_cliente', '=', 'v.id_cliente')
            ->leftJoin('cotizaciones as c', 'c.id_venta', '=', 'v.id_venta')
            ->where('v.id_empresa', $empresa)
            ->whereIn('v.id_venta', $ventaIds)
            ->select('v.id_venta', 'v.serie', 'v.numero', 'v.estado', 'v.total', 'v.id_cliente',
                'cl.mercado as id_mercado', 'c.cotizacion_id')
            ->get();
        if ($rows->isEmpty()) throw new \RuntimeException('No hay ventas válidas.');

        $anuladas = $rows->filter(fn ($row) => (string) $row->estado === self::ESTADO_ANULADA)
            ->map(fn ($row) => $row->serie . '-' . $row->numero);
        if ($anuladas->isNotEmpty()) {
            throw new \RuntimeException('Ventas anuladas: ' . $anuladas->implode(', ') . '. No se pueden repartir.');
        }

        $pesos = $this->pesosPorVenta($rows->pluck('id_venta')->all());
        $pesoTotal = 0.0;
        foreach ($rows as $row) { $pesoTotal += (float) ($pesos[$row->id_venta] ?? 0); }

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
                    'id_venta'       => $row->id_venta,
                    // El pedido que la originó, si vino de uno: sirve de rastro.
                    'id_cotizacion'  => $row->cotizacion_id,
                    'id_cliente'     => $row->id_cliente,
                    'id_mercado'     => $row->id_mercado ?: null,
                    'peso'           => round((float) ($pesos[$row->id_venta] ?? 0), 2),
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

    /**
     * Agrega pedidos a un despacho existente (aumento de última hora).
     * Mismas reglas que crear: solo facturados, no repetidos, y avisa
     * si el peso supera la capacidad del vehículo.
     *
     * @return array{agregados:int, peso_total:float, advertencias:array}
     */
    public function agregarPedidos(int $idDespacho, array $pedidosIds, int $empresa): array
    {
        $despacho = DB::table('tms_despachos')->where('id', $idDespacho)->where('id_empresa', $empresa)->first();
        if (! $despacho) throw new \RuntimeException('Despacho no encontrado.');

        if (! in_array($despacho->estado, ['PLANIFICADO', 'CARGADO'], true)) {
            throw new \RuntimeException("No se pueden agregar pedidos a un despacho {$despacho->estado}.");
        }

        $ventaIds = array_values(array_unique(array_map('intval', $pedidosIds)));
        if (! $ventaIds) throw new \RuntimeException('Selecciona al menos una venta.');

        $choque = array_intersect($ventaIds, $this->pedidosYaDespachados());
        if ($choque) throw new \RuntimeException('Algunas ventas ya están en este u otro despacho.');

        $rows = DB::table('ventas as v')
            ->join('clientes as cl', 'cl.id_cliente', '=', 'v.id_cliente')
            ->leftJoin('cotizaciones as c', 'c.id_venta', '=', 'v.id_venta')
            ->where('v.id_empresa', $empresa)
            ->whereIn('v.id_venta', $ventaIds)
            ->select('v.id_venta', 'v.serie', 'v.numero', 'v.estado', 'v.total', 'v.id_cliente',
                'cl.mercado as id_mercado', 'c.cotizacion_id')
            ->get();
        if ($rows->isEmpty()) throw new \RuntimeException('No hay ventas válidas.');

        $anuladas = $rows->filter(fn ($row) => (string) $row->estado === self::ESTADO_ANULADA)
            ->map(fn ($row) => $row->serie . '-' . $row->numero);
        if ($anuladas->isNotEmpty()) {
            throw new \RuntimeException('Ventas anuladas: ' . $anuladas->implode(', ') . '. No se pueden repartir.');
        }

        $pesos = $this->pesosPorVenta($rows->pluck('id_venta')->all());
        $pesoNuevo = 0.0;
        foreach ($rows as $row) { $pesoNuevo += (float) ($pesos[$row->id_venta] ?? 0); }
        $pesoTotal = round((float) $despacho->peso_total + $pesoNuevo, 2);

        $advertencias = [];
        $veh = DB::table('tms_vehiculos')->where('id', $despacho->id_vehiculo)->first();
        if ($veh && $pesoTotal > (float) $veh->capacidad_kg) {
            $advertencias[] = 'Con el aumento, el peso (' . $pesoTotal . ' kg) supera la capacidad del vehículo (' . round((float) $veh->capacidad_kg, 2) . ' kg).';
        }

        DB::transaction(function () use ($idDespacho, $rows, $pesos, $pesoTotal) {
            $orden = (int) DB::table('tms_despacho_pedidos')->where('id_despacho', $idDespacho)->max('orden');

            $detalles = [];
            foreach ($rows as $row) {
                $detalles[] = [
                    'id_despacho'    => $idDespacho,
                    'id_venta'       => $row->id_venta,
                    'id_cotizacion'  => $row->cotizacion_id,
                    'id_cliente'     => $row->id_cliente,
                    'id_mercado'     => $row->id_mercado ?: null,
                    'peso'           => round((float) ($pesos[$row->id_venta] ?? 0), 2),
                    'monto'          => round((float) $row->total, 2),
                    'orden'          => ++$orden,
                    'estado_entrega' => 'PENDIENTE',
                ];
            }
            DB::table('tms_despacho_pedidos')->insert($detalles);

            DB::table('tms_despachos')->where('id', $idDespacho)
                ->update(['peso_total' => $pesoTotal, 'updated_at' => now()]);
        });

        return ['agregados' => $rows->count(), 'peso_total' => $pesoTotal, 'advertencias' => $advertencias];
    }

    /**
     * Consolidados del reporte RES DESPACHO.
     *
     * @param array $mercadoIds  Filtra a pedidos de estos mercados (vacío = todos).
     * @param array $medidas     Filtra a líneas con estas unidades de medida (vacío = todas).
     */
    public function reporte(int $idDespacho, array $mercadoIds = [], array $medidas = []): array
    {
        $ventaIds = DB::table('tms_despacho_pedidos')
            ->where('id_despacho', $idDespacho)
            ->when($mercadoIds, fn ($q) => $q->whereIn('id_mercado', $mercadoIds))
            ->whereNotNull('id_venta')
            ->pluck('id_venta')->all();

        $porArticulo = collect();
        if ($ventaIds) {
            $porArticulo = DB::table('productos_ventas as pv')
                ->join('productos as p', 'p.id_producto', '=', 'pv.id_producto')
                ->whereIn('pv.id_venta', $ventaIds)
                ->when($medidas, fn ($q) => $q->whereIn('pv.medida', $medidas))
                ->groupBy('p.id_producto', 'p.codigo', 'p.descripcion')
                ->select('p.id_producto', 'p.codigo', 'p.descripcion',
                    DB::raw('SUM(pv.cantidad) as cantidad'),
                    DB::raw('SUM(pv.cantidad * COALESCE(p.peso_bruto, 0)) as kilos'))
                ->orderBy('p.descripcion')
                ->get();

            // Desglose por tamaño de pedido: cuántos pedidos llevan X cantidad
            // de cada producto (el almacén arma los bultos por pedido).
            $porTamano = DB::table('productos_ventas as pv')
                ->join('productos as p', 'p.id_producto', '=', 'pv.id_producto')
                ->whereIn('pv.id_venta', $ventaIds)
                ->when($medidas, fn ($q) => $q->whereIn('pv.medida', $medidas))
                ->groupBy('p.id_producto', 'pv.cantidad', 'pv.medida')
                ->select('p.id_producto', 'pv.medida',
                    'pv.cantidad as tamano',
                    DB::raw('COUNT(*) as pedidos'),
                    DB::raw('SUM(pv.cantidad) as total'))
                ->orderBy('pv.cantidad')
                ->get()
                ->groupBy('id_producto');

            $porArticulo->each(fn ($a) => $a->detalle = $porTamano->get($a->id_producto, collect()));
        }

        $porCliente = DB::table('tms_despacho_pedidos as dp')
            ->leftJoin('clientes as cl', 'cl.id_cliente', '=', 'dp.id_cliente')
            ->where('dp.id_despacho', $idDespacho)
            ->when($mercadoIds, fn ($q) => $q->whereIn('dp.id_mercado', $mercadoIds))
            ->groupBy('dp.id_cliente', 'cl.documento', 'cl.datos')
            ->select('cl.documento', DB::raw("COALESCE(cl.datos, '-') as denominacion"),
                DB::raw('COUNT(*) as pedidos'), DB::raw('SUM(dp.peso) as kilos'), DB::raw('SUM(dp.monto) as total'))
            ->orderBy('cl.datos')
            ->get();

        // ── Por mercado (productos agrupados por mercado) ────────────────
        $porMercado = collect();
        if ($ventaIds) {
            $porMercado = DB::table('productos_ventas as pv')
                ->join('productos as p', 'p.id_producto', '=', 'pv.id_producto')
                ->join('tms_despacho_pedidos as dp', 'dp.id_venta', '=', 'pv.id_venta')
                ->join('tms_mercados as m', 'm.id', '=', 'dp.id_mercado')
                ->where('dp.id_despacho', $idDespacho)
                ->when($mercadoIds, fn ($q) => $q->whereIn('dp.id_mercado', $mercadoIds))
                ->when($medidas, fn ($q) => $q->whereIn('pv.medida', $medidas))
                ->groupBy('m.id', 'm.nombre', 'p.id_producto', 'p.codigo', 'p.descripcion')
                ->select('m.id as mercado_id', 'm.nombre as mercado_nombre',
                    'p.codigo', 'p.descripcion',
                    DB::raw('SUM(pv.cantidad) as cantidad'),
                    DB::raw('SUM(pv.cantidad * COALESCE(p.peso_bruto, 0)) as kilos'))
                ->orderBy('m.nombre')
                ->orderBy('p.descripcion')
                ->get()
                ->groupBy('mercado_nombre');
        }

        return [
            'por_articulo' => $porArticulo,
            'por_cliente'  => $porCliente,
            'por_mercado'  => $porMercado,
        ];
    }
}
