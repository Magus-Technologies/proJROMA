<?php

namespace App\Imports;

use App\Models\Categoria;
use App\Models\Marca;
use App\Models\Producto;
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

        $categoriaId = null;
        if (! empty($row['categoria'])) {
            $categoriaId = Categoria::firstOrCreate(
                ['id_empresa' => $this->idEmpresa, 'nombre' => trim($row['categoria'])],
                ['estado' => '1'],
            )->id_categoria;
        }

        $marcaId = null;
        if (! empty($row['marca'])) {
            $marcaId = Marca::firstOrCreate(
                ['id_empresa' => $this->idEmpresa, 'nombre' => trim($row['marca'])],
                ['estado' => '1'],
            )->id_marca;
        }

        $this->creados++;

        return new Producto([
            'codigo'        => $codigo !== '' ? $codigo : null,
            'cod_barra'     => $codBarra !== '' ? $codBarra : null,
            'descripcion'   => trim($row['descripcion']),
            'id_categoria'  => $categoriaId,
            'id_marca'      => $marcaId,
            'precio'        => (float) $row['precio'],
            'precio_mayor'  => isset($row['precio_mayor']) && $row['precio_mayor'] !== '' ? (float) $row['precio_mayor'] : null,
            'costo'         => isset($row['costo']) && $row['costo'] !== '' ? (float) $row['costo'] : 0,
            'cantidad'      => isset($row['stock_inicial']) && $row['stock_inicial'] !== '' ? (int) $row['stock_inicial'] : 0,
            'id_empresa'    => $this->idEmpresa,
            'sucursal'      => $this->sucursal,
            'estado'        => '1',
            // Columnas NOT NULL heredadas del sistema legacy (mismo default que CreateAction)
            'ultima_salida' => now()->toDateString(),
            'codsunat'      => '-',
        ]);
    }

    public function rules(): array
    {
        return [
            'codigo'        => 'nullable|max:50',
            'cod_barra'     => 'nullable|max:50',
            'descripcion'   => 'required|max:255',
            'categoria'     => 'nullable|max:150',
            'marca'         => 'nullable|max:150',
            'precio'        => 'required|numeric|min:0',
            'precio_mayor'  => 'nullable|numeric|min:0',
            'costo'         => 'nullable|numeric|min:0',
            'stock_inicial' => 'nullable|integer|min:0',
        ];
    }

    public function customValidationAttributes(): array
    {
        return [
            'codigo'        => 'Código',
            'cod_barra'     => 'Cod. Barra',
            'descripcion'   => 'Descripción',
            'categoria'     => 'Categoría',
            'marca'         => 'Marca',
            'precio'        => 'Precio',
            'precio_mayor'  => 'Precio Mayor',
            'costo'         => 'Costo',
            'stock_inicial' => 'Stock Inicial',
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
