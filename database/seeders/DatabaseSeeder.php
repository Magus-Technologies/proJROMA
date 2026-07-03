<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            MotivosMovimientoSeeder::class,
            TipoVehiculoSeeder::class,
            TmsSeeder::class,
            TmsConductorSeeder::class,
            TmsVehiculoSeeder::class,
            TmsRutaSeeder::class,
            TmsDespachoSeeder::class,
        ]);
    }
}
