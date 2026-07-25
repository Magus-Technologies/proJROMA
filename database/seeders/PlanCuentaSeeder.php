<?php

namespace Database\Seeders;

use App\Models\PlanCuenta;
use Illuminate\Database\Seeder;

class PlanCuentaSeeder extends Seeder
{
    public function run(): void
    {
        $cuentas = [
            ['codigo' => '1',  'nombre' => 'ACTIVO',          'tipo' => 'activo',     'nivel' => 1],
            ['codigo' => '10', 'nombre' => 'Efectivo y Equivalentes de Efectivo', 'tipo' => 'activo', 'nivel' => 2, 'padre' => '1'],
            ['codigo' => '101','nombre' => 'Caja',             'tipo' => 'activo',     'nivel' => 3, 'padre' => '10'],
            ['codigo' => '102','nombre' => 'Bancos',           'tipo' => 'activo',     'nivel' => 3, 'padre' => '10'],
            ['codigo' => '103','nombre' => 'Caja Chica',       'tipo' => 'activo',     'nivel' => 3, 'padre' => '10'],
            ['codigo' => '12', 'nombre' => 'Cuentas por Cobrar Comerciales', 'tipo' => 'activo', 'nivel' => 2, 'padre' => '1'],
            ['codigo' => '121','nombre' => 'Facturas por Cobrar', 'tipo' => 'activo', 'nivel' => 3, 'padre' => '12'],
            ['codigo' => '122','nombre' => 'Letras por Cobrar', 'tipo' => 'activo',   'nivel' => 3, 'padre' => '12'],
            ['codigo' => '14', 'nombre' => 'Cuentas por Cobrar Diversas', 'tipo' => 'activo', 'nivel' => 2, 'padre' => '1'],
            ['codigo' => '16', 'nombre' => 'Existencias (Inventarios)', 'tipo' => 'activo', 'nivel' => 2, 'padre' => '1'],
            ['codigo' => '161','nombre' => 'Mercaderías',      'tipo' => 'activo',     'nivel' => 3, 'padre' => '16'],
            ['codigo' => '162','nombre' => 'Materias Primas',  'tipo' => 'activo',     'nivel' => 3, 'padre' => '16'],
            ['codigo' => '163','nombre' => 'Envases y Embalajes', 'tipo' => 'activo',  'nivel' => 3, 'padre' => '16'],
            ['codigo' => '18', 'nombre' => 'Servicios y Otros Contratados Anticipadamente', 'tipo' => 'activo', 'nivel' => 2, 'padre' => '1'],
            ['codigo' => '19', 'nombre' => 'Estimación de Cuentas de Cobranza Dudosa', 'tipo' => 'activo', 'nivel' => 2, 'padre' => '1'],
            ['codigo' => '2',  'nombre' => 'PASIVO',           'tipo' => 'pasivo',    'nivel' => 1],
            ['codigo' => '20', 'nombre' => 'Cuentas por Pagar Comerciales', 'tipo' => 'pasivo', 'nivel' => 2, 'padre' => '2'],
            ['codigo' => '201','nombre' => 'Facturas por Pagar', 'tipo' => 'pasivo', 'nivel' => 3, 'padre' => '20'],
            ['codigo' => '202','nombre' => 'Letras por Pagar', 'tipo' => 'pasivo',   'nivel' => 3, 'padre' => '20'],
            ['codigo' => '21', 'nombre' => 'Cuentas por Pagar Diversas', 'tipo' => 'pasivo', 'nivel' => 2, 'padre' => '2'],
            ['codigo' => '23', 'nombre' => 'Remuneraciones por Pagar', 'tipo' => 'pasivo', 'nivel' => 2, 'padre' => '2'],
            ['codigo' => '24', 'nombre' => 'Tributos por Pagar', 'tipo' => 'pasivo', 'nivel' => 2, 'padre' => '2'],
            ['codigo' => '241','nombre' => 'IGV por Pagar',    'tipo' => 'pasivo',     'nivel' => 3, 'padre' => '24'],
            ['codigo' => '242','nombre' => 'IR por Pagar',     'tipo' => 'pasivo',     'nivel' => 3, 'padre' => '24'],
            ['codigo' => '3',  'nombre' => 'PATRIMONIO',       'tipo' => 'patrimonio','nivel' => 1],
            ['codigo' => '30', 'nombre' => 'Capital',          'tipo' => 'patrimonio', 'nivel' => 2, 'padre' => '3'],
            ['codigo' => '301','nombre' => 'Capital Social',   'tipo' => 'patrimonio', 'nivel' => 3, 'padre' => '30'],
            ['codigo' => '31', 'nombre' => 'Resultados Acumulados', 'tipo' => 'patrimonio', 'nivel' => 2, 'padre' => '3'],
            ['codigo' => '32', 'nombre' => 'Resultado del Ejercicio', 'tipo' => 'patrimonio', 'nivel' => 2, 'padre' => '3'],
            ['codigo' => '4',  'nombre' => 'INGRESOS',         'tipo' => 'ingreso',   'nivel' => 1],
            ['codigo' => '40', 'nombre' => 'Ventas',           'tipo' => 'ingreso',   'nivel' => 2, 'padre' => '4'],
            ['codigo' => '401','nombre' => 'Ventas Netas',     'tipo' => 'ingreso',   'nivel' => 3, 'padre' => '40'],
            ['codigo' => '402','nombre' => 'Devoluciones sobre Ventas', 'tipo' => 'ingreso', 'nivel' => 3, 'padre' => '40'],
            ['codigo' => '41', 'nombre' => 'Otros Ingresos',   'tipo' => 'ingreso',   'nivel' => 2, 'padre' => '4'],
            ['codigo' => '5',  'nombre' => 'COSTOS',           'tipo' => 'costo',     'nivel' => 1],
            ['codigo' => '50', 'nombre' => 'Costo de Ventas',  'tipo' => 'costo',     'nivel' => 2, 'padre' => '5'],
            ['codigo' => '501','nombre' => 'Costo de Mercaderías Vendidas', 'tipo' => 'costo', 'nivel' => 3, 'padre' => '50'],
            ['codigo' => '6',  'nombre' => 'GASTOS',           'tipo' => 'gasto',     'nivel' => 1],
            ['codigo' => '60', 'nombre' => 'Gastos de Personal', 'tipo' => 'gasto',   'nivel' => 2, 'padre' => '6'],
            ['codigo' => '601','nombre' => 'Sueldos y Salarios', 'tipo' => 'gasto',    'nivel' => 3, 'padre' => '60'],
            ['codigo' => '602','nombre' => 'Beneficios Sociales', 'tipo' => 'gasto',   'nivel' => 3, 'padre' => '60'],
            ['codigo' => '61', 'nombre' => 'Gastos de Servicios Públicos', 'tipo' => 'gasto', 'nivel' => 2, 'padre' => '6'],
            ['codigo' => '611','nombre' => 'Electricidad',     'tipo' => 'gasto',      'nivel' => 3, 'padre' => '61'],
            ['codigo' => '612','nombre' => 'Agua',             'tipo' => 'gasto',      'nivel' => 3, 'padre' => '61'],
            ['codigo' => '613','nombre' => 'Teléfono e Internet', 'tipo' => 'gasto',  'nivel' => 3, 'padre' => '61'],
            ['codigo' => '62', 'nombre' => 'Gastos de Alquiler', 'tipo' => 'gasto',   'nivel' => 2, 'padre' => '6'],
            ['codigo' => '63', 'nombre' => 'Gastos de Transporte', 'tipo' => 'gasto', 'nivel' => 2, 'padre' => '6'],
            ['codigo' => '64', 'nombre' => 'Gastos de Ventas', 'tipo' => 'gasto',     'nivel' => 2, 'padre' => '6'],
            ['codigo' => '65', 'nombre' => 'Gastos Administrativos', 'tipo' => 'gasto', 'nivel' => 2, 'padre' => '6'],
            ['codigo' => '66', 'nombre' => 'Gastos Financieros', 'tipo' => 'gasto',   'nivel' => 2, 'padre' => '6'],
            ['codigo' => '68', 'nombre' => 'Depreciación y Amortización', 'tipo' => 'gasto', 'nivel' => 2, 'padre' => '6'],
            ['codigo' => '69', 'nombre' => 'Costo de Ventas (Gasto)', 'tipo' => 'gasto', 'nivel' => 2, 'padre' => '6'],
        ];

        foreach ($cuentas as $c) {
            $padreId = null;
            if (isset($c['padre'])) {
                $padre = PlanCuenta::where('codigo', $c['padre'])->first();
                $padreId = $padre?->id;
            }
            PlanCuenta::create([
                'codigo' => $c['codigo'],
                'nombre' => $c['nombre'],
                'tipo' => $c['tipo'],
                'nivel' => $c['nivel'],
                'padre_id' => $padreId,
                'estado' => true,
            ]);
        }
    }
}
