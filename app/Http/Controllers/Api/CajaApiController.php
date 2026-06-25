<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CajaEmpresa;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class CajaApiController extends Controller
{
    private function empresa(): int  { return (int) session('id_empresa'); }
    private function sucursal(): int { return (int) session('sucursal'); }
    private function usuarioId(): int { return (int) (auth()->user()->usuario_id ?? 0); }

    public function registros(Request $r): mixed
    {
        $q = CajaEmpresa::where('id_empresa', $this->empresa())
            ->where('sucursal', $this->sucursal());

        if ($r->filled('instrumento')) {
            $q->where('instrumento_tipo', $r->instrumento);
        }

        return DataTables::of($q->orderByDesc('fecha'))->make(true);
    }

    public function ingreso(Request $r): JsonResponse
    {
        $r->validate([
            'descripcion' => 'required|string|max:245',
            'monto'       => 'required|numeric|min:0.01',
        ]);

        CajaEmpresa::create([
            'id_empresa'      => $this->empresa(),
            'sucursal'        => $this->sucursal(),
            'fecha'           => now()->toDateString(),
            'tipo'            => 'INGRESO',
            'descripcion'     => $r->descripcion,
            'monto'           => $r->monto,
            'instrumento_tipo'=> $r->instrumento_tipo ?? null,
            'instrumento_id'  => $r->instrumento_id ?? null,
            'id_usuario'      => $this->usuarioId(),
        ]);

        return response()->json(['res' => true]);
    }

    public function egreso(Request $r): JsonResponse
    {
        $r->validate([
            'descripcion' => 'required|string|max:245',
            'monto'       => 'required|numeric|min:0.01',
        ]);

        CajaEmpresa::create([
            'id_empresa'      => $this->empresa(),
            'sucursal'        => $this->sucursal(),
            'fecha'           => now()->toDateString(),
            'tipo'            => 'EGRESO',
            'descripcion'     => $r->descripcion,
            'monto'           => $r->monto,
            'instrumento_tipo'=> $r->instrumento_tipo ?? null,
            'instrumento_id'  => $r->instrumento_id ?? null,
            'id_usuario'      => $this->usuarioId(),
        ]);

        return response()->json(['res' => true]);
    }
}
