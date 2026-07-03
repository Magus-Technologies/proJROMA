<?php

namespace Database\Seeders;

use App\Models\TmsMercado;
use App\Models\TmsRuta;
use App\Models\TmsRutaPunto;
use Illuminate\Database\Seeder;

class TmsRutaSeeder extends Seeder
{
    public function run(): void
    {
        $empresa = 12;
        $sucursal = 1;

        $mercados = TmsMercado::where('id_empresa', $empresa)
            ->where('sucursal', $sucursal)
            ->pluck('id', 'nombre');

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
            [
                'nombre' => 'Ruta Expresa',
                'descripcion' => 'Ruta directa a mercados principales',
                'puntos' => ['Mercado Central', 'Mercado Mayorista', 'Mercado de Surquillo'],
            ],
            [
                'nombre' => 'Ruta Periférica',
                'descripcion' => 'Cobertura mercados alejados',
                'puntos' => ['Mercado El Bosque', 'Mercado Zonal Palomino', 'Mercado Ciudad de Dios'],
            ],
        ];

        foreach ($rutaData as $rd) {
            $existing = TmsRuta::where('id_empresa', $empresa)
                ->where('sucursal', $sucursal)
                ->where('nombre', $rd['nombre'])
                ->first();

            if ($existing) {
                // Update existing ruta
                $existing->update(['descripcion' => $rd['descripcion']]);
                $ruta = $existing;
            } else {
                $ruta = TmsRuta::create([
                    'nombre'      => $rd['nombre'],
                    'descripcion' => $rd['descripcion'],
                    'id_empresa'  => $empresa,
                    'sucursal'    => $sucursal,
                    'estado'      => 1,
                ]);
            }

            // Delete existing puntos for this ruta
            TmsRutaPunto::where('id_ruta', $ruta->id)->delete();

            // Create nuevos puntos
            foreach ($rd['puntos'] as $orden => $nombreMercado) {
                if ($mercados->has($nombreMercado)) {
                    TmsRutaPunto::create([
                        'id_ruta'    => $ruta->id,
                        'tipo'       => 'MERCADO',
                        'id_mercado' => $mercados[$nombreMercado],
                        'orden'      => $orden + 1,
                    ]);
                }
            }
        }

        $this->command->info(count($rutaData) . ' rutas creadas/actualizadas con puntos.');
    }
}
