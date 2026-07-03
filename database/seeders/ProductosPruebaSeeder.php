<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Productos de prueba con peso por unidad, para probar el flujo de
 * cotización → aumento → despacho por peso (capacidad del vehículo).
 *
 * Caso clave: el mismo fideo viene en presentaciones de 1 kg, 1/2 kg y 250 g,
 * cada una es un producto distinto con su propio peso unitario. Un pedido de
 * "35 kilos" se arma combinando unidades de estas presentaciones.
 *
 * Ejecutar: php artisan db:seed --class=ProductosPruebaSeeder
 */
class ProductosPruebaSeeder extends Seeder
{
    private const EMPRESA  = 12;
    private const SUCURSAL = 1;
    private const ALMACEN  = '1';

    public function run(): void
    {
        $categorias = $this->catalogo('categorias', 'id_categoria', ['Fideos y Pastas', 'Abarrotes', 'Aceites', 'Lácteos y Conservas']);
        $marcas     = $this->catalogo('marcas', 'id_marca', ['Roma', 'Don Vittorio', 'Costeño', 'Primor', 'Gloria', 'Florida']);

        foreach (['Paquete', 'Bolsa', 'Botella', 'Lata', 'Saco'] as $nombre) {
            DB::table('presentaciones')->insertOrIgnore([
                'id_empresa' => self::EMPRESA, 'nombre' => $nombre, 'estado' => 1,
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }

        //           código      descripción                         categoría           marca          medida    present.   und/pres  peso(kg) costo   precio  stock
        $productos = [
            ['TEST-001', 'SPAGHETTI ROMA 1K',                 'Fideos y Pastas',  'Roma',         'Unidad', 'Paquete', 10, 1.00,  3.20,  4.50, 200],
            ['TEST-002', 'SPAGHETTI ROMA 1/2K',               'Fideos y Pastas',  'Roma',         'Unidad', 'Paquete', 20, 0.50,  1.70,  2.40, 300],
            ['TEST-003', 'SPAGHETTI ROMA 250G',               'Fideos y Pastas',  'Roma',         'Unidad', 'Paquete', 40, 0.25,  0.90,  1.30, 400],
            ['TEST-004', 'MACARRON DON VITTORIO 1K',          'Fideos y Pastas',  'Don Vittorio', 'Unidad', 'Paquete', 10, 1.00,  3.50,  4.90, 150],
            ['TEST-005', 'MACARRON DON VITTORIO 1/2K',        'Fideos y Pastas',  'Don Vittorio', 'Unidad', 'Paquete', 20, 0.50,  1.85,  2.60, 250],
            ['TEST-006', 'CABELLO DE ANGEL DON VITTORIO 250G','Fideos y Pastas',  'Don Vittorio', 'Unidad', 'Paquete', 40, 0.25,  0.95,  1.40, 350],
            ['TEST-007', 'TALLARIN ROMA 1K',                  'Fideos y Pastas',  'Roma',         'Unidad', 'Paquete', 10, 1.00,  3.30,  4.60, 180],
            ['TEST-008', 'ARROZ COSTEÑO EXTRA 5K',            'Abarrotes',        'Costeño',      'Unidad', 'Bolsa',    5, 5.00, 18.50, 24.90,  80],
            ['TEST-009', 'ARROZ COSTEÑO EXTRA 750G',          'Abarrotes',        'Costeño',      'Unidad', 'Bolsa',   20, 0.75,  3.10,  4.20, 240],
            ['TEST-010', 'AZUCAR RUBIA 1K',                   'Abarrotes',        'Costeño',      'Unidad', 'Bolsa',   25, 1.00,  3.40,  4.50, 500],
            ['TEST-011', 'AZUCAR RUBIA 5K',                   'Abarrotes',        'Costeño',      'Unidad', 'Bolsa',    5, 5.00, 16.00, 21.50,  60],
            ['TEST-012', 'HARINA SIN PREPARAR 1K',            'Abarrotes',        'Costeño',      'Unidad', 'Bolsa',   10, 1.00,  4.20,  5.80, 120],
            ['TEST-013', 'ACEITE PRIMOR 1L',                  'Aceites',          'Primor',       'Unidad', 'Botella', 12, 0.92,  8.90, 11.50, 144],
            ['TEST-014', 'ACEITE PRIMOR 5L',                  'Aceites',          'Primor',       'Unidad', 'Botella',  4, 4.60, 39.00, 48.90,  40],
            ['TEST-015', 'LECHE GLORIA EVAPORADA 400G',       'Lácteos y Conservas', 'Gloria',    'Unidad', 'Lata',    24, 0.40,  3.60,  4.50, 480],
            ['TEST-016', 'ATUN FLORIDA TROZOS 170G',          'Lácteos y Conservas', 'Florida',   'Unidad', 'Lata',    48, 0.17,  5.20,  6.90, 288],
        ];

        $creados = 0;
        foreach ($productos as [$codigo, $desc, $cat, $marca, $medida, $presenta, $cntPres, $peso, $costo, $precio, $stock]) {
            $existe = DB::table('productos')
                ->where('id_empresa', self::EMPRESA)->where('codigo', $codigo)->exists();
            if ($existe) continue;

            DB::table('productos')->insert([
                'codigo'         => $codigo,
                'cod_barra'      => '77501' . str_pad((string) $creados, 8, '0', STR_PAD_LEFT),
                'descripcion'    => $desc,
                'precio'         => $precio,
                'costo'          => $costo,
                'cantidad'       => $stock,
                'peso_bruto'     => $peso,
                'medida'         => $medida,
                'presentaciones' => $presenta,
                'cnt_presenta'   => (string) $cntPres,
                'id_categoria'   => $categorias[$cat],
                'id_marca'       => $marcas[$marca],
                'id_empresa'     => self::EMPRESA,
                'sucursal'       => self::SUCURSAL,
                'almacen'        => self::ALMACEN,
                'estado'         => '1',
                'activo'         => 1,
                'iscbp'          => '0',
                'usar_barra'     => '0',
                'codsunat'       => '-',
                'ultima_salida'  => now()->toDateString(),
            ]);
            $creados++;
        }

        $this->command?->info("Productos de prueba creados: {$creados}");
    }

    /** Crea (si no existen) los nombres en un catálogo y devuelve [nombre => id]. */
    private function catalogo(string $tabla, string $pk, array $nombres): array
    {
        $ids = [];
        foreach ($nombres as $nombre) {
            $id = DB::table($tabla)->where('id_empresa', self::EMPRESA)->where('nombre', $nombre)->value($pk);
            if (! $id) {
                $id = DB::table($tabla)->insertGetId([
                    'nombre' => $nombre, 'id_empresa' => self::EMPRESA, 'estado' => '1',
                ], $pk);
            }
            $ids[$nombre] = $id;
        }

        return $ids;
    }
}
