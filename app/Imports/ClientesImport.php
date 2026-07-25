<?php

namespace App\Imports;

use App\Models\Cliente;
use App\Models\TmsMercado;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ClientesImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError
{
    use Importable, SkipsErrors;

    public function __construct(
        protected int $idEmpresa,
    ) {}

    public function model(array $row)
    {
        $mercadoId = null;
        if (! empty($row['mercado_zona'])) {
            $mercado = TmsMercado::where('id_empresa', $this->idEmpresa)
                ->where('nombre', trim($row['mercado_zona']))
                ->first();
            if ($mercado) {
                $mercadoId = $mercado->id;
            }
        }

        return Cliente::create([
            'documento'  => trim($row['ruc_dni'] ?? ''),
            'datos'      => trim($row['nombre_razon_social']),
            'direccion'  => trim($row['direccion'] ?? ''),
            'distrito'   => trim($row['distrito'] ?? ''),
            'telefono'   => trim((string) ($row['telefono'] ?? '')),
            'email'      => trim($row['email'] ?? ''),
            'mercado'    => $mercadoId,
            'id_empresa' => $this->idEmpresa,
        ]);
    }

    public function rules(): array
    {
        return [
            'ruc_dni'             => 'nullable|max:15',
            'nombre_razon_social' => 'required|max:200',
            'direccion'           => 'nullable|max:200',
            'distrito'            => 'nullable|max:100',
            'telefono'            => 'nullable|max:20',
            'email'               => 'nullable|email|max:100',
        ];
    }

    public function customValidationAttributes(): array
    {
        return [
            'ruc_dni'             => 'RUC/DNI',
            'nombre_razon_social' => 'Nombre / Razón Social',
            'direccion'           => 'Dirección',
            'distrito'            => 'Distrito',
            'telefono'            => 'Teléfono',
            'email'               => 'Email',
        ];
    }

    public function headingRow(): int
    {
        return 1;
    }
}
