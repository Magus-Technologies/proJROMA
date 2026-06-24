<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class ProductosApiController extends Controller
{
    private function empresa(): int  { return (int) session('id_empresa'); }
    private function sucursal(): int { return (int) session('sucursal'); }

    public function listar(Request $request): mixed
    {
        $query = Producto::deEmpresa($this->empresa())
            ->activos()
            ->when($request->filled('almacenId'), fn($q) => $q->where('almacen', $request->almacenId));
        return DataTables::of($query)->make(true);
    }

    public function serverside(Request $request): mixed
    {
        return $this->listar($request);
    }

    public function guardar(Request $request): JsonResponse
    {
        $request->validate([
            'descripcion' => 'required|string|max:245',
            'precio'      => 'required|numeric|min:0',
            'costo'       => 'nullable|numeric|min:0',
            'cantidad'    => 'nullable|integer|min:0',
        ]);

        $producto = Producto::create(array_merge(
            $request->only(['descripcion','precio','costo','cantidad','codsunat','almacen',
                           'cod_barra','precio2','precio3','precio4','precio_mayor',
                           'precio_menor','peso_bruto','razon_social','ruc','iscbp',
                           'usar_barra','codigo','precio_unidad']),
            [
                'id_empresa'    => $this->empresa(),
                'sucursal'      => $this->sucursal(),
                'ultima_salida' => now()->toDateString(),
                'estado'        => '1',
            ]
        ));

        return response()->json(['res' => true, 'id' => $producto->id_producto]);
    }

    public function getOne(Request $request): JsonResponse
    {
        $request->validate(['id_producto' => 'required|integer']);
        return response()->json(
            Producto::deEmpresa($this->empresa())->findOrFail($request->id_producto)
        );
    }

    public function editar(Request $request): JsonResponse
    {
        $request->validate([
            'id_producto'  => 'required|integer',
            'descripcion'  => 'required|string|max:245',
            'precio'       => 'required|numeric|min:0',
        ]);

        Producto::deEmpresa($this->empresa())
            ->where('id_producto', $request->id_producto)
            ->update($request->only([
                'descripcion','precio','precio2','precio3','precio4',
                'costo','cantidad','codsunat','almacen','cod_barra',
                'precio_mayor','precio_menor','razon_social','ruc',
                'iscbp','precio_unidad','codigo',
            ]));

        return response()->json(['res' => true]);
    }

    public function borrar(Request $request): JsonResponse
    {
        $request->validate(['id_producto' => 'required|integer']);
        Producto::deEmpresa($this->empresa())
            ->where('id_producto', $request->id_producto)
            ->update(['estado' => '0', 'activo' => 0]);
        return response()->json(['res' => true]);
    }

    public function agregarPorLista(Request $request): JsonResponse
    {
        $request->validate(['lista' => 'required|array']);
        DB::beginTransaction();
        try {
            foreach ($request->lista as $item) {
                Producto::updateOrCreate(
                    ['codigo' => $item['codigoProd'], 'id_empresa' => $this->empresa()],
                    [
                        'descripcion'   => $item['descripcicon'],
                        'precio'        => $item['precio'],
                        'precio2'       => $item['precio2']       ?? 0,
                        'precio3'       => $item['precio3']       ?? 0,
                        'precio4'       => $item['precio4']       ?? 0,
                        'almacen'       => $item['almacen']       ?? '1',
                        'precio_unidad' => $item['precio_unidad'] ?? 0,
                        'costo'         => $item['costo']         ?? 0,
                        'cantidad'      => $item['cantidad']      ?? 0,
                        'codsunat'      => $item['codSunat']      ?? '',
                        'id_empresa'    => $this->empresa(),
                        'sucursal'      => $this->sucursal(),
                        'ultima_salida' => now()->toDateString(),
                        'estado'        => '1',
                    ]
                );
            }
            DB::commit();
            return response()->json(['res' => true]);
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Error importar productos: ' . $e->getMessage());
            return response()->json(['res' => false, 'msg' => 'Error al importar productos.'], 500);
        }
    }

    public function porRazonSocial(Request $request): JsonResponse
    {
        return response()->json(
            Producto::deEmpresa($this->empresa())->activos()
                ->when($request->filled('razon'), fn($q) =>
                    $q->where('razon_social', 'like', "%{$request->razon}%")
                )
                ->select('id_producto','descripcion','razon_social','ruc','precio','cantidad')
                ->limit(100)
                ->get()
        );
    }
}
