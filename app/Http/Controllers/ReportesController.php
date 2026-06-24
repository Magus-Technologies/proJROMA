<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\Empresa;
use Barryvdh\DomPDF\Facade\Pdf;

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

        // A4 en puntos: 595.28 x 841.89
        $pdf = Pdf::loadView('pdf.comprobante', compact('v', 'empresa'))
            ->setPaper([0, 0, 595.28, 841.89], 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled'      => false,
                'defaultFont'          => 'sans-serif',
                'dpi'                  => 96,
                'margin_top'           => 15,
                'margin_bottom'        => 15,
                'margin_left'          => 15,
                'margin_right'         => 15,
            ]);

        return $pdf->stream("comprobante-{$v->documento_completo}.pdf");
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

        $pdf = Pdf::loadView('pdf.voucher8cm', compact('v', 'empresa'))
            ->setPaper([0, 0, 226.77, 900], 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled'      => false,
                'defaultFont'          => 'monospace',
                'dpi'                  => 96,
                'margin_top'           => 5,
                'margin_bottom'        => 5,
                'margin_left'          => 5,
                'margin_right'         => 5,
            ]);

        return $pdf->stream("voucher-{$v->documento_completo}.pdf");
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
        return response('<h2 style="font-family:Arial;padding:40px">Nota PDF — En desarrollo</h2>', 200)->header('Content-Type','text/html');
    }
    public function comprobanteCotizacion(int $coti): \Illuminate\Http\Response
    {
        return response('<h2 style="font-family:Arial;padding:40px">Cotización PDF — En desarrollo</h2>', 200)->header('Content-Type','text/html');
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
