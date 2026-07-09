<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>COMPRA {{ $documentoCompleto }}</title>
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

        .badge { display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 8pt; font-weight: bold; }
        .badge-pendiente { background: #fef3c7; color: #92400e; }
        .badge-recibida  { background: #d1fae5; color: #065f46; }
    </style>
</head>
<body>
    <div>
        <!-- Encabezado -->
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
                            REGISTRO DE COMPRA
                        </div>
                        <div style="text-align:center; padding:9px; font-size:16px; font-weight:bold; color:#000;">
                            {{ $documentoCompleto }}
                        </div>
                    </div>
                </td>
            </tr>
        </table>

        <!-- Proveedor + datos de la compra -->
        <table style="width:100%; border-collapse:separate; border-spacing:10px 0; margin-left:-10px; margin-bottom:14px;">
            <tr>
                <td style="width:55%; vertical-align:top;">
                    <div class="card">
                        <div class="label" style="margin-bottom:5px;">PROVEEDOR</div>
                        <div class="value" style="line-height:1.7;">
                            <span class="label">Razón social:</span> {{ $compra->proveedor->razon_social ?? '—' }}<br>
                            <span class="label">RUC:</span> {{ $compra->proveedor->ruc ?? '—' }}<br>
                            @if($compra->proveedor->direccion ?? '')
                                <span class="label">Dirección:</span> {{ $compra->proveedor->direccion }}<br>
                            @endif
                            @if($compra->proveedor->telefono ?? '')
                                <span class="label">Teléfono:</span> {{ $compra->proveedor->telefono }}
                            @endif
                        </div>
                    </div>
                </td>
                <td style="width:45%; vertical-align:top;">
                    <div class="card">
                        <div class="label" style="margin-bottom:5px;">DATOS DE LA COMPRA</div>
                        <div class="value" style="line-height:1.7;">
                            <span class="label">Tipo de documento:</span> {{ $tipoDocumento }}<br>
                            <span class="label">Fecha de emisión:</span> {{ optional($compra->fecha_emision)->format('d/m/Y') ?? '—' }}<br>
                            <span class="label">Forma de pago:</span> {{ $formaPago }}<br>
                            @if($instrumento)
                                <span class="label">Instrumento:</span> {{ $instrumento }}<br>
                            @endif
                        </div>
                        {{-- El badge va en su propio bloque: dompdf no maneja bien
                             un inline-block con padding dentro de una línea de texto. --}}
                        <div style="margin-top:7px;">
                            @if((int) $compra->recepcionado === 0)
                                <span class="badge badge-pendiente">PENDIENTE DE RECEPCIÓN</span>
                            @else
                                <span class="badge badge-recibida">RECEPCIONADA</span>
                            @endif
                        </div>
                    </div>
                </td>
            </tr>
        </table>

        <!-- Detalle -->
        <table class="products-table">
            <thead>
                <tr>
                    <th style="width:8%;">ÍTEM</th>
                    <th style="width:14%;">CÓDIGO</th>
                    <th style="width:42%; text-align:left;">DESCRIPCIÓN</th>
                    <th style="width:10%;">CANT.</th>
                    <th style="width:13%;">COSTO UNIT.</th>
                    <th style="width:13%;">IMPORTE</th>
                </tr>
            </thead>
            <tbody>
                @foreach($lineas as $i => $l)
                    <tr>
                        <td style="text-align:center;">{{ $i + 1 }}</td>
                        <td style="text-align:center;">{{ $l->codigo ?? '—' }}</td>
                        <td>{{ $l->descripcion ?? 'Producto #' . $l->id_producto }}</td>
                        <td style="text-align:center;">{{ rtrim(rtrim(number_format((float) $l->cantidad, 2), '0'), '.') }}</td>
                        <td style="text-align:right;">S/ {{ number_format((float) $l->costo, 2) }}</td>
                        <td style="text-align:right;">S/ {{ number_format((float) $l->cantidad * (float) $l->costo, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Total -->
        <table style="width:100%; margin-top:10px; border-collapse:collapse;">
            <tr>
                <td style="width:60%; vertical-align:top;">
                    @if($compra->direccion)
                        <div class="card">
                            <span class="label">Observación:</span>
                            <span class="value">{{ $compra->direccion }}</span>
                        </div>
                    @endif
                </td>
                <td style="width:40%; vertical-align:top;">
                    <table style="width:100%; border-collapse:collapse;">
                        <tr>
                            <td style="padding:8px 10px; background:#bfc4cc; font-weight:bold; font-size:10pt; text-align:right; border-radius:6px 0 0 6px;">
                                TOTAL COMPRA
                            </td>
                            <td style="padding:8px 10px; background:#bfc4cc; font-weight:bold; font-size:12pt; text-align:right; border-radius:0 6px 6px 0;">
                                S/ {{ number_format((float) $compra->total, 2) }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <!-- Pie -->
        <div class="footer">
            <p>{{ $empresa->razon_social ?? '' }} | RUC: {{ $empresa->ruc ?? '' }}</p>
            <p style="margin-top:4px">
                Documento interno de control de compras — no constituye comprobante de pago.
            </p>
        </div>
    </div>
</body>
</html>
