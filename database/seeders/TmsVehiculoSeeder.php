<?php

namespace Database\Seeders;

use App\Models\TmsTipoVehiculo;
use App\Models\TmsVehiculo;
use Illuminate\Database\Seeder;

class TmsVehiculoSeeder extends Seeder
{
    public function run(): void
    {
        $empresa = 12;
        $sucursal = 1;

        $tipos = TmsTipoVehiculo::where('id_empresa', $empresa)
            ->pluck('id', 'nombre');

        $vehiculos = [
            ['placa' => 'ABC-123', 'id_tipo' => $tipos['CAMIONETA'], 'marca' => 'Toyota',  'modelo' => 'Hilux',        'anio' => 2020, 'capacidad_kg' => 1000, 'tara_kg' => 1800, 'soat_vence' => '2026-12-31', 'rev_tecnica_vence' => '2026-06-30'],
            ['placa' => 'DEF-456', 'id_tipo' => $tipos['CAMIONETA'], 'marca' => 'Nissan',  'modelo' => 'NP300',        'anio' => 2021, 'capacidad_kg' => 1200, 'tara_kg' => 1750, 'soat_vence' => '2026-11-30', 'rev_tecnica_vence' => '2026-05-15'],
            ['placa' => 'GHI-789', 'id_tipo' => $tipos['FURGONETA'], 'marca' => 'Hyundai', 'modelo' => 'H-1',          'anio' => 2019, 'capacidad_kg' => 800,  'tara_kg' => 1650, 'soat_vence' => '2026-10-15', 'rev_tecnica_vence' => '2026-04-20'],
            ['placa' => 'JKL-012', 'id_tipo' => $tipos['FURGONETA'], 'marca' => 'Mercedes','modelo' => 'Sprinter',     'anio' => 2022, 'capacidad_kg' => 900,  'tara_kg' => 1900, 'soat_vence' => '2027-01-20', 'rev_tecnica_vence' => '2027-01-10'],
            ['placa' => 'MNO-345', 'id_tipo' => $tipos['CAMION'],    'marca' => 'Volvo',   'modelo' => 'FH 440',       'anio' => 2020, 'capacidad_kg' => 8000, 'tara_kg' => 5000, 'soat_vence' => '2026-08-05', 'rev_tecnica_vence' => '2026-07-01'],
            ['placa' => 'PQR-678', 'id_tipo' => $tipos['CAMION'],    'marca' => 'Scania',  'modelo' => 'G410',         'anio' => 2021, 'capacidad_kg' => 10000,'tara_kg' => 5500, 'soat_vence' => '2026-09-12', 'rev_tecnica_vence' => '2026-08-20'],
            ['placa' => 'STU-901', 'id_tipo' => $tipos['CAMION'],    'marca' => 'Freightliner','modelo' => 'M2 106',  'anio' => 2018, 'capacidad_kg' => 12000,'tara_kg' => 6000, 'soat_vence' => '2026-07-30', 'rev_tecnica_vence' => '2026-06-15'],
            ['placa' => 'VWX-234', 'id_tipo' => $tipos['MOTO'],      'marca' => 'Honda',   'modelo' => 'XR 150',       'anio' => 2023, 'capacidad_kg' => 50,   'tara_kg' => 130,  'soat_vence' => '2027-03-15', 'rev_tecnica_vence' => '2027-02-28'],
            ['placa' => 'YZA-567', 'id_tipo' => $tipos['MOTO'],      'marca' => 'Yamaha',  'modelo' => 'FZ 250',       'anio' => 2022, 'capacidad_kg' => 80,   'tara_kg' => 140,  'soat_vence' => '2027-02-28', 'rev_tecnica_vence' => '2027-01-15'],
            ['placa' => 'BCD-890', 'id_tipo' => $tipos['OTRO'],      'marca' => 'Mitsubishi','modelo' => 'L200',       'anio' => 2020, 'capacidad_kg' => 500,  'tara_kg' => 1200, 'soat_vence' => '2026-12-01', 'rev_tecnica_vence' => '2026-11-10'],
            ['placa' => 'EFG-123', 'id_tipo' => $tipos['CAMIONETA'], 'marca' => 'Ford',    'modelo' => 'Ranger',       'anio' => 2022, 'capacidad_kg' => 1100, 'tara_kg' => 1850, 'soat_vence' => '2027-05-20', 'rev_tecnica_vence' => '2027-04-10'],
            ['placa' => 'HIJ-456', 'id_tipo' => $tipos['CAMION'],    'marca' => 'Kenworth','modelo' => 'T680',         'anio' => 2023, 'capacidad_kg' => 15000,'tara_kg' => 7000, 'soat_vence' => '2027-06-30', 'rev_tecnica_vence' => '2027-06-01'],
            ['placa' => 'KLM-789', 'id_tipo' => $tipos['FURGONETA'], 'marca' => 'Peugeot', 'modelo' => 'Partner',      'anio' => 2021, 'capacidad_kg' => 650,  'tara_kg' => 1400, 'soat_vence' => '2026-12-15', 'rev_tecnica_vence' => '2026-11-20'],
            ['placa' => 'NOP-012', 'id_tipo' => $tipos['TUKTUK'],    'marca' => 'Bajaj',   'modelo' => 'Qute',         'anio' => 2023, 'capacidad_kg' => 300,  'tara_kg' => 400,  'soat_vence' => '2027-08-10', 'rev_tecnica_vence' => '2027-07-05'],
            ['placa' => 'QRS-345', 'id_tipo' => $tipos['CAMIONETA'], 'marca' => 'Chevrolet','modelo' => 'Silverado',    'anio' => 2019, 'capacidad_kg' => 1300, 'tara_kg' => 2000, 'soat_vence' => '2026-10-25', 'rev_tecnica_vence' => '2026-09-30'],
        ];

        foreach ($vehiculos as $v) {
            TmsVehiculo::firstOrCreate(
                ['placa' => $v['placa']],
                array_merge($v, [
                    'id_empresa' => $empresa,
                    'sucursal'   => $sucursal,
                    'estado'     => 1,
                    'largo_m'    => rand(250, 600) / 100,
                    'ancho_m'    => rand(140, 250) / 100,
                    'alto_m'     => rand(120, 220) / 100,
                    'capacidad_m3' => round(rand(300, 3000) / 100, 2),
                ])
            );
        }

        $this->command->info('15 vehículos creados.');
    }
}
