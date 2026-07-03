<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Hoja de Ruta - {{ $despacho->codigo }}</title>
    <style>
        @page { margin: 50px 40px 50px 40px; }
        body { font-family: 'Arial', sans-serif; font-size: 9pt; color: #333; margin: 0; padding: 0; }
        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        .header-table td { vertical-align: top; }
        .info-grid { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        .info-grid td { padding: 4px 8px; border: 1px solid #999; font-size: 8pt; }
        .info-grid .label { background: #bfc4cc; font-weight: bold; width: 20%; }
        table.data { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        table.data thead { background: #bfc4cc; }
        table.data th { padding: 5px 4px; font-size: 7.5pt; font-weight: bold; border: 1px solid #999; text-align: center; }
        table.data td { padding: 3px 4px; font-size: 8pt; border: 1px solid #ccc; }
        table.data tbody tr:nth-child(even) td { background: #f1f3f5; }
        table.data tfoot td { background: #e5e7eb; font-weight: bold; font-size: 8pt; border: 1px solid #999; }
        .title { font-size: 14pt; font-weight: bold; text-align: center; text-transform: uppercase; margin-bottom: 12px; color: #dc2626; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .footer { text-align: center; margin-top: 30px; padding-top: 15px; border-top: 1px solid #ddd; font-size: 7.5pt; color: #666; }
    </style>
</head>
<body>
    @include('pdf.partials.header-pdf', [
        'empresa' => $empresa,
        'tituloDoc' => 'HOJA DE RUTA / DESPACHO',
        'numeroDoc' => $despacho->codigo,
    ])

    <table class="info-grid">
        <tr>
            <td class="label">Ruta</td>
            <td colspan="3">{{ $despacho->ruta?->nombre ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Fecha de reparto</td>
            <td>{{ optional($despacho->fecha_reparto)->format('d/m/Y') }}</td>
            <td class="label">Estado</td>
            <td>{{ ucfirst(strtolower(str_replace('_', ' ', $despacho->estado))) }}</td>
        </tr>
        <tr>
            <td class="label">Vehículo</td>
            <td>{{ $despacho->vehiculo?->placa ?? '-' }} ({{ $despacho->vehiculo?->marca ?? '' }} {{ $despacho->vehiculo?->modelo ?? '' }})</td>
            <td class="label">Capacidad</td>
            <td>{{ number_format((float) ($despacho->vehiculo?->capacidad_kg ?? 0), 0) }} kg</td>
        </tr>
        <tr>
            <td class="label">Conductor</td>
            <td>{{ $despacho->conductor?->nombres ?? '-' }}</td>
            <td class="label">Licencia</td>
            <td>{{ $despacho->conductor?->licencia ?? '-' }} ({{ $despacho->conductor?->licencia_categoria ?? '-' }})</td>
        </tr>
        @if($despacho->observaciones)
        <tr>
            <td class="label">Observaciones</td>
            <td colspan="3">{{ $despacho->observaciones }}</td>
        </tr>
        @endif
    </table>

    <div style="font-size: 10pt; font-weight: bold; margin: 12px 0 6px;">Por artículo (carga)</div>
    @php
        $totCant = $porArticulo->sum('cantidad');
        $totKilos = $porArticulo->sum('kilos');
    @endphp
    <table class="data">
        <thead>
            <tr>
                <th style="width:15%">Código</th>
                <th style="width:50%">Descripción</th>
                <th style="width:15%">Cant.</th>
                <th style="width:20%">Kilos</th>
            </tr>
        </thead>
        <tbody>
            @forelse($porArticulo as $a)
            <tr>
                <td>{{ $a->codigo }}</td>
                <td>{{ $a->descripcion }}</td>
                <td class="text-right">{{ number_format((float) $a->cantidad, 2) }}</td>
                <td class="text-right">{{ number_format((float) $a->kilos, 2) }}</td>
            </tr>
            @empty
            <tr><td colspan="4" class="text-center" style="color:#999;padding:12px;">Sin artículos.</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2">Total general</td>
                <td class="text-right">{{ number_format((float) $totCant, 2) }}</td>
                <td class="text-right">{{ number_format((float) $totKilos, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <div style="font-size: 10pt; font-weight: bold; margin: 12px 0 6px;">Por cliente (reparto)</div>
    @php
        $totMonto = $porCliente->sum('total');
        $totPedidos = $porCliente->sum('pedidos');
    @endphp
    <table class="data">
        <thead>
            <tr>
                <th style="width:15%">Documento</th>
                <th style="width:40%">Denominación</th>
                <th style="width:10%">Ped.</th>
                <th style="width:15%">Kilos</th>
                <th style="width:20%">Total S/</th>
            </tr>
        </thead>
        <tbody>
            @forelse($porCliente as $c)
            <tr>
                <td>{{ $c->documento ?: '-' }}</td>
                <td>{{ $c->denominacion }}</td>
                <td class="text-center">{{ $c->pedidos }}</td>
                <td class="text-right">{{ number_format((float) $c->kilos, 2) }}</td>
                <td class="text-right">{{ number_format((float) $c->total, 2) }}</td>
            </tr>
            @empty
            <tr><td colspan="5" class="text-center" style="color:#999;padding:12px;">Sin clientes.</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2">Total general</td>
                <td class="text-center">{{ $totPedidos }}</td>
                <td class="text-right">{{ number_format((float) $totKilos, 2) }}</td>
                <td class="text-right">{{ number_format((float) $totMonto, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <div style="font-size: 10pt; font-weight: bold; margin: 16px 0 6px;">Por mercado (carga por zona)</div>
    @forelse($porMercado as $mercadoNombre => $items)
        @php
            $mTotCant = $items->sum('cantidad');
            $mTotKilos = $items->sum('kilos');
        @endphp
        <div style="margin-bottom: 10px; border: 1px solid #ccc; border-radius: 4px; overflow: hidden;">
            <div style="background: #e5e7eb; padding: 5px 8px; font-size: 9pt; font-weight: bold;">{{ $mercadoNombre }}</div>
            <table class="data" style="margin-bottom: 0;">
                <thead>
                    <tr>
                        <th style="width:15%">Código</th>
                        <th style="width:50%">Descripción</th>
                        <th style="width:15%">Cant.</th>
                        <th style="width:20%">Kilos</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $a)
                    <tr>
                        <td>{{ $a->codigo }}</td>
                        <td>{{ $a->descripcion }}</td>
                        <td class="text-right">{{ number_format((float) $a->cantidad, 2) }}</td>
                        <td class="text-right">{{ number_format((float) $a->kilos, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2">Subtotal {{ $mercadoNombre }}</td>
                        <td class="text-right">{{ number_format((float) $mTotCant, 2) }}</td>
                        <td class="text-right">{{ number_format((float) $mTotKilos, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    @empty
        <p style="color:#999;font-style:italic;">Sin productos por mercado.</p>
    @endforelse

    <div class="footer">
        Generado por ProjRoma — projroma.com | {{ now()->format('d/m/Y H:i:s') }}
    </div>
</body>
</html>
