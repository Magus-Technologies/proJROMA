<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Guías de Remisión {{ $despacho->codigo }}</title>
    <style>
        @page { margin: 45px 40px 45px 40px; }
        body { font-family: 'Arial', sans-serif; font-size: 9pt; color: #333; margin: 0; padding: 0; }
        p, div, span, table, td, th, tr { margin: 0; padding: 0; }

        .products-table { width: 100%; border-collapse: collapse; margin-bottom: 5px; border: 2px solid #999; border-radius: 6px; overflow: hidden; }
        .products-table thead { background: #bfc4cc; }
        .products-table th { padding: 6px 4px; font-size: 7.5pt; font-weight: bold; border: 1px solid #999; text-align: center; }
        .products-table td { padding: 4px 5px; font-size: 8pt; border-bottom: 1px solid #e5e7eb; vertical-align: top; }
        .products-table tbody tr:nth-child(even) td { background: #f1f3f5; }

        .footer { text-align: center; margin-top: 20px; padding-top: 12px; border-top: 1px solid #ddd; font-size: 8pt; color: #666; }
        .card { border: 1px solid #777; border-radius: 8px; padding: 10px; }
        .label { font-weight: bold; font-size: 8pt; color: #000; }
        .value { font-size: 8pt; color: #000; }
    </style>
</head>
<body>
    @foreach($guias as $guia)
        @include('pdf.partials.guia-remision-body')

        @unless($loop->last)
            <div style="page-break-after: always;"></div>
        @endunless
    @endforeach
</body>
</html>
