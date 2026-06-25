<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class CotizacionesApiController extends Controller
{
    private function empresa(): int { return (int) session('id_empresa'); }

    public function listar(Request $request): mixed
    {
        $query = DB::table('cotizaciones as c')
            ->leftJoin('clientes as cl', 'cl.id_cliente', '=', 'c.id_cliente')
            ->where('c.id_empresa', $this->empresa())
            ->select([
                'c.cotizacion_id',
                'c.numero',
                'c.fecha',
                'cl.datos as cliente_nombre',
                'c.total',
                'c.estado',
            ]);

        return DataTables::of($query)
            ->addColumn('estado', fn ($r) => $r->estado ?? '0')
            ->make(true);
    }
}
