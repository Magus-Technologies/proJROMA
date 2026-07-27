<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Indicadores Financieros</title>
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

        .kpis { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
        .kpis td { padding: 6px 10px; border: 1px solid #ccc; text-align: center; font-size: 8pt; }
        .kpis .label { font-weight: bold; background: #f1f3f5; }
        .kpis .value { font-size: 11pt; font-weight: bold; }

        .section-title { font-size: 10pt; font-weight: bold; color: #1e40af; margin: 14px 0 6px 0; padding-bottom: 3px; border-bottom: 2px solid #1e40af; }

        table.data { width: 100%; border-collapse: collapse; margin-bottom: 8px; font-size: 7.5pt; }
        table.data thead th { background: #bfc4cc; padding: 5px 4px; border: 1px solid #999; font-weight: bold; text-align: center; font-size: 7pt; }
        table.data tbody td { padding: 4px 5px; border: 1px solid #ddd; vertical-align: top; }
        table.data tbody tr:nth-child(even) td { background: #f9fafb; }
        .col-indicador { width: 18%; font-weight: bold; }
        .col-valor { width: 13%; text-align: right; font-weight: bold; }
        .col-formula { width: 26%; color: #888; font-size: 7pt; }
        .col-nota { width: 36%; }
        .col-estado { width: 7%; text-align: center; font-weight: bold; font-size: 6.5pt; }

        .ok       { color: #047857; }
        .atencion { color: #b45309; }
        .riesgo   { color: #b91c1c; }
        .info     { color: #374151; }

        .nota-metodo { margin-top: 14px; font-size: 6.5pt; color: #999; font-style: italic; }
        .footer { text-align: center; margin-top: 16px; padding-top: 10px; border-top: 1px solid #ddd; font-size: 7pt; color: #666; }
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
                    <div class="title">INDICADORES FINANCIEROS</div>
                    <div class="period">{{ $periodo }}</div>
                </div>
            </td>
        </tr>
    </table>

    <table class="kpis">
        <tr>
            <td class="label">Ventas del Mes</td>
            <td class="label">Utilidad Bruta</td>
            <td class="label">Utilidad Neta</td>
            <td class="label">Margen Neto</td>
        </tr>
        <tr>
            <td class="value" style="color:#2563eb;">S/ {{ number_format($data['ventas_mes'], 2) }}</td>
            <td class="value" style="color:{{ $data['utilidad_bruta'] >= 0 ? '#047857' : '#b91c1c' }};">S/ {{ number_format($data['utilidad_bruta'], 2) }}</td>
            <td class="value" style="color:{{ $data['utilidad_neta'] >= 0 ? '#047857' : '#b91c1c' }};">S/ {{ number_format($data['utilidad_neta'], 2) }}</td>
            <td class="value" style="color:{{ $data['margen_neto'] >= 10 ? '#047857' : ($data['margen_neto'] >= 3 ? '#b45309' : '#b91c1c') }};">{{ $data['margen_neto'] }}%</td>
        </tr>
    </table>

    @foreach($secciones as $sec)
        <div class="section-title">{{ $sec['titulo'] }}</div>
        <table class="data">
            <thead>
                <tr>
                    <th>Indicador</th>
                    <th>Valor</th>
                    <th>Cómo se calcula</th>
                    <th>Interpretación</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sec['items'] as $item)
                    <tr>
                        <td class="col-indicador">{{ $item['label'] }}</td>
                        <td class="col-valor {{ $item['estado'] }}">{{ $item['valor'] }}</td>
                        <td class="col-formula">{{ $item['formula'] }}</td>
                        <td class="col-nota">{{ $item['nota'] }}</td>
                        <td class="col-estado {{ $item['estado'] }}">
                            {{ ['ok' => 'ÓPTIMO', 'atencion' => 'ATENCIÓN', 'riesgo' => 'RIESGO', 'info' => 'INFO'][$item['estado']] }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach

    <div class="nota-metodo">
        * Indicadores calculados automáticamente desde Ventas, Compras, Inventario y Caja del mes en curso.
        Son aproximaciones operativas de gestión y no reemplazan a los estados financieros contables.
    </div>

    <div class="footer">
        Generado el {{ now()->format('d/m/Y H:i') }} | {{ $usuario ?? '' }}
    </div>
</body>
</html>
