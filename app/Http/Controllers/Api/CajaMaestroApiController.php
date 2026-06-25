<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class CajaMaestroApiController extends Controller
{
    private function empresa(): int  { return (int) session('id_empresa'); }
    private function sucursal(): int { return (int) session('sucursal'); }

    public function listar(): mixed
    {
        return DataTables::of(
            DB::table('cajas')
                ->where('cajas.id_empresa', $this->empresa())
                ->where('cajas.sucursal', $this->sucursal())
                ->leftJoin('usuarios as u', 'u.usuario_id', '=', 'cajas.id_usuario_responsable')
                ->select('cajas.*', DB::raw('COALESCE(CONCAT(u.nombres, " ", u.apellidos), "-") as responsable'))
        )->make(true);
    }

    public function guardar(Request $r): JsonResponse
    {
        $r->validate([
            'nombre' => 'required|string|max:100',
            'tipo'   => 'required|in:GENERAL,CHICA,VENDEDOR',
        ]);

        $id = DB::table('cajas')->insertGetId([
            'id_empresa'             => $this->empresa(),
            'sucursal'               => $this->sucursal(),
            'nombre'                 => $r->nombre,
            'tipo'                   => $r->tipo,
            'id_usuario_responsable' => $r->id_usuario_responsable ?? null,
            'id_caja_padre'          => $r->tipo === 'CHICA' ? ($r->id_caja_padre ?? null) : null,
            'monto_fondo_fijo'       => $r->tipo === 'CHICA' ? ($r->monto_fondo_fijo ?? 0) : null,
            'saldo_actual'           => 0,
            'moneda'                 => 'PEN',
            'estado'                 => 'ACTIVA',
        ]);

        return response()->json(['res' => true, 'id' => $id]);
    }

    public function editar(Request $r): JsonResponse
    {
        $r->validate([
            'id'     => 'required|integer',
            'nombre' => 'required|string|max:100',
        ]);

        DB::table('cajas')->where('id', $r->id)
            ->where('id_empresa', $this->empresa())
            ->update([
                'nombre'                 => $r->nombre,
                'id_usuario_responsable' => $r->id_usuario_responsable ?? null,
                'monto_fondo_fijo'       => $r->monto_fondo_fijo ?? null,
                'estado'                 => $r->estado ?? 'ACTIVA',
            ]);

        return response()->json(['res' => true]);
    }

    public function toggle(Request $r): JsonResponse
    {
        $r->validate(['id' => 'required|integer']);
        $row = DB::table('cajas')->where('id_empresa', $this->empresa())->where('id', $r->id)->first();
        if (!$row) return response()->json(['res' => false, 'msg' => 'No encontrada.'], 404);
        $new = $row->estado === 'ACTIVA' ? 'INACTIVA' : 'ACTIVA';
        DB::table('cajas')->where('id', $r->id)->update(['estado' => $new]);
        return response()->json(['res' => true, 'estado' => $new]);
    }

    public function opciones(): JsonResponse
    {
        $cajas = DB::table('cajas')
            ->where('id_empresa', $this->empresa())
            ->where('sucursal', $this->sucursal())
            ->where('estado', 'ACTIVA')
            ->get(['id', 'nombre', 'tipo', 'id_usuario_responsable']);

        $usuarios = DB::table('usuarios')
            ->where('id_empresa', $this->empresa())
            ->where('estado', '1')
            ->get(['usuario_id', 'nombres', 'apellidos']);

        return response()->json(['cajas' => $cajas, 'usuarios' => $usuarios]);
    }
}
