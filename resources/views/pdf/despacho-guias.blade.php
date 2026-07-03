<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Guías de Reparto - {{ $despacho->codigo }}</title>
    <style>
        @page { margin: 25px 20px; }
        body { font-family: 'Arial', sans-serif; font-size: 7.5pt; color: #000; margin: 0; padding: 0; }
        .par { width: 100%; border-collapse: collapse; }
        .par > tr > td, .par td.copia-cell { vertical-align: top; }
        .ticket { width: 100%; border-collapse: collapse; }
        .cab { width: 100%; border-collapse: collapse; margin-bottom: 3px; }
        .cab td { vertical-align: top; }
        .empresa-nombre { font-size: 10pt; font-weight: bold; color: #1d4ed8; }
        .empresa-datos { font-size: 6.5pt; line-height: 1.35; }
        .doc-box { border: 1.5px solid #000; text-align: center; padding: 4px 6px; font-weight: bold; font-size: 8pt; }
        .info { width: 100%; border-collapse: collapse; border: 1px solid #000; margin-bottom: 3px; }
        .info td { padding: 2px 4px; font-size: 7pt; border-bottom: 1px solid #ccc; }
        .items { width: 100%; border-collapse: collapse; border: 1px solid #000; }
        .items th { border: 1px solid #000; padding: 2px 3px; font-size: 6.5pt; background: #eee; }
        .items td { padding: 1.5px 3px; font-size: 7pt; border-left: 1px solid #000; border-right: 1px solid #000; }
        .pie { width: 100%; border-collapse: collapse; margin-top: 2px; }
        .pie td { font-size: 7pt; padding: 2px 3px; }
        .total-box { border: 1.5px solid #000; font-weight: bold; text-align: right; padding: 3px 6px; }
        .son { border: 1px solid #000; border-top: none; padding: 2px 4px; font-size: 6.5pt; font-weight: bold; }
        .copia-lbl { text-align: right; font-size: 7pt; font-weight: bold; padding-bottom: 2px; }
        .salto { page-break-after: always; }
    </style>
</head>
<body>

@foreach($pedidos as $idx => $p)
    @php
        $numeroDoc = $serie . '-' . str_pad((string) $p->numero, 8, '0', STR_PAD_LEFT);
        $total     = (float) $p->total;
        $entero    = floor($total);
        $decimales = str_pad((string) round(($total - $entero) * 100), 2, '0', STR_PAD_LEFT);
        $letras    = function_exists('num2letras') ? strtoupper(num2letras($entero)) : (string) $entero;
        $mercadoNombre = $mercados[$p->cliente?->mercado] ?? null;
    @endphp

    <table class="par">
        <tr>
            @foreach(['ORIGINAL', 'COPIA'] as $tipo)
            <td style="width:49%; {{ $loop->first ? 'padding-right:8px; border-right:1px dashed #999;' : 'padding-left:8px;' }}">
                <div class="copia-lbl">{{ $tipo === 'COPIA' ? 'COPIA' : '&nbsp;' }}</div>

                <table class="cab">
                    <tr>
                        <td style="width:62%;">
                            @if(!empty($logoBase64))
                                <img src="{{ $logoBase64 }}" style="max-height:30px;max-width:110px;display:block;margin-bottom:2px;">
                            @endif
                            <div class="empresa-nombre">{{ $empresa->razon_social ?? 'EMPRESA' }}</div>
                            <div class="empresa-datos">
                                @if($empresa->telefono ?? '')Central Telefónica: {{ $empresa->telefono }}<br>@endif
                                @if($empresa->email ?? '')Email: {{ $empresa->email }}@endif
                                @if($empresa->web ?? '') | Web: {{ $empresa->web }}@endif<br>
                                Dirección: {{ $empresa->direccion ?? '' }}
                            </div>
                        </td>
                        <td style="width:38%; padding-left:6px;">
                            <div class="doc-box">
                                PEDIDO<br>{{ $numeroDoc }}<br>
                                <span style="font-size:6.5pt;">ITEM: {{ $idx + 1 }} / {{ $pedidos->count() }}</span>
                            </div>
                        </td>
                    </tr>
                </table>

                <table class="info">
                    <tr>
                        <td style="width:58%;"><b>CLIENTE:</b> {{ $p->cliente?->datos ?? '-' }}</td>
                        <td style="width:42%;"><b>CELULAR:</b> {{ $p->cliente?->telefono ?? '' }}</td>
                    </tr>
                    <tr>
                        <td><b>DIRECCIÓN:</b> {{ $p->cliente?->direccion ?? '-' }}</td>
                        <td><b>VENDEDOR:</b> {{ $p->usuario?->nombres ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td><b>RUC/DNI:</b> {{ $p->cliente?->documento ?? '-' }}</td>
                        <td><b>FECHA:</b> {{ strtoupper(\Carbon\Carbon::parse($p->fecha)->translatedFormat('F d \d\e\l Y')) }}</td>
                    </tr>
                    <tr>
                        <td style="border-bottom:none;"><b>MERCADO:</b> {{ $mercadoNombre ?? '-' }} | <b>MONEDA:</b> SOLES</td>
                        <td style="border-bottom:none;"><b>PAGO:</b> {{ (int) $p->id_tipo_pago === 2 ? 'Crédito' : 'Contado' }}</td>
                    </tr>
                </table>

                <table class="items">
                    <thead>
                        <tr>
                            <th style="width:7%">ITEM</th>
                            <th style="width:43%">DESCRIPCIÓN</th>
                            <th style="width:12%">CANT.</th>
                            <th style="width:14%">MEDIDA</th>
                            <th style="width:11%">PRECIO</th>
                            <th style="width:13%">SUBTOTAL</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($p->productos as $linea)
                        <tr>
                            <td style="text-align:center;">{{ $loop->iteration }}</td>
                            <td>{{ $linea->producto?->descripcion ?? ('Producto #' . $linea->id_producto) }}</td>
                            <td style="text-align:center;">{{ rtrim(rtrim(number_format((float) $linea->cantidad, 2), '0'), '.') }}</td>
                            <td style="text-align:center;">{{ $linea->medida ?: 'Unidad' }}</td>
                            <td style="text-align:right;">{{ number_format((float) $linea->precio, 2) }}</td>
                            <td style="text-align:right;">{{ number_format((float) $linea->cantidad * (float) $linea->precio, 2) }}</td>
                        </tr>
                        @endforeach
                        <tr><td colspan="6" style="border-bottom:1px solid #000;">&nbsp;</td></tr>
                    </tbody>
                </table>
                <div class="son">SON: | {{ $letras }} CON {{ $decimales }}/100 SOLES</div>

                <table class="pie">
                    <tr>
                        <td style="width:60%;"><b>Observaciones:</b> {{ $p->observacion }}</td>
                        <td style="width:40%;">
                            <table style="width:100%; border-collapse:collapse;">
                                <tr>
                                    <td class="total-box" style="width:55%; font-size:7pt;">Total a Pagar:</td>
                                    <td class="total-box" style="width:45%;">S/ {{ number_format($total, 2) }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr><td colspan="2"><b>Saldo Pendiente:</b> 0 &nbsp;&nbsp;|&nbsp;&nbsp; Despacho {{ $despacho->codigo }} — Reparto {{ optional($despacho->fecha_reparto)->format('d/m/Y') }} — {{ $despacho->vehiculo?->placa }} / {{ $despacho->conductor?->nombres }}</td></tr>
                </table>
            </td>
            @endforeach
        </tr>
    </table>

    @if(! $loop->last)<div class="salto"></div>@endif
@endforeach

</body>
</html>
