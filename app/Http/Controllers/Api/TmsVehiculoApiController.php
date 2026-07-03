<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class TmsVehiculoApiController extends Controller
{
    private function empresa(): int  { return (int) session('id_empresa'); }
    private function sucursal(): int { return (int) session('sucursal'); }

    public function listar(Request $r): mixed
    {
        $q = DB::table('tms_vehiculos')
            ->join('tms_tipos_vehiculo', 'tms_vehiculos.id_tipo', '=', 'tms_tipos_vehiculo.id')
            ->where('tms_vehiculos.id_empresa', $this->empresa())
            ->where('tms_vehiculos.sucursal', $this->sucursal())
            ->select(
                'tms_vehiculos.id', 'tms_vehiculos.placa', 'tms_tipos_vehiculo.nombre as tipo',
                'tms_vehiculos.marca', 'tms_vehiculos.modelo', 'tms_vehiculos.anio',
                'tms_vehiculos.capacidad_kg', 'tms_vehiculos.tara_kg',
                'tms_vehiculos.largo_m', 'tms_vehiculos.ancho_m', 'tms_vehiculos.alto_m',
                'tms_vehiculos.capacidad_m3',
                'tms_vehiculos.soat_vence', 'tms_vehiculos.rev_tecnica_vence', 'tms_vehiculos.estado'
            );

        return DataTables::of($q)->make(true);
    }

    private function rules(): array
    {
        return [
            'placa'             => 'required|string|max:15',
            'id_tipo'          => 'required|integer|exists:tms_tipos_vehiculo,id',
            'marca'            => 'nullable|string|max:60',
            'modelo'           => 'nullable|string|max:60',
            'anio'             => 'nullable|integer',
            'capacidad_kg'     => 'required|numeric|min:0',
            'tara_kg'          => 'nullable|numeric|min:0',
            'largo_m'          => 'nullable|numeric|min:0',
            'ancho_m'          => 'nullable|numeric|min:0',
            'alto_m'           => 'nullable|numeric|min:0',
            'capacidad_m3'     => 'nullable|numeric|min:0',
            'soat_vence'       => 'nullable|date',
            'rev_tecnica_vence'=> 'nullable|date',
        ];
    }

    private function payload(Request $r): array
    {
        return [
            'placa'             => strtoupper(trim($r->placa)),
            'id_tipo'           => $r->id_tipo,
            'marca'             => $r->marca ?? null,
            'modelo'            => $r->modelo ?? null,
            'anio'              => $r->anio ?? null,
            'capacidad_kg'      => $r->capacidad_kg ?? 0,
            'tara_kg'           => $r->tara_kg ?? null,
            'largo_m'           => $r->largo_m ?? null,
            'ancho_m'           => $r->ancho_m ?? null,
            'alto_m'            => $r->alto_m ?? null,
            'capacidad_m3'      => $r->capacidad_m3 ?? null,
            'soat_vence'        => $r->soat_vence ?? null,
            'rev_tecnica_vence' => $r->rev_tecnica_vence ?? null,
        ];
    }

    public function guardar(Request $r): JsonResponse
    {
        $r->validate($this->rules());

        $data = $this->payload($r) + [
            'id_empresa' => $this->empresa(),
            'sucursal'   => $this->sucursal(),
            'estado'     => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        $id = DB::table('tms_vehiculos')->insertGetId($data);
        return response()->json(['res' => true, 'id' => $id]);
    }

    public function editar(Request $r): JsonResponse
    {
        $r->validate($this->rules() + ['id' => 'required|integer']);

        DB::table('tms_vehiculos')
            ->where('id', $r->id)
            ->where('id_empresa', $this->empresa())
            ->update($this->payload($r) + [
                'estado'     => (int) ($r->estado ?? 1),
                'updated_at' => now(),
            ]);

        return response()->json(['res' => true]);
    }

    public function toggle(Request $r): JsonResponse
    {
        $r->validate(['id' => 'required|integer']);
        $row = DB::table('tms_vehiculos')->where('id_empresa', $this->empresa())->where('id', $r->id)->first();
        if (!$row) return response()->json(['res' => false, 'msg' => 'No encontrado.'], 404);
        $new = $row->estado ? 0 : 1;
        DB::table('tms_vehiculos')->where('id', $r->id)->update(['estado' => $new, 'updated_at' => now()]);
        return response()->json(['res' => true, 'estado' => $new]);
    }
}
