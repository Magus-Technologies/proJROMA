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
            ->where('id_empresa', $this->empresa())
            ->where('sucursal', $this->sucursal())
            ->select(
                'id', 'placa', 'tipo', 'marca', 'modelo', 'anio',
                'capacidad_kg', 'tara_kg', 'largo_m', 'ancho_m', 'alto_m', 'capacidad_m3',
                'soat_vence', 'rev_tecnica_vence', 'estado'
            );

        return DataTables::of($q)->make(true);
    }

    private function rules(): array
    {
        return [
            'placa'             => 'required|string|max:15',
            'tipo'             => 'required|in:CAMIONETA,FURGONETA,CAMION,MOTO,OTRO',
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
            'tipo'              => $r->tipo,
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
