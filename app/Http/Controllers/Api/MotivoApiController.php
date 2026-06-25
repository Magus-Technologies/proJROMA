<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MotivoMovimiento;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class MotivoApiController extends Controller
{
    private function empresa(): int { return (int) session('id_empresa'); }

    /** Todos los motivos de la empresa (gestión). */
    public function listar(): JsonResponse
    {
        return response()->json(
            MotivoMovimiento::where('id_empresa', $this->empresa())
                ->orderBy('tipo')->orderBy('nombre')->get()
        );
    }

    public function guardar(Request $request): JsonResponse
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:120',
            'tipo'   => 'required|in:I,S',
            'estado' => 'nullable|in:0,1',
        ]);
        $data['id_empresa'] = $this->empresa();
        $data['es_sistema'] = 0;
        $data['estado']     = $request->input('estado', '1');

        $m = MotivoMovimiento::create($data);
        return response()->json(['res' => true, 'id' => $m->id_motivo]);
    }

    public function editar(Request $request): JsonResponse
    {
        $request->validate([
            'id'     => 'required|integer',
            'nombre' => 'required|string|max:120',
            'tipo'   => 'required|in:I,S',
            'estado' => 'nullable|in:0,1',
        ]);

        MotivoMovimiento::where('id_empresa', $this->empresa())
            ->where('id_motivo', $request->id)
            ->update(['nombre' => $request->nombre, 'tipo' => $request->tipo, 'estado' => $request->input('estado', '1')]);

        return response()->json(['res' => true]);
    }

    public function toggle(Request $request): JsonResponse
    {
        $request->validate(['id' => 'required|integer']);
        $m = MotivoMovimiento::where('id_empresa', $this->empresa())->where('id_motivo', $request->id)->firstOrFail();
        $m->update(['estado' => $m->estado === '1' ? '0' : '1']);
        return response()->json(['res' => true, 'estado' => $m->estado]);
    }

    public function borrar(Request $request): JsonResponse
    {
        $request->validate(['id' => 'required|integer']);
        $m = MotivoMovimiento::where('id_empresa', $this->empresa())->where('id_motivo', $request->id)->firstOrFail();

        if ((int) $m->es_sistema === 1) {
            return response()->json(['res' => false, 'msg' => 'No se puede eliminar un motivo del sistema.'], 409);
        }
        $enUso = DB::table('inventario_movimientos')->where('id_motivo', $m->id_motivo)->exists();
        if ($enUso) {
            return response()->json(['res' => false, 'msg' => 'No se puede eliminar: el motivo tiene movimientos registrados.'], 409);
        }

        $m->delete();
        return response()->json(['res' => true]);
    }
}
