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
    public function comprobantePedidos(int $coti): \Illuminate\Http\Response        { return $this->comprobanteCotizacion($coti); }
    public function comprobantePedido(string $n): \Illuminate\Http\Response
    {
        return response('<h2 style="font-family:Arial;padding:40px">Pedido PDF — En desarrollo</h2>', 200)->header('Content-Type','text/html');
    }
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
    public function ventasVendedor(): \Illuminate\View\View    { return view('reportes.ventas-vendedor'); }
    public function deudaCobros(): \Illuminate\View\View       { return view('reportes.deudas-cobros'); }
    public function deudaVendedor(): \Illuminate\View\View     { return view('reportes.deudas-vendedor'); }
    public function deudaRuta(): \Illuminate\View\View         { return view('reportes.deudas-ruta'); }
    public function reporteCliente(int $id): \Illuminate\View\View { return view('reportes.cliente',compact('id')); }
    public function reporteCompra(int $id): \Illuminate\View\View  { return view('reportes.compra',compact('id')); }
    public function pedidoCamion(): \Illuminate\View\View      { return view('reportes.pedido-camion'); }
    public function reporteLogistico(): \Illuminate\View\View  { return view('reportes.logistico'); }
    public function ingresosEgresos(int $id): \Illuminate\View\View{ return view('reportes.ingresos-egresos',compact('id')); }
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
    public function exportarExcelProducto(): \Symfony\Component\HttpFoundation\Response           { return response('Excel en desarrollo',200); }
    public function exportarExcelCaja(int $id): \Symfony\Component\HttpFoundation\Response        { return response('Excel en desarrollo',200); }
    public function pedidoClientes(): \Symfony\Component\HttpFoundation\Response                   { return response('Excel en desarrollo',200); }
}
