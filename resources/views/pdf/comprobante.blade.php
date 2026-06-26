<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $v->tipoDocumento?->tipo_doc ?? 'NOTA DE VENTA' }} {{ $v->documento_completo }}</title>
    <style>
        @page { margin: 50px 40px 50px 40px; }
        body { font-family: 'Arial', sans-serif; font-size: 9pt; color: #333; margin: 0; padding: 0; }
        p, div, span, table, td, th, tr { margin: 0; padding: 0; }

        .products-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
            border: 2px solid #999;
            border-radius: 6px;
            overflow: hidden;
        }
        .products-table thead { background: #bfc4cc; }
        .products-table th { padding: 6px 4px; font-size: 7.5pt; font-weight: bold; border: 1px solid #999; text-align: center; }
        .products-table td { padding: 3px 4px; font-size: 8pt; border: none; vertical-align: top; }
        .products-table tbody tr:nth-child(even) td { background: #f1f3f5; }

        .footer { text-align: center; margin-top: 30px; padding-top: 15px; border-top: 1px solid #ddd; font-size: 9pt; color: #666; }

        .badge-estado { display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 8pt; font-weight: bold; }
        .badge-anulada { background: #fee2e2; color: #991b1b; }
        .badge-activa  { background: #d1fae5; color: #065f46; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <table style="width: 100%; margin-bottom: 20px; border-collapse: collapse;">
            <tr>
                <td style="width: 63%; vertical-align: top; text-align: left; padding-right: 15px;">
                    @if(!empty($logoBase64))
                    <img src="{{ $logoBase64 }}" style="max-height:65px;max-width:200px;margin-bottom:6px;display:block;">
                    @endif
                    <div style="font-size: 14pt; font-weight: bold; color: #dc2626; line-height: 1.1;">
                        {{ $empresa->razon_social ?? 'EMPRESA' }}
                    </div>
                    <div style="font-size: 8pt; color: #555; margin-top: 5px; line-height: 1.6;">
                        {{ $empresa->direccion ?? '' }}<br>
                        @if($empresa->telefono ?? '')<span style="font-weight:bold;">TELEF.:</span> {{ $empresa->telefono }}<br>@endif
                        @if($empresa->email ?? '')<span style="font-weight:bold;">Correo:</span> {{ $empresa->email }}@endif
                    </div>
                </td>
                <td style="width: 37%; vertical-align: top; text-align: right; padding: 0;">
                    <div style="border: 2px solid #bfc4cc; border-radius: 10px; overflow: hidden; width: 240px; float: right;">
                        <div style="text-align: center; padding: 8px 10px; font-size: 12px; font-weight: bold; color: #000;">
                            R.U.C. {{ $empresa->ruc ?? '' }}
                        </div>
                        <div style="background: #bfc4cc; text-align: center; padding: 10px; font-size: 14px; font-weight: bold; color: #000;">
                            {{ $v->tipoDocumento?->tipo_doc ?? 'NOTA DE VENTA' }}
                        </div>
                        <div style="text-align: center; padding: 10px; font-size: 17px; font-weight: bold; color: #000;">
                            {{ $v->documento_completo }}
                        </div>
                    </div>
                </td>
            </tr>
        </table>

        <!-- Client Info (Cards) -->
        <table style="width: 100%; border-collapse: separate; border-spacing: 10px 0; margin-left: -10px; margin-bottom: 20px;">
            <tr>
                <!-- Tarjeta Izquierda: Datos del Cliente -->
                <td style="width: 48%; vertical-align: top; border: 1px solid #777; border-radius: 10px; padding: 10px;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="font-weight: bold; font-size: 8pt; padding-bottom: 4px; width: 25%; vertical-align: top; color: #000;">CLIENTE:</td>
                            <td style="font-size: 8pt; color: #000; padding-bottom: 4px; vertical-align: top;">{{ $v->cliente?->datos ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold; font-size: 8pt; padding-bottom: 4px; vertical-align: top; color: #000;">{{ strlen($v->cliente?->documento ?? '') == 11 ? 'RUC' : (strlen($v->cliente?->documento ?? '') == 8 ? 'DNI' : 'DOC') }}:</td>
                            <td style="font-size: 8pt; color: #000; padding-bottom: 4px; vertical-align: top;">{{ $v->cliente?->documento ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold; font-size: 8pt; padding-bottom: 4px; vertical-align: top; color: #000;">DIRECCIÓN:</td>
                            <td style="font-size: 8pt; color: #000; padding-bottom: 4px; vertical-align: top;">{{ $v->cliente?->direccion ?? $v->direccion ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold; font-size: 8pt; vertical-align: top; color: #000;">CELULAR:</td>
                            <td style="font-size: 8pt; color: #000; vertical-align: top;">{{ $v->cliente?->telefono ?? '-' }}</td>
                        </tr>
                    </table>
                </td>

                <!-- Tarjeta Derecha: Datos del Documento -->
                <td style="width: 48%; vertical-align: top; border: 1px solid #777; border-radius: 10px; padding: 10px;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="font-weight: bold; font-size: 8pt; padding-bottom: 4px; width: 45%; vertical-align: top; color: #000;">FECHA EMISIÓN:</td>
                            <td style="font-size: 8pt; color: #000; padding-bottom: 4px; vertical-align: top;">{{ $v->fecha_emision ? $v->fecha_emision->format('d/m/Y') : '-' }}</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold; font-size: 8pt; padding-bottom: 4px; vertical-align: top; color: #000;">MONEDA:</td>
                            <td style="font-size: 8pt; color: #000; padding-bottom: 4px; vertical-align: top;">SOLES</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold; font-size: 8pt; padding-bottom: 4px; vertical-align: top; color: #000;">FORMA DE PAGO:</td>
                            <td style="font-size: 8pt; color: #000; padding-bottom: 4px; vertical-align: top;">{{ $v->id_tipo_pago == 1 ? 'CONTADO' : 'CRÉDITO' }}</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold; font-size: 8pt; padding-bottom: 4px; vertical-align: top; color: #000;">VENDEDOR:</td>
                            <td style="font-size: 8pt; color: #000; padding-bottom: 4px; vertical-align: top;">{{ ($v->vendedor?->nombres ?? '') . ' ' . ($v->vendedor?->apellidos ?? '') }}</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold; font-size: 8pt; vertical-align: top; color: #000;">ESTADO:</td>
                            <td style="font-size: 8pt; color: #000; vertical-align: top;">
                                @if($v->estado == '0')
                                    <span class="badge-estado badge-anulada">ANULADA</span>
                                @else
                                    <span class="badge-estado badge-activa">ACTIVA</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <!-- Products Table -->
        @php
            $simbolo = 'S/';
        @endphp
        <table class="products-table">
            <thead>
                <tr>
                    <th width="4%">N°</th>
                    <th width="7%">CANT.</th>
                    <th width="8%">UNIDAD</th>
                    <th width="11%">CODIGO</th>
                    <th width="33%" style="text-align: left; padding-left: 5px;">DESCRIPCIÓN</th>
                    <th width="9%" style="text-align: right;">V.UNIT.</th>
                    <th width="9%" style="text-align: right;">P.UNIT.</th>
                    <th width="9%" style="text-align: right;">TOTAL</th>
                    @if($v->apli_igv == '1')<th width="6%">IGV</th>@endif
                </tr>
            </thead>
            <tbody>
                @forelse($v->productosVenta as $i => $p)
                @php
                    $desc = (!empty(trim($p->descripcion ?? '')))
                        ? $p->descripcion
                        : ($p->producto?->descripcion ?? 'Sin descripción');

                    $afectacion = match((int)($p->igv_prod ?? 0)) {
                        1 => 'EXO',
                        2 => 'INA',
                        default => 'GRA',
                    };
                @endphp
                <tr>
                    <td style="text-align: center;">{{ $i + 1 }}</td>
                    <td style="text-align: center; font-size: 8.5pt;">{{ number_format($p->cantidad, 3) }}</td>
                    <td style="text-align: center;">{{ $p->medida ?? 'UNIDAD' }}</td>
                    <td style="text-align: center;">{{ $p->producto?->codigo ?? '-' }}</td>
                    <td style="padding-left: 5px;">{{ $desc }}</td>
                    <td style="text-align: right;">{{ number_format($p->precio, 2) }}</td>
                    <td style="text-align: right;">{{ number_format($p->precio, 2) }}</td>
                    <td style="text-align: right;">{{ number_format($p->total ?? ($p->cantidad * $p->precio), 2) }}</td>
                    @if($v->apli_igv == '1')
                    <td style="text-align: center;">{{ $afectacion }}</td>
                    @endif
                </tr>
                @empty
                <tr>
                    <td colspan="@if($v->apli_igv == '1')9 @else 8 @endif" style="text-align: center; padding: 15px; color: #999;">Sin productos</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @php
            $subtotal = $v->subtotal ?? round($v->total / 1.18, 2);
            $igvMonto = round($v->total - $subtotal, 2);
            $pagado   = $v->pagos?->where('estado','1')->sum('monto') ?? 0;
            $saldo    = round($v->total - $pagado, 2);
            $enLetras = strtoupper(num2letras($v->total ?? 0));
        @endphp

        <!-- Total Letters -->
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 5px; border: 2px solid #999; border-radius: 6px;">
            <tr>
                <td style="padding: 6px 10px; font-size: 10pt; font-weight: bold; font-style: italic; text-align: center; text-transform: uppercase;">
                    SON: {{ $enLetras }} CON 00/100 SOLES
                </td>
            </tr>
        </table>

        <!-- Observaciones -->
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 5px; border: 2px solid #999; border-radius: 6px;">
            <tr>
                <td style="width: 15%; padding: 6px 10px; font-weight: bold; font-size: 8pt; vertical-align: top;">OBSERVACIONES:</td>
                <td style="width: 85%; padding: 6px 10px; font-size: 8pt; vertical-align: top;">{{ $v->observacion ?? '-' }}</td>
            </tr>
        </table>

        <!-- Bottom Section: Info and Totals -->
        <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
            <tr>
                <!-- Left side: Info -->
                <td style="width: 55%; vertical-align: top; padding-right: 10px;">
                    <div style="font-size: 8pt; font-weight: bold; margin-bottom: 5px;">
                        SALDO PENDIENTE: {{ $simbolo }} {{ number_format($saldo, 2) }}
                    </div>
                </td>

                <!-- Right side: Totals -->
                <td style="width: 45%; vertical-align: top;">
                    <!-- Caja Superior: Desglose -->
                    <table style="width: 100%; border-collapse: separate; border-spacing: 0; border: 2px solid #999; border-radius: 6px; margin-bottom: 5px;">
                        <tr>
                            <td style="padding: 3px 10px 1px 10px; text-align: right; font-size: 8pt; width: 65%;">OP. INAFECTAS: {{ $simbolo }}</td>
                            <td style="padding: 3px 10px 1px 10px; text-align: right; font-size: 8pt; width: 35%;">0.00</td>
                        </tr>
                        <tr>
                            <td style="padding: 1px 10px; text-align: right; font-size: 8pt;">OP. GRAVADAS: {{ $simbolo }}</td>
                            <td style="padding: 1px 10px; text-align: right; font-size: 8pt;">{{ number_format($subtotal, 2) }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 1px 10px 3px 10px; text-align: right; font-size: 8pt;">I.G.V. 18.0%: {{ $simbolo }}</td>
                            <td style="padding: 1px 10px 3px 10px; text-align: right; font-size: 8pt;">{{ number_format($igvMonto, 2) }}</td>
                        </tr>
                    </table>

                    <!-- Caja Inferior: Total -->
                    <table style="width: 100%; border-collapse: separate; border-spacing: 0; border: 2px solid #999; border-radius: 6px; background-color: #bfc4cc;">
                        <tr>
                            <td style="padding: 6px 10px; text-align: right; font-size: 13pt; font-weight: bold; width: 65%;">TOTAL A PAGAR: {{ $simbolo }}</td>
                            <td style="padding: 6px 10px; text-align: right; font-size: 13pt; font-weight: bold; width: 35%; color: #000;">{{ number_format($v->total, 2) }}</td>
                        </tr>
                    </table>
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