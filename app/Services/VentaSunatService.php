<?php

namespace App\Services;

use App\Models\Empresa;
use App\Models\Venta;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

/**
 * Emisión electrónica de facturas y boletas a SUNAT vía api-sunat-laravel.
 *   generarXml() → firma el XML y lo guarda en storage (NO envía)
 *   enviar()     → manda a SUNAT el XML ya generado y guarda el CDR (sincrónico)
 */
class VentaSunatService
{
    private string $apiUrl;

    public function __construct()
    {
        $this->apiUrl = rtrim((string) config('sunat.api_url'), '/');
    }

    /** Genera y firma el XML, lo guarda en el storage local. No envía a SUNAT. */
    public function generarXml(Venta $venta): array
    {
        // `pagos` = cuotas del crédito; SUNAT las exige en el XML.
        $venta->loadMissing(['cliente', 'productosVenta', 'pagos']);
        $empresa = Empresa::find($venta->id_empresa);

        if (! $empresa) {
            return ['ok' => false, 'msg' => 'No se encontró la empresa de la venta.'];
        }

        $documento = $this->tipoDocumento($venta);
        if (! $documento) {
            return ['ok' => false, 'msg' => 'Este tipo de documento no se envía a SUNAT (solo factura o boleta).'];
        }

        $cred = $empresa->credencialesSunat();

        try {
            $gen = Http::timeout(30)->post("{$this->apiUrl}/api/v1/generar/comprobante", $this->payload($venta, $empresa, $cred, $documento));
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

        // Guardar el XML (crudo, legible) en el storage de ESTE sistema
        $xmlRuta = "sunat/xml/{$cred['ruc']}/{$nombre}.xml";
        Storage::disk('local')->put($xmlRuta, $xml);

        $venta->update([
            'xml_ruta'      => $xmlRuta,
            'hash_cpe'      => $hash,
            // No pisar un estado ya aceptado
            'sunat_estado'  => $venta->sunat_estado === 'aceptado' ? 'aceptado' : 'pendiente',
            'sunat_mensaje' => $venta->sunat_estado === 'aceptado' ? $venta->sunat_mensaje : 'XML generado, pendiente de envío.',
        ]);

        return ['ok' => true, 'msg' => "XML generado y guardado ({$nombre}.xml).", 'nombre' => $nombre, 'xml' => $xml];
    }

    /** Envía a SUNAT el XML ya generado (lo genera si aún no existe) y guarda el CDR. */
    public function enviar(Venta $venta): array
    {
        $empresa = Empresa::find($venta->id_empresa);
        if (! $empresa) {
            return ['ok' => false, 'msg' => 'No se encontró la empresa de la venta.'];
        }

        if (! $this->tipoDocumento($venta)) {
            return ['ok' => false, 'msg' => 'Este tipo de documento no se envía a SUNAT (solo factura o boleta).'];
        }

        $cred = $empresa->credencialesSunat();

        // Usar el XML ya generado (el que revisó el usuario); si no existe, generarlo ahora.
        if (blank($venta->xml_ruta) || ! Storage::disk('local')->exists($venta->xml_ruta)) {
            $gen = $this->generarXml($venta);
            if (! $gen['ok']) {
                return $gen;
            }
            $nombre = $gen['nombre'];
            $xml    = $gen['xml'];
        } else {
            $nombre = pathinfo($venta->xml_ruta, PATHINFO_FILENAME);
            $xml    = Storage::disk('local')->get($venta->xml_ruta);
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
            $venta->update([
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
            'cdr_ruta'      => $cdrRuta,
            'enviado_sunat' => '1',
            'sunat_estado'  => 'aceptado',
            'sunat_mensaje' => 'Aceptado por SUNAT.',
        ]);

        return ['ok' => true, 'msg' => 'Comprobante aceptado por SUNAT.'];
    }

    /** 'factura' | 'boleta' | null según el tipo de documento de la venta. */
    private function tipoDocumento(Venta $venta): ?string
    {
        return match ((int) $venta->id_tido) {
            2       => 'factura',
            1       => 'boleta',
            default => null,
        };
    }

    /** Arma el JSON que espera api-sunat-laravel para generar el comprobante. */
    private function payload(Venta $venta, Empresa $empresa, array $cred, string $documento): array
    {
        $cliente = $venta->cliente;
        $docCli  = (string) ($cliente?->documento ?? '');
        $tipoDocCli = strlen($docCli) === 11 ? '6' : '1';

        $esCredito = (int) $venta->id_tipo_pago === 2;

        // SUNAT exige el detalle de las cuotas en todo comprobante a crédito.
        $cuotasCredito = $esCredito
            ? $venta->pagos
                ->map(fn ($c): array => [
                    'monto' => round((float) $c->monto, 2),
                    'fecha' => optional($c->fecha)->format('Y-m-d'),
                ])
                ->filter(fn (array $c): bool => filled($c['fecha']) && $c['monto'] > 0)
                ->values()
                ->toArray()
            : [];

        return [
            ...($esCredito && $cuotasCredito !== [] ? ['cuotas_credito' => $cuotasCredito] : []),
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
                'unidad'       => $this->unidadSunat($p->medida),
                'cantidad'     => (float) $p->cantidad,
                'precio'       => (float) $p->precio,
                'tipo_igv'     => $venta->tipo_igv ?: 'gravado', // afectación de todo el comprobante
            ])->values()->toArray(),
        ];
    }

    /**
     * Traduce la medida del producto al código del catálogo 03 de SUNAT
     * (UN/ECE). SUNAT rechaza con error 2936 cualquier valor fuera del
     * catálogo, así que lo desconocido cae en NIU (unidad de bienes).
     */
    private function unidadSunat(?string $medida): string
    {
        return match (mb_strtoupper(trim((string) $medida))) {
            '', 'UNIDAD', 'UNIDADES', 'UND', 'UN', 'NIU' => 'NIU',
            'KILOS', 'KILO', 'KILOGRAMO', 'KG', 'KGM'    => 'KGM',
            'CAJA', 'CJ', 'BX'                           => 'BX',
            'BOLSA', 'BS', 'BG'                          => 'BG',
            'SACO', 'SC', 'SA'                           => 'SA',
            'PAQUETE', 'PQTE', 'PQ', 'PK'                => 'PK',
            'DISPLAY', 'DS'                              => 'PK',
            'LITRO', 'LITROS', 'LT', 'LTR'               => 'LTR',
            'GALON', 'GALÓN', 'GLL'                      => 'GLL',
            'GRAMO', 'GRAMOS', 'GRM'                     => 'GRM',
            'TONELADA', 'TNE'                            => 'TNE',
            'DOCENA', 'DZN'                              => 'DZN',
            'CIENTO', 'CEN'                              => 'CEN',
            'MILLAR', 'MIL'                              => 'MIL',
            'PAR', 'PR'                                  => 'PR',
            'METRO', 'MTR'                               => 'MTR',
            'BOTELLA', 'BO'                              => 'BO',
            'LATA', 'CA'                                 => 'CA',
            'SERVICIO', 'SERVICIOS', 'ZZ'                => 'ZZ',
            default                                      => 'NIU',
        };
    }
}
