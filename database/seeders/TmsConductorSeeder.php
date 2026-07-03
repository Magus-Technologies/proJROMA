<?php

namespace Database\Seeders;

use App\Models\TmsConductor;
use Illuminate\Database\Seeder;

class TmsConductorSeeder extends Seeder
{
    public function run(): void
    {
        $empresa = 12;
        $sucursal = 1;

        $conductores = [
            ['nombres' => 'Alejandro Torres Vega',          'documento' => '11223344', 'licencia' => 'T11223344', 'licencia_categoria' => 'A-II', 'licencia_vence' => '2027-06-15', 'telefono' => '987654001'],
            ['nombres' => 'Fernanda Castillo Ríos',          'documento' => '22334455', 'licencia' => 'T22334455', 'licencia_categoria' => 'A-I',  'licencia_vence' => '2027-08-20', 'telefono' => '987654002'],
            ['nombres' => 'Ricardo Paredes Lozano',          'documento' => '33445566', 'licencia' => 'T33445566', 'licencia_categoria' => 'A-III','licencia_vence' => '2026-12-10', 'telefono' => '987654003'],
            ['nombres' => 'Camila Guerrero Mendoza',         'documento' => '44556677', 'licencia' => 'T44556677', 'licencia_categoria' => 'A-II', 'licencia_vence' => '2028-01-05', 'telefono' => '987654004'],
            ['nombres' => 'Esteban Flores Morales',          'documento' => '55667788', 'licencia' => 'T55667788', 'licencia_categoria' => 'A-I',  'licencia_vence' => '2027-03-22', 'telefono' => '987654005'],
            ['nombres' => 'Valentina Herrera Pizarro',       'documento' => '66778899', 'licencia' => 'T66778899', 'licencia_categoria' => 'A-II', 'licencia_vence' => '2027-11-30', 'telefono' => '987654006'],
            ['nombres' => 'Migángel Rojas Córdova',          'documento' => '77889900', 'licencia' => 'T77889900', 'licencia_categoria' => 'A-III','licencia_vence' => '2026-09-18', 'telefono' => '987654007'],
            ['nombres' => 'Gabriela Salazar Hence',          'documento' => '88990011', 'licencia' => 'T88990011', 'licencia_categoria' => 'A-I',  'licencia_vence' => '2028-04-14', 'telefono' => '987654008'],
            ['nombres' => 'David Quispe Mamani',             'documento' => '99001122', 'licencia' => 'T99001122', 'licencia_categoria' => 'A-II', 'licencia_vence' => '2027-07-07', 'telefono' => '987654009'],
            ['nombres' => 'Sofía Beltrán Cardenas',           'documento' => '00112233', 'licencia' => 'T00112233', 'licencia_categoria' => 'A-I',  'licencia_vence' => '2028-02-28', 'telefono' => '987654010'],
            ['nombres' => 'Paolo Hurtado Ávila',             'documento' => '10293847', 'licencia' => 'P10293847', 'licencia_categoria' => 'A-II', 'licencia_vence' => '2026-10-05', 'telefono' => '987654011'],
            ['nombres' => 'Lucía Montenegro Pacheco',        'documento' => '56473829', 'licencia' => 'L56473829', 'licencia_categoria' => 'A-III','licencia_vence' => '2027-05-12', 'telefono' => '987654012'],
            ['nombres' => 'Hernán Bravo Cuestas',            'documento' => '37485920', 'licencia' => 'H37485920', 'licencia_categoria' => 'A-I',  'licencia_vence' => '2027-09-25', 'telefono' => '987654013'],
            ['nombres' => 'Patricia Villanueva Soto',        'documento' => '84756291', 'licencia' => 'P84756291', 'licencia_categoria' => 'A-II', 'licencia_vence' => '2028-06-01', 'telefono' => '987654014'],
            ['nombres' => 'Gonzalo Tapia Chávez',            'documento' => '19283746', 'licencia' => 'G19283746', 'licencia_categoria' => 'A-III','licencia_vence' => '2026-08-19', 'telefono' => '987654015'],
        ];

        foreach ($conductores as $c) {
            TmsConductor::firstOrCreate(
                ['documento' => $c['documento']],
                array_merge($c, [
                    'id_empresa' => $empresa,
                    'sucursal'   => $sucursal,
                    'estado'     => 1,
                ])
            );
        }

        $this->command->info('15 conductores adicionales creados.');
    }
}
