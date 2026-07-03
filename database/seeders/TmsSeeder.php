<?php

namespace Database\Seeders;

use App\Models\TmsConductor;
use App\Models\TmsMercado;
use App\Models\TmsRuta;
use App\Models\TmsRutaPunto;
use App\Models\TmsTipoVehiculo;
use App\Models\TmsVehiculo;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TmsSeeder extends Seeder
{
    public function run(): void
    {
        $empresa = 1;
        $sucursal = 1;

        // ── Tipos de Vehículo ──────────────────────────────────────────
        $tipos = [];
        foreach (['CAMIONETA', 'FURGONETA', 'CAMION', 'MOTO', 'OTRO'] as $nombre) {
            $tipos[$nombre] = TmsTipoVehiculo::firstOrCreate(
                ['id_empresa' => $empresa, 'nombre' => $nombre],
                ['estado' => 1]
            )->id;
        }

        // ── Conductores ────────────────────────────────────────────────
        $conductores = [
            ['nombres' => 'Carlos Mendoza López',       'documento' => '12345678', 'licencia' => 'Q12345678', 'licencia_categoria' => 'A-II', 'telefono' => '987654321'],
            ['nombres' => 'María García Torres',        'documento' => '23456789', 'licencia' => 'Q23456789', 'licencia_categoria' => 'A-I',  'telefono' => '976543210'],
            ['nombres' => 'Juan Pérez Castillo',        'documento' => '34567890', 'licencia' => 'Q34567890', 'licencia_categoria' => 'A-III','telefono' => '965432109'],
            ['nombres' => 'Roberto Huamán Quispe',      'documento' => '45678901', 'licencia' => 'Q45678901', 'licencia_categoria' => 'A-II', 'telefono' => '954321098'],
            ['nombres' => 'Luis Fernández Rojas',       'documento' => '56789012', 'licencia' => 'Q56789012', 'licencia_categoria' => 'A-I',  'telefono' => '943210987'],
            ['nombres' => 'Pedro Gutiérrez Silva',      'documento' => '67890123', 'licencia' => 'Q67890123', 'licencia_categoria' => 'A-II', 'telefono' => '932109876'],
            ['nombres' => 'Jorge Ramírez Paredes',      'documento' => '78901234', 'licencia' => 'Q78901234', 'licencia_categoria' => 'A-III','telefono' => '921098765'],
            ['nombres' => 'Diego Sánchez Vargas',       'documento' => '89012345', 'licencia' => 'Q89012345', 'licencia_categoria' => 'A-I',  'telefono' => '910987654'],
            ['nombres' => 'Andrés Navarro Flores',      'documento' => '90123456', 'licencia' => 'Q90123456', 'licencia_categoria' => 'A-II', 'telefono' => '909876543'],
            ['nombres' => 'César Delgado Ríos',         'documento' => '01234567', 'licencia' => 'Q01234567', 'licencia_categoria' => 'A-I',  'telefono' => '898765432'],
        ];

        foreach ($conductores as $c) {
            TmsConductor::create(array_merge($c, [
                'id_empresa' => $empresa,
                'sucursal'   => $sucursal,
                'estado'     => 1,
            ]));
        }

        $this->command->info('10 conductores creados.');

        // ── Mercados ───────────────────────────────────────────────────
        $mercados = [
            ['nombre' => 'Mercado Central',           'direccion' => 'Jr. Junín 750',                    'distrito' => 'Cercado de Lima',       'telefono' => '01-4281234'],
            ['nombre' => 'Mercado San José',          'direccion' => 'Av. Argentina 3200',               'distrito' => 'Cercado de Lima',       'telefono' => '01-3367890'],
            ['nombre' => 'Mercado La Victoria',       'direccion' => 'Av. 28 de Julio 1500',             'distrito' => 'La Victoria',           'telefono' => '01-2254567'],
            ['nombre' => 'Mercado de Surquillo',      'direccion' => 'Av. Paseo de la República 4500',   'distrito' => 'Surquillo',             'telefono' => '01-4412345'],
            ['nombre' => 'Mercado San Martín',        'direccion' => 'Av. San Martín 800',               'distrito' => 'Miraflores',            'telefono' => '01-4456789'],
            ['nombre' => 'Mercado Mayorista',         'direccion' => 'Carretera Central Km 5',           'distrito' => 'Santa Anita',           'telefono' => '01-3623456'],
            ['nombre' => 'Mercado Ciudad de Dios',    'direccion' => 'Av. Manuel Prado s/n',             'distrito' => 'San Juan de Miraflores','telefono' => '01-2789012'],
            ['nombre' => 'Mercado Unicachi',          'direccion' => 'Av. Separadora Industrial 2500',   'distrito' => 'Villa El Salvador',     'telefono' => '01-2895678'],
            ['nombre' => 'Mercado Las Gardenias',     'direccion' => 'Av. Las Gardenias 500',            'distrito' => 'Los Olivos',            'telefono' => '01-5213456'],
            ['nombre' => 'Mercado Zonal Palomino',    'direccion' => 'Av. Próceres de la Independencia 2000', 'distrito' => 'San Juan de Lurigancho','telefono' => '01-3769012'],
            ['nombre' => 'Mercado Túpac Amaru',       'direccion' => 'Av. Túpac Amaru 1800',            'distrito' => 'Independencia',         'telefono' => '01-5245678'],
            ['nombre' => 'Mercado El Bosque',         'direccion' => 'Av. El Bosque 1200',              'distrito' => 'Comas',                 'telefono' => '01-5357890'],
            ['nombre' => 'Mercado San Felipe',        'direccion' => 'Av. San Felipe 400',              'distrito' => 'Jesús María',           'telefono' => '01-4631234'],
            ['nombre' => 'Mercado Tres Cabezas',      'direccion' => 'Av. Venezuela 5000',              'distrito' => 'Cercado de Lima',       'telefono' => '01-3305678'],
            ['nombre' => 'Mercado Municipal de Barranco', 'direccion' => 'Av. San Martín 150',          'distrito' => 'Barranco',              'telefono' => '01-2478901'],
        ];

        foreach ($mercados as $m) {
            TmsMercado::create(array_merge($m, [
                'id_empresa' => $empresa,
                'sucursal'   => $sucursal,
                'estado'     => 1,
            ]));
        }

        $this->command->info('15 mercados creados.');

        // ── Vehículos ──────────────────────────────────────────────────
        $vehiculos = [
            ['placa' => 'ABC-123', 'id_tipo' => $tipos['CAMIONETA'], 'marca' => 'Toyota',  'modelo' => 'Hilux',       'anio' => 2020, 'capacidad_kg' => 1000, 'tara_kg' => 1800, 'soat_vence' => '2026-12-31'],
            ['placa' => 'DEF-456', 'id_tipo' => $tipos['CAMIONETA'], 'marca' => 'Nissan',  'modelo' => 'NP300',       'anio' => 2021, 'capacidad_kg' => 1200, 'tara_kg' => 1750, 'soat_vence' => '2026-11-30'],
            ['placa' => 'GHI-789', 'id_tipo' => $tipos['FURGONETA'], 'marca' => 'Hyundai', 'modelo' => 'H-1',         'anio' => 2019, 'capacidad_kg' => 800,  'tara_kg' => 1650, 'soat_vence' => '2026-10-15'],
            ['placa' => 'JKL-012', 'id_tipo' => $tipos['FURGONETA'], 'marca' => 'Mercedes','modelo' => 'Sprinter',    'anio' => 2022, 'capacidad_kg' => 900,  'tara_kg' => 1900, 'soat_vence' => '2027-01-20'],
            ['placa' => 'MNO-345', 'id_tipo' => $tipos['CAMION'],    'marca' => 'Volvo',   'modelo' => 'FH 440',      'anio' => 2020, 'capacidad_kg' => 8000, 'tara_kg' => 5000, 'soat_vence' => '2026-08-05'],
            ['placa' => 'PQR-678', 'id_tipo' => $tipos['CAMION'],    'marca' => 'Scania',  'modelo' => 'G410',        'anio' => 2021, 'capacidad_kg' => 10000,'tara_kg' => 5500, 'soat_vence' => '2026-09-12'],
            ['placa' => 'STU-901', 'id_tipo' => $tipos['CAMION'],    'marca' => 'Freightliner','modelo' => 'M2 106', 'anio' => 2018, 'capacidad_kg' => 12000,'tara_kg' => 6000, 'soat_vence' => '2026-07-30'],
            ['placa' => 'VWX-234', 'id_tipo' => $tipos['MOTO'],      'marca' => 'Honda',   'modelo' => 'XR 150',      'anio' => 2023, 'capacidad_kg' => 50,   'tara_kg' => 130,  'soat_vence' => '2027-03-15'],
            ['placa' => 'YZA-567', 'id_tipo' => $tipos['MOTO'],      'marca' => 'Yamaha',  'modelo' => 'FZ 250',      'anio' => 2022, 'capacidad_kg' => 80,   'tara_kg' => 140,  'soat_vence' => '2027-02-28'],
            ['placa' => 'BCD-890', 'id_tipo' => $tipos['OTRO'],      'marca' => 'Mitsubishi','modelo' => 'L200',      'anio' => 2020, 'capacidad_kg' => 500,  'tara_kg' => 1200, 'soat_vence' => '2026-12-01'],
        ];

        foreach ($vehiculos as $v) {
            TmsVehiculo::create(array_merge($v, [
                'id_empresa' => $empresa,
                'sucursal'   => $sucursal,
                'estado'     => 1,
            ]));
        }

        $this->command->info('10 vehículos creados.');

        // ── Rutas con puntos ───────────────────────────────────────────
        $rutaData = [
            [
                'nombre' => 'Ruta Centro',
                'descripcion' => 'Mercados del centro de Lima',
                'puntos' => ['Mercado Central', 'Mercado San José', 'Mercado Tres Cabezas'],
            ],
            [
                'nombre' => 'Ruta Sur',
                'descripcion' => 'Mercados de la zona sur',
                'puntos' => ['Mercado de Surquillo', 'Mercado San Martín', 'Mercado Ciudad de Dios', 'Mercado Municipal de Barranco'],
            ],
            [
                'nombre' => 'Ruta Este',
                'descripcion' => 'Mercados de la zona este',
                'puntos' => ['Mercado Mayorista', 'Mercado La Victoria', 'Mercado Zonal Palomino'],
            ],
            [
                'nombre' => 'Ruta Norte',
                'descripcion' => 'Mercados de la zona norte',
                'puntos' => ['Mercado Las Gardenias', 'Mercado Túpac Amaru', 'Mercado El Bosque'],
            ],
            [
                'nombre' => 'Ruta San Felipe',
                'descripcion' => 'Cobertura Jesús María y alrededores',
                'puntos' => ['Mercado San Felipe', 'Mercado Unicachi'],
            ],
        ];

        $mercadosMap = TmsMercado::where('id_empresa', $empresa)->pluck('id', 'nombre');

        foreach ($rutaData as $rd) {
            $ruta = TmsRuta::create([
                'nombre'      => $rd['nombre'],
                'descripcion' => $rd['descripcion'],
                'id_empresa'  => $empresa,
                'sucursal'    => $sucursal,
                'estado'      => 1,
            ]);

            foreach ($rd['puntos'] as $orden => $nombreMercado) {
                if ($mercadosMap->has($nombreMercado)) {
                    TmsRutaPunto::create([
                        'id_ruta'    => $ruta->id,
                        'tipo'       => 'MERCADO',
                        'id_mercado' => $mercadosMap[$nombreMercado],
                        'orden'      => $orden + 1,
                    ]);
                }
            }
        }

        $this->command->info('5 rutas creadas con puntos.');

        // ── Despachos con pedidos y costos ─────────────────────────────
        $rutas = TmsRuta::where('id_empresa', $empresa)->get();
        $vehiculos = TmsVehiculo::where('id_empresa', $empresa)->get();
        $conductores = TmsConductor::where('id_empresa', $empresa)->get();

        $despachosData = [
            ['codigo' => 'DESP-001', 'fecha_reparto' => '2026-07-01', 'estado' => 'CERRADO',   'obs' => 'Primer despacho del mes'],
            ['codigo' => 'DESP-002', 'fecha_reparto' => '2026-07-02', 'estado' => 'CERRADO',   'obs' => null],
            ['codigo' => 'DESP-003', 'fecha_reparto' => '2026-07-03', 'estado' => 'EN_RUTA',   'obs' => 'Salida 6:00 am'],
            ['codigo' => 'DESP-004', 'fecha_reparto' => '2026-07-03', 'estado' => 'CARGADO',   'obs' => 'Esperando confirmación'],
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

            // 2-4 pedidos por despacho
            $numPedidos = rand(2, 4);
            $pedidoPeso = $pesoTotal / $numPedidos;
            $mercadosRuta = TmsRutaPunto::where('id_ruta', $ruta->id)->where('tipo', 'MERCADO')->get();

            for ($p = 0; $p < $numPedidos; $p++) {
                $mercado = $mercadosRuta[$p % max(1, $mercadosRuta->count())];
                DB::table('tms_despacho_pedidos')->insert([
                    'id_despacho'    => $despachoId,
                    'id_cotizacion'  => rand(100, 999),
                    'id_cliente'     => rand(1, 100),
                    'id_mercado'     => $mercado ? $mercado->id_mercado : null,
                    'peso'           => round($pedidoPeso, 2),
                    'monto'          => round(rand(50, 500) + rand(0, 99) / 100, 2),
                    'orden'          => $p + 1,
                    'estado_entrega' => $dd['estado'] === 'CERRADO' ? 'ENTREGADO' : 'PENDIENTE',
                    'hora_entrega'   => $dd['estado'] === 'CERRADO' ? now()->subDays(rand(1, 3)) : null,
                ]);
            }

            // 1-2 costos por despacho
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
