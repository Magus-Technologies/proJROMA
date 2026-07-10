<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CatalogoMolitaliaSeeder extends Seeder
{
    private const EMPRESA = 12;

    public function run(): void
    {
        $this->command?->info('Cargando catálogo Molitalia...');

        $categorias  = $this->seedCategorias();
        $this->seedSubcategorias($categorias);

        $marcas = $this->seedMarcas();
        $this->seedSubmarcas($marcas);

        $this->seedUnidadesMedida();
        $this->seedPresentaciones();

        $this->command?->info('Catálogo Molitalia cargado correctamente.');
    }

    private function seedCategorias(): array
    {
        $data = [
            ['nombre' => 'Fideos y Pastas',             'descripcion' => 'Pastas largas, cortas, instantáneas y especiales'],
            ['nombre' => 'Salsas y Conservas',           'descripcion' => 'Salsas de tomate, conservas vegetales y de pescado'],
            ['nombre' => 'Aceites y Margarinas',         'descripcion' => 'Aceites comestibles y margarinas para cocina'],
            ['nombre' => 'Harinas y Panificación',       'descripcion' => 'Harinas de trigo y pre-mezclas para panadería'],
            ['nombre' => 'Galletas y Snacks',            'descripcion' => 'Galletas dulces, saladas y snacks variados'],
            ['nombre' => 'Bebidas y Jugos',              'descripcion' => 'Néctares, jugos en polvo y bebidas hidratantes'],
            ['nombre' => 'Limpieza e Higiene',           'descripcion' => 'Jabones, detergentes y productos de limpieza'],
            ['nombre' => 'Arroz y Abarrotes',            'descripcion' => 'Arroz, azúcar, legumbres y abarrotes en general'],
        ];

        $ids = [];
        foreach ($data as $item) {
            $id = DB::table('categorias')->insertGetId([
                'nombre'      => $item['nombre'],
                'descripcion' => $item['descripcion'],
                'id_empresa'  => self::EMPRESA,
                'estado'      => '1',
            ], 'id_categoria');
            $ids[$item['nombre']] = $id;
        }

        return $ids;
    }

    private function seedSubcategorias(array $categorias): void
    {
        $data = [
            ['nombre' => 'Fideos Largos',             'categoria' => 'Fideos y Pastas'],
            ['nombre' => 'Fideos Cortos',             'categoria' => 'Fideos y Pastas'],
            ['nombre' => 'Pastas Instantáneas',       'categoria' => 'Fideos y Pastas'],
            ['nombre' => 'Pastas Especiales',         'categoria' => 'Fideos y Pastas'],
            ['nombre' => 'Salsas de Tomate',          'categoria' => 'Salsas y Conservas'],
            ['nombre' => 'Conservas Vegetales',        'categoria' => 'Salsas y Conservas'],
            ['nombre' => 'Conservas de Pescado',       'categoria' => 'Salsas y Conservas'],
            ['nombre' => 'Aceites Comestibles',         'categoria' => 'Aceites y Margarinas'],
            ['nombre' => 'Margarinas',                 'categoria' => 'Aceites y Margarinas'],
            ['nombre' => 'Harinas',                    'categoria' => 'Harinas y Panificación'],
            ['nombre' => 'Pre-mezclas',                'categoria' => 'Harinas y Panificación'],
            ['nombre' => 'Galletas Dulces',            'categoria' => 'Galletas y Snacks'],
            ['nombre' => 'Galletas Saladas',           'categoria' => 'Galletas y Snacks'],
            ['nombre' => 'Snacks',                     'categoria' => 'Galletas y Snacks'],
            ['nombre' => 'Néctares',                   'categoria' => 'Bebidas y Jugos'],
            ['nombre' => 'Jugos en Polvo',             'categoria' => 'Bebidas y Jugos'],
            ['nombre' => 'Bebidas Hidratantes',        'categoria' => 'Bebidas y Jugos'],
            ['nombre' => 'Jabones de Tocador',          'categoria' => 'Limpieza e Higiene'],
            ['nombre' => 'Detergentes',                'categoria' => 'Limpieza e Higiene'],
            ['nombre' => 'Arroz',                      'categoria' => 'Arroz y Abarrotes'],
            ['nombre' => 'Azúcar',                     'categoria' => 'Arroz y Abarrotes'],
            ['nombre' => 'Legumbres',                  'categoria' => 'Arroz y Abarrotes'],
        ];

        foreach ($data as $item) {
            DB::table('subcategorias')->insertOrIgnore([
                'nombre'       => $item['nombre'],
                'descripcion'  => null,
                'id_categoria' => $categorias[$item['categoria']],
                'id_empresa'   => self::EMPRESA,
                'estado'       => '1',
            ]);
        }
    }

    private function seedMarcas(): array
    {
        $data = [
            ['nombre' => 'Molitalia',  'descripcion' => 'Marca principal de pastas y harinas'],
            ['nombre' => 'Don Vittorio','descripcion' => 'Pastas premium'],
            ['nombre' => 'Primor',     'descripcion' => 'Aceites comestibles'],
            ['nombre' => 'Sello de Oro','descripcion' => 'Margarinas y aceites'],
            ['nombre' => 'Nutri-V',    'descripcion' => 'Galletas y snacks nutritivos'],
            ['nombre' => 'Opal',       'descripcion' => 'Jabones y detergentes'],
            ['nombre' => 'Patito',     'descripcion' => 'Galletas tradicionales'],
            ['nombre' => 'Activ',      'descripcion' => 'Bebidas hidratantes y jugos'],
        ];

        $ids = [];
        foreach ($data as $item) {
            $id = DB::table('marcas')->insertGetId([
                'nombre'      => $item['nombre'],
                'descripcion' => $item['descripcion'],
                'id_empresa'  => self::EMPRESA,
                'estado'      => '1',
            ], 'id_marca');
            $ids[$item['nombre']] = $id;
        }

        return $ids;
    }

    private function seedSubmarcas(array $marcas): void
    {
        $data = [
            ['nombre' => 'Molitalia Clásico',    'marca' => 'Molitalia'],
            ['nombre' => 'Molitalia Premium',    'marca' => 'Molitalia'],
            ['nombre' => 'Don Vittorio Spaghetti','marca' => 'Don Vittorio'],
            ['nombre' => 'Don Vittorio Tallarín', 'marca' => 'Don Vittorio'],
            ['nombre' => 'Primor Clásico',       'marca' => 'Primor'],
            ['nombre' => 'Primor Soflax',        'marca' => 'Primor'],
            ['nombre' => 'Sello de Oro Clásico',  'marca' => 'Sello de Oro'],
            ['nombre' => 'Sello de Oro Light',   'marca' => 'Sello de Oro'],
            ['nombre' => 'Nutri-V Original',     'marca' => 'Nutri-V'],
            ['nombre' => 'Nutri-V Integral',     'marca' => 'Nutri-V'],
            ['nombre' => 'Opal Clásico',         'marca' => 'Opal'],
            ['nombre' => 'Opal Active',          'marca' => 'Opal'],
            ['nombre' => 'Patito Clásico',       'marca' => 'Patito'],
            ['nombre' => 'Patito Rellenas',      'marca' => 'Patito'],
            ['nombre' => 'Activ Original',       'marca' => 'Activ'],
            ['nombre' => 'Activ Frescura',       'marca' => 'Activ'],
        ];

        foreach ($data as $item) {
            DB::table('submarcas')->insertOrIgnore([
                'nombre'       => $item['nombre'],
                'descripcion'  => null,
                'id_marca'     => $marcas[$item['marca']],
                'id_empresa'   => self::EMPRESA,
                'estado'       => '1',
            ]);
        }
    }

    private function seedUnidadesMedida(): void
    {
        $data = [
            ['nombre' => 'Unidad',      'abreviatura' => 'und'],
            ['nombre' => 'Kilogramo',   'abreviatura' => 'kg'],
            ['nombre' => 'Litro',       'abreviatura' => 'L'],
            ['nombre' => 'Gramo',       'abreviatura' => 'g'],
            ['nombre' => 'Mililitro',   'abreviatura' => 'mL'],
            ['nombre' => 'Caja',        'abreviatura' => 'cja'],
            ['nombre' => 'Paquete',     'abreviatura' => 'pqte'],
            ['nombre' => 'Saco',        'abreviatura' => 'saco'],
        ];

        foreach ($data as $item) {
            DB::table('unidades_medida')->insertOrIgnore([
                'id_empresa'  => self::EMPRESA,
                'nombre'      => $item['nombre'],
                'abreviatura' => $item['abreviatura'],
                'estado'      => 1,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }
    }

    private function seedPresentaciones(): void
    {
        $data = [
            'Bolsa', 'Botella', 'Caja', 'Frasco', 'Lata',
            'Paquete', 'Saco', 'Sachet', 'Sobre', 'Tarro',
            'Dispensador', 'Envase',
        ];

        foreach ($data as $nombre) {
            DB::table('presentaciones')->insertOrIgnore([
                'id_empresa' => self::EMPRESA,
                'nombre'     => $nombre,
                'estado'     => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
