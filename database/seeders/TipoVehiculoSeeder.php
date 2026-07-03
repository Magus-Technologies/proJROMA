<?php

namespace Database\Seeders;

use App\Models\TmsTipoVehiculo;
use Illuminate\Database\Seeder;

class TipoVehiculoSeeder extends Seeder
{
    public function run(): void
    {
        $tipos = ['CAMIONETA', 'FURGONETA', 'CAMION', 'MOTO', 'OTRO'];

        foreach ($tipos as $nombre) {
            TmsTipoVehiculo::firstOrCreate(
                ['id_empresa' => 1, 'nombre' => $nombre],
                ['estado' => 1]
            );
        }
    }
}
