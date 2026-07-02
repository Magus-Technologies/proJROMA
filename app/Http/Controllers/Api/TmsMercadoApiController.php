<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class TmsMercadoApiController extends Controller
{
    private function empresa(): int  { return (int) session('id_empresa'); }
    private function sucursal(): int { return (int) session('sucursal'); }

    public function listar(Request $r): mixed
    {
        $q = DB::table('tms_mercados')
            ->where('id_empresa', $this->empresa())
            ->where('sucursal', $this->sucursal())
            ->select('id', 'nombre', 'direccion', 'referencia', 'distrito', 'telefono', 'estado');

        return DataTables::of($q)->make(true);
    }

    public function guardar(Request $r): JsonResponse
    {
        $r->validate([
            'nombre'    => 'required|string|max:120',
            'direccion' => 'required|string|max:245',
        ]);

        $id = DB::table('tms_mercados')->insertGetId([
            'id_empresa' => $this->empresa(),
            'sucursal'   => $this->sucursal(),
            'nombre'     => $r->nombre,
            'direccion'  => $r->direccion,
            'referencia' => $r->referencia ?? null,
            'distrito'   => $r->distrito ?? null,
            'telefono'   => $r->telefono ?? null,
            'estado'     => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['res' => true, 'id' => $id]);
    }

    public function editar(Request $r): JsonResponse
    {
        $r->validate([
            'id'        => 'required|integer',
            'nombre'    => 'required|string|max:120',
            'direccion' => 'required|string|max:245',
        ]);

        DB::table('tms_mercados')
            ->where('id', $r->id)
            ->where('id_empresa', $this->empresa())
            ->update([
                'nombre'     => $r->nombre,
                'direccion'  => $r->direccion,
                'referencia' => $r->referencia ?? null,
                'distrito'   => $r->distrito ?? null,
                'telefono'   => $r->telefono ?? null,
                'estado'     => (int) ($r->estado ?? 1),
                'updated_at' => now(),
            ]);

        return response()->json(['res' => true]);
    }

    public function toggle(Request $r): JsonResponse
    {
        $r->validate(['id' => 'required|integer']);
        $row = DB::table('tms_mercados')->where('id_empresa', $this->empresa())->where('id', $r->id)->first();
        if (!$row) return response()->json(['res' => false, 'msg' => 'No encontrado.'], 404);
        $new = $row->estado ? 0 : 1;
        DB::table('tms_mercados')->where('id', $r->id)->update(['estado' => $new, 'updated_at' => now()]);
        return response()->json(['res' => true, 'estado' => $new]);
    }
}
