<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Listado de clientes - {{ $despacho->codigo }}</title>
    <style>
        @page { margin: 50px 40px 50px 40px; }
        body { font-family: 'Arial', sans-serif; font-size: 9pt; color: #333; margin: 0; padding: 0; }
        .info-grid { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        .info-grid td { padding: 4px 8px; border: 1px solid #999; font-size: 8pt; }
        .info-grid .label { background: #bfc4cc; font-weight: bold; width: 20%; }
        table.data { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        table.data thead { background: #bfc4cc; }
        table.data th { padding: 5px 4px; font-size: 8pt; font-weight: bold; border: 1px solid #999; text-align: center; }
        table.data td { padding: 4px 6px; font-size: 9pt; border: 1px solid #ccc; }
        table.data tbody tr:nth-child(even) td { background: #f1f3f5; }
        table.data tfoot td { background: #e5e7eb; font-weight: bold; font-size: 9pt; border: 1px solid #999; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .mercado td { background: #dbeafe !important; font-weight: bold; font-size: 8pt; }
        .footer { text-align: center; margin-top: 30px; padding-top: 15px; border-top: 1px solid #ddd; font-size: 7.5pt; color: #666; }
    </style>
</head>
<body>
    @include('pdf.partials.header-pdf', [
        'empresa'   => $empresa,
        'tituloDoc' => 'LISTADO DE CLIENTES',
        'numeroDoc' => $despacho->codigo,
    ])

    <table class="info-grid">
        <tr>
            <td class="label">Ruta</td>
            <td>{{ $despacho->ruta?->nombre ?? '-' }}</td>
            <td class="label">Fecha de reparto</td>
            <td>{{ optional($despacho->fecha_reparto)->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <td class="label">Vehículo</td>
            <td>{{ $despacho->vehiculo?->placa ?? '-' }}</td>
            <td class="label">Conductor</td>
            <td>{{ $despacho->conductor?->nombres ?? '-' }}</td>
        </tr>
        @if($filtroMercados)
        <tr>
            <td class="label">Mercados</td>
            <td colspan="3">{{ $filtroMercados }}</td>
        </tr>
        @endif
    </table>

    <table class="data">
        <thead>
            <tr>
                <th style="width:6%;">N°</th>
                <th style="width:16%;">VENTA</th>
                <th style="text-align:left;">CLIENTE</th>
                <th style="width:18%;">TOTAL</th>
            </tr>
        </thead>
        <tbody>
            @php($n = 0)
            @foreach ($clientes as $mercado => $filas)
                @if ($clientes->count() > 1)
                    <tr class="mercado">
                        <td colspan="4">{{ $mercado }}</td>
                    </tr>
                @endif
                @foreach ($filas as $fila)
                    <tr>
                        <td class="text-center">{{ ++$n }}</td>
                        <td class="text-center">{{ $fila->numero_venta ?? '—' }}</td>
                        <td>{{ $fila->cliente }}</td>
                        <td class="text-right">S/ {{ number_format((float) $fila->total, 2) }}</td>
                    </tr>
                @endforeach
            @endforeach
            @if ($n === 0)
                <tr>
                    <td colspan="4" class="text-center" style="padding:14px;">Este despacho no tiene pedidos.</td>
                </tr>
            @endif
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2" class="text-center">{{ $n }} {{ $n === 1 ? 'cliente' : 'clientes' }}</td>
                <td class="text-right">TOTAL</td>
                <td class="text-right">S/ {{ number_format($total, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        {{ $empresa->razon_social ?? '' }} | RUC: {{ $empresa->ruc ?? '' }} — generado el {{ now()->format('d/m/Y H:i') }}
    </div>
</body>
</html>
