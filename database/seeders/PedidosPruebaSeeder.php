<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Escenario de prueba del flujo de despacho:
 *
 *  "Hoy viernes: todos los pedidos (cotizaciones NV) del día; hasta las 6 pm
 *   se carga la mercadería al vehículo según su capacidad."
 *
 * Crea:
 *  - Ruta "Ruta SJL (PRUEBA)" con los mercados reales: Canto Chico I y II,
 *    Milagros y Canto Rey I (donde sí hay clientes).
 *  - 8 pedidos de HOY para clientes reales de esos mercados, usando los
 *    productos TEST-xxx (que tienen peso). Horas variadas: la mayoría antes
 *    de las 6 pm; el pedido de las 18:30 y un AUMENTO de las 19:15 quedan
 *    después del corte, para probar la separación de "lo faltante".
 *
 * Ejecutar: php artisan db:seed --class=PedidosPruebaSeeder
 */
class PedidosPruebaSeeder extends Seeder
{
    private const EMPRESA  = 12;
    private const SUCURSAL = 1;
    private const TIDO     = 6;   // Nota de Venta = pedido
    private const USUARIO  = 40;

    private const MERCADOS_RUTA = [8, 9, 13, 16]; // Canto Chico I/II, Milagros, Canto Rey I

    public function run(): void
    {
        if (DB::table('cotizaciones')->where('id_empresa', self::EMPRESA)
            ->where('observacion', 'like', 'PRUEBA DESPACHO%')->exists()) {
            $this->command?->warn('Ya existen pedidos de prueba (observacion "PRUEBA DESPACHO..."). No se duplican.');
            return;
        }

        // 1) Ruta de prueba con mercados reales.
        $idRuta = DB::table('tms_rutas')->where('id_empresa', self::EMPRESA)
            ->where('nombre', 'Ruta SJL (PRUEBA)')->value('id');
        if (! $idRuta) {
            $idRuta = DB::table('tms_rutas')->insertGetId([
                'id_empresa' => self::EMPRESA, 'sucursal' => self::SUCURSAL,
                'nombre' => 'Ruta SJL (PRUEBA)',
                'descripcion' => 'Ruta de prueba: mercados reales de San Juan de Lurigancho',
                'estado' => 1, 'created_at' => now(), 'updated_at' => now(),
            ]);
            $orden = 1;
            foreach (self::MERCADOS_RUTA as $idMercado) {
                DB::table('tms_ruta_puntos')->insert([
                    'id_ruta' => $idRuta, 'tipo' => 'MERCADO',
                    'id_mercado' => $idMercado, 'id_cliente' => null, 'orden' => $orden++,
                ]);
            }
        }

        // 2) Productos TEST con su peso.
        $prod = DB::table('productos')->where('id_empresa', self::EMPRESA)
            ->where('codigo', 'like', 'TEST-%')
            ->get(['id_producto', 'codigo', 'descripcion', 'precio', 'costo', 'peso_bruto', 'medida'])
            ->keyBy('codigo');
        if ($prod->isEmpty()) {
            $this->command?->error('No hay productos TEST-xxx. Ejecuta antes ProductosPruebaSeeder.');
            return;
        }

        // 3) Un cliente real por cada mercado de la ruta (2 por mercado).
        $clientes = [];
        foreach (self::MERCADOS_RUTA as $idMercado) {
            $ids = DB::table('clientes')->where('id_empresa', self::EMPRESA)
                ->where('mercado', (string) $idMercado)->orderBy('id_cliente')->limit(2)->pluck('id_cliente')->all();
            foreach ($ids as $id) $clientes[] = [$id, $idMercado];
        }
        if (count($clientes) < 8) {
            $this->command?->error('No hay suficientes clientes en los mercados de la ruta.');
            return;
        }

        $hoy = now()->toDateString();

        // 4) Pedidos: [hora, líneas => [codigo, cantidad]]
        //    El ejemplo de los 35 kg: pedido #1 (SPAGHETTI 1K x20 + 1/2K x20 + 250G x20 = 35 kg).
        $pedidos = [
            ['08:15', [['TEST-001', 20], ['TEST-002', 20], ['TEST-003', 20]]],                       // 35.00 kg
            ['08:40', [['TEST-008', 10], ['TEST-010', 25]]],                                          // 75.00 kg
            ['09:30', [['TEST-004', 15], ['TEST-005', 10], ['TEST-006', 20]]],                        // 25.00 kg
            ['10:05', [['TEST-013', 24], ['TEST-014', 6]]],                                           // 49.68 kg
            ['11:20', [['TEST-015', 48], ['TEST-016', 48], ['TEST-012', 12]]],                        // 39.36 kg
            ['15:45', [['TEST-011', 8], ['TEST-009', 20]]],                                           // 55.00 kg
            ['17:50', [['TEST-001', 30], ['TEST-007', 20]]],                                          // 50.00 kg — justo antes del corte
            ['18:30', [['TEST-008', 20], ['TEST-014', 10]]],                                          // 146.00 kg — DESPUÉS de las 6 pm
        ];

        $doc = DB::table('documentos_empresas')->where('id_empresa', self::EMPRESA)
            ->where('sucursal', self::SUCURSAL)->where('id_tido', self::TIDO)->first();
        $numero = (int) $doc->numero;

        $creadas = [];
        foreach ($pedidos as $i => [$hora, $lineas]) {
            [$idCliente, ] = $clientes[$i];
            $numero++;

            $total = 0;
            foreach ($lineas as [$codigo, $cant]) $total += $prod[$codigo]->precio * $cant;

            $idCoti = DB::table('cotizaciones')->insertGetId([
                'numero'         => $numero,
                'id_tido'        => self::TIDO,
                'id_tipo_pago'   => 1,
                'fecha'          => $hoy,
                'direccion'      => '1',
                'id_cliente'     => $idCliente,
                'total'          => round($total, 2),
                'estado'         => '1',
                'id_empresa'     => self::EMPRESA,
                'sucursal'       => self::SUCURSAL,
                'usar_precio'    => 1,
                'moneda'         => 1,
                'id_usuario'     => self::USUARIO,
                'observacion'    => 'PRUEBA DESPACHO — pedido ' . ($i + 1) . ' (' . $hora . ')',
                'fecha_registro' => "$hoy $hora:00",
            ], 'cotizacion_id');

            foreach ($lineas as [$codigo, $cant]) {
                DB::table('productos_cotis')->insert([
                    'id_coti'        => $idCoti,
                    'id_producto'    => $prod[$codigo]->id_producto,
                    'cantidad'       => $cant,
                    'precio'         => $prod[$codigo]->precio,
                    'costo'          => $prod[$codigo]->costo,
                    'medida'         => $prod[$codigo]->medida ?? 'Unidad',
                    'presenta'       => 1,
                    'presenta_cnt'   => 1,
                    'fecha_registro' => "$hoy $hora:00",
                    'id_usuario'     => self::USUARIO,
                ]);
            }
            $creadas[] = $idCoti;
        }

        // 5) AUMENTO fuera de horario: al pedido de las 08:15 el cliente pide
        //    10 spaghetti 1K más a las 19:15 → línea nueva con su propia hora.
        DB::table('productos_cotis')->insert([
            'id_coti'        => $creadas[0],
            'id_producto'    => $prod['TEST-001']->id_producto,
            'cantidad'       => 10,
            'precio'         => $prod['TEST-001']->precio,
            'costo'          => $prod['TEST-001']->costo,
            'medida'         => 'Unidad',
            'presenta'       => 1,
            'presenta_cnt'   => 1,
            'fecha_registro' => "$hoy 19:15:00",
            'id_usuario'     => self::USUARIO,
        ]);
        DB::table('cotizaciones')->where('cotizacion_id', $creadas[0])
            ->increment('total', round($prod['TEST-001']->precio * 10, 2));

        DB::table('documentos_empresas')->where('id_empresa', self::EMPRESA)
            ->where('sucursal', self::SUCURSAL)->where('id_tido', self::TIDO)
            ->update(['numero' => $numero]);

        $this->command?->info('Ruta de prueba id ' . $idRuta . ' + 8 pedidos de hoy creados (cotizaciones: ' . implode(', ', $creadas) . ').');
    }
}
