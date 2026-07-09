<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Comprobantes {{ $despacho->codigo }}</title>
    <style>
        @page { margin: 50px 40px 50px 40px; }
        body { font-family: 'Arial', sans-serif; font-size: 9pt; color: #333; margin: 0; padding: 0; }
        p, div, span, table, td, th, tr { margin: 0; padding: 0; }

        .products-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
            border: 2px solid #999;
            border-radius: 6px;
            overflow: hidden;
        }
        .products-table thead { background: #bfc4cc; }
        .products-table th { padding: 6px 4px; font-size: 7.5pt; font-weight: bold; border: 1px solid #999; text-align: center; }
        .products-table td { padding: 3px 4px; font-size: 8pt; border: none; vertical-align: top; }
        .products-table tbody tr:nth-child(even) td { background: #f1f3f5; }

        .footer { text-align: center; margin-top: 30px; padding-top: 15px; border-top: 1px solid #ddd; font-size: 9pt; color: #666; }

        .badge-estado { display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 8pt; font-weight: bold; }
        .badge-anulada { background: #fee2e2; color: #991b1b; }
        .badge-activa  { background: #d1fae5; color: #065f46; }

        .salto-pagina { page-break-after: always; }
    </style>
</head>
<body>
    @foreach($ventas as $venta)
        @include('pdf.partials.comprobante-body', ['v' => $venta])

        @unless($loop->last)
            <div class="salto-pagina"></div>
        @endunless
    @endforeach
</body>
</html>
