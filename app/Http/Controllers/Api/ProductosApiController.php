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
        $query = Producto::query()
            ->leftJoin('categorias as cat', 'cat.id_categoria', '=', 'productos.id_categoria')
            ->leftJoin('marcas as mar', 'mar.id_marca', '=', 'productos.id_marca')
            ->where('productos.id_empresa', $this->empresa())
            ->where('productos.estado', '1')
            ->when($request->filled('almacenId'), fn($q) => $q->where('productos.almacen', $request->almacenId))
            ->select('productos.*', 'cat.nombre as categoria_nombre', 'mar.nombre as marca_nombre');
        return DataTables::of($query)->make(true);
    }

    public function serverside(Request $request): mixed
    {
        return $this->listar($request);
    }

    /**
     * Catálogo: un solo producto por código, con Stock = suma de todos los almacenes.
     * Se usa en "Registro de Productos" (vista de catálogo, no por almacén).
     */
    public function catalogo(Request $request): mixed
    {
        $sub = DB::table('productos')
            ->where('id_empresa', $this->empresa())
            ->where('estado', '1')
            // agrupa por código; si no tiene código, cada producto es su propio grupo
            ->groupBy(DB::raw("COALESCE(NULLIF(codigo,''), CONCAT('ID-', id_producto))"))
            ->select(
                DB::raw('MIN(id_producto) as id_producto'),
                DB::raw('MAX(codigo) as codigo'),
                DB::raw('MAX(descripcion) as descripcion'),
                DB::raw('MAX(precio) as precio'),
                DB::raw('MAX(id_categoria) as id_categoria'),
                DB::raw('MAX(id_marca) as id_marca'),
                DB::raw('MAX(medida) as medida'),
                DB::raw('MAX(imagen) as imagen'),
                DB::raw('SUM(cantidad) as stock_total')
            );

        $query = DB::query()->fromSub($sub, 'p')
            ->leftJoin('categorias as cat', 'cat.id_categoria', '=', 'p.id_categoria')
            ->leftJoin('marcas as mar', 'mar.id_marca', '=', 'p.id_marca')
            ->select(
                'p.id_producto',
                'p.codigo',
                'p.descripcion',
                'p.precio',
                'p.id_categoria',
                'p.id_marca',
                'p.medida',
                'p.imagen',
                'p.stock_total',
                'cat.nombre as categoria_nombre',
                'mar.nombre as marca_nombre'
            );

        return DataTables::of($query)
            ->filterColumn('codigo', function ($q, $kw) { $q->where('p.codigo', 'like', "%{$kw}%"); })
            ->filterColumn('descripcion', function ($q, $kw) { $q->where('p.descripcion', 'like', "%{$kw}%"); })
            ->filterColumn('precio', function ($q, $kw) { $q->where('p.precio', 'like', "%{$kw}%"); })
            ->make(true);
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
                           'usar_barra','codigo','precio_unidad',
                           'id_categoria','id_subcategoria','id_marca','id_submarca',
                           'medida','presentaciones','cnt_presenta','imagen']),
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
                'id_categoria','id_subcategoria','id_marca','id_submarca',
                'medida','presentaciones','cnt_presenta','imagen',
            ]));

        return response()->json(['res' => true]);
    }

    public function borrar(Request $request): JsonResponse
    {
        $request->validate(['id_producto' => 'required|integer']);
        $id = (int) $request->id_producto;

        // Validación de integridad: no borrar si está en uso
        $enUso = [];
        if (DB::table('productos_ventas')->where('id_producto', $id)->exists())  $enUso[] = 'ventas';
        if (DB::table('productos_compras')->where('id_producto', $id)->exists()) $enUso[] = 'compras';
        if (DB::table('productos_cotis')->where('id_producto', $id)->exists())   $enUso[] = 'cotizaciones';

        if ($enUso) {
            return response()->json([
                'res' => false,
                'msg' => 'No se puede eliminar el producto porque tiene ' . implode(', ', $enUso) . ' registradas.',
            ], 409);
        }

        Producto::deEmpresa($this->empresa())
            ->where('id_producto', $id)
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

    public function subirImagen(Request $request): JsonResponse
    {
        $request->validate(['imagen' => 'required|image|max:3072']);   // máx 3MB
        $file = $request->file('imagen');
        $name = 'prod_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('uploads/productos'), $name);
        $path = 'uploads/productos/' . $name;

        return response()->json(['res' => true, 'path' => $path, 'url' => asset($path)]);
    }

    public function stockPorAlmacen(string $codigo): JsonResponse
    {
        if (str_starts_with($codigo, 'ID-')) {
            $id = (int) substr($codigo, 3);
            $ids = collect([$id]);
        } else {
            $ids = DB::table('productos')
                ->where('codigo', $codigo)
                ->where('id_empresa', $this->empresa())
                ->pluck('id_producto');
        }

        if ($ids->isEmpty()) {
            return response()->json([]);
        }

        $empresaId = $this->empresa();

        $latest = DB::table('inventario_movimientos as m2')
            ->select('m2.id_producto', 'm2.almacen', DB::raw('MAX(m2.id_movimiento) as max_id'))
            ->whereIn('m2.id_producto', $ids)
            ->where('m2.id_empresa', $empresaId)
            ->groupBy('m2.id_producto', 'm2.almacen');

        $rows = DB::table('inventario_movimientos as m')
            ->joinSub($latest, 'latest', fn($j) => $j->on('m.id_movimiento', '=', 'latest.max_id'))
            ->join('almacenes as a', fn($j) => $j->on('a.codigo', '=', 'm.almacen')->on('a.id_empresa', '=', 'm.id_empresa'))
            ->where('m.id_empresa', $empresaId)
            ->select('a.nombre as almacen', DB::raw('SUM(m.stock_nuevo) as stock'))
            ->groupBy('a.nombre', 'm.almacen')
            ->orderBy('a.nombre')
            ->get();

        return response()->json($rows);
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
