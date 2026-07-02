<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class TmsRutaApiController extends Controller
{
    private function empresa(): int  { return (int) session('id_empresa'); }
    private function sucursal(): int { return (int) session('sucursal'); }

    // ── Rutas (maestro) ────────────────────────────────────────────────────
    public function listar(Request $r): mixed
    {
        $q = DB::table('tms_rutas as r')
            ->where('r.id_empresa', $this->empresa())
            ->where('r.sucursal', $this->sucursal())
            ->select(
                'r.id', 'r.nombre', 'r.descripcion', 'r.estado',
                DB::raw('(SELECT COUNT(*) FROM tms_ruta_puntos p WHERE p.id_ruta = r.id) as puntos')
            );

        return DataTables::of($q)->make(true);
    }

    public function guardar(Request $r): JsonResponse
    {
        $r->validate(['nombre' => 'required|string|max:120']);

        $id = DB::table('tms_rutas')->insertGetId([
            'id_empresa'  => $this->empresa(),
            'sucursal'    => $this->sucursal(),
            'nombre'      => $r->nombre,
            'descripcion' => $r->descripcion ?? null,
            'estado'      => 1,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        return response()->json(['res' => true, 'id' => $id]);
    }

    public function editar(Request $r): JsonResponse
    {
        $r->validate(['id' => 'required|integer', 'nombre' => 'required|string|max:120']);

        DB::table('tms_rutas')
            ->where('id', $r->id)
            ->where('id_empresa', $this->empresa())
            ->update([
                'nombre'      => $r->nombre,
                'descripcion' => $r->descripcion ?? null,
                'estado'      => (int) ($r->estado ?? 1),
                'updated_at'  => now(),
            ]);

        return response()->json(['res' => true]);
    }

    public function toggle(Request $r): JsonResponse
    {
        $r->validate(['id' => 'required|integer']);
        $row = DB::table('tms_rutas')->where('id_empresa', $this->empresa())->where('id', $r->id)->first();
        if (!$row) return response()->json(['res' => false, 'msg' => 'No encontrada.'], 404);
        $new = $row->estado ? 0 : 1;
        DB::table('tms_rutas')->where('id', $r->id)->update(['estado' => $new, 'updated_at' => now()]);
        return response()->json(['res' => true, 'estado' => $new]);
    }

    // ── Puntos de una ruta ──────────────────────────────────────────────────
    public function puntos(int $idRuta): JsonResponse
    {
        $puntos = DB::table('tms_ruta_puntos as p')
            ->leftJoin('tms_mercados as m', 'm.id', '=', 'p.id_mercado')
            ->leftJoin('clientes as c', 'c.id_cliente', '=', 'p.id_cliente')
            ->where('p.id_ruta', $idRuta)
            ->orderBy('p.orden')
            ->select(
                'p.id', 'p.tipo', 'p.orden', 'p.id_mercado', 'p.id_cliente',
                DB::raw("COALESCE(m.nombre, c.datos, '-') as nombre"),
                DB::raw("COALESCE(m.direccion, c.direccion, '-') as direccion")
            )
            ->get();

        return response()->json(['data' => $puntos]);
    }

    public function agregarPunto(Request $r): JsonResponse
    {
        $r->validate([
            'id_ruta'    => 'required|integer',
            'tipo'       => 'required|in:MERCADO,TIENDA',
            'id_mercado' => 'required_if:tipo,MERCADO|nullable|integer',
            'id_cliente' => 'required_if:tipo,TIENDA|nullable|integer',
        ]);

        // La ruta debe ser de la empresa
        $ruta = DB::table('tms_rutas')->where('id', $r->id_ruta)->where('id_empresa', $this->empresa())->first();
        if (!$ruta) return response()->json(['res' => false, 'msg' => 'Ruta no encontrada.'], 404);

        // Evitar duplicados del mismo punto
        $dup = DB::table('tms_ruta_puntos')
            ->where('id_ruta', $r->id_ruta)
            ->where('tipo', $r->tipo)
            ->when($r->tipo === 'MERCADO', fn ($q) => $q->where('id_mercado', $r->id_mercado))
            ->when($r->tipo === 'TIENDA', fn ($q) => $q->where('id_cliente', $r->id_cliente))
            ->exists();
        if ($dup) return response()->json(['res' => false, 'msg' => 'Ese punto ya está en la ruta.'], 409);

        $orden = (int) DB::table('tms_ruta_puntos')->where('id_ruta', $r->id_ruta)->max('orden') + 1;

        $id = DB::table('tms_ruta_puntos')->insertGetId([
            'id_ruta'    => $r->id_ruta,
            'tipo'       => $r->tipo,
            'id_mercado' => $r->tipo === 'MERCADO' ? $r->id_mercado : null,
            'id_cliente' => $r->tipo === 'TIENDA' ? $r->id_cliente : null,
            'orden'      => $orden,
        ]);

        return response()->json(['res' => true, 'id' => $id]);
    }

    public function quitarPunto(Request $r): JsonResponse
    {
        $r->validate(['id' => 'required|integer']);

        DB::table('tms_ruta_puntos as p')
            ->join('tms_rutas as r', 'r.id', '=', 'p.id_ruta')
            ->where('p.id', $r->id)
            ->where('r.id_empresa', $this->empresa())
            ->delete();

        return response()->json(['res' => true]);
    }

    // ── Opciones para los selects del modal de puntos ──────────────────────
    public function mercados(): JsonResponse
    {
        $mercados = DB::table('tms_mercados')
            ->where('id_empresa', $this->empresa())
            ->where('sucursal', $this->sucursal())
            ->where('estado', 1)
            ->orderBy('nombre')
            ->get(['id', 'nombre', 'direccion']);

        return response()->json(['data' => $mercados]);
    }

    public function buscarClientes(Request $r): JsonResponse
    {
        $term = trim((string) $r->q);

        $clientes = DB::table('clientes')
            ->where('id_empresa', $this->empresa())
            ->when($term !== '', function ($q) use ($term) {
                $q->where(function ($w) use ($term) {
                    $w->where('datos', 'like', "%{$term}%")
                      ->orWhere('documento', 'like', "%{$term}%");
                });
            })
            ->orderBy('datos')
            ->limit(20)
            ->get(['id_cliente', 'datos', 'documento', 'direccion', 'distrito']);

        return response()->json(['data' => $clientes]);
    }
}
