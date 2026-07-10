<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>TRASLADO {{ $numero }}</title>
    <style>
        @page { margin: 45px 40px 45px 40px; }
        body { font-family: 'Arial', sans-serif; font-size: 9pt; color: #333; margin: 0; padding: 0; }
        p, div, span, table, td, th, tr { margin: 0; padding: 0; }

        .products-table { width: 100%; border-collapse: collapse; margin-bottom: 5px; border: 2px solid #999; border-radius: 6px; overflow: hidden; }
        .products-table thead { background: #bfc4cc; }
        .products-table th { padding: 6px 4px; font-size: 7pt; font-weight: bold; border: 1px solid #999; text-align: center; }
        .products-table td { padding: 4px 5px; font-size: 8pt; border-bottom: 1px solid #e5e7eb; vertical-align: top; }
        .products-table tbody tr:nth-child(even) td { background: #f1f3f5; }

        .footer { text-align: center; margin-top: 20px; padding-top: 12px; border-top: 1px solid #ddd; font-size: 8pt; color: #666; }
        .card { border: 1px solid #777; border-radius: 8px; padding: 10px; }
        .label { font-weight: bold; font-size: 8pt; color: #000; }
        .value { font-size: 8pt; color: #000; }

        .badge { display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 8pt; font-weight: bold; }
        .badge-activo  { background: #d1fae5; color: #065f46; }
        .badge-anulado { background: #fee2e2; color: #991b1b; }
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
                            TRASLADO DE STOCK
                        </div>
                        <div style="text-align:center; padding:9px; font-size:16px; font-weight:bold; color:#000;">
                            {{ $numero }}
                        </div>
                    </div>
                </td>
            </tr>
        </table>

        <!-- Datos del traslado -->
        <table style="width:100%; border-collapse:separate; border-spacing:10px 0; margin-left:-10px; margin-bottom:14px;">
            <tr>
                <td style="width:55%; vertical-align:top;">
                    <div class="card">
                        <div class="label" style="margin-bottom:5px;">ALMACENES</div>
                        <div class="value" style="line-height:1.7;">
                            <span class="label">Origen:</span> {{ $almacenes[$traslado->almacen_origen] ?? $traslado->almacen_origen }}<br>
                            <span class="label">Destino:</span> {{ $almacenes[$traslado->almacen_destino] ?? $traslado->almacen_destino }}
                        </div>
                    </div>
                </td>
                <td style="width:45%; vertical-align:top;">
                    <div class="card">
                        <div class="label" style="margin-bottom:5px;">DATOS DEL TRASLADO</div>
                        <div class="value" style="line-height:1.7;">
                            <span class="label">Fecha:</span> {{ optional($traslado->fecha)->format('d/m/Y H:i') ?? '—' }}<br>
                            <span class="label">Usuario:</span> {{ $traslado->usuario->nombres ?? '—' }}
                        </div>
                        <div style="margin-top:7px;">
                            @if((string) $traslado->estado === '1')
                                <span class="badge badge-activo">ACTIVO</span>
                            @else
                                <span class="badge badge-anulado">ANULADO</span>
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
                    <th style="width:5%;">ÍTEM</th>
                    <th style="width:10%;">CÓDIGO</th>
                    <th style="width:29%; text-align:left;">PRODUCTO</th>
                    <th style="width:8%;">UNIDAD</th>
                    <th style="width:7%;">CANT.</th>
                    <th style="width:8%;">STK ANT. ORIG.</th>
                    <th style="width:8%;">STK NVO. ORIG.</th>
                    <th style="width:8%;">STK ANT. DEST.</th>
                    <th style="width:8%;">STK NVO. DEST.</th>
                    <th style="width:9%;">COSTO</th>
                </tr>
            </thead>
            <tbody>
                @foreach($lineas as $i => $l)
                    @php
                        $costo = $l->costo ?? $l->costo_actual;
                        $lineaActiva = (string) ($l->estado ?? '1') === '1';
                    @endphp
                    <tr>
                        <td style="text-align:center;">{{ $i + 1 }}</td>
                        <td style="text-align:center;">{{ $l->codigo ?: '—' }}</td>
                        <td style="{{ $lineaActiva ? '' : 'text-decoration:line-through; color:#999;' }}">
                            {{ $l->descripcion ?? 'Producto #' . $l->id_producto }}
                            @unless($lineaActiva) (ANULADO) @endunless
                        </td>
                        <td style="text-align:center;">{{ $l->medida ?: '—' }}</td>
                        <td style="text-align:center; font-weight:bold;">{{ (int) $l->cantidad }}</td>
                        <td style="text-align:center;">{{ (int) $l->stock_ant_origen }}</td>
                        <td style="text-align:center;">{{ (int) $l->stock_nuevo_origen }}</td>
                        <td style="text-align:center;">{{ (int) $l->stock_ant_destino }}</td>
                        <td style="text-align:center;">{{ (int) $l->stock_nuevo_destino }}</td>
                        <td style="text-align:right;">{{ $costo !== null ? 'S/ ' . number_format((float) $costo, 2) : '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Totales / Observación -->
        <table style="width:100%; margin-top:10px; border-collapse:collapse;">
            <tr>
                <td style="width:60%; vertical-align:top;">
                    @if($traslado->observacion)
                        <div class="card">
                            <span class="label">Observaciones:</span>
                            <span class="value">{{ $traslado->observacion }}</span>
                        </div>
                    @endif
                </td>
                <td style="width:40%; vertical-align:top;">
                    <table style="width:100%; border-collapse:collapse;">
                        <tr>
                            <td style="padding:8px 10px; background:#bfc4cc; font-weight:bold; font-size:10pt; text-align:right; border-radius:6px 0 0 6px;">
                                TOTAL UNIDADES
                            </td>
                            <td style="padding:8px 10px; background:#bfc4cc; font-weight:bold; font-size:12pt; text-align:right; border-radius:0 6px 6px 0;">
                                {{ (int) $lineas->where('estado', '1')->sum('cantidad') }}
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
                Documento interno de traslado entre almacenes — no constituye comprobante de pago.
            </p>
        </div>
    </div>
</body>
</html>
