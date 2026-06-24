<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReportesController extends Controller
{
    public function ventasPdf(): \Illuminate\View\View         { return view('reportes.ventas'); }
    public function ventasVendedor(): \Illuminate\View\View    { return view('reportes.ventas-vendedor'); }
    public function deudaCobros(): \Illuminate\View\View       { return view('reportes.deudas-cobros'); }
    public function deudaVendedor(): \Illuminate\View\View     { return view('reportes.deudas-vendedor'); }
    public function deudaRuta(): \Illuminate\View\View         { return view('reportes.deudas-ruta'); }
    public function reporteCliente(int $id): \Illuminate\View\View { return view('reportes.cliente', compact('id')); }
    public function reporteCompra(int $id): \Illuminate\View\View  { return view('reportes.compra', compact('id')); }
    public function pedidoCamion(): \Illuminate\View\View      { return view('reportes.pedido-camion'); }
    public function reporteLogistico(): \Illuminate\View\View  { return view('reportes.logistico'); }
    public function ingresosEgresos(int $id): \Illuminate\View\View{ return view('reportes.ingresos-egresos', compact('id')); }

    public function comprobanteVenta(int $venta): \Illuminate\Http\Response    { return response('PDF', 200); }
    public function comprobanteVentaMa4(int $venta): \Illuminate\Http\Response { return response('PDF', 200); }
    public function voucher8cm(int $voucher): \Illuminate\Http\Response        { return response('PDF', 200); }
    public function voucher56cm(int $voucher): \Illuminate\Http\Response       { return response('PDF', 200); }
    public function guiaRemisionPdf(int $guia): \Illuminate\Http\Response      { return response('PDF', 200); }
    public function notaElectronicaPdf(int $nota): \Illuminate\Http\Response   { return response('PDF', 200); }
    public function comprobanteCotizacion(int $coti): \Illuminate\Http\Response     { return response('PDF', 200); }
    public function comprobanteCotizacionA4(int $coti): \Illuminate\Http\Response   { return response('PDF', 200); }
    public function comprobantePedidos(int $coti): \Illuminate\Http\Response        { return response('PDF', 200); }
    public function comprobantePedido(string $numero): \Illuminate\Http\Response    { return response('PDF', 200); }

    public function exportarExcel(string $fecha): \Symfony\Component\HttpFoundation\Response { return response('XLS', 200); }
    public function exportarExcelProducto(): \Symfony\Component\HttpFoundation\Response     { return response('XLS', 200); }
    public function exportarExcelCaja(int $id): \Symfony\Component\HttpFoundation\Response  { return response('XLS', 200); }
    public function pedidoClientes(): \Symfony\Component\HttpFoundation\Response            { return response('XLS', 200); }
}
