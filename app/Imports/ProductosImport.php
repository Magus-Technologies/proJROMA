<?php

namespace App\Imports;

use App\Models\Categoria;
use App\Models\Marca;
use App\Models\Presentacion;
use App\Models\Producto;
use App\Models\Subcategoria;
use App\Models\Submarca;
use App\Models\UnidadMedida;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ProductosImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError, SkipsOnFailure
{
    use Importable, SkipsErrors, SkipsFailures;

    private int $creados = 0;
    private int $duplicados = 0;

    public function __construct(
        protected int $idEmpresa,
        protected int $sucursal,
    ) {}

    public function model(array $row)
    {
        $codigo = trim((string) ($row['codigo'] ?? ''));
        $codBarra = trim((string) ($row['cod_barra'] ?? ''));

        // No duplicar: si ya existe un producto de la empresa con el mismo
        // código o código de barras, se salta la fila.
        if ($codigo !== '' || $codBarra !== '') {
            $existe = Producto::deEmpresa($this->idEmpresa)
                ->where(function ($q) use ($codigo, $codBarra) {
                    if ($codigo !== '') {
                        $q->orWhere('codigo', $codigo);
                    }
                    if ($codBarra !== '') {
                        $q->orWhere('cod_barra', $codBarra);
                    }
                })
                ->exists();

            if ($existe) {
                $this->duplicados++;

                return null;
            }
        }

        // Unidad de medida y presentación se guardan como texto (nombre),
        // creándolas en sus catálogos si no existen — igual que el formulario.
        $medida = null;
        if (! empty($row['unidad_de_medida'])) {
            $medida = UnidadMedida::firstOrCreate(
                ['id_empresa' => $this->idEmpresa, 'nombre' => trim($row['unidad_de_medida'])],
                ['estado' => 1],
            )->nombre;
        }

        $presentacion = null;
        if (! empty($row['presentacion'])) {
            $presentacion = Presentacion::firstOrCreate(
                ['id_empresa' => $this->idEmpresa, 'nombre' => trim($row['presentacion'])],
                ['estado' => 1],
            )->nombre;
        }

        $categoriaId = null;
        $subcategoriaId = null;
        if (! empty($row['categoria'])) {
            $categoriaId = Categoria::firstOrCreate(
                ['id_empresa' => $this->idEmpresa, 'nombre' => trim($row['categoria'])],
                ['estado' => '1'],
            )->id_categoria;

            // La subcategoría solo tiene sentido colgada de su categoría
            if (! empty($row['subcategoria'])) {
                $subcategoriaId = Subcategoria::firstOrCreate(
                    ['id_empresa' => $this->idEmpresa, 'id_categoria' => $categoriaId, 'nombre' => trim($row['subcategoria'])],
                    ['estado' => '1'],
                )->id_subcategoria;
            }
        }

        $marcaId = null;
        $submarcaId = null;
        if (! empty($row['marca'])) {
            $marcaId = Marca::firstOrCreate(
                ['id_empresa' => $this->idEmpresa, 'nombre' => trim($row['marca'])],
                ['estado' => '1'],
            )->id_marca;

            if (! empty($row['submarca'])) {
                $submarcaId = Submarca::firstOrCreate(
                    ['id_empresa' => $this->idEmpresa, 'id_marca' => $marcaId, 'nombre' => trim($row['submarca'])],
                    ['estado' => '1'],
                )->id_submarca;
            }
        }

        $this->creados++;

        return new Producto([
            'cod_barra'       => $codBarra !== '' ? $codBarra : null,
            'codigo'          => $codigo !== '' ? $codigo : null,
            'descripcion'     => trim($row['descripcion']),
            'medida'          => $medida,
            'presentaciones'  => $presentacion,
            'cnt_presenta'    => isset($row['unid_por_presentacion']) && $row['unid_por_presentacion'] !== '' ? (int) $row['unid_por_presentacion'] : null,
            'peso_bruto'      => (float) $row['peso_kg'],
            'id_categoria'    => $categoriaId,
            'id_subcategoria' => $subcategoriaId,
            'id_marca'        => $marcaId,
            'id_submarca'     => $submarcaId,
            'precio'          => isset($row['precio']) && $row['precio'] !== '' ? (float) $row['precio'] : 0,
            'costo'           => isset($row['costo']) && $row['costo'] !== '' ? (float) $row['costo'] : 0,
            // Catálogo de productos: el stock se gestiona en Almacén, no aquí.
            'cantidad'        => 0,
            'id_empresa'      => $this->idEmpresa,
            'sucursal'        => $this->sucursal,
            'estado'          => '1',
            'activo'          => 1,
            // Columnas NOT NULL heredadas del sistema legacy (mismo default que CreateAction)
            'ultima_salida'   => now()->toDateString(),
            'codsunat'        => '-',
        ]);
    }

    public function rules(): array
    {
        return [
            'cod_barra'             => 'nullable|max:50',
            'codigo'                => 'nullable|max:50',
            'descripcion'           => 'required|max:200',
            'unidad_de_medida'      => 'nullable|max:60',
            'presentacion'          => 'nullable|max:60',
            'unid_por_presentacion' => 'nullable|numeric|min:0',
            'peso_kg'               => 'required|numeric|min:0.01',
            'categoria'             => 'nullable|max:100',
            'subcategoria'          => 'nullable|max:100',
            'marca'                 => 'nullable|max:100',
            'submarca'              => 'nullable|max:100',
            'precio'                => 'nullable|numeric|min:0',
            'costo'                 => 'nullable|numeric|min:0',
        ];
    }

    public function customValidationAttributes(): array
    {
        return [
            'cod_barra'             => 'Cod. Barra',
            'codigo'                => 'Código',
            'descripcion'           => 'Descripción',
            'unidad_de_medida'      => 'Unidad de Medida',
            'presentacion'          => 'Presentación',
            'unid_por_presentacion' => 'Unid. por Presentación',
            'peso_kg'               => 'Peso (kg)',
            'categoria'             => 'Categoría',
            'subcategoria'          => 'Subcategoría',
            'marca'                 => 'Marca',
            'submarca'              => 'Submarca',
            'precio'                => 'Precio',
            'costo'                 => 'Costo',
        ];
    }

    public function headingRow(): int
    {
        return 1;
    }

    public function getCreados(): int
    {
        return $this->creados;
    }

    public function getDuplicados(): int
    {
        return $this->duplicados;
    }
}
