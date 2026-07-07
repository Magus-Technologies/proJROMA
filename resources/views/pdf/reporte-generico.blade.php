<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $titulo }}</title>
    <style>
        @page { margin: 50px 40px 50px 40px; }
        body { font-family: 'Arial', sans-serif; font-size: 9pt; color: #333; margin: 0; padding: 0; }
        p, div, span, table, td, th, tr { margin: 0; padding: 0; }

        .report-table { width: 100%; border-collapse: collapse; margin-bottom: 5px; border: 2px solid #999; }
        .report-table thead { background: #bfc4cc; }
        .report-table th { padding: 6px 4px; font-size: 7.5pt; font-weight: bold; border: 1px solid #999; text-align: center; }
        .report-table td { padding: 4px 5px; font-size: 8pt; border-bottom: 1px solid #e5e7eb; vertical-align: top; }
        .report-table tbody tr:nth-child(even) td { background: #f1f3f5; }
        .report-table tbody tr.total td { background: #dbeafe; font-weight: bold; border-top: 2px solid #999; }
        .text-right { text-align: right; }

        .footer { text-align: center; margin-top: 30px; padding-top: 15px; border-top: 1px solid #ddd; font-size: 8pt; color: #666; }
    </style>
</head>
<body>
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
                    {{ $empresa->direccion ?? '' }}
                </div>
            </td>
            <td style="width: 37%; vertical-align: top; text-align: right; padding: 0;">
                <div style="border: 2px solid #bfc4cc; border-radius: 10px; overflow: hidden; width: 240px; float: right;">
                    <div style="text-align: center; padding: 8px 10px; font-size: 12px; font-weight: bold; color: #000;">
                        R.U.C. {{ $empresa->ruc ?? '' }}
                    </div>
                    <div style="background: #bfc4cc; text-align: center; padding: 10px; font-size: 12px; font-weight: bold; color: #000;">
                        {{ mb_strtoupper($titulo) }}
                    </div>
                    <div style="text-align: center; padding: 8px 10px; font-size: 10px; font-weight: bold;">
                        {{ $periodo }}
                    </div>
                </div>
            </td>
        </tr>
    </table>

    <table class="report-table">
        <thead>
            <tr>
                @foreach ($cabeceras as $cabecera)
                    <th>{{ $cabecera }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse ($filas as $i => $fila)
                <tr class="{{ ($ultimaFilaEsTotal ?? false) && $loop->last ? 'total' : '' }}">
                    @foreach ($fila as $col => $celda)
                        <td class="{{ in_array($col, $columnasMoneda ?? []) ? 'text-right' : '' }}">
                            {{ in_array($col, $columnasMoneda ?? []) && is_numeric($celda) ? 'S/ ' . number_format((float) $celda, 2) : $celda }}
                        </td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($cabeceras) }}" style="padding: 15px; text-align: center;">Sin datos en el periodo</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Generado el {{ now()->format('d/m/Y H:i') }}
    </div>
</body>
</html>
