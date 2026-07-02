<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class TmsConductorApiController extends Controller
{
    private function empresa(): int  { return (int) session('id_empresa'); }
    private function sucursal(): int { return (int) session('sucursal'); }

    public function listar(Request $r): mixed
    {
        $q = DB::table('tms_conductores')
            ->where('id_empresa', $this->empresa())
            ->where('sucursal', $this->sucursal())
            ->select(
                'id', 'nombres', 'documento', 'licencia', 'licencia_categoria',
                'licencia_vence', 'telefono', 'estado'
            );

        return DataTables::of($q)->make(true);
    }

    private function rules(): array
    {
        return [
            'nombres'            => 'required|string|max:120',
            'documento'          => 'nullable|string|max:15',
            'licencia'           => 'nullable|string|max:30',
            'licencia_categoria' => 'nullable|string|max:10',
            'licencia_vence'     => 'nullable|date',
            'telefono'           => 'nullable|string|max:20',
        ];
    }

    private function payload(Request $r): array
    {
        return [
            'nombres'            => $r->nombres,
            'documento'          => $r->documento ?? null,
            'licencia'           => $r->licencia ?? null,
            'licencia_categoria' => $r->licencia_categoria ?? null,
            'licencia_vence'     => $r->licencia_vence ?? null,
            'telefono'           => $r->telefono ?? null,
        ];
    }

    public function guardar(Request $r): JsonResponse
    {
        $r->validate($this->rules());

        $id = DB::table('tms_conductores')->insertGetId($this->payload($r) + [
            'id_empresa' => $this->empresa(),
            'sucursal'   => $this->sucursal(),
            'estado'     => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['res' => true, 'id' => $id]);
    }

    public function editar(Request $r): JsonResponse
    {
        $r->validate($this->rules() + ['id' => 'required|integer']);

        DB::table('tms_conductores')
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
        $row = DB::table('tms_conductores')->where('id_empresa', $this->empresa())->where('id', $r->id)->first();
        if (!$row) return response()->json(['res' => false, 'msg' => 'No encontrado.'], 404);
        $new = $row->estado ? 0 : 1;
        DB::table('tms_conductores')->where('id', $r->id)->update(['estado' => $new, 'updated_at' => now()]);
        return response()->json(['res' => true, 'estado' => $new]);
    }
}
