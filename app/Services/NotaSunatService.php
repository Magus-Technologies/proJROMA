<?php

namespace App\Services;

use App\Models\Empresa;
use App\Models\NotaElectronica;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

/**
 * Emisión electrónica de notas de crédito/débito a SUNAT vía api-sunat-laravel.
 * Mismo flujo que VentaSunatService:
 *   generarXml() → firma el XML y lo guarda en storage (NO envía)
 *   enviar()     → manda a SUNAT el XML ya generado y guarda el CDR (sincrónico)
 */
class NotaSunatService
{
    private string $apiUrl;

    public function __construct()
    {
        $this->apiUrl = rtrim((string) config('sunat.api_url'), '/');
    }

    /** Genera y firma el XML de la nota, lo guarda en storage. No envía a SUNAT. */
    public function generarXml(NotaElectronica $nota): array
    {
        $nota->loadMissing(['venta.cliente', 'venta.productosVenta.producto']);
        $empresa = Empresa::find($nota->id_empresa);

        if (! $empresa) {
            return ['ok' => false, 'msg' => 'No se encontró la empresa de la nota.'];
        }

        if (! $nota->venta) {
            return ['ok' => false, 'msg' => 'La nota no tiene una venta asociada.'];
        }

        $cred = $empresa->credencialesSunat();

        try {
            $gen = Http::timeout(30)->post("{$this->apiUrl}/api/v1/generar/nota", $this->payload($nota, $empresa, $cred));
            $genData = $gen->json();
        } catch (\Throwable $e) {
            return ['ok' => false, 'msg' => 'No se pudo conectar con el servicio SUNAT (generar).'];
        }

        if (! ($genData['estado'] ?? false)) {
            return ['ok' => false, 'msg' => $genData['mensaje'] ?? 'Error al generar el XML de la nota.'];
        }

        $nombre = $genData['data']['nombre_archivo'];
        $xml    = $genData['data']['contenido_xml'];
        $hash   = $genData['data']['hash'] ?? null;

        $xmlRuta = "sunat/xml/{$cred['ruc']}/{$nombre}.xml";
        Storage::disk('local')->put($xmlRuta, $xml);

        $nota->update([
            'nombre_xml'    => $nombre,
            'hash'          => $hash,
            'xml_ruta'      => $xmlRuta,
            'sunat_estado'  => $nota->sunat_estado === 'aceptado' ? 'aceptado' : 'pendiente',
            'sunat_mensaje' => $nota->sunat_estado === 'aceptado' ? $nota->sunat_mensaje : 'XML generado, pendiente de envío.',
        ]);

        return ['ok' => true, 'msg' => "XML generado y guardado ({$nombre}.xml).", 'nombre' => $nombre, 'xml' => $xml];
    }

    /** Envía a SUNAT el XML ya generado (lo genera si aún no existe) y guarda el CDR. */
    public function enviar(NotaElectronica $nota): array
    {
        $empresa = Empresa::find($nota->id_empresa);
        if (! $empresa) {
            return ['ok' => false, 'msg' => 'No se encontró la empresa de la nota.'];
        }

        if ($nota->estado === '0') {
            return ['ok' => false, 'msg' => 'No se puede enviar una nota anulada.'];
        }

        $cred = $empresa->credencialesSunat();

        if (blank($nota->xml_ruta) || ! Storage::disk('local')->exists($nota->xml_ruta)) {
            $gen = $this->generarXml($nota);
            if (! $gen['ok']) {
                return $gen;
            }
            $nombre = $gen['nombre'];
            $xml    = $gen['xml'];
        } else {
            $nombre = pathinfo($nota->xml_ruta, PATHINFO_FILENAME);
            $xml    = Storage::disk('local')->get($nota->xml_ruta);
        }

        try {
            $env = Http::timeout(40)->post("{$this->apiUrl}/api/v1/enviar/documento/electronico", [
                'ruc'                 => $cred['ruc'],
                'usuario'             => $cred['usuario'],
                'clave'               => $cred['clave'],
                'endpoint'            => $cred['endpoint'],
                'nombre_documento'    => $nombre,
                'contenido_documento' => $xml,
            ]);
            $envData = $env->json();
        } catch (\Throwable $e) {
            return ['ok' => false, 'msg' => 'No se pudo conectar con el servicio SUNAT (enviar).'];
        }

        if (! ($envData['estado'] ?? false)) {
            $nota->update([
                'sunat_estado'  => 'rechazado',
                'sunat_mensaje' => $envData['mensaje'] ?? 'Rechazado por SUNAT.',
            ]);

            return ['ok' => false, 'msg' => $envData['mensaje'] ?? 'SUNAT rechazó la nota.'];
        }

        $cdrRuta = null;
        if (! empty($envData['cdr'])) {
            $cdrRuta = "sunat/cdr/{$cred['ruc']}/R-{$nombre}.zip";
            Storage::disk('local')->put($cdrRuta, base64_decode($envData['cdr'], true) ?: '');
        }

        $nota->update([
            'cdr_ruta'      => $cdrRuta,
            'enviado_sunat' => '1',
            'sunat_estado'  => 'aceptado',
            'sunat_mensaje' => 'Aceptada por SUNAT.',
        ]);

        return ['ok' => true, 'msg' => 'Nota aceptada por SUNAT.'];
    }

    /** Arma el JSON que espera api-sunat-laravel para generar la nota. */
    private function payload(NotaElectronica $nota, Empresa $empresa, array $cred): array
    {
        $venta   = $nota->venta;
        $cliente = $venta->cliente;

        $esBoleta        = str_starts_with(strtoupper($venta->serie ?? ''), 'B');
        $docAfectado     = $esBoleta ? 'boleta' : 'factura';
        $tipoDocAfectado = $esBoleta ? '03' : '01';
        $tipoDocNota     = $nota->tipo === 'credito' ? '07' : '08';

        // La nota DEBE espejar la afectación IGV del comprobante que corrige.
        $tipoIgv = $venta->tipo_igv ?: 'gravado';

        // Si la nota cubre el total del comprobante, se detallan sus líneas.
        // Si es parcial (descuento, disminución de valor), se emite una sola
        // línea por el monto de la nota — así los importes cuadran con SUNAT.
        $esTotal = abs((float) $nota->total - (float) $venta->total) < 0.01;

        $detalles = $esTotal
            ? ($venta->productosVenta ?? collect())->map(fn ($p): array => [
                'descripcion' => $p->descripcion ?: ($p->producto?->descripcion ?? 'Producto'),
                'cantidad'    => (float) $p->cantidad,
                'unidad'      => $p->medida ?: 'NIU',
                'precio'      => (float) $p->precio,
                'codsunat'    => $p->producto?->codsunat ?? 'ZZ',
                'tipo_igv'    => $tipoIgv,
            ])->values()->toArray()
            : [[
                'descripcion' => $nota->motivo_desc ?: 'Ajuste',
                'cantidad'    => 1,
                'unidad'      => 'NIU',
                'precio'      => (float) $nota->total,
                'codsunat'    => 'ZZ',
                'tipo_igv'    => $tipoIgv,
            ]];

        return [
            'endpoint'              => $cred['endpoint'],
            'documento'             => $nota->tipo,
            'empresa' => [
                'ruc'          => $cred['ruc'],
                'usuario'      => $cred['usuario'],
                'clave'        => $cred['clave'],
                'razon_social' => $empresa->razon_social,
                'direccion'    => $empresa->direccion ?? '',
            ],
            'cliente' => [
                'num_doc'    => $cliente?->documento ?? '00000000',
                'rzn_social' => $cliente?->datos ?? 'Cliente',
                'tipo_doc'   => strlen((string) ($cliente?->documento ?? '')) === 11 ? '6' : '1',
            ],
            'serie'                 => $nota->serie,
            'numero'                => (string) $nota->numero,
            'fecha_emision'         => optional($nota->fecha_emision)->format('Y-m-d') ?? now()->format('Y-m-d'),
            'tipoDoc'               => $tipoDocNota,
            'serie_numero_afectado' => $venta->documento_completo,
            'cod_motivo'            => $nota->cod_motivo,
            'des_motivo'            => $nota->motivo_desc ?: (string) $nota->cod_motivo,
            'doc_afectado'          => $docAfectado,
            'tipo_doc_afectado'     => $tipoDocAfectado,
            'total'                 => (float) $nota->total,
            // No forzamos mtoImpVenta ni mtoValorUnitario: la API los calcula
            // desde los detalles según el tipo_igv, y así los importes cuadran.
            'detalles'              => $detalles,
        ];
    }
}
