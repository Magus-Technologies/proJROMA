<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>GUÍA DE REMISIÓN {{ $guia->serie }}-{{ str_pad($guia->numero, 8, '0', STR_PAD_LEFT) }}</title>
    <style>
        @page { margin: 45px 40px 45px 40px; }
        body { font-family: 'Arial', sans-serif; font-size: 9pt; color: #333; margin: 0; padding: 0; }
        p, div, span, table, td, th, tr { margin: 0; padding: 0; }

        .products-table { width: 100%; border-collapse: collapse; margin-bottom: 5px; border: 2px solid #999; border-radius: 6px; overflow: hidden; }
        .products-table thead { background: #bfc4cc; }
        .products-table th { padding: 6px 4px; font-size: 7.5pt; font-weight: bold; border: 1px solid #999; text-align: center; }
        .products-table td { padding: 4px 5px; font-size: 8pt; border-bottom: 1px solid #e5e7eb; vertical-align: top; }
        .products-table tbody tr:nth-child(even) td { background: #f1f3f5; }

        .footer { text-align: center; margin-top: 20px; padding-top: 12px; border-top: 1px solid #ddd; font-size: 8pt; color: #666; }
        .card { border: 1px solid #777; border-radius: 8px; padding: 10px; }
        .label { font-weight: bold; font-size: 8pt; color: #000; }
        .value { font-size: 8pt; color: #000; }
    </style>
</head>
<body>
    <div>
        <!-- Header -->
        <table style="width:100%; margin-bottom:16px; border-collapse:collapse;">
            <tr>
                <td style="width:60%; vertical-align:top; padding-right:15px;">
                    @if(!empty($logoBase64))
                        <img src="{{ $logoBase64 }}" style="max-height:60px;max-width:190px;margin-bottom:6px;display:block;">
                    @endif
                    <div style="font-size:13pt; font-weight:bold; color:#dc2626; line-height:1.1;">
                        {{ $empresa->razon_social ?? 'EMPRESA' }}
                    </div>
                    <div style="font-size:8pt; color:#555; margin-top:4px; line-height:1.6;">
                        {{ $empresa->direccion ?? '' }}<br>
                        @if($empresa->telefono ?? '')<span style="font-weight:bold;">TELEF.:</span> {{ $empresa->telefono }}<br>@endif
                        @if($empresa->email ?? '')<span style="font-weight:bold;">Correo:</span> {{ $empresa->email }}@endif
                    </div>
                </td>
                <td style="width:40%; vertical-align:top; text-align:right;">
                    <div style="border:2px solid #bfc4cc; border-radius:10px; overflow:hidden; width:230px; float:right;">
                        <div style="text-align:center; padding:7px 10px; font-size:11px; font-weight:bold; color:#000;">
                            R.U.C. {{ $empresa->ruc ?? '' }}
                        </div>
                        <div style="background:#bfc4cc; text-align:center; padding:9px; font-size:12px; font-weight:bold; color:#000;">
                            GUÍA DE REMISIÓN REMITENTE
                        </div>
                        <div style="text-align:center; padding:9px; font-size:16px; font-weight:bold; color:#000;">
                            {{ $guia->serie }}-{{ str_pad($guia->numero, 8, '0', STR_PAD_LEFT) }}
                        </div>
                    </div>
                </td>
            </tr>
        </table>

        <!-- Datos del traslado -->
        <table style="width:100%; border-collapse:separate; border-spacing:10px 0; margin-left:-10px; margin-bottom:14px;">
            <tr>
                <!-- Izquierda: Destinatario -->
                <td style="width:48%; vertical-align:top;" class="card">
                    <div style="font-size:8pt; font-weight:bold; color:#555; margin-bottom:6px; text-transform:uppercase; border-bottom:1px solid #e5e7eb; padding-bottom:4px;">Destinatario</div>
                    <table style="width:100%; border-collapse:collapse;">
                        <tr>
                            <td class="label" style="width:35%; padding-bottom:4px; vertical-align:top;">CLIENTE:</td>
                            <td class="value" style="padding-bottom:4px; vertical-align:top;">{{ $guia->venta?->cliente?->datos ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="label" style="padding-bottom:4px; vertical-align:top;">{{ strlen($guia->venta?->cliente?->documento ?? '') == 11 ? 'RUC' : 'DNI' }}:</td>
                            <td class="value" style="padding-bottom:4px; vertical-align:top;">{{ $guia->venta?->cliente?->documento ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="label" style="vertical-align:top;">DIR. LLEGADA:</td>
                            <td class="value" style="vertical-align:top;">{{ $guia->dir_llegada ?? '-' }}</td>
                        </tr>
                    </table>
                </td>

                <!-- Derecha: Datos del traslado -->
                <td style="width:48%; vertical-align:top;" class="card">
                    <div style="font-size:8pt; font-weight:bold; color:#555; margin-bottom:6px; text-transform:uppercase; border-bottom:1px solid #e5e7eb; padding-bottom:4px;">Datos del Traslado</div>
                    <table style="width:100%; border-collapse:collapse;">
                        <tr>
                            <td class="label" style="width:45%; padding-bottom:4px; vertical-align:top;">FECHA EMISIÓN:</td>
                            <td class="value" style="padding-bottom:4px; vertical-align:top;">{{ $guia->fecha_emision ? $guia->fecha_emision->format('d/m/Y') : '-' }}</td>
                        </tr>
                        <tr>
                            <td class="label" style="padding-bottom:4px; vertical-align:top;">TIPO TRANSP.:</td>
                            <td class="value" style="padding-bottom:4px; vertical-align:top;">{{ $guia->tipo_transporte == '01' ? 'Público' : 'Privado' }}</td>
                        </tr>
                        <tr>
                            <td class="label" style="padding-bottom:4px; vertical-align:top;">PESO TOTAL:</td>
                            <td class="value" style="padding-bottom:4px; vertical-align:top;">{{ $guia->peso ?? '-' }} KG</td>
                        </tr>
                        <tr>
                            <td class="label" style="vertical-align:top;">N° BULTOS:</td>
                            <td class="value" style="vertical-align:top;">{{ $guia->nro_bultos ?? '-' }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <!-- Transporte -->
        <table style="width:100%; border-collapse:collapse; margin-bottom:12px;" class="card">
            <tr>
                <td style="width:50%;">
                    <span class="label">TRANSPORTISTA:</span>
                    <span class="value"> {{ $guia->razon_transporte ?? '-' }}</span>
                </td>
                <td style="width:25%;">
                    <span class="label">RUC/DNI:</span>
                    <span class="value"> {{ $guia->ruc_transporte ?? '-' }}</span>
                </td>
                <td style="width:25%;">
                    <span class="label">VEHÍCULO:</span>
                    <span class="value"> {{ $guia->vehiculo ?? '-' }}</span>
                </td>
            </tr>
            <tr style="margin-top:4px;">
                <td colspan="2" style="padding-top:5px;">
                    <span class="label">CHOFER / BREVETE:</span>
                    <span class="value"> {{ $guia->chofer_brevete ?? '-' }}</span>
                </td>
                <td style="padding-top:5px;">
                    <span class="label">REF. COMPROBANTE:</span>
                    <span class="value"> {{ $guia->venta?->documento_completo ?? '-' }}</span>
                </td>
            </tr>
        </table>

        <!-- Productos -->
        <table class="products-table">
            <thead>
                <tr>
                    <th width="5%">N°</th>
                    <th width="10%">CANT.</th>
                    <th width="10%">UNIDAD</th>
                    <th width="75%" style="text-align:left; padding-left:6px;">DESCRIPCIÓN</th>
                </tr>
            </thead>
            <tbody>
                @forelse($guia->detalles as $i => $d)
                <tr>
                    <td style="text-align:center;">{{ $i + 1 }}</td>
                    <td style="text-align:center;">{{ number_format($d->cantidad, 2) }}</td>
                    <td style="text-align:center;">{{ $d->unidad ?? 'NIU' }}</td>
                    <td style="padding-left:6px;">{{ $d->detalles ?? '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="text-align:center; padding:15px; color:#999;">Sin productos</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Firmas -->
        <table style="width:100%; border-collapse:collapse; margin-top:30px;">
            <tr>
                <td style="width:45%; text-align:center; vertical-align:bottom;">
                    <div style="border-top:1px solid #333; padding-top:6px; font-size:8pt; font-weight:bold;">FIRMA Y SELLO REMITENTE</div>
                </td>
                <td style="width:10%;"></td>
                <td style="width:45%; text-align:center; vertical-align:bottom;">
                    <div style="border-top:1px solid #333; padding-top:6px; font-size:8pt; font-weight:bold;">FIRMA Y SELLO DESTINATARIO</div>
                </td>
            </tr>
        </table>

        <!-- Footer -->
        <div class="footer">
            <p>{{ $empresa->razon_social ?? '' }} | RUC: {{ $empresa->ruc ?? '' }}</p>
        </div>
    </div>
</body>
</html>
