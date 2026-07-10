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
                    @if(!empty($empresa->propaganda))
                    <div style="font-size: 8pt; color: #666; font-style: italic; margin-top: 2px;">
                        {{ $empresa->propaganda }}
                    </div>
                    @endif
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

        {{-- Cuotas del crédito: SUNAT exige detallarlas en el comprobante --}}
        @if ((int) ($v->id_tipo_pago ?? 1) === 2 && ($v->pagos?->isNotEmpty() ?? false))
            <table class="products-table" style="margin-bottom: 5px;">
                <thead>
                    <tr>
                        <th colspan="4" style="text-align:left; padding:5px 8px;">
                            FORMA DE PAGO: CRÉDITO — {{ $v->pagos->count() }} CUOTA{{ $v->pagos->count() > 1 ? 'S' : '' }}
                        </th>
                    </tr>
                    <tr>
                        <th style="width:12%;">CUOTA</th>
                        <th style="width:28%;">VENCIMIENTO</th>
                        <th style="width:30%;">MEDIO DE PAGO</th>
                        <th style="width:30%;">IMPORTE</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($v->pagos as $i => $cuota)
                        <tr>
                            <td style="text-align:center;">{{ str_pad($i + 1, 3, '0', STR_PAD_LEFT) }}</td>
                            <td style="text-align:center;">{{ optional($cuota->fecha)->format('d/m/Y') ?? '—' }}</td>
                            <td style="text-align:center;">
                                {{ ucfirst(strtolower($cuota->tipo_pago ?? 'Efectivo')) }}
                                @if ($cuota->estado === '1')
                                    <span style="color:#065f46; font-weight:bold;">· PAGADA</span>
                                @endif
                            </td>
                            <td style="text-align:right; padding-right:8px;">S/ {{ number_format((float) $cuota->monto, 2) }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td colspan="3" style="text-align:right; font-weight:bold; padding-right:8px;">SALDO PENDIENTE</td>
                        <td style="text-align:right; font-weight:bold; padding-right:8px;">S/ {{ number_format($saldo, 2) }}</td>
                    </tr>
                </tbody>
            </table>
        @endif

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

        {{-- QR de SUNAT: solo existe si el XML ya fue firmado (necesita el hash) --}}
        @if (!empty($qr))
            <table style="width:100%; border-collapse:collapse; margin-top:10px;">
                <tr>
                    <td style="width:110px; vertical-align:top;">
                        <img src="{{ $qr }}" style="width:100px; height:100px;" alt="Código QR SUNAT">
                    </td>
                    <td style="vertical-align:middle; font-size:7.5pt; color:#555; padding-left:8px;">
                        Representación impresa del comprobante electrónico.<br>
                        Escaneá el código para validar sus datos ante SUNAT.<br>
                        <span style="font-size:6.5pt; word-break:break-all;">
                            Autorizado mediante resolución de SUNAT.
                        </span>
                    </td>
                </tr>
            </table>
        @endif

        <!-- Footer -->
        <div class="footer">
            <p>{{ $empresa->razon_social ?? '' }} | RUC: {{ $empresa->ruc ?? '' }}</p>
            <p style="margin-top:4px">
                Consulte su comprobante en: <strong>{{ url('/consulta') }}</strong>
            </p>
        </div>
    </div>
