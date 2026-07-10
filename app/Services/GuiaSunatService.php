<?php

namespace App\Services;

use App\Models\Empresa;
use App\Models\GuiaRemision;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

/**
 * Envío de Guías de Remisión electrónicas (GRE) a SUNAT a través del
 * microservicio api-sunat-laravel. Flujo asíncrono:
 *   1. generar/guia/remision  → XML firmado
 *   2. enviar/guia/remision    → devuelve un ticket
 *   3. consulta/.../{ticket}    → devuelve el CDR (aceptado/rechazado)
 */
class GuiaSunatService
{
    private string $apiUrl;

    public function __construct()
    {
        $this->apiUrl = rtrim((string) config('sunat.api_url'), '/');
    }

    /** Genera el XML y lo envía a SUNAT. Guarda el ticket en la guía. */
    /**
     * Genera y firma el XML de la guía y lo guarda en el storage local.
     * NO envía a SUNAT y NO requiere las credenciales GRE: el endpoint de
     * generación solo necesita el certificado del RUC.
     */
    public function generarXml(GuiaRemision $guia): array
    {
        $guia->loadMissing(['empresa', 'venta.cliente', 'detalles']);
        $empresa = $guia->empresa ?? Empresa::find($guia->id_empresa);

        if (! $empresa) {
            return ['ok' => false, 'datos_invalidos' => true, 'msg' => 'No se encontró la empresa de la guía.'];
        }

        if ($guia->detalles->isEmpty()) {
            return ['ok' => false, 'datos_invalidos' => true, 'msg' => 'La guía no tiene productos para trasladar.'];
        }

        try {
            $gen = Http::timeout(30)->post("{$this->apiUrl}/api/v1/generar/guia/remision", $this->payloadGenerar($guia, $empresa));
            $genData = $gen->json();
        } catch (\Throwable $e) {
            // El servicio no respondió: no es culpa de los datos de la guía.
            return ['ok' => false, 'datos_invalidos' => false, 'msg' => 'No se pudo conectar con el servicio SUNAT (generar).'];
        }

        if (! ($genData['estado'] ?? false)) {
            return [
                'ok'              => false,
                'datos_invalidos' => true,
                'msg'             => static::detalleDelError($genData, 'Error al generar el XML de la guía.'),
            ];
        }

        $nombre = $genData['data']['nombre_archivo'];
        $xml    = $genData['data']['contenido_xml'];
        $hash   = $genData['data']['hash'] ?? null;

        $xmlRuta = "sunat/xml/{$empresa->credencialesSunat()['ruc']}/{$nombre}.xml";
        Storage::disk('local')->put($xmlRuta, $xml);

        $yaAceptada = $guia->estado_gre === 'aceptado';

        $guia->update([
            'nombre_xml'    => $nombre,
            'hash'          => $hash,
            'xml_ruta'      => $xmlRuta,
            'estado_gre'    => $yaAceptada ? 'aceptado' : 'pendiente',
            'mensaje_sunat' => $yaAceptada ? $guia->mensaje_sunat : 'XML generado, pendiente de envío.',
        ]);

        return ['ok' => true, 'msg' => "XML generado y guardado ({$nombre}.xml).", 'nombre' => $nombre, 'xml' => $xml];
    }

    /** Envía a SUNAT el XML ya generado (lo genera si aún no existe). Devuelve un ticket. */
    public function enviar(GuiaRemision $guia): array
    {
        $guia->loadMissing(['empresa', 'venta.cliente', 'detalles']);
        $empresa = $guia->empresa ?? Empresa::find($guia->id_empresa);

        if (blank($empresa->gre_client_id) || blank($empresa->gre_client_secret)) {
            return ['ok' => false, 'msg' => 'Faltan las credenciales GRE (client_id/secret) en la configuración de la empresa.'];
        }

        if ($guia->estado === '0') {
            return ['ok' => false, 'msg' => 'No se puede enviar una guía anulada.'];
        }

        // Usar el XML ya generado (el que revisó el usuario); si no existe, generarlo.
        if (blank($guia->xml_ruta) || ! Storage::disk('local')->exists($guia->xml_ruta)) {
            $gen = $this->generarXml($guia);
            if (! $gen['ok']) {
                return $gen;
            }
            $nombreXml = $gen['nombre'];
            $xml       = $gen['xml'];
        } else {
            $nombreXml = pathinfo($guia->xml_ruta, PATHINFO_FILENAME);
            $xml       = Storage::disk('local')->get($guia->xml_ruta);
        }

        try {
            $cred = $empresa->credencialesSunat();

            $env = Http::timeout(40)->post("{$this->apiUrl}/api/v1/enviar/guia/remision", [
                'ruc'                 => $cred['ruc'],
                'usuario'             => $cred['usuario'],
                'clave'               => $cred['clave'],
                // Las credenciales GRE (OAuth2) son siempre las reales de la
                // empresa: SUNAT no ofrece unas de prueba.
                'client_id'           => $empresa->gre_client_id,
                'secret_client'       => $empresa->gre_client_secret,
                'endpoint'            => $cred['endpoint'],
                'nombre_documento'    => $nombreXml,
                'contenido_documento' => $xml,
            ]);
            $envData = $env->json();
        } catch (\Throwable $e) {
            return ['ok' => false, 'msg' => 'No se pudo conectar con el servicio SUNAT (enviar).'];
        }

        if (! ($envData['estado'] ?? false)) {
            $msg = static::detalleDelError($envData, 'SUNAT rechazó el envío de la guía.');
            $guia->update(['estado_gre' => 'rechazado', 'mensaje_sunat' => $msg]);

            return ['ok' => false, 'msg' => $msg];
        }

        $guia->update([
            'ticket_sunat'  => $envData['ticker'] ?? null,
            'estado_gre'    => 'enviado',
            'enviado_sunat' => '1',
            'mensaje_sunat' => 'Enviado. Ticket: ' . ($envData['ticker'] ?? '—'),
        ]);

        return ['ok' => true, 'msg' => 'Guía enviada. Ticket: ' . ($envData['ticker'] ?? '—') . '. Consultá el ticket para ver el resultado.'];
    }

    /** Consulta el estado del ticket y guarda el CDR (aceptado/rechazado). */
    public function consultarTicket(GuiaRemision $guia): array
    {
        $empresa = $guia->empresa ?? Empresa::find($guia->id_empresa);
        $cred    = $empresa->credencialesSunat();

        if (blank($guia->ticket_sunat)) {
            return ['ok' => false, 'msg' => 'La guía no tiene un ticket para consultar. Enviala primero a SUNAT.'];
        }

        try {
            $res = Http::timeout(40)->post("{$this->apiUrl}/api/v1/consulta/documento/ticker/{$guia->ticket_sunat}", [
                'ruc'           => $cred['ruc'],
                'usuario'       => $cred['usuario'],
                'clave'         => $cred['clave'],
                'client_id'     => $empresa->gre_client_id,
                'secret_client' => $empresa->gre_client_secret,
                'endpoint'      => $cred['endpoint'],
            ]);
            $data = $res->json();
        } catch (\Throwable $e) {
            return ['ok' => false, 'msg' => 'No se pudo conectar con el servicio SUNAT (consulta).'];
        }

        if ($data['estado'] ?? false) {
            // Guardar el CDR (respuesta oficial de SUNAT) en el storage local.
            $cdrRuta = null;
            if (! empty($data['cdr'])) {
                $nombre  = $guia->nombre_xml ?: ($guia->serie . '-' . $guia->numero);
                $cdrRuta = "sunat/cdr/{$cred['ruc']}/R-{$nombre}.zip";
                Storage::disk('local')->put($cdrRuta, base64_decode($data['cdr'], true) ?: '');
            }

            $guia->update([
                'estado_gre'    => 'aceptado',
                'codigo_sunat'  => '0',
                'mensaje_sunat' => $data['mensaje'] ?? 'Aceptado por SUNAT.',
                'cdr_url'       => $data['url_ref_sunat'] ?? null,
                'cdr_ruta'      => $cdrRuta,
            ]);

            return ['ok' => true, 'msg' => $data['mensaje'] ?? 'Guía aceptada por SUNAT.'];
        }

        $guia->update([
            'estado_gre'    => 'rechazado',
            'mensaje_sunat' => $data['mensaje'] ?? 'Rechazado por SUNAT.',
        ]);

        return ['ok' => false, 'msg' => $data['mensaje'] ?? 'SUNAT rechazó la guía.'];
    }

    /**
     * La API responde 422 con un mensaje genérico y el detalle campo por campo
     * en `errores`. Sin ese detalle un rechazo es imposible de diagnosticar.
     *
     * @param  array<string, mixed>|null  $respuesta
     */
    private static function detalleDelError(?array $respuesta, string $porDefecto): string
    {
        $mensaje = $respuesta['mensaje'] ?? $porDefecto;

        $detalle = collect($respuesta['errores'] ?? [])
            ->map(fn ($mensajes, $campo): string => $campo . ': ' . implode(' ', (array) $mensajes))
            ->take(4)
            ->implode(' | ');

        return trim($mensaje . ($detalle !== '' ? " ({$detalle})" : ''));
    }

    /** Arma el JSON que espera api-sunat-laravel para generar el XML. */
    private function payloadGenerar(GuiaRemision $guia, Empresa $empresa): array
    {
        $cliente = $guia->venta?->cliente;
        $docCli  = (string) ($cliente?->documento ?? '');
        $tipoCli = match (strlen($docCli)) { 8 => '1', 11 => '6', default => '0' };

        // projRoma: tipo_transporte 1=Privado, 2=Público
        // SUNAT: mod_traslado 01=Público, 02=Privado  (¡invertido!)
        $modTraslado = ((string) $guia->tipo_transporte === '2') ? '01' : '02';

        // Mismo criterio que ventas y notas: en beta se firma con el RUC de
        // prueba (que sí tiene certificado); en producción, con el real.
        $cred = $empresa->credencialesSunat();

        $payload = [
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
                'tipo_doc'   => $tipoCli === '0' ? '1' : $tipoCli,
                'rzn_social' => $cliente?->datos ?? 'Cliente',
                'direccion'  => $cliente?->direccion ?? '',
            ],
            'documento'     => 'remitente',
            'endpoint'      => $cred['endpoint'],
            'serie'         => $guia->serie ?: 'T001',
            'numero'        => (string) $guia->numero,
            'fecha_emision' => ($guia->fecha_emision ?? now())->format('Y-m-d'),
            'datos_envio' => [
                'cod_traslado'      => $guia->motivo_traslado ?: '01',
                'mod_traslado'      => $modTraslado,
                'fecha_traslado'    => ($guia->fecha_traslado ?? $guia->fecha_emision ?? now())->format('Y-m-d'),
                'peso_total'        => (float) ($guia->peso ?: 1),
                'unidad_medida'     => $guia->und_peso_total ?: 'KGM',
                // Siempre como string: SUNAT valida el ubigeo como cadena de 6
                // caracteres y un ubigeo sin cero inicial llega aquí como int.
                'ubigeo_salida'     => (string) ($guia->ubigeo_partida ?: ($empresa->ubigeo ?? '')),
                'direccion_salida'  => (string) ($guia->dir_partida ?: ($empresa->direccion ?? '')),
                'ubigeo_llegada'    => (string) ($guia->ubigeo ?: ''),
                'direccion_llegada' => (string) ($guia->dir_llegada ?: ''),
            ],
            'detalles' => $guia->detalles->map(fn ($d): array => [
                'descripcion' => $d->detalles ?: 'Producto',
                'cantidad'    => (float) $d->cantidad,
                'unidad'      => $d->unidad ?: 'NIU',
                'cod_producto' => (string) ($d->id_producto ?: 'P001'),
            ])->values()->toArray(),
        ];

        if ($modTraslado === '01') {
            // Transporte público
            $payload['transportista'] = [
                'num_doc'    => $guia->ruc_transporte ?: '',
                'rzn_social' => $guia->razon_transporte ?: '',
                'nro_mtc'    => $guia->transportista_nro_mtc ?: '',
            ];
        } else {
            // Transporte privado — placa + conductor (opcional pero completo)
            $payload['transportista'] = [
                'placa_chofer'    => $guia->vehiculo ?: '',
                'dni_chofer'      => $guia->conductor_documento ?: '',
                'nombre_chofer'   => $guia->conductor_nombres ?: '',
                'apellido_chofer' => $guia->conductor_apellidos ?: '',
                'licencia_chofer' => $guia->conductor_licencia ?: '',
            ];
        }

        return $payload;
    }
}
