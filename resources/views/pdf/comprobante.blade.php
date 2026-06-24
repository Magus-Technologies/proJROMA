<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<style>
    * { margin:0; padding:0; box-sizing:border-box; }
    body {
        font-family: Arial, Helvetica, sans-serif;
        font-size: 10px;
        color: #111;
        width: 100%;
    }
    .tbl-header {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 8px;
        padding-bottom: 8px;
        border-bottom: 2px solid #1a1a6e;
    }
    .empresa-nom {
        font-size: 12px;
        font-weight: bold;
        text-transform: uppercase;
        color: #1a1a6e;
        line-height: 1.4;
    }
    .empresa-info { font-size: 8.5px; color: #333; margin-top: 3px; line-height: 1.6; }
    .doc-box { border: 2px solid #1a1a6e; text-align: center; padding: 8px 10px; width: 170px; }
    .doc-ruc  { font-size: 9px; font-weight: bold; }
    .doc-tipo { font-size: 12px; font-weight: bold; text-transform: uppercase; color: #1a1a6e; margin: 5px 0; }
    .doc-serie{ font-size: 12px; font-weight: bold; border-top: 1px solid #1a1a6e; padding-top: 5px; margin-top: 5px; }
    .tbl-cliente { width: 100%; border-collapse: collapse; border: 1px solid #aaa; margin-bottom: 8px; }
    .tbl-cliente td { padding: 3px 6px; font-size: 9px; border-bottom: 1px solid #eee; }
    .lbl { font-weight: bold; background: #f5f5f5; width: 90px; }
    .tbl-prods { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
    .tbl-prods th { background: #1a1a6e; color: white; padding: 5px 4px; font-size: 9px; text-align: left; border: 1px solid #1a1a6e; }
    .tbl-prods td { padding: 4px; border: 1px solid #ddd; font-size: 9px; vertical-align: top; }
    .tbl-prods tr:nth-child(even) td { background: #f9f9f9; }
    .tr { text-align: right; }
    .tc { text-align: center; }
    .tbl-footer { width: 100%; border-collapse: collapse; margin-top: 10px; }
    .letras-cell { vertical-align: top; padding-right: 10px; font-size: 9px; }
    .tbl-totales { width: 235px; border-collapse: collapse; }
    .tbl-totales td { padding: 3px 7px; font-size: 9.5px; border: 1px solid #ccc; }
    .tlbl { font-weight: bold; background: #f0f0f0; }
    .tfin td { background: #1a1a6e !important; color: white !important; font-weight: bold; font-size: 11px; }
    .badge-act { background: #16a34a; color: white; padding: 2px 10px; font-size: 9px; font-weight: bold; }
    .badge-anu { background: #dc2626; color: white; padding: 2px 10px; font-size: 9px; font-weight: bold; }
    .footer { margin-top: 20px; text-align: center; font-size: 7.5px; color: #999; border-top: 1px solid #ddd; padding-top: 6px; }
</style>
</head>
<body>

<table class="tbl-header">
    <tr>
        <td style="vertical-align:top; width:63%; padding-bottom:8px">
            <div class="empresa-nom">{{ $empresa?->razon_social ?? 'EMPRESA' }}</div>
            <div class="empresa-info">
                RUC: {{ $empresa?->ruc ?? '-' }}<br>
                {{ $empresa?->direccion ?? '' }}<br>
                @if($empresa?->telefono)Central Telefónica: {{ $empresa->telefono }}<br>@endif
                @if($empresa?->email)Email: {{ $empresa->email }}@endif
            </div>
        </td>
        <td style="vertical-align:top; text-align:right; width:37%; padding-bottom:8px">
            <div class="doc-box">
                <div class="doc-ruc">RUC: {{ $empresa?->ruc ?? '-' }}</div>
                <div class="doc-tipo">{{ $v->tipoDocumento?->tipo_doc ?? 'NOTA DE VENTA' }}</div>
                <div class="doc-serie">{{ $v->documento_completo }}</div>
            </div>
        </td>
    </tr>
</table>

<table class="tbl-cliente">
    <tr>
        <td class="lbl">CLIENTE:</td>
        <td style="width:38%"><strong>{{ $v->cliente?->datos ?? '-' }}</strong></td>
        <td class="lbl" style="width:85px">CELULAR:</td>
        <td>{{ $v->cliente?->telefono ?? '-' }}</td>
    </tr>
    <tr>
        <td class="lbl">DIRECCIÓN:</td>
        <td>{{ $v->cliente?->direccion ?? $v->direccion ?? '-' }}</td>
        <td class="lbl">VENDEDOR:</td>
        <td>{{ ($v->vendedor?->nombres ?? '') . ' ' . ($v->vendedor?->apellidos ?? '') }}</td>
    </tr>
    <tr>
        <td class="lbl">RUC/DNI:</td>
        <td>{{ $v->cliente?->documento ?? '-' }}</td>
        <td class="lbl">FECHA:</td>
        <td>{{ strtoupper($v->fecha_emision?->translatedFormat('d \d\e F \d\e\l, Y') ?? '-') }}</td>
    </tr>
    <tr>
        <td class="lbl">MONEDA:</td>
        <td>SOLES</td>
        <td class="lbl">PAGO:</td>
        <td>{{ $v->id_tipo_pago == 1 ? 'Contado' : 'Crédito' }}</td>
    </tr>
    @if($v->observacion)
    <tr>
        <td class="lbl">OBSERVACIÓN:</td>
        <td colspan="3">{{ $v->observacion }}</td>
    </tr>
    @endif
</table>

<table class="tbl-prods">
    <thead>
        <tr>
            <th class="tc" style="width:5%">ITEM</th>
            <th class="tc" style="width:9%">CANTIDAD</th>
            <th style="width:52%">DESCRIPCION</th>
            <th class="tr" style="width:12%">PRECIO</th>
            <th class="tr" style="width:13%">SUB TOTAL</th>
            @if($v->apli_igv == '1')<th class="tc" style="width:7%">P.IGV</th>@endif
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
            <td class="tc">{{ $i + 1 }}</td>
            <td class="tc">{{ number_format($p->cantidad, 0) }}</td>
            <td>
                <strong>{{ $desc }}</strong>
                @if($p->producto?->codigo)
                    <br><span style="color:#777;font-size:8px">Cód: {{ $p->producto->codigo }}</span>
                @endif
            </td>
            <td class="tr">{{ number_format($p->precio, 2) }}</td>
            <td class="tr">{{ number_format($p->total, 2) }}</td>
            @if($v->apli_igv == '1')
            <td class="tc">{{ $afectacion }}</td>
            @endif
        </tr>
        @empty
        <tr>
            <td colspan="6" style="text-align:center;padding:15px;color:#999">Sin productos</td>
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

<table class="tbl-footer">
    <tr>
        <td class="letras-cell">
            <div style="margin-bottom:5px">
                <strong>SON: | {{ $enLetras }} SOLES</strong>
            </div>
            @if($v->observacion)
                <div style="font-size:8.5px;margin-bottom:5px">Observaciones: {{ $v->observacion }}</div>
            @endif
            <div style="margin-bottom:8px">
                Saldo Pendiente: <strong>{{ number_format($saldo, 2) }}</strong>
            </div>
            @if($v->estado == '0')
                <span class="badge-anu">ANULADA</span>
            @else
                <span class="badge-act">ACTIVA</span>
            @endif
        </td>
        <td style="vertical-align:top; text-align:right">
            <table class="tbl-totales">
                <tr><td class="tlbl">Op. Inafectas:</td><td class="tr">S/ 0.00</td></tr>
                <tr><td class="tlbl">Op. Gravadas:</td><td class="tr">S/ {{ number_format($subtotal, 2) }}</td></tr>
                <tr><td class="tlbl">IGV (18%):</td><td class="tr">S/ {{ number_format($igvMonto, 2) }}</td></tr>
                <tr class="tfin">
                    <td>Total a Pagar:</td>
                    <td class="tr">S/ {{ number_format($v->total, 2) }}</td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<div class="footer">
    Generado por TitanicSAC — titanicsac.com | {{ now()->format('d/m/Y H:i:s') }}
</div>

</body>
</html>
