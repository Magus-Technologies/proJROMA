<?php

namespace Database\Seeders;

use App\Models\TmsConductor;
use App\Models\TmsRuta;
use App\Models\TmsRutaPunto;
use App\Models\TmsVehiculo;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TmsDespachoSeeder extends Seeder
{
    public function run(): void
    {
        $empresa = 1;
        $sucursal = 1;

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('tms_despacho_costos')->truncate();
        DB::table('tms_despacho_pedidos')->truncate();
        DB::table('tms_despachos')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $rutas = TmsRuta::where('id_empresa', $empresa)->get();
        $vehiculos = TmsVehiculo::where('id_empresa', $empresa)->get();
        $conductores = TmsConductor::where('id_empresa', $empresa)->get();

        if ($rutas->isEmpty() || $vehiculos->isEmpty() || $conductores->isEmpty()) {
            $this->command->error('Ejecuta primero TmsSeeder (faltan rutas, vehículos o conductores).');
            return;
        }

        $despachosData = [
            ['codigo' => 'DESP-001', 'fecha_reparto' => '2026-07-01', 'estado' => 'CERRADO',   'obs' => 'Primer despacho del mes'],
            ['codigo' => 'DESP-002', 'fecha_reparto' => '2026-07-02', 'estado' => 'CERRADO',   'obs' => null],
            ['codigo' => 'DESP-003', 'fecha_reparto' => '2026-07-03', 'estado' => 'EN_RUTA',   'obs' => 'Salida 6:00 am'],
            ['codigo' => 'DESP-004', 'fecha_reparto' => '2026-07-03', 'estado' => 'CARGADO',   'obs' => 'Esperando confirmación de salida'],
            ['codigo' => 'DESP-005', 'fecha_reparto' => '2026-07-04', 'estado' => 'PLANIFICADO', 'obs' => null],
            ['codigo' => 'DESP-006', 'fecha_reparto' => '2026-07-04', 'estado' => 'PLANIFICADO', 'obs' => 'Requiere refrigeración'],
            ['codigo' => 'DESP-007', 'fecha_reparto' => '2026-07-05', 'estado' => 'PLANIFICADO', 'obs' => null],
            ['codigo' => 'DESP-008', 'fecha_reparto' => '2026-07-05', 'estado' => 'PLANIFICADO', 'obs' => null],
        ];

        foreach ($despachosData as $i => $dd) {
            $ruta = $rutas[$i % count($rutas)];
            $vehiculo = $vehiculos[$i % count($vehiculos)];
            $conductor = $conductores[$i % count($conductores)];
            $pesoTotal = round(rand(200, 1500) + rand(0, 99) / 100, 2);

            $despachoId = DB::table('tms_despachos')->insertGetId([
                'codigo'            => $dd['codigo'],
                'fecha_reparto'     => $dd['fecha_reparto'],
                'estado'            => $dd['estado'],
                'observaciones'     => $dd['obs'],
                'id_empresa'        => $empresa,
                'sucursal'          => $sucursal,
                'id_ruta'           => $ruta->id,
                'id_vehiculo'       => $vehiculo->id,
                'id_conductor'      => $conductor->id,
                'peso_total'        => $pesoTotal,
                'id_usuario_creacion' => 40,
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);

            $numPedidos = rand(2, 4);
            $pedidoPeso = round($pesoTotal / $numPedidos, 2);
            $mercadosRuta = TmsRutaPunto::where('id_ruta', $ruta->id)->where('tipo', 'MERCADO')->get();

            for ($p = 0; $p < $numPedidos; $p++) {
                $mercado = $mercadosRuta[$p % max(1, $mercadosRuta->count())];
                DB::table('tms_despacho_pedidos')->insert([
                    'id_despacho'    => $despachoId,
                    'id_cotizacion'  => rand(100, 999),
                    'id_cliente'     => rand(1, 100),
                    'id_mercado'     => $mercado ? $mercado->id_mercado : null,
                    'peso'           => $pedidoPeso,
                    'monto'          => round(rand(50, 500) + rand(0, 99) / 100, 2),
                    'orden'          => $p + 1,
                    'estado_entrega' => $dd['estado'] === 'CERRADO' ? 'ENTREGADO' : 'PENDIENTE',
                    'hora_entrega'   => $dd['estado'] === 'CERRADO' ? now()->subDays(rand(1, 3)) : null,
                ]);
            }

            $conceptos = ['COMBUSTIBLE', 'PEAJE', 'VIATICOS'];
            $numCostos = rand(1, 2);
            for ($c = 0; $c < $numCostos; $c++) {
                DB::table('tms_despacho_costos')->insert([
                    'id_despacho' => $despachoId,
                    'concepto'    => $conceptos[$c],
                    'monto'       => round(rand(20, 200) + rand(0, 99) / 100, 2),
                    'id_usuario'  => 40,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
            }
        }

        $this->command->info('8 despachos creados con pedidos y costos.');
    }
}
