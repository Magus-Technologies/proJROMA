<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Reconstruye los mercados reales (IDs 1-22) a los que apuntan los 1,900+
 * clientes en clientes.mercado. Los nombres se dedujeron analizando las
 * direcciones de los propios clientes de cada mercado (MDO CANTO CHICO,
 * MDO MILAGROS, etc.). Se insertan con ID explícito para que el enlace
 * cliente→mercado quede funcionando sin tocar la tabla clientes.
 *
 * También completa el ubigeo de los mercados de prueba existentes (23-37)
 * a partir de su distrito.
 *
 * Ejecutar: php artisan db:seed --class=MercadosClientesSeeder
 */
class MercadosClientesSeeder extends Seeder
{
    private const EMPRESA  = 12;
    private const SUCURSAL = 1;

    private const UBIGEO_SJL   = '150132'; // San Juan de Lurigancho
    private const UBIGEO_RIMAC = '150128'; // Rímac

    public function run(): void
    {
        //    id  nombre                              distrito                   ubigeo              dirección (referencial)
        $mercados = [
            [ 1, 'Mercado El Progreso I',            'San Juan de Lurigancho',  self::UBIGEO_SJL,   'Av. El Progreso s/n'],
            [ 2, 'Mercado El Progreso II',           'San Juan de Lurigancho',  self::UBIGEO_SJL,   'Av. El Progreso cdra. 11'],
            [ 3, 'Mercado Centro Cívico',            'San Juan de Lurigancho',  self::UBIGEO_SJL,   'Av. José Granda s/n'],
            [ 4, 'Mercado Virgen del Carmen',        'San Juan de Lurigancho',  self::UBIGEO_SJL,   'Av. Canto Grande s/n'],
            [ 5, 'Mercado El Bosque',                'San Juan de Lurigancho',  self::UBIGEO_SJL,   'Av. El Bosque s/n'],
            [ 6, 'Mercado Gladis Carrillo',          'San Juan de Lurigancho',  self::UBIGEO_SJL,   'Av. Santa Rosa s/n'],
            [ 7, 'Mercado Villa Norte',              'San Juan de Lurigancho',  self::UBIGEO_SJL,   'Av. Las Flores s/n'],
            [ 8, 'Mercado Canto Chico I',            'San Juan de Lurigancho',  self::UBIGEO_SJL,   'Av. Canto Chico s/n'],
            [ 9, 'Mercado Canto Chico II',           'San Juan de Lurigancho',  self::UBIGEO_SJL,   'Av. Canto Chico cdra. 5'],
            [10, 'Mercado Ascopro',                  'San Juan de Lurigancho',  self::UBIGEO_SJL,   'Jr. Apatitos s/n'],
            [11, 'Mercado María Parado de Bellido',  'San Juan de Lurigancho',  self::UBIGEO_SJL,   'Av. María Parado de Bellido s/n'],
            [12, 'Mercado Micaela Bastidas',         'San Juan de Lurigancho',  self::UBIGEO_SJL,   'Av. Micaela Bastidas s/n'],
            [13, 'Mercado Milagros',                 'San Juan de Lurigancho',  self::UBIGEO_SJL,   'Av. Los Milagros s/n'],
            [14, 'Mercado Ciudad y Campo',           'Rímac',                   self::UBIGEO_RIMAC, 'Av. Alcázar s/n'],
            [15, 'Mercado Ventura Rossy',            'San Juan de Lurigancho',  self::UBIGEO_SJL,   'Av. Próceres de la Independencia s/n'],
            [16, 'Mercado Canto Rey I',              'San Juan de Lurigancho',  self::UBIGEO_SJL,   'Av. Canto Rey s/n'],
            [17, 'Mercado Canto Rey II',             'San Juan de Lurigancho',  self::UBIGEO_SJL,   'Av. Canto Rey cdra. 8'],
            [18, 'Mercado Ventura Rossy II',         'San Juan de Lurigancho',  self::UBIGEO_SJL,   'Av. Próceres de la Independencia cdra. 20'],
            [19, 'Mercado N° 2',                     'San Juan de Lurigancho',  self::UBIGEO_SJL,   'Jr. Apatitos 1610'],
            [20, 'Mercado Corazón de Jesús',         'San Juan de Lurigancho',  self::UBIGEO_SJL,   'Av. Corazón de Jesús s/n'],
            [21, 'Mercado Central SJL',              'San Juan de Lurigancho',  self::UBIGEO_SJL,   'Av. Próceres de la Independencia cdra. 15'],
            [22, 'Mercado 19 de Enero',              'San Juan de Lurigancho',  self::UBIGEO_SJL,   'Av. 19 de Enero s/n'],
        ];

        $creados = 0;
        foreach ($mercados as [$id, $nombre, $distrito, $ubigeo, $direccion]) {
            $insertado = DB::table('tms_mercados')->insertOrIgnore([
                'id'         => $id,
                'id_empresa' => self::EMPRESA,
                'sucursal'   => self::SUCURSAL,
                'nombre'     => $nombre,
                'direccion'  => $direccion,
                'referencia' => 'Nombre reconstruido desde las direcciones de sus clientes',
                'distrito'   => $distrito,
                'ubigeo'     => $ubigeo,
                'telefono'   => null,
                'estado'     => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $creados += $insertado;
        }

        // Completa el ubigeo de los mercados que tienen distrito pero no código.
        $pendientes = DB::table('tms_mercados')
            ->where('id_empresa', self::EMPRESA)
            ->where(fn ($q) => $q->whereNull('ubigeo')->orWhere('ubigeo', ''))
            ->whereNotNull('distrito')->where('distrito', '<>', '')
            ->get(['id', 'distrito']);

        $completados = 0;
        foreach ($pendientes as $m) {
            $nombreDistrito = mb_strtoupper(str_replace('Cercado de Lima', 'Lima', $m->distrito));
            $u = DB::table('ubigeo_inei')
                ->where('departamento', '15')->where('provincia', '01')
                ->where('distrito', '<>', '00')
                ->where('nombre', $nombreDistrito)
                ->first();
            if ($u) {
                DB::table('tms_mercados')->where('id', $m->id)
                    ->update(['ubigeo' => $u->departamento . $u->provincia . $u->distrito]);
                $completados++;
            }
        }

        $this->command?->info("Mercados creados: {$creados} — Ubigeos completados en existentes: {$completados}");
    }
}
