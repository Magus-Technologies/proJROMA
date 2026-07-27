<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<style>
    * { margin:0; padding:0; box-sizing:border-box; }
    body { font-family: 'Courier New', monospace; font-size: 9px; width: 226px; color:#000; }
    .center { text-align:center; }
    .bold   { font-weight:bold; }
    .line   { border-top:1px dashed #000; margin:4px 0; }
    .empresa-nom { font-size:11px; font-weight:bold; text-align:center; text-transform:uppercase; }
    .doc-tipo    { font-size:10px; font-weight:bold; text-align:center; margin:3px 0; }
    .doc-serie   { font-size:10px; font-weight:bold; text-align:center; }
    table.prods  { width:100%; border-collapse:collapse; margin:4px 0; }
    table.prods th { font-size:8px; border-bottom:1px solid #000; text-align:left; padding:1px 0; }
    table.prods td { font-size:8px; padding:1px 0; vertical-align:top; }
    .td-r { text-align:right; }
    .td-c { text-align:center; }
    /* dompdf NO soporta flexbox: las líneas de totales se alinean con tablas. */
    table.tot   { width:100%; border-collapse:collapse; }
    table.tot td { padding:1px 0; }
    .sub-line td   { font-size:8px; }
    .total-line td { font-size:11px; font-weight:bold; padding:2px 0; }
    .td-right { text-align:right; }
    .gracias    { text-align:center; font-size:8px; margin-top:6px; }
    .qr-box  { text-align:center; margin-top:6px; }
    .qr-box img { width:120px; height:120px; }
    .qr-cap  { font-size:7px; margin-top:2px; }
</style>
</head>
<body>

@if(!empty($logoBase64))
<div style="text-align:center;margin-bottom:4px"><img src="{{ $logoBase64 }}" style="max-height:40px;max-width:160px;"></div>
@endif
<div class="empresa-nom">{{ $empresa?->razon_social ?? 'EMPRESA' }}</div>
@if(!empty($empresa?->propaganda))
<div class="center" style="font-size:8px;font-style:italic">{{ $empresa->propaganda }}</div>
@endif
<div class="center" style="font-size:8px">RUC: {{ $empresa?->ruc ?? '-' }}</div>
<div class="center" style="font-size:8px">{{ $empresa?->direccion ?? '' }}</div>
<div class="center" style="font-size:8px">Telf: {{ $empresa?->telefono ?? '' }}</div>

<div class="line"></div>

<div class="doc-tipo">{{ $v->tipoDocSunat?->nombre ?? 'NOTA DE VENTA' }}</div>
<div class="doc-serie">{{ $v->documento_completo }}</div>

<div class="line"></div>

<div style="font-size:8px">
    <div><span class="bold">Cliente: </span>{{ $v->cliente?->datos ?? '-' }}</div>
    <div><span class="bold">RUC/DNI: </span>{{ $v->cliente?->documento ?? '-' }}</div>
    <div><span class="bold">Fecha:   </span>{{ $v->fecha_emision?->format('d/m/Y') }}</div>
    <div><span class="bold">Pago:    </span>{{ $v->id_tipo_pago == 1 ? 'Contado' : 'Crédito' }}</div>
</div>

<div class="line"></div>

<table class="prods">
    <thead>
        <tr>
            <th style="width:50%">DESCRIPCION</th>
            <th class="td-c" style="width:15%">CANT</th>
            <th class="td-r" style="width:15%">P.U.</th>
            <th class="td-r" style="width:20%">TOTAL</th>
        </tr>
    </thead>
    <tbody>
        @foreach($v->productosVenta as $p)
        <tr>
            <td>{{ Str::limit($p->descripcion, 20) }}</td>
            <td class="td-c">{{ number_format($p->cantidad, 0) }}</td>
            <td class="td-r">{{ number_format($p->precio, 2) }}</td>
            <td class="td-r">{{ number_format($p->total, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="line"></div>

@php
    // Respeta la afectación del comprobante: gravado descompone el 18%;
    // exonerado / inafecto no llevan IGV (base = total).
    $esGravado = ($v->tipo_igv ?? 'gravado') === 'gravado';
    $subtotal  = $esGravado ? ($v->subtotal ?? round($v->total / 1.18, 2)) : $v->total;
    $igvM      = $esGravado ? round($v->total - $subtotal, 2) : 0;
    $etiquetaBase = $esGravado ? 'Op. Gravadas:' : (($v->tipo_igv ?? '') === 'exonerado' ? 'Op. Exoneradas:' : 'Op. Inafectas:');
@endphp

<table class="tot">
    <tr class="sub-line"><td>{{ $etiquetaBase }}</td><td class="td-right">S/ {{ number_format($subtotal, 2) }}</td></tr>
    @if($esGravado)
    <tr class="sub-line"><td>IGV (18%):</td><td class="td-right">S/ {{ number_format($igvM, 2) }}</td></tr>
    @endif
</table>

<div class="line"></div>

<table class="tot">
    <tr class="total-line"><td>TOTAL:</td><td class="td-right">S/ {{ number_format($v->total, 2) }}</td></tr>
</table>

<div class="line"></div>

@if(!empty($qr))
<div class="qr-box">
    <img src="{{ $qr }}" alt="QR">
    <div class="qr-cap">Representación impresa del comprobante electrónico.</div>
</div>
<div class="line"></div>
@endif

<div class="gracias">
    <div>¡Gracias por su compra!</div>
    <div style="margin-top:4px">{{ now()->format('d/m/Y H:i') }}</div>
    @if($v->estado == '0')
        <div style="margin-top:4px; font-weight:bold">*** ANULADA ***</div>
    @endif
</div>

</body>
</html>
