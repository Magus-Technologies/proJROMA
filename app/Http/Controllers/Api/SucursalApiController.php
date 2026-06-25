<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sucursal;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class SucursalApiController extends Controller
{
    private function empresa(): int { return (int) session('id_empresa'); }

    /** ?activos=1 para solo activas (selects). */
    public function listar(Request $request): JsonResponse
    {
        return response()->json(
            Sucursal::where('empresa_id', $this->empresa())
                ->when($request->boolean('activos'), fn ($q) => $q->where('estado', '1'))
                ->orderBy('cod_sucursal')
                ->get()
        );
    }

    public function guardar(Request $request): JsonResponse
    {
        $data = $request->validate([
            'nombre'       => 'required|string|max:150',
            'cod_sucursal' => 'required|integer|min:1',
            'direccion'    => 'nullable|string|max:150',
            'estado'       => 'nullable|in:0,1',
        ]);
        $data['empresa_id'] = $this->empresa();
        $data['estado']     = $request->input('estado', '1');

        $s = Sucursal::create($data);
        return response()->json(['res' => true, 'id' => $s->id_sucursal]);
    }

    public function editar(Request $request): JsonResponse
    {
        $request->validate([
            'id'           => 'required|integer',
            'nombre'       => 'required|string|max:150',
            'cod_sucursal' => 'required|integer|min:1',
            'direccion'    => 'nullable|string|max:150',
            'estado'       => 'nullable|in:0,1',
        ]);

        Sucursal::where('empresa_id', $this->empresa())
            ->where('id_sucursal', $request->id)
            ->update([
                'nombre'       => $request->nombre,
                'cod_sucursal' => $request->cod_sucursal,
                'direccion'    => $request->direccion,
                'estado'       => $request->input('estado', '1'),
            ]);

        return response()->json(['res' => true]);
    }

    public function toggle(Request $request): JsonResponse
    {
        $request->validate(['id' => 'required|integer']);
        $s = Sucursal::where('empresa_id', $this->empresa())->where('id_sucursal', $request->id)->firstOrFail();
        $s->update(['estado' => $s->estado === '1' ? '0' : '1']);
        return response()->json(['res' => true, 'estado' => $s->estado]);
    }

    public function borrar(Request $request): JsonResponse
    {
        $request->validate(['id' => 'required|integer']);
        $s = Sucursal::where('empresa_id', $this->empresa())->where('id_sucursal', $request->id)->firstOrFail();

        // No eliminar si hay usuarios asignados a esa sucursal
        $enUso = DB::table('usuarios')->where('id_empresa', $this->empresa())->where('sucursal', $s->cod_sucursal)->exists();
        if ($enUso) {
            return response()->json(['res' => false, 'msg' => 'No se puede eliminar: hay usuarios asignados a esta sucursal.'], 409);
        }

        $s->delete();
        return response()->json(['res' => true]);
    }
}
