<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Cotización {{ $documentoCompleto }}</title>
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

        .cuotas-table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 8pt; }
        .cuotas-table th { background: #bfc4cc; color: #000; padding: 5px 8px; text-align: left; border: 1px solid #ddd; }
        .cuotas-table td { padding: 5px 8px; border: 1px solid #ddd; }

        .footer { text-align: center; margin-top: 30px; padding-top: 15px; border-top: 1px solid #ddd; font-size: 9pt; color: #666; }

        .badge-estado { display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 8pt; font-weight: bold; }
        .badge-anulada { background: #fee2e2; color: #991b1b; }
        .badge-activa  { background: #d1fae5; color: #065f46; }
        .badge-facturada { background: #dbeafe; color: #1e40af; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <table style="width: 100%; margin-bottom: 20px; border-collapse: collapse;">
            <tr>
                <td style="width: 63%; vertical-align: top; text-align: left; padding-right: 15px;">
                    <div style="font-size: 15pt; font-weight: bold; color: #dc2626; line-height: 1.1; margin-top: 5px;">
                        {{ $empresa->razon_social ?? 'EMPRESA' }}
                    </div>
                    <div style="font-weight: bold; font-size: 9pt; color: #000; margin-bottom: 3px; text-transform: uppercase; margin-top: 8px;">
                        {{ $empresa->razon_social ?? 'EMPRESA' }}
                    </div>
                    <div style="font-size: 8pt; color: #000; margin-bottom: 2px; font-weight: bold;">
                        {{ $empresa->direccion ?? '' }}
                    </div>
                    <div style="font-size: 8pt; color: #000; margin-bottom: 2px;">
                        <span style="font-weight: bold;">TELEF.:</span> {{ $empresa->telefono ?? '' }}
                    </div>
                    <div style="font-size: 8pt; color: #000;">
                        <span style="font-weight: bold;">Correo:</span> {{ $empresa->email ?? '' }}
                    </div>
                </td>
                <td style="width: 37%; vertical-align: top; text-align: right; padding: 0;">
                    <div style="border: 2px solid #bfc4cc; border-radius: 10px; overflow: hidden; width: 240px; float: right;">
                        <div style="text-align: center; padding: 8px 10px; font-size: 12px; font-weight: bold; color: #000;">
                            R.U.C. {{ $empresa->ruc ?? '' }}
                        </div>
                        <div style="background: #bfc4cc; text-align: center; padding: 10px; font-size: 14px; font-weight: bold; color: #000;">
                            COTIZACIÓN
                        </div>
                        <div style="text-align: center; padding: 10px; font-size: 17px; font-weight: bold; color: #000;">
                            {{ $documentoCompleto }}
                        </div>
                    </div>
                </td>
            </tr>
        </table>

        <!-- Company Details -->
        <table style="width: 100%; border-collapse: collapse; margin-top: -5px; margin-bottom: 10px;">
            <tr>
                <td style="text-align: left; padding: 0;">
                    <div style="font-weight: bold; font-size: 9pt; color: #000; margin-bottom: 3px; text-transform: uppercase;">
                        {{ $empresa->razon_social ?? 'EMPRESA' }}
                    </div>
                    <div style="font-size: 8pt; color: #000; margin-bottom: 2px; font-weight: bold;">
                        {{ $empresa->direccion ?? '' }}
                    </div>
                    <div style="font-size: 8pt; color: #000; margin-bottom: 2px;">
                        <span style="font-weight: bold;">TELEF.:</span> {{ $empresa->telefono ?? '' }}
                    </div>
                    <div style="font-size: 8pt; color: #000;">
                        <span style="font-weight: bold;">Correo:</span> {{ $empresa->email ?? '' }}
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
                            <td style="font-size: 8pt; color: #000; padding-bottom: 4px; vertical-align: top;">{{ $coti->cliente?->datos ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold; font-size: 8pt; padding-bottom: 4px; vertical-align: top; color: #000;">{{ strlen($coti->cliente?->documento ?? '') == 11 ? 'RUC' : (strlen($coti->cliente?->documento ?? '') == 8 ? 'DNI' : 'DOC') }}:</td>
                            <td style="font-size: 8pt; color: #000; padding-bottom: 4px; vertical-align: top;">{{ $coti->cliente?->documento ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold; font-size: 8pt; padding-bottom: 4px; vertical-align: top; color: #000;">DIRECCIÓN:</td>
                            <td style="font-size: 8pt; color: #000; padding-bottom: 4px; vertical-align: top;">{{ $coti->cliente?->direccion ?? $coti->direccion ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold; font-size: 8pt; vertical-align: top; color: #000;">CELULAR:</td>
                            <td style="font-size: 8pt; color: #000; vertical-align: top;">{{ $coti->cliente?->telefono ?? '-' }}</td>
                        </tr>
                    </table>
                </td>

                <!-- Tarjeta Derecha: Datos del Documento -->
                <td style="width: 48%; vertical-align: top; border: 1px solid #777; border-radius: 10px; padding: 10px;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="font-weight: bold; font-size: 8pt; padding-bottom: 4px; width: 45%; vertical-align: top; color: #000;">FECHA EMISIÓN:</td>
                            <td style="font-size: 8pt; color: #000; padding-bottom: 4px; vertical-align: top;">{{ \Carbon\Carbon::parse($coti->fecha)->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold; font-size: 8pt; padding-bottom: 4px; vertical-align: top; color: #000;">MONEDA:</td>
                            <td style="font-size: 8pt; color: #000; padding-bottom: 4px; vertical-align: top;">SOLES</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold; font-size: 8pt; padding-bottom: 4px; vertical-align: top; color: #000;">FORMA DE PAGO:</td>
                            <td style="font-size: 8pt; color: #000; padding-bottom: 4px; vertical-align: top;">{{ $coti->id_tipo_pago == 1 ? 'CONTADO' : 'CRÉDITO' }}</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold; font-size: 8pt; padding-bottom: 4px; vertical-align: top; color: #000;">USUARIO:</td>
                            <td style="font-size: 8pt; color: #000; padding-bottom: 4px; vertical-align: top;">{{ ($coti->usuario?->nombres ?? '') . ' ' . ($coti->usuario?->apellidos ?? '') }}</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold; font-size: 8pt; vertical-align: top; color: #000;">ESTADO:</td>
                            <td style="font-size: 8pt; color: #000; vertical-align: top;">
                                @if($coti->estado === '0')
                                    <span class="badge-estado badge-anulada">ANULADA</span>
                                @elseif($coti->estado === '3')
                                    <span class="badge-estado badge-facturada">FACTURADA</span>
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
        <table class="products-table">
            <thead>
                <tr>
                    <th width="4%">N°</th>
                    <th width="8%">CANT.</th>
                    <th width="8%">UNIDAD</th>
                    <th width="12%">CODIGO</th>
                    <th width="38%" style="text-align: left; padding-left: 5px;">DESCRIPCIÓN</th>
                    <th width="10%" style="text-align: right;">V.UNIT.</th>
                    <th width="10%" style="text-align: right;">TOTAL</th>
                </tr>
            </thead>
            <tbody>
                @forelse($coti->productos as $i => $p)
                @php
                    $desc = $p->producto?->descripcion ?? 'Sin descripción';
                    $cant = $p->cantidad;
                    $precio = $p->precio;
                    $totalProd = $cant * $precio;
                @endphp
                <tr>
                    <td style="text-align: center;">{{ $i + 1 }}</td>
                    <td style="text-align: center; font-size: 8.5pt;">{{ number_format($cant, 3) }}</td>
                    <td style="text-align: center;">{{ $p->medida ?? 'UNIDAD' }}</td>
                    <td style="text-align: center;">{{ $p->producto?->codigo ?? '-' }}</td>
                    <td style="padding-left: 5px;">{{ $desc }}</td>
                    <td style="text-align: right;">{{ number_format($precio, 2) }}</td>
                    <td style="text-align: right;">{{ number_format($totalProd, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 15px; color: #999;">Sin productos</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @php
            $total    = $coti->total ?? 0;
            $subtotal = round($total / 1.18, 2);
            $igv      = round($total - $subtotal, 2);
            $enLetras = strtoupper(num2letras($total));
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
                <td style="width: 85%; padding: 6px 10px; font-size: 8pt; vertical-align: top;">{{ $coti->observacion ?? '-' }}</td>
            </tr>
        </table>

        <!-- Bottom Section: Info and Totals -->
        <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
            <tr>
                <!-- Left side: Info -->
                <td style="width: 55%; vertical-align: top; padding-right: 10px;">
                    @if($coti->estado === '0')
                        <span class="badge-estado badge-anulada" style="margin-bottom: 8px;">ANULADA</span><br>
                    @elseif($coti->estado === '3')
                        <span class="badge-estado badge-facturada" style="margin-bottom: 8px;">FACTURADA</span><br>
                    @else
                        <span class="badge-estado badge-activa" style="margin-bottom: 8px;">ACTIVA</span><br>
                    @endif
                </td>

                <!-- Right side: Totals -->
                <td style="width: 45%; vertical-align: top;">
                    <!-- Caja Superior: Desglose -->
                    <table style="width: 100%; border-collapse: separate; border-spacing: 0; border: 2px solid #999; border-radius: 6px; margin-bottom: 5px;">
                        <tr>
                            <td style="padding: 3px 10px 1px 10px; text-align: right; font-size: 8pt; width: 65%;">OP. GRAVADAS: S/</td>
                            <td style="padding: 3px 10px 1px 10px; text-align: right; font-size: 8pt; width: 35%;">{{ number_format($subtotal, 2) }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 1px 10px 3px 10px; text-align: right; font-size: 8pt;">I.G.V. 18.0%: S/</td>
                            <td style="padding: 1px 10px 3px 10px; text-align: right; font-size: 8pt;">{{ number_format($igv, 2) }}</td>
                        </tr>
                    </table>

                    <!-- Caja Inferior: Total -->
                    <table style="width: 100%; border-collapse: separate; border-spacing: 0; border: 2px solid #999; border-radius: 6px; background-color: #bfc4cc;">
                        <tr>
                            <td style="padding: 6px 10px; text-align: right; font-size: 13pt; font-weight: bold; width: 65%;">TOTAL: S/</td>
                            <td style="padding: 6px 10px; text-align: right; font-size: 13pt; font-weight: bold; width: 35%; color: #000;">{{ number_format($total, 2) }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <!-- Footer -->
        <div class="footer">
            <p>Esta cotización es válida por 30 días a partir de la fecha de emisión.</p>
            <p style="margin-top: 4px;">{{ $empresa->razon_social ?? '' }} | RUC: {{ $empresa->ruc ?? '' }}</p>
        </div>
    </div>
</body>
</html>