<?php

namespace App\Services;

use App\Models\Empresa;
use App\Models\GuiaRemision;
use Illuminate\Support\Facades\Http;

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
    public function enviar(GuiaRemision $guia): array
    {
        $guia->loadMissing(['empresa', 'venta.cliente', 'detalles']);
        $empresa = $guia->empresa ?? Empresa::find($guia->id_empresa);

        if (blank($empresa->gre_client_id) || blank($empresa->gre_client_secret)) {
            return ['ok' => false, 'msg' => 'Faltan las credenciales GRE (client_id/secret) en la configuración de la empresa.'];
        }

        // ── 1) Generar el XML firmado ──────────────────────────────────────
        try {
            $gen = Http::timeout(30)->post("{$this->apiUrl}/api/v1/generar/guia/remision", $this->payloadGenerar($guia, $empresa));
            $genData = $gen->json();
        } catch (\Throwable $e) {
            return ['ok' => false, 'msg' => 'No se pudo conectar con el servicio SUNAT (generar).'];
        }

        if (! ($genData['estado'] ?? false)) {
            return ['ok' => false, 'msg' => $genData['mensaje'] ?? 'Error al generar el XML de la guía.'];
        }

        $nombreXml = $genData['data']['nombre_archivo'];
        $xml       = $genData['data']['contenido_xml'];
        $hash      = $genData['data']['hash'] ?? null;

        // ── 2) Enviar a SUNAT (devuelve ticket) ────────────────────────────
        try {
            $env = Http::timeout(40)->post("{$this->apiUrl}/api/v1/enviar/guia/remision", [
                'ruc'                 => $empresa->ruc,
                'usuario'             => $empresa->user_sol ?? '',
                'clave'               => $empresa->clave_sol ?? '',
                'client_id'           => $empresa->gre_client_id,
                'secret_client'       => $empresa->gre_client_secret,
                'endpoint'            => ($empresa->modo ?? '') === 'produccion' ? 'produccion' : 'beta',
                'nombre_documento'    => $nombreXml,
                'contenido_documento' => $xml,
            ]);
            $envData = $env->json();
        } catch (\Throwable $e) {
            return ['ok' => false, 'msg' => 'No se pudo conectar con el servicio SUNAT (enviar).'];
        }

        if (! ($envData['estado'] ?? false)) {
            $guia->update(['estado_gre' => 'rechazado', 'mensaje_sunat' => $envData['mensaje'] ?? 'Rechazado en el envío.']);

            return ['ok' => false, 'msg' => $envData['mensaje'] ?? 'SUNAT rechazó el envío de la guía.'];
        }

        $guia->update([
            'nombre_xml'    => $nombreXml,
            'hash'          => $hash,
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

        if (blank($guia->ticket_sunat)) {
            return ['ok' => false, 'msg' => 'La guía no tiene un ticket para consultar. Enviala primero a SUNAT.'];
        }

        try {
            $res = Http::timeout(40)->post("{$this->apiUrl}/api/v1/consulta/documento/ticker/{$guia->ticket_sunat}", [
                'ruc'           => $empresa->ruc,
                'usuario'       => $empresa->user_sol ?? '',
                'clave'         => $empresa->clave_sol ?? '',
                'client_id'     => $empresa->gre_client_id,
                'secret_client' => $empresa->gre_client_secret,
                'endpoint'      => ($empresa->modo ?? '') === 'produccion' ? 'produccion' : 'beta',
            ]);
            $data = $res->json();
        } catch (\Throwable $e) {
            return ['ok' => false, 'msg' => 'No se pudo conectar con el servicio SUNAT (consulta).'];
        }

        if ($data['estado'] ?? false) {
            $guia->update([
                'estado_gre'    => 'aceptado',
                'codigo_sunat'  => '0',
                'mensaje_sunat' => $data['mensaje'] ?? 'Aceptado por SUNAT.',
                'cdr_url'       => $data['url_ref_sunat'] ?? null,
            ]);

            return ['ok' => true, 'msg' => $data['mensaje'] ?? 'Guía aceptada por SUNAT.'];
        }

        $guia->update([
            'estado_gre'    => 'rechazado',
            'mensaje_sunat' => $data['mensaje'] ?? 'Rechazado por SUNAT.',
        ]);

        return ['ok' => false, 'msg' => $data['mensaje'] ?? 'SUNAT rechazó la guía.'];
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

        $payload = [
            'empresa' => [
                'ruc'          => $empresa->ruc,
                'usuario'      => $empresa->user_sol ?? '',
                'clave'        => $empresa->clave_sol ?? '',
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
            'endpoint'      => ($empresa->modo ?? '') === 'produccion' ? 'produccion' : 'beta',
            'serie'         => $guia->serie ?: 'T001',
            'numero'        => (string) $guia->numero,
            'fecha_emision' => ($guia->fecha_emision ?? now())->format('Y-m-d'),
            'datos_envio' => [
                'cod_traslado'      => $guia->motivo_traslado ?: '01',
                'mod_traslado'      => $modTraslado,
                'fecha_traslado'    => ($guia->fecha_traslado ?? $guia->fecha_emision ?? now())->format('Y-m-d'),
                'peso_total'        => (float) ($guia->peso ?: 1),
                'unidad_medida'     => $guia->und_peso_total ?: 'KGM',
                'ubigeo_salida'     => $guia->ubigeo_partida ?: ($empresa->ubigeo ?? ''),
                'direccion_salida'  => $guia->dir_partida ?: ($empresa->direccion ?? ''),
                'ubigeo_llegada'    => $guia->ubigeo ?: '',
                'direccion_llegada' => $guia->dir_llegada ?: '',
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
