<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class UsuariosApiController extends Controller
{
    public function render(Request $request): mixed
    {
        $query = User::query()
            ->leftJoin('roles', 'usuarios.id_rol', '=', 'roles.rol_id')
            ->select(
                'usuarios.usuario_id',
                'usuarios.usuario',
                'usuarios.email',
                'usuarios.sucursal',
                'usuarios.estado',
                'roles.nombre as rol_nombre'
            )
            ->selectRaw("TRIM(CONCAT(usuarios.nombres, ' ', usuarios.apellidos)) as nombre_completo")
            ->where('usuarios.id_empresa', session('id_empresa'));

        return DataTables::of($query)->make(true);
    }
}