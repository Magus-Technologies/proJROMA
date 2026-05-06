<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\{Cliente, Producto};
use Illuminate\Http\{Request, JsonResponse};
use Illuminate\Routing\Attributes\Middleware;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

// ══════════════════════════════════════════════════════════════════════════════
// CLIENTES API
// ══════════════════════════════════════════════════════════════════════════════
#[Middleware(['auth:sanctum', 'check.empresa'])]
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

// ══════════════════════════════════════════════════════════════════════════════
// PRODUCTOS API
// ══════════════════════════════════════════════════════════════════════════════
#[Middleware(['auth:sanctum', 'check.empresa'])]
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

    public function serverside(Request $request): mixed { return $this->listar($request); }

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
            ['id_empresa' => $this->empresa(), 'sucursal' => $this->sucursal(),
             'ultima_salida' => now()->toDateString(), 'estado' => '1']
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
        $request->validate(['id_producto' => 'required|integer', 'descripcion' => 'required|string', 'precio' => 'required|numeric']);
        Producto::deEmpresa($this->empresa())
            ->where('id_producto', $request->id_producto)
            ->update($request->only(['descripcion','precio','precio2','precio3','precio4',
                                     'costo','cantidad','codsunat','almacen','cod_barra',
                                     'precio_mayor','precio_menor','razon_social','ruc',
                                     'iscbp','precio_unidad','codigo']));
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
                ->when($request->filled('razon'), fn($q) => $q->where('razon_social','like',"%{$request->razon}%"))
                ->select('id_producto','descripcion','razon_social','ruc','precio','cantidad')
                ->limit(100)->get()
        );
    }
}
