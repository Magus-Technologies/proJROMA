<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class ClientesApiController extends Controller
{
    private function empresa(): int { return (int) session('id_empresa'); }

    public function listar(Request $request): mixed
    {
        $query = Cliente::deEmpresa($this->empresa())->with('ruta');
        return DataTables::of($query)->make(true);
    }

    public function render(Request $request): JsonResponse
    {
        $clientes = Cliente::deEmpresa($this->empresa())
            ->when($request->filled('ruta'),  fn($q) => $q->where('id_ruta', $request->ruta))
            ->when($request->filled('term'),  fn($q) => $q->buscar($request->term))
            ->orderBy('datos')
            ->get(['id_cliente','documento','datos','direccion',
                   'telefono','dias_visitas','id_ruta','ultima_venta','total_venta']);
        return response()->json($clientes);
    }

    public function insertar(Request $request): JsonResponse
    {
        $request->validate([
            'documento' => 'required|string|max:11',
            'datos'     => 'required|string|max:245',
            'direccion' => 'nullable|string|max:245',
            'telefono'  => 'nullable|string|max:200',
            'email'     => 'nullable|email|max:200',
            'id_ruta'   => 'nullable|integer',
        ]);

        $cliente = Cliente::create(array_merge(
            $request->only(['documento','datos','direccion','distrito',
                           'telefono','email','dias_visitas','id_ruta','mercado']),
            ['id_empresa' => $this->empresa()]
        ));

        return response()->json(['res' => true, 'id' => $cliente->id_cliente]);
    }

    public function getOne(Request $request): JsonResponse
    {
        $request->validate(['id_cliente' => 'required|integer']);
        return response()->json(
            Cliente::deEmpresa($this->empresa())->findOrFail($request->id_cliente)
        );
    }

    public function editar(Request $request): JsonResponse
    {
        $request->validate(['id_cliente' => 'required|integer', 'datos' => 'required|string|max:245']);
        Cliente::deEmpresa($this->empresa())->findOrFail($request->id_cliente)
            ->update($request->only(['documento','datos','direccion','distrito',
                                     'telefono','email','dias_visitas','id_ruta','mercado']));
        return response()->json(['res' => true]);
    }

    public function borrar(Request $request): JsonResponse
    {
        $request->validate(['id_cliente' => 'required|integer']);
        $c = Cliente::deEmpresa($this->empresa())->findOrFail($request->id_cliente);
        if ($c->ventas()->exists()) {
            return response()->json(['res' => false, 'msg' => 'No se puede eliminar: el cliente tiene ventas registradas.'], 422);
        }
        $c->delete();
        return response()->json(['res' => true]);
    }

    public function buscarDatos(Request $request): JsonResponse
    {
        $term = $request->get('term', '');
        return response()->json(
            Cliente::deEmpresa($this->empresa())
                ->buscar($term)
                ->limit(20)
                ->get(['id_cliente','documento','datos','direccion','telefono','email'])
        );
    }

    public function insertarXLista(Request $request): JsonResponse
    {
        $request->validate(['lista' => 'required|array']);
        DB::beginTransaction();
        try {
            foreach ($request->lista as $item) {
                Cliente::updateOrCreate(
                    ['documento' => $item['documento'], 'id_empresa' => $this->empresa()],
                    array_merge($item, ['id_empresa' => $this->empresa()])
                );
            }
            DB::commit();
            return response()->json(['res' => true]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['res' => false, 'msg' => 'Error al importar.'], 500);
        }
    }
}
