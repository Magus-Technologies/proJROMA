<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Almacen;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class AlmacenApiController extends Controller
{
    private function empresa(): int { return (int) session('id_empresa'); }

    /** ?activos=1 para traer solo activos (selects). */
    public function listar(Request $request): JsonResponse
    {
        return response()->json(
            Almacen::where('id_empresa', $this->empresa())
                ->when($request->boolean('activos'), fn ($q) => $q->where('estado', '1'))
                ->orderBy('nombre')
                ->get()
        );
    }

    public function guardar(Request $request): JsonResponse
    {
        $data = $request->validate([
            'nombre'      => 'required|string|max:150',
            'codigo'      => 'nullable|string|max:50',
            'descripcion' => 'nullable|string|max:255',
            'id_sucursal' => 'nullable|integer',
            'estado'      => 'nullable|in:0,1',
        ]);
        $data['id_empresa'] = $this->empresa();
        $data['estado']     = $request->input('estado', '1');

        $a = Almacen::create($data);
        return response()->json(['res' => true, 'id' => $a->id_almacen]);
    }

    public function editar(Request $request): JsonResponse
    {
        $request->validate([
            'id'          => 'required|integer',
            'nombre'      => 'required|string|max:150',
            'codigo'      => 'nullable|string|max:50',
            'descripcion' => 'nullable|string|max:255',
            'id_sucursal' => 'nullable|integer',
            'estado'      => 'nullable|in:0,1',
        ]);

        Almacen::where('id_empresa', $this->empresa())
            ->where('id_almacen', $request->id)
            ->update([
                'nombre'      => $request->nombre,
                'codigo'      => $request->codigo,
                'descripcion' => $request->descripcion,
                'id_sucursal' => $request->id_sucursal,
                'estado'      => $request->input('estado', '1'),
            ]);

        return response()->json(['res' => true]);
    }

    public function toggle(Request $request): JsonResponse
    {
        $request->validate(['id' => 'required|integer']);
        $a = Almacen::where('id_empresa', $this->empresa())->where('id_almacen', $request->id)->firstOrFail();
        $a->update(['estado' => $a->estado === '1' ? '0' : '1']);
        return response()->json(['res' => true, 'estado' => $a->estado]);
    }

    public function borrar(Request $request): JsonResponse
    {
        $request->validate(['id' => 'required|integer']);
        $a = Almacen::where('id_empresa', $this->empresa())->where('id_almacen', $request->id)->firstOrFail();

        // Validación: no eliminar si hay productos en este almacén (por su código actual)
        if ($a->codigo !== null) {
            $enUso = DB::table('productos')
                ->where('id_empresa', $this->empresa())
                ->where('almacen', $a->codigo)
                ->where('estado', '1')
                ->exists();
            if ($enUso) {
                return response()->json([
                    'res' => false,
                    'msg' => 'No se puede eliminar: hay productos en este almacén.',
                ], 409);
            }
        }

        $a->delete();
        return response()->json(['res' => true]);
    }
}
