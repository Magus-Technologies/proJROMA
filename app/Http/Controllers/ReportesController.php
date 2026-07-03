<?php

namespace App\Http\Controllers;

use App\Models\Cotizacion;
use App\Models\GuiaRemision;
use App\Models\NotaElectronica;
use App\Models\Venta;
use App\Models\Empresa;
use App\Models\DocumentoEmpresa;
use App\Services\PdfService;
use Illuminate\Support\Facades\Storage;

class ReportesController extends Controller
{
    private function getEmpresa(): ?Empresa
    {
        return Empresa::find(session('id_empresa'));
    }

    private function getLogoBase64(?Empresa $empresa): string
    {
        if (!$empresa?->logo) {
            return '';
        }
        // Try public disk first (Filament v5 uploads with ->disk('public'))
        if (Storage::disk('public')->exists($empresa->logo)) {
            $path = Storage::disk('public')->path($empresa->logo);
            $mime = mime_content_type($path);
            return 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($path));
        }
        // Fallback: legacy path directly under public/storage/
        $legacy = public_path('storage/' . $empresa->logo);
        if (file_exists($legacy)) {
            $mime = mime_content_type($legacy);
            return 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($legacy));
        }
        return '';
    }

    public function comprobanteVenta(int $venta): \Illuminate\Http\Response
    {
        $v = Venta::with([
            'cliente',
            'productosVenta.producto',
            'tipoDocumento',
            'empresa',
            'vendedor',
            'pagos',
        ])->findOrFail($venta);

        $empresa = $this->getEmpresa() ?? Empresa::find($v->id_empresa);

        return PdfService::a4()
            ->generar('pdf.comprobante', compact('v', 'empresa'), "comprobante-{$v->documento_completo}.pdf");
    }

    public function comprobanteVentaMa4(int $venta): \Illuminate\Http\Response
    {
        return $this->comprobanteVenta($venta);
    }

    public function voucher8cm(int $voucher): \Illuminate\Http\Response
    {
        $v = Venta::with([
            'cliente',
            'productosVenta.producto',
            'tipoDocumento',
            'empresa',
            'pagos',
        ])->findOrFail($voucher);

        $empresa = $this->getEmpresa() ?? Empresa::find($v->id_empresa);

        return PdfService::ticket()
            ->setOption('defaultFont', 'monospace')
            ->generar('pdf.voucher8cm', compact('v', 'empresa'), "voucher-{$v->documento_completo}.pdf");
    }

    public function voucher56cm(int $voucher): \Illuminate\Http\Response
    {
        return $this->voucher8cm($voucher);
    }

    public function guiaRemisionPdf(int $guia): \Illuminate\Http\Response
    {
        $guia    = GuiaRemision::with(['venta.cliente', 'detalles'])->findOrFail($guia);
        $empresa = $this->getEmpresa() ?? Empresa::find($guia->id_empresa);

        $logoBase64 = $this->getLogoBase64($empresa);

        $serie = $guia->serie . '-' . str_pad($guia->numero, 8, '0', STR_PAD_LEFT);

        return PdfService::a4()
            ->generar('pdf.guia-remision', compact('guia', 'empresa', 'logoBase64'), "guia-{$serie}.pdf");
    }
    public function notaElectronicaPdf(int $nota): \Illuminate\Http\Response
    {
        $nota = NotaElectronica::with([
            'venta.cliente',
            'venta.productosVenta.producto',
        ])->findOrFail($nota);

        $empresa = $this->getEmpresa() ?? Empresa::find($nota->id_empresa);
        $serie   = $nota->serie . '-' . str_pad($nota->numero, 8, '0', STR_PAD_LEFT);

        return PdfService::a4()
            ->generar('pdf.nota-electronica', compact('nota', 'empresa'), "nota-{$serie}.pdf");
    }
    public function comprobanteCotizacion(int $coti): \Illuminate\Http\Response
    {
        $coti = Cotizacion::with([
            'cliente',
            'productos.producto',
            'usuario',
        ])->findOrFail($coti);

        $empresa    = $this->getEmpresa();
        $logoBase64 = $this->getLogoBase64($empresa);

        $doc = DocumentoEmpresa::where('id_empresa', session('id_empresa'))
            ->where('sucursal', session('sucursal'))
            ->where('id_tido', 6)
            ->first();

        $documentoCompleto = $doc
            ? $doc->serie . '-' . str_pad($coti->numero, 8, '0', STR_PAD_LEFT)
            : 'NV-' . str_pad($coti->numero, 8, '0', STR_PAD_LEFT);

        return PdfService::a4()
            ->generar('pdf.cotizacion', compact('coti', 'empresa', 'documentoCompleto', 'logoBase64'), "cotizacion-{$coti->numero}.pdf");
    }
    public function comprobanteCotizacionA4(int $coti): \Illuminate\Http\Response  { return $this->comprobanteCotizacion($coti); }
    public function ventasPdf(\Illuminate\Http\Request $request): \Illuminate\Http\Response
    {
        $desde = $request->query('desde', now()->startOfMonth()->toDateString());
        $hasta = $request->query('hasta', now()->endOfMonth()->toDateString());

        $ventas = Venta::with(['cliente'])
            ->where('id_empresa', (int) session('id_empresa'))
            ->where('sucursal', (int) session('sucursal'))
            ->whereBetween('fecha_emision', [$desde, $hasta])
            ->orderBy('fecha_emision')
            ->orderBy('id_venta')
            ->get();

        $empresa = $this->getEmpresa();
        $periodo = \Carbon\Carbon::parse($desde)->format('d/m/Y') . ' — ' . \Carbon\Carbon::parse($hasta)->format('d/m/Y');

        return PdfService::a4()
            ->generar('pdf.reporte-ventas', compact('ventas', 'empresa', 'periodo'), "reporte-ventas-{$desde}-{$hasta}.pdf");
    }
    public function reporteCliente(int $id): \Illuminate\View\View { return view('reportes.cliente',compact('id')); }
    public function reporteCompra(int $id): \Illuminate\View\View  { return view('reportes.compra',compact('id')); }
    public function ingresosEgresos(int $id): \Illuminate\View\View{ return view('reportes.ingresos-egresos',compact('id')); }
    /**
     * Guías de reparto del despacho: un ticket PEDIDO (original + copia)
     * por cada cliente, en un solo PDF para impresión masiva.
     */
    public function despachoGuiasPdf(int $id): \Illuminate\Http\Response
    {
        $despacho = \App\Models\TmsDespacho::with(['ruta', 'vehiculo', 'conductor'])->findOrFail($id);
        $empresa  = $this->getEmpresa() ?? Empresa::find($despacho->id_empresa);

        $mercadoIds = collect(explode(',', (string) request('mercados')))
            ->filter()->map(fn ($v) => (int) $v)->values()->all();

        $cotIds = \Illuminate\Support\Facades\DB::table('tms_despacho_pedidos')
            ->where('id_despacho', $id)
            ->when($mercadoIds, fn ($q) => $q->whereIn('id_mercado', $mercadoIds))
            ->orderBy('orden')->pluck('id_cotizacion')->all();

        $pedidos = Cotizacion::with(['cliente', 'productos.producto', 'usuario'])
            ->whereIn('cotizacion_id', $cotIds)
            ->get()
            ->sortBy(fn ($c) => array_search($c->cotizacion_id, $cotIds))
            ->values();

        $mercados = \App\Models\TmsMercado::where('id_empresa', $despacho->id_empresa)
            ->pluck('nombre', 'id');

        $serie = DocumentoEmpresa::where('id_empresa', $despacho->id_empresa)
            ->where('sucursal', $despacho->sucursal)
            ->where('id_tido', 6)->value('serie') ?? 'NV';

        return PdfService::a4()
            ->generar('pdf.despacho-guias', [
                'despacho'   => $despacho,
                'empresa'    => $empresa,
                'pedidos'    => $pedidos,
                'mercados'   => $mercados,
                'serie'      => $serie,
                'logoBase64' => $this->getLogoBase64($empresa),
            ], "guias-reparto-{$despacho->codigo}.pdf");
    }

    public function despachoReportePdf(int $id, ?int $mercadoId = null): \Illuminate\Http\Response
    {
        $despacho = \App\Models\TmsDespacho::with(['ruta', 'vehiculo', 'conductor', 'pedidos'])->findOrFail($id);
        $empresa  = $this->getEmpresa() ?? Empresa::find($despacho->id_empresa);

        // Filtros: ?mercados=1,2&medidas=Kilos,Unidad (o el mercado de la ruta legada).
        $mercadoIds = collect(explode(',', (string) request('mercados')))
            ->filter()->map(fn ($v) => (int) $v)->values()->all();
        if ($mercadoId) $mercadoIds = [$mercadoId];

        $medidas = collect(explode(',', (string) request('medidas')))
            ->map(fn ($v) => trim($v))->filter()->values()->all();

        $data = app(\App\Services\TmsDespachoService::class)->reporte($id, $mercadoIds, $medidas);

        $nombresMercados = $mercadoIds
            ? \App\Models\TmsMercado::whereIn('id', $mercadoIds)->pluck('nombre')->implode(', ')
            : null;

        return PdfService::a4()
            ->generar('pdf.despacho-reporte', [
                'despacho'      => $despacho,
                'empresa'       => $empresa,
                'porArticulo'   => $data['por_articulo'],
                'porCliente'    => $data['por_cliente'],
                'porMercado'    => $data['por_mercado'],
                'mercadoFiltro' => null,
                'filtroMercados' => $nombresMercados,
                'filtroMedidas'  => $medidas ? implode(', ', $medidas) : null,
            ], "hoja-carga-{$despacho->codigo}.pdf");
    }

    public function exportarExcel(string $fecha): \Symfony\Component\HttpFoundation\Response
    {
        // $fecha llega como 'YYYY-MM' (mes a exportar)
        $inicio = \Carbon\Carbon::createFromFormat('Y-m', $fecha)->startOfMonth();
        $fin    = $inicio->copy()->endOfMonth();

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\VentasExport(
                (int) session('id_empresa'),
                (int) session('sucursal'),
                $inicio->toDateString(),
                $fin->toDateString(),
            ),
            "ventas-{$fecha}.xlsx",
        );
    }
}
