<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Yajra\DataTables\Facades\DataTables;

class EmpresaApiController extends Controller
{
    public function listar(Request $request): mixed
    {
        $query = Empresa::query()->select([
            'id_empresa','ruc','razon_social','comercial','cod_sucursal',
            'direccion','email','telefono','estado','user_sol','logo',
            'distrito','provincia','departamento','igv',
        ]);

        return DataTables::of($query)
            ->addColumn('estado_html', fn ($e) => $e->estado === '1' ? '<span class="inline-block rounded-full px-2.5 py-0.5 text-[10px] font-bold bg-emerald-100 text-emerald-700">Activo</span>' : '<span class="inline-block rounded-full px-2.5 py-0.5 text-[10px] font-bold bg-red-100 text-red-700">Inactivo</span>')
            ->addColumn('acciones', fn ($e) => "
                <div class='flex justify-center gap-1'>
                    <button onclick='editar({$e->id_empresa})' class='h-7 w-7 flex items-center justify-center rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-600' title='Editar'><i class='ti ti-pencil text-sm'></i></button>
                    <button onclick='toggle({$e->id_empresa})' class='h-7 w-7 flex items-center justify-center rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-600' title='Activar/Desactivar'><i class='ti ti-power text-sm'></i></button>
                    <button onclick='eliminar({$e->id_empresa})' class='h-7 w-7 flex items-center justify-center rounded-lg bg-red-50 hover:bg-red-100 text-red-600' title='Eliminar'><i class='ti ti-trash text-sm'></i></button>
                </div>")
            ->rawColumns(['estado_html','acciones'])
            ->make(true);
    }

    public function getOne(Request $request): JsonResponse
    {
        $request->validate(['id_empresa' => 'required|integer']);
        $empresa = Empresa::findOrFail($request->id_empresa);
        return response()->json($empresa);
    }

    public function guardar(Request $request): JsonResponse
    {
        $data = $request->validate([
            'ruc'               => 'required|string|min:11|max:11',
            'razon_social'      => 'required|string|max:245',
            'comercial'         => 'nullable|string|max:245',
            'cod_sucursal'      => 'nullable|string|max:4',
            'direccion'         => 'nullable|string|max:245',
            'email'             => 'nullable|email|max:145',
            'telefono'          => 'nullable|string|max:30',
            'telefono2'         => 'nullable|string|max:30',
            'telefono3'         => 'nullable|string|max:30',
            'estado'            => 'nullable|in:0,1',
            'password'          => 'nullable|string|max:45',
            'user_sol'          => 'nullable|string|max:45',
            'clave_sol'         => 'nullable|string|max:45',
            'logo'              => 'nullable|string|max:200',
            'ubigeo'            => 'nullable|string|max:6',
            'distrito'          => 'nullable|string|max:45',
            'provincia'         => 'nullable|string|max:45',
            'departamento'      => 'nullable|string|max:45',
            'tipo_impresion'    => 'nullable|in:1,2',
            'modo'              => 'nullable|string|max:50',
            'igv'               => 'nullable|numeric|min:0|max:1',
            'propaganda'        => 'nullable|string|max:250',
        ]);

        $data['estado'] = $request->input('estado', '1');
        if (empty($data['igv'])) $data['igv'] = 0.18;

        $empresa = Empresa::create($data);
        return response()->json(['res' => true, 'id' => $empresa->id_empresa]);
    }

    public function editar(Request $request): JsonResponse
    {
        $request->validate([
            'id_empresa'        => 'required|integer',
            'ruc'               => 'required|string|min:11|max:11',
            'razon_social'      => 'required|string|max:245',
        ]);

        $empresa = Empresa::findOrFail($request->id_empresa);

        $fields = [
            'ruc','razon_social','comercial','cod_sucursal','direccion',
            'email','telefono','telefono2','telefono3','estado',
            'user_sol','clave_sol','logo','ubigeo','distrito',
            'provincia','departamento','tipo_impresion','modo','igv','propaganda',
        ];

        $update = array_filter($request->only($fields), fn ($v) => $v !== null);

        if (!empty($update['password'])) {
            $update['password'] = Hash::make($update['password']);
        } else {
            unset($update['password']);
        }

        $empresa->update($update);
        return response()->json(['res' => true]);
    }

    public function toggle(Request $request): JsonResponse
    {
        $request->validate(['id_empresa' => 'required|integer']);
        $empresa = Empresa::findOrFail($request->id_empresa);
        $empresa->update(['estado' => $empresa->estado === '1' ? '0' : '1']);
        return response()->json(['res' => true, 'estado' => $empresa->estado]);
    }

    public function eliminar(Request $request): JsonResponse
    {
        $request->validate(['id_empresa' => 'required|integer']);
        $empresa = Empresa::findOrFail($request->id_empresa);

        $enUso = DB::table('usuarios')->where('id_empresa', $empresa->id_empresa)->exists();
        if ($enUso) {
            return response()->json(['res' => false, 'msg' => 'No se puede eliminar: hay usuarios asignados a esta empresa.'], 422);
        }

        $empresa->delete();
        return response()->json(['res' => true]);
    }

    public function subirCertificado(Request $request): JsonResponse
    {
        $request->validate([
            'id_empresa'  => 'required|integer',
            'certificado' => 'required|file|max:512',
        ]);

        $empresa = Empresa::findOrFail($request->id_empresa);
        $apiUrl  = config('sunat.api_url');

        try {
            $response = Http::attach(
                'certificado',
                $request->file('certificado')->getContent(),
                $empresa->ruc . '.pem'
            )->post("{$apiUrl}/v1/guardar/certificado/{$empresa->ruc}");

            $body = $response->json();

            if ($response->successful() && ($body['estado'] ?? false)) {
                return response()->json(['res' => true, 'msg' => 'Certificado subido correctamente.']);
            }

            return response()->json(['res' => false, 'msg' => $body['mensaje'] ?? 'Error al subir el certificado.'], 422);

        } catch (\Throwable $e) {
            return response()->json(['res' => false, 'msg' => 'No se pudo conectar con el servicio SUNAT.'], 500);
        }
    }

    public function buscarRuc(Request $request): JsonResponse
    {
        $request->validate(['ruc' => 'required|string|min:11|max:11']);
        $empresa = Empresa::where('ruc', $request->ruc)->first();
        if (!$empresa) {
            return response()->json(['res' => false, 'msg' => 'Empresa no encontrada.'], 404);
        }
        return response()->json(['res' => true, 'empresa' => $empresa]);
    }
}
