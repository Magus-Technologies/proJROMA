<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class ProveedoresApiController extends Controller
{
    private function empresa(): int { return (int) session('id_empresa'); }

    public function listar(Request $request): mixed
    {
        $query = DB::table('proveedores')
            ->where('id_empresa', $this->empresa())
            ->select([
                'proveedor_id',
                'ruc as num_doc',
                'razon_social as nombre',
                'nombre_comercial',
                'telefono',
                'email',
            ]);

        return DataTables::of($query)->make(true);
    }

    public function getOne(Request $request): JsonResponse
    {
        $request->validate(['proveedor_id' => 'required|integer']);

        $proveedor = DB::table('proveedores')
            ->where('id_empresa', $this->empresa())
            ->where('proveedor_id', $request->proveedor_id)
            ->select([
                'proveedor_id',
                'ruc as num_doc',
                'razon_social as nombre',
                'nombre_comercial',
                'direccion',
                'telefono',
                'email',
            ])
            ->first();

        if (!$proveedor) abort(404);

        return response()->json($proveedor);
    }

    public function guardar(Request $request): JsonResponse
    {
        $request->validate([
            'num_doc'          => 'required|string|max:11',
            'nombre'           => 'required|string|max:200',
            'nombre_comercial' => 'nullable|string|max:255',
            'direccion'        => 'nullable|string|max:100',
            'telefono'         => 'nullable|string|max:100',
            'email'            => 'nullable|email|max:150',
        ]);

        $exists = DB::table('proveedores')
            ->where('id_empresa', $this->empresa())
            ->where('ruc', $request->num_doc)
            ->exists();

        if ($exists) {
            return response()->json(['res' => false, 'msg' => 'Ya existe un proveedor con ese RUC/Doc.'], 422);
        }

        $id = DB::table('proveedores')->insertGetId([
            'ruc'              => $request->num_doc,
            'razon_social'     => $request->nombre,
            'nombre_comercial' => $request->nombre_comercial ?? '',
            'direccion'        => $request->direccion ?? '',
            'direccion2'       => '',
            'telefono'         => $request->telefono ?? '',
            'telefono2'        => '',
            'email'            => $request->email ?? '',
            'id_empresa'       => $this->empresa(),
            'fecha_create'     => now(),
            'estado'           => 1,
        ]);

        return response()->json(['res' => true, 'id' => $id, 'msg' => 'Proveedor registrado.']);
    }

    public function actualizar(Request $request): JsonResponse
    {
        $request->validate([
            'proveedor_id'     => 'required|integer',
            'num_doc'          => 'required|string|max:11',
            'nombre'           => 'required|string|max:200',
            'nombre_comercial' => 'nullable|string|max:255',
            'direccion'        => 'nullable|string|max:100',
            'telefono'         => 'nullable|string|max:100',
            'email'            => 'nullable|email|max:150',
        ]);

        $proveedor = DB::table('proveedores')
            ->where('id_empresa', $this->empresa())
            ->where('proveedor_id', $request->proveedor_id)
            ->first();

        if (!$proveedor) abort(404);

        $duplicate = DB::table('proveedores')
            ->where('id_empresa', $this->empresa())
            ->where('ruc', $request->num_doc)
            ->where('proveedor_id', '!=', $request->proveedor_id)
            ->exists();

        if ($duplicate) {
            return response()->json(['res' => false, 'msg' => 'Ya existe otro proveedor con ese RUC/Doc.'], 422);
        }

        DB::table('proveedores')
            ->where('proveedor_id', $request->proveedor_id)
            ->update([
                'ruc'              => $request->num_doc,
                'razon_social'     => $request->nombre,
                'nombre_comercial' => $request->nombre_comercial ?? '',
                'direccion'        => $request->direccion ?? '',
                'telefono'         => $request->telefono ?? '',
                'email'            => $request->email ?? '',
            ]);

        return response()->json(['res' => true, 'msg' => 'Proveedor actualizado.']);
    }

    public function eliminar(Request $request): JsonResponse
    {
        $request->validate(['proveedor_id' => 'required|integer']);

        $proveedor = DB::table('proveedores')
            ->where('id_empresa', $this->empresa())
            ->where('proveedor_id', $request->proveedor_id)
            ->first();

        if (!$proveedor) abort(404);

        $hasCompras = DB::table('compras')
            ->where('id_proveedor', $request->proveedor_id)
            ->exists();

        if ($hasCompras) {
            return response()->json(['res' => false, 'msg' => 'No se puede eliminar: el proveedor tiene compras registradas.'], 422);
        }

        DB::table('proveedores')
            ->where('proveedor_id', $request->proveedor_id)
            ->update(['estado' => 0]);

        return response()->json(['res' => true, 'msg' => 'Proveedor eliminado.']);
    }
}
