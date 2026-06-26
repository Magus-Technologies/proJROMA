<?php

namespace App\Http\Controllers;

use App\Models\Cotizacion;
use App\Models\NotaElectronica;
use App\Models\Venta;
use App\Models\Empresa;
use App\Models\DocumentoEmpresa;
use App\Services\PdfService;

class ReportesController extends Controller
{
    private function getEmpresa(): ?Empresa
    {
        return Empresa::find(session('id_empresa'));
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
        return response('<h2 style="font-family:Arial;padding:40px">Guía PDF — En desarrollo</h2>', 200)->header('Content-Type','text/html');
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

        $empresa = $this->getEmpresa();

        $doc = DocumentoEmpresa::where('id_empresa', session('id_empresa'))
            ->where('sucursal', session('sucursal'))
            ->where('id_tido', 6)
            ->first();

        $documentoCompleto = $doc
            ? $doc->serie . '-' . str_pad($coti->numero, 8, '0', STR_PAD_LEFT)
            : 'NV-' . str_pad($coti->numero, 8, '0', STR_PAD_LEFT);

        return PdfService::a4()
            ->generar('pdf.cotizacion', compact('coti', 'empresa', 'documentoCompleto'), "cotizacion-{$coti->numero}.pdf");
    }
    public function comprobanteCotizacionA4(int $coti): \Illuminate\Http\Response  { return $this->comprobanteCotizacion($coti); }
    public function comprobantePedidos(int $coti): \Illuminate\Http\Response        { return $this->comprobanteCotizacion($coti); }
    public function comprobantePedido(string $n): \Illuminate\Http\Response
    {
        return response('<h2 style="font-family:Arial;padding:40px">Pedido PDF — En desarrollo</h2>', 200)->header('Content-Type','text/html');
    }
    public function ventasPdf(): \Illuminate\View\View         { return view('reportes.ventas'); }
    public function ventasVendedor(): \Illuminate\View\View    { return view('reportes.ventas-vendedor'); }
    public function deudaCobros(): \Illuminate\View\View       { return view('reportes.deudas-cobros'); }
    public function deudaVendedor(): \Illuminate\View\View     { return view('reportes.deudas-vendedor'); }
    public function deudaRuta(): \Illuminate\View\View         { return view('reportes.deudas-ruta'); }
    public function reporteCliente(int $id): \Illuminate\View\View { return view('reportes.cliente',compact('id')); }
    public function reporteCompra(int $id): \Illuminate\View\View  { return view('reportes.compra',compact('id')); }
    public function pedidoCamion(): \Illuminate\View\View      { return view('reportes.pedido-camion'); }
    public function reporteLogistico(): \Illuminate\View\View  { return view('reportes.logistico'); }
    public function ingresosEgresos(int $id): \Illuminate\View\View{ return view('reportes.ingresos-egresos',compact('id')); }
    public function exportarExcel(string $fecha): \Symfony\Component\HttpFoundation\Response      { return response('Excel en desarrollo',200); }
    public function exportarExcelProducto(): \Symfony\Component\HttpFoundation\Response           { return response('Excel en desarrollo',200); }
    public function exportarExcelCaja(int $id): \Symfony\Component\HttpFoundation\Response        { return response('Excel en desarrollo',200); }
    public function pedidoClientes(): \Symfony\Component\HttpFoundation\Response                   { return response('Excel en desarrollo',200); }
}
