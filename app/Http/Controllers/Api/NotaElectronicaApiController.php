<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\NotaElectronica;
use App\Models\Venta;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Yajra\DataTables\Facades\DataTables;

class NotaElectronicaApiController extends Controller
{
    private function empresa(): int  { return (int) session('id_empresa'); }
    private function sucursal(): int { return (int) session('sucursal'); }

    public function listar(Request $request): mixed
    {
        $query = NotaElectronica::with(['venta.cliente', 'venta.tipoDocumento'])
            ->where('id_empresa', $this->empresa())
            ->where('sucursal', $this->sucursal());

        return DataTables::of($query)
            ->addColumn('id_nota',              fn($n) => $n->nota_id)
            ->addColumn('documento',            fn($n) => $n->serie . '-' . str_pad($n->numero, 8, '0', STR_PAD_LEFT))
            ->addColumn('comprobante_afectado', fn($n) => $n->venta?->documento_completo ?? '-')
            ->addColumn('cliente_nombre',       fn($n) => $n->venta?->cliente?->datos ?? '-')
            ->addColumn('tipo_label',           fn($n) => $n->tipo === 'credito' ? 'Nota de Crédito' : 'Nota de Débito')
            ->make(true);
    }

    public function buscarVenta(Request $request): JsonResponse
    {
        $term = $request->get('term', '');
        $ventas = Venta::with(['cliente', 'tipoDocumento'])
            ->where('id_empresa', $this->empresa())
            ->where('sucursal', $this->sucursal())
            ->where('estado', '1')
            ->where(fn($q) => $q
                ->whereHas('cliente', fn($c) => $c->where('datos', 'like', "%{$term}%"))
                ->orWhere(DB::raw("CONCAT(serie, '-', LPAD(numero, 8, '0'))"), 'like', "%{$term}%")
            )
            ->limit(20)
            ->get(['id_venta', 'id_cliente', 'id_tido', 'serie', 'numero', 'total', 'fecha_emision']);

        return response()->json($ventas->map(fn($v) => [
            'id_venta'  => $v->id_venta,
            'documento' => $v->documento_completo,
            'cliente'   => $v->cliente?->datos ?? '-',
            'total'     => $v->total,
            'tipo_doc'  => $v->tipoDocumento?->tipo_doc ?? '-',
        ]));
    }

    public function cargarVenta(Request $request): JsonResponse
    {
        $request->validate(['id_venta' => 'required|integer']);
        $venta = Venta::with(['cliente', 'productosVenta.producto', 'tipoDocumento'])
            ->where('id_empresa', $this->empresa())
            ->findOrFail($request->id_venta);

        return response()->json([
            'id_venta'  => $venta->id_venta,
            'documento' => $venta->documento_completo,
            'cliente'   => $venta->cliente?->datos,
            'total'     => $venta->total,
            'tipo_doc'  => $venta->tipoDocumento?->tipo_doc ?? '-',
            'productos' => $venta->productosVenta->map(fn($p) => [
                'descripcion' => $p->descripcion ?? $p->producto?->descripcion ?? '-',
                'cantidad'    => (float) $p->cantidad,
                'unidad'      => $p->medida ?? 'NIU',
                'precio'      => (float) $p->precio,
                'total'       => round((float) $p->cantidad * (float) $p->precio, 2),
            ]),
        ]);
    }

    public function guardar(Request $request): JsonResponse
    {
        $request->validate([
            'id_venta'   => 'required|integer',
            'tipo'       => 'required|in:credito,debito',
            'cod_motivo' => 'required|string|max:5',
            'motivo'     => 'required|string|max:255',
            'total'      => 'required|numeric|min:0',
        ]);

        $serie  = $request->tipo === 'credito' ? 'EC01' : 'ED01';
        $numero = (int) DB::table('notas_electronicas')
            ->where('id_empresa', $this->empresa())
            ->where('sucursal', $this->sucursal())
            ->where('serie', $serie)
            ->max('numero') + 1;

        $nota = NotaElectronica::create([
            'id_venta'      => $request->id_venta,
            'tipo'          => $request->tipo,
            'cod_motivo'    => $request->cod_motivo,
            'motivo'        => $request->motivo,
            'id_empresa'    => $this->empresa(),
            'sucursal'      => $this->sucursal(),
            'serie'         => $serie,
            'numero'        => $numero,
            'total'         => $request->total,
            'fecha_emision' => now()->toDateString(),
            'estado'        => '1',
            'enviado_sunat' => '0',
        ]);

        return response()->json(['res' => true, 'id_nota' => $nota->id_nota, 'msg' => 'Nota registrada correctamente.']);
    }

    public function enviarSunat(Request $request): JsonResponse
    {
        $request->validate(['id_nota' => 'required|integer']);

        $nota = NotaElectronica::with(['venta.cliente', 'venta.productosVenta.producto', 'venta.tipoDocumento'])
            ->where('id_empresa', $this->empresa())
            ->findOrFail($request->id_nota);

        if ($nota->enviado_sunat === '1') {
            return response()->json(['res' => false, 'msg' => 'La nota ya fue enviada a SUNAT.'], 422);
        }

        if ($nota->estado === '0') {
            return response()->json(['res' => false, 'msg' => 'No se puede enviar una nota anulada.'], 422);
        }

        $empresa = Empresa::findOrFail($this->empresa());
        $venta   = $nota->venta;
        $cliente = $venta?->cliente;

        $serieVenta      = strtoupper($venta->serie ?? '');
        $esBoleta        = str_starts_with($serieVenta, 'B');
        $docAfectado     = $esBoleta ? 'boleta' : 'factura';
        $tipoDocAfectado = $esBoleta ? '03' : '01';
        $tipoDocNota     = $nota->tipo === 'credito' ? '07' : '08';

        $detalles = ($venta->productosVenta ?? collect())->map(fn($p) => [
            'descripcion'      => $p->descripcion ?? $p->producto?->descripcion ?? 'Producto',
            'cantidad'         => (float) $p->cantidad,
            'unidad'           => $p->medida ?? 'NIU',
            'precio'           => (float) $p->precio,
            'mtoValorUnitario' => round((float) $p->precio / 1.18, 6),
            'codsunat'         => $p->producto?->codsunat ?? 'ZZ',
        ])->values()->toArray();

        $payload = [
            'endpoint'              => ($empresa->modo ?? '') === 'produccion' ? 'produccion' : 'beta',
            'documento'             => $nota->tipo,
            'empresa'               => [
                'ruc'          => $empresa->ruc,
                'usuario'      => $empresa->user_sol ?? '',
                'clave'        => $empresa->clave_sol ?? '',
                'razon_social' => $empresa->razon_social,
                'direccion'    => $empresa->direccion ?? '',
            ],
            'cliente'               => [
                'num_doc'    => $cliente?->documento ?? '00000000',
                'rzn_social' => $cliente?->datos ?? 'Cliente',
                'tipo_doc'   => strlen($cliente?->documento ?? '') === 11 ? '6' : '1',
            ],
            'serie'                 => $nota->serie,
            'numero'                => (string) $nota->numero,
            'fecha_emision'         => $nota->fecha_emision?->format('Y-m-d'),
            'tipoDoc'               => $tipoDocNota,
            'serie_numero_afectado' => $venta->documento_completo,
            'cod_motivo'            => $nota->cod_motivo,
            'des_motivo'            => $nota->motivo,
            'doc_afectado'          => $docAfectado,
            'tipo_doc_afectado'     => $tipoDocAfectado,
            'total'                 => (float) $nota->total,
            'mtoImpVenta'           => (float) $nota->total,
            'detalles'              => $detalles,
        ];

        $apiUrl = config('sunat.api_url');

        try {
            $genResp = Http::timeout(30)->post("{$apiUrl}/v1/generar/nota", $payload);
            $genData = $genResp->json();

            if (!($genData['estado'] ?? false)) {
                return response()->json(['res' => false, 'msg' => $genData['mensaje'] ?? 'Error al generar la nota XML.'], 422);
            }

            $xmlSigned     = $genData['data']['contenido_xml'];
            $hash          = $genData['data']['hash'];
            $nombreArchivo = $genData['data']['nombre_archivo'];

            $envResp = Http::timeout(30)->post("{$apiUrl}/v1/enviar/documento/electronico", [
                'ruc'                 => $empresa->ruc,
                'usuario'             => $empresa->user_sol ?? '',
                'clave'               => $empresa->clave_sol ?? '',
                'endpoint'            => ($empresa->modo ?? '') === 'produccion' ? 'produccion' : 'beta',
                'nombre_documento'    => $nombreArchivo,
                'contenido_documento' => $xmlSigned,
            ]);
            $envData = $envResp->json();

            if (!($envData['estado'] ?? false)) {
                return response()->json(['res' => false, 'msg' => $envData['mensaje'] ?? 'SUNAT rechazó el documento.'], 422);
            }

            $nota->update([
                'enviado_sunat' => '1',
                'hash'          => $hash,
                'nombre_xml'    => $nombreArchivo,
            ]);

            return response()->json(['res' => true, 'msg' => 'Nota enviada a SUNAT correctamente.', 'hash' => $hash]);

        } catch (\Throwable) {
            return response()->json(['res' => false, 'msg' => 'No se pudo conectar con el servicio SUNAT.'], 503);
        }
    }

    public function anular(Request $request): JsonResponse
    {
        $request->validate(['id_nota' => 'required|integer']);
        $nota = NotaElectronica::where('id_empresa', $this->empresa())->findOrFail($request->id_nota);

        if ($nota->enviado_sunat === '1') {
            return response()->json(['res' => false, 'msg' => 'No se puede anular una nota ya enviada a SUNAT.'], 422);
        }

        $nota->update(['estado' => '0']);
        return response()->json(['res' => true, 'msg' => 'Nota anulada.']);
    }
}
