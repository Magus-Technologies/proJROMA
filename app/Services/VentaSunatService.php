<?php

namespace App\Services;

use App\Models\Empresa;
use App\Models\Venta;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

/**
 * Emisión electrónica de facturas y boletas a SUNAT vía api-sunat-laravel.
 * Flujo sincrónico:
 *   1. generar/comprobante  → XML firmado (se guarda en storage local)
 *   2. enviar/documento/electronico → devuelve el CDR de inmediato
 */
class VentaSunatService
{
    private string $apiUrl;

    public function __construct()
    {
        $this->apiUrl = rtrim((string) config('sunat.api_url'), '/');
    }

    public function enviar(Venta $venta): array
    {
        $venta->loadMissing(['cliente', 'productosVenta']);
        $empresa = Empresa::find($venta->id_empresa);

        if (! $empresa) {
            return ['ok' => false, 'msg' => 'No se encontró la empresa de la venta.'];
        }

        // Solo factura (id_tido 2) y boleta (id_tido 1) van a SUNAT
        $documento = match ((int) $venta->id_tido) {
            2       => 'factura',
            1       => 'boleta',
            default => null,
        };

        if (! $documento) {
            return ['ok' => false, 'msg' => 'Este tipo de documento no se envía a SUNAT (solo factura o boleta).'];
        }

        $cred = $empresa->credencialesSunat();

        // ── 1) Generar el XML firmado ──────────────────────────────────────
        try {
            $gen = Http::timeout(30)->post("{$this->apiUrl}/v1/generar/comprobante", $this->payload($venta, $empresa, $cred, $documento));
            $genData = $gen->json();
        } catch (\Throwable $e) {
            return ['ok' => false, 'msg' => 'No se pudo conectar con el servicio SUNAT (generar).'];
        }

        if (! ($genData['estado'] ?? false)) {
            return ['ok' => false, 'msg' => $genData['mensaje'] ?? 'Error al generar el XML del comprobante.'];
        }

        $nombre = $genData['data']['nombre_archivo'];
        $xml    = $genData['data']['contenido_xml'];
        $hash   = $genData['data']['hash'] ?? null;

        // Guardar el XML en el storage de ESTE sistema
        $xmlRuta = "sunat/xml/{$cred['ruc']}/{$nombre}.xml";
        Storage::disk('local')->put($xmlRuta, base64_decode($xml, true) ?: $xml);

        // ── 2) Enviar a SUNAT (CDR inmediato) ──────────────────────────────
        try {
            $env = Http::timeout(40)->post("{$this->apiUrl}/v1/enviar/documento/electronico", [
                'ruc'                 => $cred['ruc'],
                'usuario'             => $cred['usuario'],
                'clave'               => $cred['clave'],
                'endpoint'            => $cred['endpoint'],
                'nombre_documento'    => $nombre,
                'contenido_documento' => $xml,
            ]);
            $envData = $env->json();
        } catch (\Throwable $e) {
            $venta->update(['xml_ruta' => $xmlRuta, 'hash_cpe' => $hash]);

            return ['ok' => false, 'msg' => 'No se pudo conectar con el servicio SUNAT (enviar).'];
        }

        if (! ($envData['estado'] ?? false)) {
            $venta->update([
                'xml_ruta'      => $xmlRuta,
                'hash_cpe'      => $hash,
                'sunat_estado'  => 'rechazado',
                'sunat_mensaje' => $envData['mensaje'] ?? 'Rechazado por SUNAT.',
            ]);

            return ['ok' => false, 'msg' => $envData['mensaje'] ?? 'SUNAT rechazó el comprobante.'];
        }

        // Guardar el CDR en el storage de este sistema
        $cdrRuta = null;
        if (! empty($envData['cdr'])) {
            $cdrRuta = "sunat/cdr/{$cred['ruc']}/R-{$nombre}.zip";
            Storage::disk('local')->put($cdrRuta, base64_decode($envData['cdr'], true) ?: '');
        }

        $venta->update([
            'xml_ruta'      => $xmlRuta,
            'cdr_ruta'      => $cdrRuta,
            'hash_cpe'      => $hash,
            'enviado_sunat' => '1',
            'sunat_estado'  => 'aceptado',
            'sunat_mensaje' => 'Aceptado por SUNAT.',
        ]);

        return ['ok' => true, 'msg' => 'Comprobante aceptado por SUNAT.'];
    }

    /** Arma el JSON que espera api-sunat-laravel para generar el comprobante. */
    private function payload(Venta $venta, Empresa $empresa, array $cred, string $documento): array
    {
        $cliente = $venta->cliente;
        $docCli  = (string) ($cliente?->documento ?? '');

        // Boleta: cliente puede ser genérico (DNI). Factura: exige RUC.
        $tipoDocCli = strlen($docCli) === 11 ? '6' : '1';

        return [
            'documento'     => $documento,
            'endpoint'      => $cred['endpoint'],
            'empresa' => [
                'ruc'          => $cred['ruc'],
                'usuario'      => $cred['usuario'],
                'clave'        => $cred['clave'],
                'razon_social' => $empresa->razon_social,
                'direccion'    => $empresa->direccion ?? '',
                'ubigeo'       => $empresa->ubigeo ?? '',
            ],
            'cliente' => [
                'num_doc'    => $docCli ?: '00000000',
                'rzn_social' => $cliente?->datos ?? 'Cliente varios',
                'tipo_doc'   => $tipoDocCli,
                'direccion'  => $cliente?->direccion ?? '',
            ],
            'serie'         => $venta->serie,
            'numero'        => (string) $venta->numero,
            'fecha_emision' => ($venta->fecha_emision ?? now())->format('Y-m-d'),
            'moneda'        => 'PEN',
            'forma_pago'    => ((int) $venta->id_tipo_pago === 2) ? 'credito' : 'contado',
            'total'         => (float) $venta->total,
            'detalles'      => $venta->productosVenta->map(fn ($p): array => [
                'cod_producto' => (string) $p->id_producto,
                'descripcion'  => $p->descripcion ?: 'Producto',
                'unidad'       => $p->medida ?: 'NIU',
                'cantidad'     => (float) $p->cantidad,
                'precio'       => (float) $p->precio, // precio con IGV incluido; la API desglosa
            ])->values()->toArray(),
        ];
    }
}
