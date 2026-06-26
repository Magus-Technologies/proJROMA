<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $nota->tipo === 'credito' ? 'NOTA DE CRÉDITO' : 'NOTA DE DÉBITO' }} {{ $nota->serie }}-{{ str_pad($nota->numero, 8, '0', STR_PAD_LEFT) }}</title>
    <style>
        @page { margin: 50px 40px 50px 40px; }
        body { font-family: 'Arial', sans-serif; font-size: 9pt; color: #333; margin: 0; padding: 0; }
        p, div, span, table, td, th, tr { margin: 0; padding: 0; }
        .products-table { width: 100%; border-collapse: collapse; margin-bottom: 5px; border: 2px solid #999; }
        .products-table thead { background: #bfc4cc; }
        .products-table th { padding: 6px 4px; font-size: 7.5pt; font-weight: bold; border: 1px solid #999; text-align: center; }
        .products-table td { padding: 3px 4px; font-size: 8pt; border: none; vertical-align: top; }
        .products-table tbody tr:nth-child(even) td { background: #f1f3f5; }
        .footer { text-align: center; margin-top: 30px; padding-top: 15px; border-top: 1px solid #ddd; font-size: 9pt; color: #666; }
        .badge { display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 8pt; font-weight: bold; }
        .badge-credito { background: #dbeafe; color: #1e40af; }
        .badge-debito  { background: #fed7aa; color: #9a3412; }
    </style>
</head>
<body>
<div>
    {{-- Header --}}
    <table style="width:100%;margin-bottom:20px;border-collapse:collapse;">
        <tr>
            <td style="width:63%;vertical-align:top;text-align:left;padding-right:15px;">
                @if(!empty($logoBase64))
                <img src="{{ $logoBase64 }}" style="max-height:65px;max-width:200px;margin-bottom:6px;display:block;">
                @endif
                <div style="font-size:14pt;font-weight:bold;color:#dc2626;line-height:1.1;">
                    {{ $empresa->razon_social ?? 'EMPRESA' }}
                </div>
                <div style="font-size:8pt;color:#555;margin-top:5px;line-height:1.6;">
                    {{ $empresa->direccion ?? '' }}<br>
                    @if($empresa->telefono ?? '')<span style="font-weight:bold;">TELEF.:</span> {{ $empresa->telefono }}<br>@endif
                    @if($empresa->email ?? '')<span style="font-weight:bold;">Correo:</span> {{ $empresa->email }}@endif
                </div>
            </td>
            <td style="width:37%;vertical-align:top;text-align:right;padding:0;">
                <div style="border:2px solid #bfc4cc;border-radius:10px;overflow:hidden;width:240px;float:right;">
                    <div style="text-align:center;padding:8px 10px;font-size:11px;font-weight:bold;color:#000;">
                        R.U.C. {{ $empresa->ruc ?? '' }}
                    </div>
                    <div style="background:#bfc4cc;text-align:center;padding:10px;font-size:13px;font-weight:bold;color:#000;">
                        {{ $nota->tipo === 'credito' ? 'NOTA DE CRÉDITO' : 'NOTA DE DÉBITO' }}
                    </div>
                    <div style="text-align:center;padding:10px;font-size:16px;font-weight:bold;color:#000;">
                        {{ $nota->serie }}-{{ str_pad($nota->numero, 8, '0', STR_PAD_LEFT) }}
                    </div>
                </div>
            </td>
        </tr>
    </table>

    {{-- Datos del documento y cliente --}}
    <table style="width:100%;border-collapse:separate;border-spacing:10px 0;margin-left:-10px;margin-bottom:20px;">
        <tr>
            <td style="width:48%;vertical-align:top;border:1px solid #777;border-radius:10px;padding:10px;">
                <table style="width:100%;border-collapse:collapse;">
                    <tr>
                        <td style="font-weight:bold;font-size:8pt;padding-bottom:4px;width:35%;color:#000;">CLIENTE:</td>
                        <td style="font-size:8pt;color:#000;padding-bottom:4px;">{{ $nota->venta?->cliente?->datos ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight:bold;font-size:8pt;padding-bottom:4px;color:#000;">{{ strlen($nota->venta?->cliente?->documento ?? '') == 11 ? 'RUC' : 'DNI' }}:</td>
                        <td style="font-size:8pt;color:#000;padding-bottom:4px;">{{ $nota->venta?->cliente?->documento ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight:bold;font-size:8pt;color:#000;">DIRECCIÓN:</td>
                        <td style="font-size:8pt;color:#000;">{{ $nota->venta?->cliente?->direccion ?? '-' }}</td>
                    </tr>
                </table>
            </td>
            <td style="width:48%;vertical-align:top;border:1px solid #777;border-radius:10px;padding:10px;">
                <table style="width:100%;border-collapse:collapse;">
                    <tr>
                        <td style="font-weight:bold;font-size:8pt;padding-bottom:4px;width:55%;color:#000;">FECHA EMISIÓN:</td>
                        <td style="font-size:8pt;color:#000;padding-bottom:4px;">{{ $nota->fecha_emision?->format('d/m/Y') ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight:bold;font-size:8pt;padding-bottom:4px;color:#000;">COMP. AFECTADO:</td>
                        <td style="font-size:8pt;color:#000;padding-bottom:4px;">{{ $nota->venta?->documento_completo ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight:bold;font-size:8pt;color:#000;">MOTIVO:</td>
                        <td style="font-size:8pt;color:#000;">{{ $nota->cod_motivo }} — {{ $nota->motivo }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- Productos --}}
    <table class="products-table">
        <thead>
            <tr>
                <th width="5%">N°</th>
                <th width="9%">CANT.</th>
                <th width="9%">UNIDAD</th>
                <th width="36%" style="text-align:left;padding-left:5px;">DESCRIPCIÓN</th>
                <th width="12%" style="text-align:right;">V.UNIT.</th>
                <th width="12%" style="text-align:right;">P.UNIT.</th>
                <th width="12%" style="text-align:right;">TOTAL</th>
            </tr>
        </thead>
        <tbody>
            @forelse($nota->venta?->productosVenta ?? [] as $i => $p)
            @php
                $desc = (!empty(trim($p->descripcion ?? ''))) ? $p->descripcion : ($p->producto?->descripcion ?? '-');
            @endphp
            <tr>
                <td style="text-align:center;">{{ $i + 1 }}</td>
                <td style="text-align:center;">{{ number_format($p->cantidad, 3) }}</td>
                <td style="text-align:center;">{{ $p->medida ?? 'UNIDAD' }}</td>
                <td style="padding-left:5px;">{{ $desc }}</td>
                <td style="text-align:right;">{{ number_format($p->precio, 2) }}</td>
                <td style="text-align:right;">{{ number_format($p->precio, 2) }}</td>
                <td style="text-align:right;">{{ number_format($p->cantidad * $p->precio, 2) }}</td>
            </tr>
            @empty
            <tr><td colspan="7" style="text-align:center;padding:15px;color:#999;">Sin ítems</td></tr>
            @endforelse
        </tbody>
    </table>

    {{-- Total --}}
    <table style="width:100%;border-collapse:collapse;margin-top:10px;">
        <tr>
            <td style="width:55%;vertical-align:top;padding-right:10px;">
                @if($nota->hash)
                <div style="font-size:7pt;color:#666;margin-top:5px;">Hash: {{ $nota->hash }}</div>
                @endif
            </td>
            <td style="width:45%;vertical-align:top;">
                <table style="width:100%;border-collapse:separate;border-spacing:0;border:2px solid #999;border-radius:6px;background:#bfc4cc;">
                    <tr>
                        <td style="padding:6px 10px;text-align:right;font-size:13pt;font-weight:bold;width:60%;">TOTAL: S/</td>
                        <td style="padding:6px 10px;text-align:right;font-size:13pt;font-weight:bold;width:40%;color:#000;">{{ number_format($nota->total, 2) }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="footer">
        <p>{{ $empresa->razon_social ?? '' }} | RUC: {{ $empresa->ruc ?? '' }} | {{ $nota->tipo === 'credito' ? 'Nota de Crédito' : 'Nota de Débito' }} Electrónica</p>
    </div>
</div>
</body>
</html>
