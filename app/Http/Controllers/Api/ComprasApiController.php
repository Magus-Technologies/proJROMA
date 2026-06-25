<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Compra;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ComprasApiController extends Controller
{
    private function empresa(): int { return (int) session('id_empresa'); }

    public function listar(Request $request): mixed
    {
        $query = Compra::with(['proveedor', 'tipoDocumento'])
            ->deEmpresa($this->empresa());

        return DataTables::of($query)
            ->addColumn('documento',        fn ($c) => trim(($c->serie ?? '') . '-' . ($c->numero ?? ''), '-'))
            ->addColumn('proveedor_nombre', fn ($c) => trim($c->proveedor?->razon_social
                                                        ?? $c->proveedor?->nombre_comercial
                                                        ?? $c->proveedor?->nombre
                                                        ?? '-'))
            ->addColumn('tipo_doc',         fn ($c) => $c->tipoDocumento?->tipo_doc ?? '-')
            ->addColumn('acciones',         fn ($c) => $c->id_compra)
            ->make(true);
    }
}
