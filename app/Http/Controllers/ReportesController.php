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
    public function reporteCotizaciones(\Illuminate\Http\Request $request): mixed
    {
        $request->validate([
            'tipo'    => 'required|in:general,producto,vendedor',
            'periodo' => 'required|in:todo,anio,mes',
            'anio'    => 'nullable|integer',
            'mes'     => 'nullable|integer|between:1,12',
            'formato' => 'required|in:xlsx,pdf',
        ]);

        $empresa  = (int) session('id_empresa');
        $sucursal = (int) session('sucursal');

        $rango = function ($query, string $columna) use ($request): void {
            if ($request->periodo !== 'todo' && $request->anio) {
                $query->whereYear($columna, $request->anio);
            }
            if ($request->periodo === 'mes' && $request->mes) {
                $query->whereMonth($columna, $request->mes);
            }
        };

        $meses = [1 => 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
        $periodo = match ($request->periodo) {
            'anio' => "Año {$request->anio}",
            'mes'  => ($meses[(int) $request->mes] ?? $request->mes) . " {$request->anio}",
            default => 'Todo el historial',
        };

        $estadoLabel = fn (?string $e): string => match ($e) {
            '1' => 'Activa', '3' => 'Convertida', '0' => 'Anulada', default => (string) $e,
        };

        if ($request->tipo === 'general') {
            $titulo    = 'Reporte de Cotizaciones';
            $cabeceras = ['Número', 'Fecha', 'Cliente', 'Vendedor', 'Estado', 'Total (S/)'];
            $moneda    = [5];

            $query = Cotizacion::with(['cliente', 'usuario'])
                ->where('id_empresa', $empresa)->where('sucursal', $sucursal);
            $rango($query, 'fecha');

            $registros = $query->orderBy('fecha')->orderBy('cotizacion_id')->get();
            $filas = $registros->map(fn (Cotizacion $c): array => [
                'COT-' . str_pad((string) $c->numero, 8, '0', STR_PAD_LEFT),
                $c->fecha?->format('d/m/Y') ?? '',
                $c->cliente?->datos ?? '—',
                $c->usuario?->nombres ?? '—',
                $estadoLabel($c->estado),
                (float) $c->total,
            ])->toArray();
            $filas[] = ['', '', '', '', 'TOTAL', $registros->where('estado', '!=', '0')->sum('total')];
        } elseif ($request->tipo === 'producto') {
            $titulo    = 'Cotizaciones por Producto';
            $cabeceras = ['Producto', 'Cant. cotizada', 'N° cotizaciones', 'Monto (S/)'];
            $moneda    = [3];

            $query = \Illuminate\Support\Facades\DB::table('productos_cotis as pc')
                ->join('cotizaciones as c', 'c.cotizacion_id', '=', 'pc.id_coti')
                ->leftJoin('productos as p', 'p.id_producto', '=', 'pc.id_producto')
                ->where('c.id_empresa', $empresa)->where('c.sucursal', $sucursal)
                ->where('c.estado', '!=', '0');
            $rango($query, 'c.fecha');

            $registros = $query
                ->selectRaw('COALESCE(p.descripcion, CONCAT("Producto #", pc.id_producto)) as producto')
                ->selectRaw('SUM(pc.cantidad) as cantidad, COUNT(DISTINCT c.cotizacion_id) as veces, SUM(pc.cantidad * pc.precio) as monto')
                ->groupBy('producto')->orderByDesc('monto')->get();

            $filas = $registros->map(fn ($r): array => [
                $r->producto, (float) $r->cantidad, (int) $r->veces, (float) $r->monto,
            ])->toArray();
            $filas[] = ['TOTAL', $registros->sum('cantidad'), '', $registros->sum('monto')];
        } else {
            $titulo    = 'Cotizaciones por Vendedor';
            $cabeceras = ['Vendedor', 'Rol', 'N° cotizaciones', 'Convertidas', 'Anuladas', 'Monto (S/)'];
            $moneda    = [5];

            $query = Cotizacion::where('id_empresa', $empresa)->where('sucursal', $sucursal);
            $rango($query, 'fecha');
            $porUsuario = $query->get()->groupBy('id_usuario');

            // Every VENDEDOR of the company appears, even with zero activity;
            // anyone else who quoted (e.g. an admin) is appended with their role.
            $usuarios = \Illuminate\Support\Facades\DB::table('usuarios as u')
                ->leftJoin('roles as r', 'r.rol_id', '=', 'u.id_rol')
                ->where('u.id_empresa', $empresa)
                ->where(fn ($q) => $q
                    ->where('r.nombre', 'VENDEDOR')
                    ->orWhereIn('u.usuario_id', $porUsuario->keys()->filter()->all()))
                ->select('u.usuario_id', \Illuminate\Support\Facades\DB::raw("TRIM(CONCAT(u.nombres, ' ', COALESCE(u.apellidos, ''))) as nombre"), 'r.nombre as rol')
                ->orderBy('nombre')
                ->get();

            $filas = $usuarios->map(function ($u) use ($porUsuario): array {
                $grupo = $porUsuario->get($u->usuario_id, collect());

                return [
                    $u->nombre,
                    ucfirst(strtolower($u->rol ?? '—')),
                    $grupo->count(),
                    $grupo->where('estado', '3')->count(),
                    $grupo->where('estado', '0')->count(),
                    (float) $grupo->where('estado', '!=', '0')->sum('total'),
                ];
            })->sortByDesc(5)->values()->toArray();

            // Quotes whose creator no longer exists in usuarios
            $huerfanas = $porUsuario->filter(fn ($g, $id) => ! $usuarios->pluck('usuario_id')->contains($id))->flatten();
            if ($huerfanas->isNotEmpty()) {
                $filas[] = [
                    '— Usuario eliminado —', '—',
                    $huerfanas->count(),
                    $huerfanas->where('estado', '3')->count(),
                    $huerfanas->where('estado', '0')->count(),
                    (float) $huerfanas->where('estado', '!=', '0')->sum('total'),
                ];
            }

            $todas = $porUsuario->flatten();
            $filas[] = [
                'TOTAL', '',
                $todas->count(),
                $todas->where('estado', '3')->count(),
                $todas->where('estado', '0')->count(),
                (float) $todas->where('estado', '!=', '0')->sum('total'),
            ];
        }

        $slug = 'cotizaciones-' . $request->tipo . '-' . now()->format('Y-m-d');

        if ($request->formato === 'pdf') {
            $empresaModel = $this->getEmpresa();
            $logoBase64   = $this->getLogoBase64($empresaModel);

            return PdfService::a4()->generar('pdf.reporte-generico', [
                'titulo'            => $titulo,
                'periodo'           => $periodo,
                'cabeceras'         => $cabeceras,
                'filas'             => $filas,
                'columnasMoneda'    => $moneda,
                'ultimaFilaEsTotal' => true,
                'empresa'           => $empresaModel,
                'logoBase64'        => $logoBase64,
            ], "{$slug}.pdf");
        }

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\ReporteGenericoExport($titulo, $cabeceras, $filas, $moneda, true),
            "{$slug}.xlsx",
        );
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
