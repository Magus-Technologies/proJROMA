<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reporte de Utilidades</title>
    <style>
        @page { margin: 45px 35px 45px 35px; }
        body { font-family: 'Arial', sans-serif; font-size: 8pt; color: #333; margin: 0; padding: 0; }
        * { margin: 0; padding: 0; box-sizing: border-box; }

        .header { width: 100%; margin-bottom: 15px; }
        .header-left { width: 60%; vertical-align: top; padding-right: 15px; }
        .header-right { width: 40%; vertical-align: top; text-align: right; }
        .header-right .box { border: 2px solid #999; border-radius: 8px; overflow: hidden; display: inline-block; min-width: 220px; }
        .header-right .box .ruc { padding: 6px 10px; font-size: 10pt; font-weight: bold; text-align: center; }
        .header-right .box .title { background: #bfc4cc; padding: 8px 10px; font-size: 11pt; font-weight: bold; text-align: center; }
        .header-right .box .period { padding: 6px 10px; font-size: 9pt; font-weight: bold; text-align: center; }

        .kpis { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        .kpis td { padding: 6px 10px; border: 1px solid #ccc; text-align: center; font-size: 8pt; }
        .kpis .label { font-weight: bold; background: #f1f3f5; }
        .kpis .value { font-size: 11pt; font-weight: bold; }

        .section-title { font-size: 10pt; font-weight: bold; color: #1e40af; margin: 15px 0 6px 0; padding-bottom: 3px; border-bottom: 2px solid #1e40af; }

        table.data { width: 100%; border-collapse: collapse; margin-bottom: 8px; font-size: 7.5pt; }
        table.data thead th { background: #bfc4cc; padding: 5px 4px; border: 1px solid #999; font-weight: bold; text-align: center; font-size: 7pt; }
        table.data tbody td { padding: 3px 4px; border: 1px solid #ddd; vertical-align: top; }
        table.data tbody tr:nth-child(even) td { background: #f9fafb; }
        table.data tbody tr.total td { background: #dbeafe; font-weight: bold; border-top: 2px solid #999; }
        .text-right { text-align: right !important; }
        .text-danger { color: #dc2626; }
        .text-success { color: #16a34a; }
        .text-warning { color: #ca8a04; }

        .footer { text-align: center; margin-top: 20px; padding-top: 10px; border-top: 1px solid #ddd; font-size: 7pt; color: #666; }
    </style>
</head>
<body>
    <table class="header">
        <tr>
            <td class="header-left">
                @if(!empty($logoBase64))
                    <img src="{{ $logoBase64 }}" style="max-height:55px;max-width:180px;margin-bottom:5px;">
                @endif
                <div style="font-size: 13pt; font-weight: bold; color: #dc2626;">{{ $empresa->razon_social ?? 'EMPRESA' }}</div>
                <div style="font-size: 7pt; color: #555; margin-top: 3px;">{{ $empresa->direccion ?? '' }}</div>
            </td>
            <td class="header-right">
                <div class="box">
                    <div class="ruc">R.U.C. {{ $empresa->ruc ?? '' }}</div>
                    <div class="title">{{ mb_strtoupper($titulo) }}</div>
                    <div class="period">{{ $periodo }}</div>
                </div>
            </td>
        </tr>
    </table>

    <table class="kpis">
        <tr>
            <td class="label">Ventas</td>
            <td class="label">Costo Total</td>
            <td class="label">Utilidad</td>
            <td class="label">Margen General</td>
        </tr>
        <tr>
            <td class="value" style="color:#2563eb;">S/ {{ number_format($resumen['total_venta'], 2) }}</td>
            <td class="value" style="color:#ca8a04;">S/ {{ number_format($resumen['total_costo'], 2) }}</td>
            <td class="value" style="color:{{ $resumen['total_utilidad'] >= 0 ? '#16a34a' : '#dc2626' }};">
                S/ {{ number_format($resumen['total_utilidad'], 2) }}
            </td>
            <td class="value" style="color:{{ $resumen['margen_general'] >= 10 ? '#16a34a' : ($resumen['margen_general'] >= 0 ? '#ca8a04' : '#dc2626') }};">
                {{ $resumen['margen_general'] }}%
            </td>
        </tr>
    </table>

    @foreach($secciones as $sec)
        <div class="section-title">{{ $sec['titulo'] }}</div>
        <table class="data">
            <thead>
                <tr>
                    @foreach($sec['cabeceras'] as $h)
                        <th>{{ $h }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse($sec['filas'] as $f)
                    <tr>
                        @foreach($sec['cabeceras'] as $i => $h)
                            @php
                                $val = $f[array_keys($sec['cabeceras'])[$i]] ?? '';
                                $isMoney = in_array($i, $sec['cols_moneda'] ?? []);
                                $isPct = in_array($i, $sec['cols_pct'] ?? []);
                                $isNeg = is_numeric($val) && $val < 0;
                                $isLow = is_numeric($val) && $val >= 0 && $val < 10 && $i === (count($sec['cabeceras']) - 1);
                            @endphp
                            <td class="{{ $isMoney || $isPct ? 'text-right' : '' }} {{ $isNeg ? 'text-danger' : ($isLow ? 'text-warning' : '') }}">
                                @if($isMoney)
                                    S/ {{ number_format((float) $val, 2) }}
                                @elseif($isPct)
                                    {{ $val }}%
                                @else
                                    {{ $val }}
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @empty
                    <tr><td colspan="{{ count($sec['cabeceras']) }}" style="padding:10px;text-align:center;color:#999;">Sin datos</td></tr>
                @endforelse
                @if($sec['total'] ?? false)
                    <tr class="total">
                        @foreach($sec['cabeceras'] as $i => $h)
                            @php
                                $tv = $sec['total'][$i] ?? '';
                                $isMoney = in_array($i, $sec['cols_moneda'] ?? []);
                            @endphp
                            <td class="{{ $isMoney ? 'text-right' : '' }}">
                                {{ $i === 0 ? 'TOTAL' : ($isMoney ? 'S/ ' . number_format((float) $tv, 2) : $tv) }}
                            </td>
                        @endforeach
                    </tr>
                @endif
            </tbody>
        </table>
    @endforeach

    <div class="footer">
        Generado el {{ now()->format('d/m/Y H:i') }} | {{ $usuario ?? '' }}
    </div>
</body>
</html>
