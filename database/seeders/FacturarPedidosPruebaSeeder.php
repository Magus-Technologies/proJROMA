<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Paso 2 del escenario de prueba: convierte a BOLETA los pedidos creados
 * por PruebaIntegralSeeder (observación "PEDIDO TEST DESPACHO"), replicando
 * la acción "Facturar" del panel: crea la venta con el correlativo real,
 * copia las líneas y marca la cotización como Facturada (estado 3 + id_venta).
 *
 * Después de esto, los pedidos ya aparecen en "Armar Despacho".
 *
 * Idempotente: solo convierte pedidos activos sin venta vinculada.
 * Ejecutar:  php artisan db:seed --class=FacturarPedidosPruebaSeeder --force
 */
class FacturarPedidosPruebaSeeder extends Seeder
{
    /** Ajustar según la empresa donde se hizo la prueba. */
    private const EMPRESA  = 12;
    private const SUCURSAL = 1;
    private const ID_TIDO  = 1; // Boleta

    public function run(): void
    {
        $cotis = DB::table('cotizaciones')
            ->where('id_empresa', self::EMPRESA)
            ->where('observacion', 'PEDIDO TEST DESPACHO')
            ->where('estado', '1')
            ->whereNull('id_venta')
            ->orderBy('numero')
            ->get();

        if ($cotis->isEmpty()) {
            $this->command?->warn('No hay pedidos de prueba pendientes de facturar.');

            return;
        }

        $victorId = (int) DB::table('usuarios')
            ->where('id_empresa', self::EMPRESA)
            ->where('usuario', 'VICTOR')
            ->value('usuario_id');

        DB::transaction(function () use ($cotis, $victorId): void {
            $tido = DB::table('documentos_empresas')
                ->where('id_empresa', self::EMPRESA)
                ->where('sucursal', self::SUCURSAL)
                ->where('id_tido', self::ID_TIDO)
                ->lockForUpdate()
                ->first();

            if (! $tido) {
                throw new \RuntimeException('No hay serie de boleta (id_tido 1) configurada para la empresa ' . self::EMPRESA . '.');
            }

            $numero = (int) $tido->numero;

            foreach ($cotis as $coti) {
                $numero++;

                $idVenta = DB::table('ventas')->insertGetId([
                    'id_tido'           => self::ID_TIDO,
                    'id_tipo_pago'      => $coti->id_tipo_pago,
                    'fecha_emision'     => now()->toDateString(),
                    'fecha_vencimiento' => now()->toDateString(),
                    'dias_pagos'        => $coti->dias_pagos,
                    'direccion'         => $coti->direccion ?? '-',
                    'serie'             => $tido->serie,
                    'numero'            => $numero,
                    'id_cliente'        => $coti->id_cliente,
                    'total'             => $coti->total,
                    'igv'               => round($coti->total - ($coti->total / 1.18), 2),
                    'apli_igv'          => '1',
                    'estado'            => '1',
                    'enviado_sunat'     => '0',
                    'id_empresa'        => self::EMPRESA,
                    'sucursal'          => self::SUCURSAL,
                    'id_vendedor'       => $victorId ?: $coti->id_usuario,
                    'observacion'       => 'Convertido de cotización N° ' . $coti->numero,
                    'pagado'            => '0',
                    'id_coti'           => $coti->cotizacion_id,
                ]);

                $lineas = DB::table('productos_cotis')->where('id_coti', $coti->cotizacion_id)->get();
                foreach ($lineas as $prod) {
                    DB::table('productos_ventas')->insert([
                        'id_venta'     => $idVenta,
                        'id_producto'  => $prod->id_producto,
                        'cantidad'     => $prod->cantidad,
                        'precio'       => $prod->precio,
                        'costo'        => $prod->costo ?? 0,
                        'medida'       => $prod->medida ?? '',
                        'presenta'     => $prod->presenta ?? '',
                        'presenta_cnt' => $prod->presenta_cnt ?? 0,
                    ]);
                }

                DB::table('cotizaciones')->where('cotizacion_id', $coti->cotizacion_id)
                    ->update(['estado' => '3', 'id_venta' => $idVenta]);

                $this->command?->info("Pedido {$coti->numero} → {$tido->serie}-" . str_pad((string) $numero, 8, '0', STR_PAD_LEFT));
            }

            DB::table('documentos_empresas')
                ->where('id_empresa', self::EMPRESA)
                ->where('sucursal', self::SUCURSAL)
                ->where('id_tido', self::ID_TIDO)
                ->update(['numero' => $numero]);
        });

        $this->command?->info($cotis->count() . ' pedidos facturados. Ya aparecen en "Armar Despacho" (RUTA TEST DESPACHO).');
    }
}
