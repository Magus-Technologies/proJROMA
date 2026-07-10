<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Escenario de prueba integral del flujo comercial completo:
 *
 *   1. Usuario VICTOR (vendedor, clave 123456) con su caja hija.
 *   2. 5 productos con peso (DESP-001..005).
 *   3. Mercado "MERCADO TEST DESPACHO" + 20 clientes en él.
 *   4. Ruta "RUTA TEST DESPACHO" con ese mercado como punto.
 *   5. Conductor y vehículo de prueba (camión 8,000 kg).
 *   6. 20 pedidos (NV) a nombre de VICTOR, SIN facturar.
 *
 * Con eso se prueba el flujo a mano: facturar los pedidos → armar el
 * despacho → hoja de carga / guías / boletas masivas → cobrar en
 * Cuentas por Cobrar → cerrar caja con conteo → aprobar el cierre.
 *
 * Idempotente: cada bloque verifica si ya existe antes de crear.
 * Ejecutar:  php artisan db:seed --class=PruebaIntegralSeeder --force
 */
class PruebaIntegralSeeder extends Seeder
{
    /** Ajustar según la empresa/sucursal donde se hará la prueba. */
    private const EMPRESA  = 12;
    private const SUCURSAL = 1;

    public function run(): void
    {
        $ahora = now();
        $hoy   = now()->toDateString();

        // ── 0. Correlativo de pedidos (NV): sin él no se puede seguir ────
        $doc = DB::table('documentos_empresas')
            ->where('id_empresa', self::EMPRESA)
            ->where('sucursal', self::SUCURSAL)
            ->where('id_tido', 6)
            ->first();
        if (! $doc) {
            $this->command?->error('No hay serie NV (id_tido 6) configurada para la empresa ' . self::EMPRESA . '. Configúrala en Correlativos y vuelve a correr.');

            return;
        }

        // ── 1. Usuario VICTOR (vendedor) ─────────────────────────────────
        $rolVendedor = (int) (DB::table('roles')->where('nombre', 'VENDEDOR')->value('rol_id') ?? 3);

        $victorId = DB::table('usuarios')
            ->where('id_empresa', self::EMPRESA)
            ->where('usuario', 'VICTOR')
            ->value('usuario_id');

        if (! $victorId) {
            $victorId = DB::table('usuarios')->insertGetId([
                'id_empresa'       => self::EMPRESA,
                'id_rol'           => $rolVendedor,
                'usuario'          => 'VICTOR',
                'clave'            => Hash::make('123456'),
                'num_doc'          => '99887766',
                'nombres'          => 'VICTOR RAUL',
                'apellidos'        => 'CANCHARI RIQUI',
                'sucursal'         => self::SUCURSAL,
                'estado'           => '1',
                'available_status' => 1,
                'fecha_inicio'     => $hoy,
                'fecha_salida'     => '2030-12-31',
                'funciones'        => '',
            ]);
            $this->command?->info("Usuario VICTOR creado (id {$victorId}, clave 123456).");
        } else {
            $this->command?->warn("Usuario VICTOR ya existe (id {$victorId}).");
        }

        // ── 2. Caja hija de VICTOR (bajo la primera caja principal) ──────
        $tieneCaja = DB::table('cajas')
            ->where('id_empresa', self::EMPRESA)
            ->where('id_usuario_responsable', $victorId)
            ->whereNotNull('id_caja_padre')
            ->exists();

        if (! $tieneCaja) {
            $idPadre = DB::table('cajas')
                ->where('id_empresa', self::EMPRESA)
                ->whereNull('id_caja_padre')
                ->where('estado', 'ACTIVA')
                ->value('id');

            if (! $idPadre) {
                $idPadre = DB::table('cajas')->insertGetId([
                    'id_empresa'   => self::EMPRESA,
                    'sucursal'     => self::SUCURSAL,
                    'nombre'       => 'Caja Principal',
                    'saldo_actual' => 0,
                    'moneda'       => 'PEN',
                    'estado'       => 'ACTIVA',
                ]);
            }

            DB::table('cajas')->insert([
                'id_empresa'             => self::EMPRESA,
                'sucursal'               => self::SUCURSAL,
                'nombre'                 => 'CAJA VICTOR (PRUEBA)',
                'id_caja_padre'          => $idPadre,
                'id_usuario_responsable' => $victorId,
                'saldo_actual'           => 0,
                'moneda'                 => 'PEN',
                'estado'                 => 'ACTIVA',
            ]);
            $this->command?->info('Caja hija de VICTOR creada.');
        }

        // ── 3. Productos con peso ────────────────────────────────────────
        $defs = [
            ['DESP-001', 'ARROZ EXTRA SACO 10KG (TEST)',      10.50,  42.00,  35.00],
            ['DESP-002', 'AZUCAR RUBIA BOLSA 5KG (TEST)',      5.20,  24.50,  19.00],
            ['DESP-003', 'ACEITE VEGETAL CAJA 12X1L (TEST)',  12.80,  96.00,  78.00],
            ['DESP-004', 'HARINA PREPARADA BOLSA 1KG (TEST)',  1.10,   6.50,   4.80],
            ['DESP-005', 'LECHE EVAPORADA PACK 48UND (TEST)', 20.40, 168.00, 140.00],
        ];

        $productos = [];
        foreach ($defs as [$codigo, $desc, $peso, $precio, $costo]) {
            $id = DB::table('productos')
                ->where('id_empresa', self::EMPRESA)
                ->where('codigo', $codigo)
                ->value('id_producto');

            if (! $id) {
                $id = DB::table('productos')->insertGetId([
                    'cod_barra'     => '',
                    'descripcion'   => $desc,
                    'precio'        => $precio,
                    'costo'         => $costo,
                    'cantidad'      => 1000,
                    'iscbp'         => 0,
                    'id_empresa'    => self::EMPRESA,
                    'sucursal'      => self::SUCURSAL,
                    'ultima_salida' => $hoy,
                    'codsunat'      => '-',
                    'usar_barra'    => 0,
                    'peso_bruto'    => $peso,
                    'estado'        => '1',
                    'almacen'       => 1,
                    'precio2'       => 0,
                    'precio3'       => 0,
                    'precio4'       => 0,
                    'codigo'        => $codigo,
                    'activo'        => 1,
                    'medida'        => 'Unidad',
                ]);
            }
            $productos[] = ['id' => $id, 'precio' => $precio, 'costo' => $costo];
        }
        $this->command?->info('Productos DESP-001..005 listos.');

        // ── 4. Mercado + 20 clientes ─────────────────────────────────────
        $idMercado = DB::table('tms_mercados')
            ->where('id_empresa', self::EMPRESA)
            ->where('nombre', 'MERCADO TEST DESPACHO')
            ->value('id');

        if (! $idMercado) {
            $idMercado = DB::table('tms_mercados')->insertGetId([
                'id_empresa' => self::EMPRESA,
                'sucursal'   => self::SUCURSAL,
                'nombre'     => 'MERCADO TEST DESPACHO',
                'direccion'  => 'AV. DE PRUEBA 123',
                'distrito'   => 'AYACUCHO',
                'estado'     => 1,
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ]);
        }

        $clientes = [];
        for ($i = 1; $i <= 20; $i++) {
            $n   = str_pad((string) $i, 2, '0', STR_PAD_LEFT);
            $dni = '9955' . str_pad((string) $i, 4, '0', STR_PAD_LEFT);

            $idCliente = DB::table('clientes')
                ->where('id_empresa', self::EMPRESA)
                ->where('documento', $dni)
                ->value('id_cliente');

            if (! $idCliente) {
                $idCliente = DB::table('clientes')->insertGetId([
                    'documento'  => $dni,
                    'datos'      => "CLIENTE TEST DESPACHO {$n}",
                    'direccion'  => "PUESTO {$n} - MERCADO TEST",
                    'telefono'   => '9990000' . $n,
                    'id_empresa' => self::EMPRESA,
                    'mercado'    => $idMercado,
                ]);
            }
            $clientes[] = $idCliente;
        }
        $this->command?->info("Mercado {$idMercado} con 20 clientes listos.");

        // ── 5. Ruta con el mercado como punto ────────────────────────────
        $idRuta = DB::table('tms_rutas')
            ->where('id_empresa', self::EMPRESA)
            ->where('nombre', 'RUTA TEST DESPACHO')
            ->value('id');

        if (! $idRuta) {
            $idRuta = DB::table('tms_rutas')->insertGetId([
                'id_empresa'  => self::EMPRESA,
                'sucursal'    => self::SUCURSAL,
                'nombre'      => 'RUTA TEST DESPACHO',
                'descripcion' => 'Ruta de prueba para el flujo integral',
                'estado'      => 1,
                'created_at'  => $ahora,
                'updated_at'  => $ahora,
            ]);
            DB::table('tms_ruta_puntos')->insert([
                'id_ruta'    => $idRuta,
                'tipo'       => 'MERCADO',
                'id_mercado' => $idMercado,
                'id_cliente' => null,
                'orden'      => 1,
            ]);
        }

        // ── 6. Conductor y vehículo de prueba (para armar el despacho) ───
        $idTipoCamion = DB::table('tms_tipos_vehiculo')->where('nombre', 'CAMION')->value('id')
            ?? DB::table('tms_tipos_vehiculo')->insertGetId(['nombre' => 'CAMION']);

        if (! DB::table('tms_vehiculos')->where('id_empresa', self::EMPRESA)->where('placa', 'TST-999')->exists()) {
            DB::table('tms_vehiculos')->insert([
                'id_empresa'        => self::EMPRESA,
                'sucursal'          => self::SUCURSAL,
                'placa'             => 'TST-999',
                'id_tipo'           => $idTipoCamion,
                'marca'             => 'Volvo',
                'modelo'            => 'FH 440 (PRUEBA)',
                'anio'              => 2022,
                'capacidad_kg'      => 8000,
                'tara_kg'           => 5000,
                'soat_vence'        => now()->addYear()->toDateString(),
                'rev_tecnica_vence' => now()->addYear()->toDateString(),
                'estado'            => 1,
                'created_at'        => $ahora,
                'updated_at'        => $ahora,
            ]);
        }

        if (! DB::table('tms_conductores')->where('id_empresa', self::EMPRESA)->where('documento', '99887755')->exists()) {
            DB::table('tms_conductores')->insert([
                'id_empresa'         => self::EMPRESA,
                'sucursal'           => self::SUCURSAL,
                'nombres'            => 'CONDUCTOR PRUEBA INTEGRAL',
                'documento'          => '99887755',
                'licencia'           => 'Q99887755',
                'licencia_categoria' => 'A-III',
                'licencia_vence'     => now()->addYear()->toDateString(),
                'telefono'           => '999000111',
                'estado'             => 1,
                'created_at'         => $ahora,
                'updated_at'         => $ahora,
            ]);
        }
        $this->command?->info('Ruta, vehículo TST-999 y conductor de prueba listos.');

        // ── 7. Veinte pedidos (NV) sin facturar, a nombre de VICTOR ──────
        if (DB::table('cotizaciones')
            ->where('id_empresa', self::EMPRESA)
            ->where('observacion', 'PEDIDO TEST DESPACHO')
            ->exists()) {
            $this->command?->warn('Ya existen pedidos "PEDIDO TEST DESPACHO": no se duplican.');

            return;
        }

        DB::transaction(function () use ($clientes, $productos, $victorId, $ahora, $hoy): void {
            $numero = (int) DB::table('documentos_empresas')
                ->where('id_empresa', self::EMPRESA)
                ->where('sucursal', self::SUCURSAL)
                ->where('id_tido', 6)
                ->lockForUpdate()
                ->value('numero');

            foreach ($clientes as $idx => $idCliente) {
                $numero++;

                $cantLineas = ($idx % 3) + 1;
                $total = 0;
                $lineas = [];
                for ($l = 0; $l < $cantLineas; $l++) {
                    $p = $productos[($idx + $l) % count($productos)];
                    $cantidad = 2 + (($idx + $l * 3) % 9); // 2..10
                    $total += $cantidad * $p['precio'];
                    $lineas[] = [$p, $cantidad];
                }

                $idCoti = DB::table('cotizaciones')->insertGetId([
                    'numero'         => $numero,
                    'id_tido'        => 6,
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
                    'id_usuario'     => $victorId,
                    'observacion'    => 'PEDIDO TEST DESPACHO',
                    'fecha_registro' => $ahora,
                ]);

                foreach ($lineas as [$p, $cantidad]) {
                    DB::table('productos_cotis')->insert([
                        'id_producto'    => $p['id'],
                        'id_coti'        => $idCoti,
                        'cantidad'       => $cantidad,
                        'precio'         => $p['precio'],
                        'costo'          => $p['costo'],
                        'medida'         => 'Unidad',
                        'presenta'       => 1,
                        'presenta_cnt'   => 1,
                        'fecha_registro' => $ahora,
                        'id_usuario'     => $victorId,
                    ]);
                }
            }

            DB::table('documentos_empresas')
                ->where('id_empresa', self::EMPRESA)
                ->where('sucursal', self::SUCURSAL)
                ->where('id_tido', 6)
                ->update(['numero' => $numero]);
        });

        $this->command?->info('20 pedidos de prueba creados (sin facturar).');
        $this->command?->info('Flujo a probar: facturar pedidos → armar despacho (RUTA TEST DESPACHO) → PDFs → cobrar → cerrar caja.');
    }
}
