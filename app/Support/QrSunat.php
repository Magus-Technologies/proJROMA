<?php

namespace App\Support;

use App\Models\GuiaRemision;
use App\Models\Venta;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

/**
 * Código QR obligatorio en la representación impresa de los comprobantes
 * electrónicos. El contenido no es una URL: es una cadena de campos separados
 * por "|" definida por SUNAT, que permite validar el documento sin internet.
 *
 * El `hash` sale del XML firmado, así que solo hay QR si el XML ya se generó.
 */
class QrSunat
{
    /** Cadena SUNAT para factura/boleta: RUC|tipoDoc|serie-numero|IGV|total|fecha|tipoDocCliente|numDocCliente|hash */
    public static function cadenaVenta(Venta $venta): ?string
    {
        $empresa = $venta->empresa;
        $hash    = $venta->hash_cpe;

        if (blank($hash) || blank($empresa?->ruc)) {
            return null;
        }

        // Serie B* = boleta (03); el resto, factura (01).
        $tipoDoc = str_starts_with(strtoupper((string) $venta->serie), 'B') ? '03' : '01';

        $docCliente     = (string) ($venta->cliente?->documento ?? '');
        $tipoDocCliente = strlen($docCliente) === 11 ? '6' : '1';

        return implode('|', [
            $empresa->ruc,
            $tipoDoc,
            $venta->documento_completo,
            number_format((float) ($venta->igv ?? 0), 2, '.', ''),
            number_format((float) ($venta->total ?? 0), 2, '.', ''),
            optional($venta->fecha_emision)->format('Y-m-d') ?? '',
            $tipoDocCliente,
            $docCliente,
            $hash,
        ]);
    }

    /** Cadena SUNAT para guía de remisión: RUC|09|serie-numero|fecha|docDestinatario|hash */
    public static function cadenaGuia(GuiaRemision $guia): ?string
    {
        $empresa = $guia->empresa;
        $hash    = $guia->hash;

        if (blank($hash) || blank($empresa?->ruc)) {
            return null;
        }

        $serieNumero = $guia->serie . '-' . str_pad((string) $guia->numero, 8, '0', STR_PAD_LEFT);

        return implode('|', [
            $empresa->ruc,
            '09',
            $serieNumero,
            optional($guia->fecha_emision)->format('Y-m-d') ?? '',
            $guia->venta?->cliente?->documento ?? '',
            $hash,
        ]);
    }

    /** Renderiza la cadena como PNG en base64, listo para <img src="…"> en el PDF. */
    public static function imagen(?string $contenido, int $escala = 4): ?string
    {
        if (blank($contenido)) {
            return null;
        }

        try {
            $opciones = new QROptions([
                'outputType'  => QRCode::OUTPUT_IMAGE_PNG,
                'eccLevel'    => QRCode::ECC_M,
                'scale'       => $escala,
                'imageBase64' => true,
            ]);

            return (new QRCode($opciones))->render($contenido);
        } catch (\Throwable) {
            // Un QR ausente no debe impedir que se imprima el comprobante.
            return null;
        }
    }

    public static function deVenta(Venta $venta): ?string
    {
        return static::imagen(static::cadenaVenta($venta));
    }

    public static function deGuia(GuiaRemision $guia): ?string
    {
        return static::imagen(static::cadenaGuia($guia));
    }
}
