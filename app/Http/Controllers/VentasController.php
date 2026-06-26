<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\Venta;
use App\Models\DocumentoEmpresa;

class VentasController extends Controller
{
    private function empresa(): int  { return (int) session('id_empresa'); }
    private function sucursal(): int { return (int) session('sucursal'); }

    public function index(): \Illuminate\View\View
    {
        $documentos = DocumentoEmpresa::where('id_empresa', $this->empresa())
            ->where('sucursal', $this->sucursal())->get();
        return view('ventas.index', compact('documentos'));
    }

    public function formProductos(): \Illuminate\View\View
    {
        $documentos = DocumentoEmpresa::where('id_empresa', $this->empresa())
            ->where('sucursal', $this->sucursal())->get();
        return view('ventas.form-productos', compact('documentos'));
    }

    public function formServicios(): \Illuminate\View\View
    {
        return view('ventas.form-servicios');
    }

    public function editarProducto(int $id): \Illuminate\View\View
    {
        $venta = Venta::with(['cliente','productosVenta.producto','tipoDocumento'])
            ->deEmpresa($this->empresa())->findOrFail($id);
        return view('ventas.editar-producto', compact('venta'));
    }

    public function editarServicio(int $id): \Illuminate\View\View
    {
        $venta = Venta::deEmpresa($this->empresa())->findOrFail($id);
        return view('ventas.editar-servicio', compact('venta'));
    }

    public function notaElectronica(): \Illuminate\View\View
    {
        $empresa = Empresa::find($this->empresa());
        return view('ventas.nota-electronica', compact('empresa'));
    }
    public function notaElectronicaLista(): \Illuminate\View\View{ return view('ventas.nota-electronica-lista'); }
}
