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
        $empresa = 12;
        $sucursal = 1;

        $rutas = TmsRuta::where('id_empresa', $empresa)->pluck('id');
        $vehiculos = TmsVehiculo::where('id_empresa', $empresa)->pluck('id');
        $conductores = TmsConductor::where('id_empresa', $empresa)->pluck('id');

        if ($rutas->isEmpty() || $vehiculos->isEmpty() || $conductores->isEmpty()) {
            $this->command->error('Faltan datos maestros (rutas, vehículos o conductores).');
            return;
        }

        $despachosData = [
            ['codigo' => 'DESP-009', 'fecha_reparto' => '2026-07-06', 'estado' => 'PLANIFICADO', 'obs' => null],
            ['codigo' => 'DESP-010', 'fecha_reparto' => '2026-07-06', 'estado' => 'PLANIFICADO', 'obs' => 'Salida 5:30 am'],
            ['codigo' => 'DESP-011', 'fecha_reparto' => '2026-07-07', 'estado' => 'PLANIFICADO', 'obs' => null],
            ['codigo' => 'DESP-012', 'fecha_reparto' => '2026-07-07', 'estado' => 'PLANIFICADO', 'obs' => 'Carga frágil'],
            ['codigo' => 'DESP-013', 'fecha_reparto' => '2026-07-08', 'estado' => 'PLANIFICADO', 'obs' => null],
            ['codigo' => 'DESP-014', 'fecha_reparto' => '2026-07-08', 'estado' => 'PLANIFICADO', 'obs' => 'Requiere refrigeración'],
            ['codigo' => 'DESP-015', 'fecha_reparto' => '2026-07-09', 'estado' => 'PLANIFICADO', 'obs' => null],
            ['codigo' => 'DESP-016', 'fecha_reparto' => '2026-07-09', 'estado' => 'PLANIFICADO', 'obs' => null],
            ['codigo' => 'DESP-017', 'fecha_reparto' => '2026-07-10', 'estado' => 'PLANIFICADO', 'obs' => 'Entrega urgente'],
            ['codigo' => 'DESP-018', 'fecha_reparto' => '2026-07-10', 'estado' => 'PLANIFICADO', 'obs' => null],
        ];

        foreach ($despachosData as $i => $dd) {
            $ruta = $rutas[$i % count($rutas)];
            $vehiculo = $vehiculos[$i % count($vehiculos)];
            $conductor = $conductores[$i % count($conductores)];
            $pesoTotal = round(rand(200, 2000) + rand(0, 99) / 100, 2);

            $despachoId = DB::table('tms_despachos')->insertGetId([
                'codigo'            => $dd['codigo'],
                'fecha_reparto'     => $dd['fecha_reparto'],
                'estado'            => $dd['estado'],
                'observaciones'     => $dd['obs'],
                'id_empresa'        => $empresa,
                'sucursal'          => $sucursal,
                'id_ruta'           => $ruta,
                'id_vehiculo'       => $vehiculo,
                'id_conductor'      => $conductor,
                'peso_total'        => $pesoTotal,
                'id_usuario_creacion' => 40,
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);

            // 2-4 pedidos por despacho
            $numPedidos = rand(2, 4);
            $pedidoPeso = $pesoTotal / $numPedidos;
            $puntosRuta = TmsRutaPunto::where('id_ruta', $ruta)
                ->where('tipo', 'MERCADO')->get();

            for ($p = 0; $p < $numPedidos; $p++) {
                $punto = $puntosRuta[$p % max(1, $puntosRuta->count())];
                DB::table('tms_despacho_pedidos')->insert([
                    'id_despacho'    => $despachoId,
                    'id_cotizacion'  => rand(100, 999),
                    'id_cliente'     => rand(1, 100),
                    'id_mercado'     => $punto ? $punto->id_mercado : null,
                    'peso'           => round($pedidoPeso, 2),
                    'monto'          => round(rand(80, 800) + rand(0, 99) / 100, 2),
                    'orden'          => $p + 1,
                    'estado_entrega' => 'PENDIENTE',
                    'hora_entrega'   => null,
                ]);
            }

            // 1-2 costos por despacho
            $conceptos = ['COMBUSTIBLE', 'PEAJE', 'VIATICOS', 'LAVADO', 'ESTACIONAMIENTO'];
            $numCostos = rand(1, 2);
            for ($c = 0; $c < $numCostos; $c++) {
                DB::table('tms_despacho_costos')->insert([
                    'id_despacho' => $despachoId,
                    'concepto'    => $conceptos[$c],
                    'monto'       => round(rand(20, 250) + rand(0, 99) / 100, 2),
                    'id_usuario'  => 40,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
            }
        }

        $this->command->info(count($despachosData) . ' despachos creados con pedidos y costos.');
    }
}
