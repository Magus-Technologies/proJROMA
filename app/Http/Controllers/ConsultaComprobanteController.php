<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\GuiaRemision;
use App\Models\NotaElectronica;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

/**
 * Consulta pública de comprobantes (sin autenticación).
 * El cliente ingresa tipo, serie, número y su documento (DNI/RUC) — este
 * último evita que se puedan enumerar comprobantes ajenos a la fuerza bruta.
 */
class ConsultaComprobanteController extends Controller
{
    public function index()
    {
        return view('consulta.index', ['empresa' => $this->emisor()]);
    }

    /** Datos del emisor mostrados en la cabecera pública. */
    private function emisor(): ?Empresa
    {
        return Empresa::where('estado', '1')->first() ?? Empresa::first();
    }

    public function buscar(Request $request)
    {
        $datos = $request->validate([
            'tipo'      => 'required|in:venta,nota,guia',
            'serie'     => 'required|string|max:4',
            'numero'    => 'required|integer|min:1',
            'documento' => 'required|string|max:15',
        ], [], [
            'documento' => 'documento del cliente',
        ]);

        $serie     = strtoupper(trim($datos['serie']));
        $numero    = (int) $datos['numero'];
        $documento = trim($datos['documento']);

        $resultado = match ($datos['tipo']) {
            'venta' => $this->buscarVenta($serie, $numero, $documento),
            'nota'  => $this->buscarNota($serie, $numero, $documento),
            'guia'  => $this->buscarGuia($serie, $numero, $documento),
        };

        if (! $resultado) {
            return back()
                ->withInput()
                ->with('error', 'No encontramos un documento con esos datos. Revisá la serie, el número y tu documento.');
        }

        return view('consulta.index', [
            'resultado' => $resultado,
            'empresa'   => $this->emisor(),
        ]);
    }

    /** Sirve el PDF del documento. Requiere URL firmada (se genera tras una consulta válida). */
    public function pdf(string $tipo, int $id, ReportesController $reportes)
    {
        return match ($tipo) {
            'venta' => $reportes->comprobanteVenta($id),
            'nota'  => $reportes->notaElectronicaPdf($id),
            'guia'  => $reportes->guiaRemisionPdf($id),
            default => abort(404),
        };
    }

    private function buscarVenta(string $serie, int $numero, string $documento): ?array
    {
        $venta = Venta::with(['cliente', 'empresa'])
            ->where('serie', $serie)
            ->where('numero', $numero)
            ->whereHas('cliente', fn ($q) => $q->where('documento', $documento))
            ->first();

        if (! $venta) {
            return null;
        }

        return $this->armar(
            tipo: 'venta',
            id: $venta->id_venta,
            titulo: 'Comprobante de venta',
            documento: $venta->documento_completo,
            fecha: optional($venta->fecha_emision)->format('d/m/Y'),
            cliente: $venta->cliente?->datos,
            total: (float) $venta->total,
            estadoSunat: $venta->sunat_estado,
            anulado: $venta->estado === '0',
        );
    }

    private function buscarNota(string $serie, int $numero, string $documento): ?array
    {
        $nota = NotaElectronica::with(['venta.cliente'])
            ->where('serie', $serie)
            ->where('numero', $numero)
            ->whereHas('venta.cliente', fn ($q) => $q->where('documento', $documento))
            ->first();

        if (! $nota) {
            return null;
        }

        return $this->armar(
            tipo: 'nota',
            id: $nota->nota_id,
            titulo: $nota->tipo === 'credito' ? 'Nota de crédito' : 'Nota de débito',
            documento: $nota->serie . '-' . str_pad((string) $nota->numero, 8, '0', STR_PAD_LEFT),
            fecha: optional($nota->fecha_emision)->format('d/m/Y'),
            cliente: $nota->venta?->cliente?->datos,
            total: (float) $nota->total,
            estadoSunat: $nota->sunat_estado,
            anulado: $nota->estado === '0',
        );
    }

    private function buscarGuia(string $serie, int $numero, string $documento): ?array
    {
        $guia = GuiaRemision::with(['venta.cliente'])
            ->where('serie', $serie)
            ->where('numero', $numero)
            ->whereHas('venta.cliente', fn ($q) => $q->where('documento', $documento))
            ->first();

        if (! $guia) {
            return null;
        }

        return $this->armar(
            tipo: 'guia',
            id: $guia->id_guia_remision,
            titulo: 'Guía de remisión',
            documento: $guia->serie . '-' . str_pad((string) $guia->numero, 8, '0', STR_PAD_LEFT),
            fecha: optional($guia->fecha_emision)->format('d/m/Y'),
            cliente: $guia->venta?->cliente?->datos,
            total: null,
            estadoSunat: $guia->estado_gre,
            anulado: $guia->estado === '0',
        );
    }

    /** @return array<string, mixed> */
    private function armar(
        string $tipo,
        int $id,
        string $titulo,
        string $documento,
        ?string $fecha,
        ?string $cliente,
        ?float $total,
        ?string $estadoSunat,
        bool $anulado,
    ): array {
        return [
            'titulo'       => $titulo,
            'documento'    => $documento,
            'fecha'        => $fecha ?? '—',
            'cliente'      => $cliente ?? '—',
            'total'        => $total,
            'estado_sunat' => $estadoSunat ?: 'pendiente',
            'anulado'      => $anulado,
            // Link firmado y temporal: sin él, nadie puede pedir el PDF por URL.
            'pdf_url'      => URL::temporarySignedRoute('consulta.pdf', now()->addMinutes(30), [
                'tipo' => $tipo,
                'id'   => $id,
            ]),
        ];
    }
}
